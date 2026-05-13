<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Reservation;
use App\Models\Space;
use Illuminate\Http\Request;
use Inertia\Inertia;

class StaffManagementController extends Controller
{
    public function create(Reservation $reservation)
    {
        if ($reservation->user_email !== auth()->user()->email && auth()->user()->role !== 'admin') {
            abort(403);
        }

        $space = Space::findOrFail($reservation->space_id);

        return Inertia::render('Staff/CreateEvent', [
            'reservation' => [
                'id' => $reservation->id,
                'start_time' => $reservation->start_time,
                'end_time' => $reservation->end_time,
                'space_name' => $space->name,
                'space_id' => $space->id,
            ],
        ]);
    }

    public function store(Request $request, Reservation $reservation)
    {
        if ($reservation->user_email !== auth()->user()->email && auth()->user()->role !== 'admin') {
            abort(403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'ticket_price' => 'required|numeric|min:0',
            'banner_image' => 'nullable|string',
        ]);

        $event = Event::create([
            'reservation_id' => $reservation->id,
            'space_id' => $reservation->space_id,
            'name' => $validated['name'],
            'description' => $validated['description'] ?? '',
            'ticket_price' => $validated['ticket_price'],
            'banner_image' => $validated['banner_image'],
            'start_time' => $reservation->start_time,
            'end_time' => $reservation->end_time,
        ]);

        $notes = json_decode($reservation->notes, true) ?? [];
        $notes['event_id'] = $event->id;
        $reservation->update(['notes' => json_encode($notes)]);

        return redirect()->route('staff.events.manage', $event->id)
            ->with('message', 'Evento creado exitosamente.');
    }

    public function manage(Event $event)
    {
        $user = auth()->user();
        if ($event->reservation->user_email !== $user->email && $user->role !== 'admin') {
            abort(403);
        }

        $event->load('space');

        $tickets = Reservation::where('event_id', $event->id)
            ->whereNotIn('status', ['cancelada', 'rechazada'])
            ->get()
            ->map(function ($r) {
                $notes = json_decode($r->notes, true);
                $seats = [];
                if (isset($notes['asientos_reservados'])) {
                    foreach ($notes['asientos_reservados'] as $s) {
                        $seats[] = is_string($s) ? $s : ($s['seat_id'] ?? '');
                    }
                }
                return [
                    'id' => $r->id,
                    'user_name' => $r->user_name,
                    'user_email' => $r->user_email,
                    'seats' => $seats,
                    'status' => $r->status,
                    'created_at' => $r->created_at,
                ];
            });

        $occupiedSeats = $tickets->pluck('seats')->flatten()->unique()->values()->toArray();

        $allSeats = [];
        $rows = ['A', 'B', 'C', 'D', 'E', 'F'];
        for ($i = 1; $i <= ($event->space->capacity ?? 60); $i++) {
            $allSeats[] = 'A' . $i;
        }

        return Inertia::render('Staff/ManageEvent', [
            'event' => [
                'id' => $event->id,
                'name' => $event->name,
                'slug' => $event->slug,
                'ticket_price' => (float) $event->ticket_price,
                'description' => $event->description,
                'start_time' => $event->start_time,
                'end_time' => $event->end_time,
                'space' => [
                    'id' => $event->space->id,
                    'name' => $event->space->name,
                    'capacity' => $event->space->capacity,
                ],
            ],
            'tickets' => $tickets,
            'occupiedSeats' => $occupiedSeats,
            'totalRevenue' => $tickets->count() * (float) $event->ticket_price,
            'ticketsSold' => $tickets->count(),
        ]);
    }

    public function assignSeat(Request $request, Event $event)
    {
        $user = auth()->user();
        if ($event->reservation->user_email !== $user->email && $user->role !== 'admin') {
            abort(403);
        }

        $validated = $request->validate([
            'seat_id' => 'required|string',
            'guest_name' => 'required|string|max:255',
            'guest_email' => 'nullable|email',
        ]);

        $reservation = Reservation::create([
            'space_id' => $event->space_id,
            'event_id' => $event->id,
            'start_time' => $event->start_time,
            'end_time' => $event->end_time,
            'status' => 'confirmada',
            'user_name' => $validated['guest_name'],
            'user_email' => $validated['guest_email'] ?? 'guest@manual.' . $event->id,
            'notes' => json_encode([
                'asientos_reservados' => [$validated['seat_id']],
                'assigned_by' => 'staff',
                'event_id' => $event->id,
            ]),
        ]);

        return redirect()->route('staff.events.manage', $event->id)
            ->with('message', "Asiento {$validated['seat_id']} asignado a {$validated['guest_name']}.");
    }

    public function removeSeat(Request $request, Event $event)
    {
        $user = auth()->user();
        if ($event->reservation->user_email !== $user->email && $user->role !== 'admin') {
            abort(403);
        }

        $validated = $request->validate([
            'reservation_id' => 'required|exists:reservations,id',
        ]);

        $reservation = Reservation::findOrFail($validated['reservation_id']);
        $reservation->update(['status' => 'cancelada']);

        return redirect()->route('staff.events.manage', $event->id)
            ->with('message', 'Reserva cancelada y asiento liberado.');
    }
}
