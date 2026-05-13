<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Reservation;
use App\Models\Space;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class StaffRentalController extends Controller
{
    public function checkout(Request $request)
    {
        $request->validate([
            'space_id' => 'required|exists:spaces,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
        ]);

        $space = Space::findOrFail($request->space_id);
        $start = Carbon::parse($request->start_time);
        $end = Carbon::parse($request->end_time);
        $hours = max(1, $start->diffInHours($end));
        $rate = $space->price_per_hour ?? 15000;
        $total = $hours * $rate;

        return Inertia::render('Staff/Checkout', [
            'space' => [
                'id' => $space->id,
                'name' => $space->name,
                'price_per_hour' => $rate,
            ],
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'hours' => $hours,
            'total' => $total,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'space_id' => 'required|exists:spaces,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time',
            'event_name' => 'required|string|max:255',
            'event_description' => 'nullable|string',
            'event_banner' => 'nullable|string',
            'ticket_price' => 'required|numeric|min:0',
        ]);

        $user = auth()->user();

        DB::beginTransaction();
        try {
            $reservation = Reservation::create([
                'space_id' => $validated['space_id'],
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
                'status' => 'confirmada',
                'user_name' => $user->name,
                'user_email' => $user->email,
                'notes' => json_encode([
                    'reservation_type' => 'full_lease',
                    'event_name' => $validated['event_name'],
                    'asientos_reservados' => [],
                ]),
            ]);

            $event = Event::create([
                'reservation_id' => $reservation->id,
                'space_id' => $validated['space_id'],
                'name' => $validated['event_name'],
                'description' => $validated['event_description'] ?? '',
                'banner_image' => $validated['event_banner'],
                'ticket_price' => $validated['ticket_price'],
                'start_time' => $validated['start_time'],
                'end_time' => $validated['end_time'],
            ]);

            DB::commit();

            return redirect()->route('staff.events.manage', $event->id)
                ->with('message', 'Auditorio alquilado y evento creado exitosamente.');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error al crear el alquiler: ' . $e->getMessage());
        }
    }

    public function index()
    {
        $user = auth()->user();
        $rentals = Reservation::where('user_email', $user->email)
            ->where('notes', 'like', '%"reservation_type":"full_lease"%')
            ->with('space:id,name')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($r) {
                $notes = json_decode($r->notes, true) ?? [];
                $asientos = $notes['asientos_reservados'] ?? [];

                $event = \App\Models\Event::where('reservation_id', $r->id)->first();
                $totalAssigned = count($asientos);

                if ($event) {
                    $ticketCount = Reservation::where('event_id', $event->id)
                        ->where('id', '!=', $r->id)
                        ->whereNotIn('status', ['cancelada', 'rechazada'])
                        ->count();
                    $totalAssigned += $ticketCount;
                }

                return [
                    'id' => $r->id,
                    'space_name' => $r->space?->name ?? 'N/A',
                    'start_time' => $r->start_time,
                    'end_time' => $r->end_time,
                    'status' => $r->status,
                    'event_name' => $notes['event_name'] ?? 'Sin nombre',
                    'assigned' => $totalAssigned,
                    'created_at' => $r->created_at,
                    'has_event' => $event ? true : false,
                    'event_id' => $event?->id,
                ];
            });

        return Inertia::render('Staff/RentalsIndex', [
            'rentals' => $rentals,
        ]);
    }

    public function show(Reservation $reservation)
    {
        $user = auth()->user();
        if ($reservation->user_email !== $user->email && $user->role !== 'admin') {
            abort(403);
        }

        $reservation->load('space:id,name,capacity');
        $notes = json_decode($reservation->notes, true) ?? [];
        $asientos = $notes['asientos_reservados'] ?? [];

        $event = \App\Models\Event::where('reservation_id', $reservation->id)->first();
        if ($event) {
            $ticketReservations = Reservation::where('event_id', $event->id)
                ->where('id', '!=', $reservation->id)
                ->whereNotIn('status', ['cancelada', 'rechazada'])
                ->get();

            foreach ($ticketReservations as $tr) {
                $trNotes = json_decode($tr->notes, true) ?? [];
                $trSeats = $trNotes['asientos_reservados'] ?? [];
                foreach ($trSeats as $seat) {
                    $seatId = is_string($seat) ? $seat : ($seat['seat_id'] ?? '');
                    if ($seatId && !collect($asientos)->firstWhere('seat_id', $seatId)) {
                        $asientos[] = [
                            'seat_id' => $seatId,
                            'guest_name' => $tr->user_name,
                            'guest_email' => $tr->user_email,
                        ];
                    }
                }
            }
        }

        $users = User::select('id', 'name', 'email', 'role')
            ->orderBy('name')
            ->get()
            ->map(fn ($u) => ['id' => $u->id, 'name' => $u->name, 'email' => $u->email, 'role' => $u->role]);

        return Inertia::render('Staff/ManageRental', [
            'reservation' => [
                'id' => $reservation->id,
                'space_name' => $reservation->space->name,
                'start_time' => $reservation->start_time,
                'end_time' => $reservation->end_time,
                'status' => $reservation->status,
            ],
            'capacity' => $reservation->space->capacity,
            'assignedSeats' => $asientos,
            'eventName' => $notes['event_name'] ?? 'Evento',
            'users' => $users,
        ]);
    }

    public function assignSeat(Request $request, Reservation $reservation)
    {
        $user = auth()->user();
        if ($reservation->user_email !== $user->email && $user->role !== 'admin') {
            abort(403);
        }

        $request->validate([
            'seat_id' => 'required|string',
            'guest_name' => 'required|string|max:255',
            'guest_email' => 'required|email',
        ]);

        $notes = json_decode($reservation->notes, true) ?? [];
        $asientos = $notes['asientos_reservados'] ?? [];

        foreach ($asientos as $a) {
            if (($a['seat_id'] ?? '') === $request->seat_id) {
                return back()->with('error', 'El asiento ' . $request->seat_id . ' ya está asignado.');
            }
        }

        $event = \App\Models\Event::where('reservation_id', $reservation->id)->first();
        if ($event) {
            $occupied = Reservation::where('event_id', $event->id)
                ->where('id', '!=', $reservation->id)
                ->whereNotIn('status', ['cancelada', 'rechazada'])
                ->get()
                ->pluck('notes')
                ->flatMap(function ($n) {
                    $parsed = json_decode($n, true);
                    $seats = $parsed['asientos_reservados'] ?? [];
                    return array_map(fn ($s) => is_string($s) ? $s : ($s['seat_id'] ?? ''), $seats);
                })
                ->filter()
                ->unique()
                ->values()
                ->toArray();

            if (in_array($request->seat_id, $occupied)) {
                return back()->with('error', 'El asiento ' . $request->seat_id . ' ya está ocupado por otro usuario.');
            }
        }

        $asientos[] = [
            'seat_id' => $request->seat_id,
            'guest_name' => $request->guest_name,
            'guest_email' => $request->guest_email,
        ];

        $notes['asientos_reservados'] = $asientos;
        $reservation->update(['notes' => json_encode($notes)]);

        if ($event) {
            Reservation::create([
                'space_id' => $reservation->space_id,
                'event_id' => $event->id,
                'start_time' => $reservation->start_time,
                'end_time' => $reservation->end_time,
                'status' => 'confirmada',
                'user_name' => $request->guest_name,
                'user_email' => $request->guest_email,
                'notes' => json_encode([
                    'asientos_reservados' => [$request->seat_id],
                    'assigned_by' => 'staff_rental',
                    'event_id' => $event->id,
                ]),
            ]);
        }

        return back()->with('message', 'Asiento ' . $request->seat_id . ' asignado a ' . $request->guest_name . '.');
    }

    public function removeSeat(Request $request, Reservation $reservation)
    {
        $user = auth()->user();
        if ($reservation->user_email !== $user->email && $user->role !== 'admin') {
            abort(403);
        }

        $request->validate(['seat_id' => 'required|string']);

        $event = \App\Models\Event::where('reservation_id', $reservation->id)->first();

        $notes = json_decode($reservation->notes, true) ?? [];
        $asientos = $notes['asientos_reservados'] ?? [];

        $removedSeat = null;
        foreach ($asientos as $a) {
            if (($a['seat_id'] ?? '') === $request->seat_id) {
                $removedSeat = $a;
                break;
            }
        }

        $notes['asientos_reservados'] = array_values(
            array_filter($asientos, fn ($a) => ($a['seat_id'] ?? '') !== $request->seat_id)
        );

        $reservation->update(['notes' => json_encode($notes)]);

        if ($removedSeat && isset($removedSeat['guest_email'])) {
            if ($event) {
                Reservation::where('event_id', $event->id)
                    ->where('user_email', $removedSeat['guest_email'])
                    ->where('user_name', $removedSeat['guest_name'])
                    ->where('status', 'confirmada')
                    ->update(['status' => 'cancelada']);
            }
        } elseif ($event) {
            Reservation::where('event_id', $event->id)
                ->where('id', '!=', $reservation->id)
                ->whereNotIn('status', ['cancelada', 'rechazada'])
                ->get()
                ->each(function ($tr) use ($request) {
                    $trNotes = json_decode($tr->notes, true) ?? [];
                    $trSeats = $trNotes['asientos_reservados'] ?? [];
                    foreach ($trSeats as $seat) {
                        $seatId = is_string($seat) ? $seat : ($seat['seat_id'] ?? '');
                        if ($seatId === $request->seat_id) {
                            $tr->update(['status' => 'cancelada']);
                        }
                    }
                });
        }

        return back()->with('message', 'Asiento ' . $request->seat_id . ' liberado.');
    }
}
