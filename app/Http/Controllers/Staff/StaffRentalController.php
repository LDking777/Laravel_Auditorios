<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Reservation;
use App\Models\Space;
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

        $conflict = $this->findOverlap($request->space_id, $start, $end);
        if ($conflict) {
            return back()->with('error', 'El auditorio ya está reservado en ese horario por ' . e($conflict->user_name) . '.');
        }

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

    private function findOverlap($spaceId, $start, $end)
    {
        return Reservation::where('space_id', $spaceId)
            ->whereNotIn('status', ['cancelada', 'rechazada'])
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('start_time', [$start, $end])
                  ->orWhereBetween('end_time', [$start, $end])
                  ->orWhere(function ($q2) use ($start, $end) {
                      $q2->where('start_time', '<=', $start)
                         ->where('end_time', '>=', $end);
                  });
            })
            ->first();
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
        $start = Carbon::parse($validated['start_time']);
        $end = Carbon::parse($validated['end_time']);

        $conflict = $this->findOverlap($validated['space_id'], $start, $end);
        if ($conflict) {
            return back()->with('error', 'El auditorio ya está reservado en ese horario por ' . e($conflict->user_name) . '.');
        }

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


}
