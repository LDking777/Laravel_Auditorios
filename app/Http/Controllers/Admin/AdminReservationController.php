<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reservation;
use App\Models\Space;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminReservationController extends Controller
{
    public function index()
    {
        $all = Reservation::with('space:id,name,capacity')
            ->orderBy('created_at', 'desc')
            ->get();

        $fullLease = [];
        $individual = [];

        foreach ($all as $reservation) {
            $parsedNotes = $reservation->notes ? json_decode($reservation->notes, true) : [];

            $isFullLease = ($parsedNotes['reservation_type'] ?? '') === 'full_lease';

            $data = [
                'id' => $reservation->id,
                'space_name' => $reservation->space?->name ?? 'N/A',
                'user_name' => $reservation->user_name,
                'user_email' => $reservation->user_email,
                'status' => $reservation->status,
                'seats' => $parsedNotes['asientos_reservados'] ?? [],
                'start_time' => $reservation->start_time,
                'end_time' => $reservation->end_time,
                'created_at' => $reservation->created_at,
            ];

            if ($isFullLease) {
                $notes = $parsedNotes;
                $data['event_name'] = $notes['event_name'] ?? 'Sin nombre';
                $fullLease[] = $data;
            } else {
                $individual[] = $data;
            }
        }

        return Inertia::render('Admin/Reservations/Index', [
            'fullLeaseReservations' => $fullLease,
            'individualReservations' => $individual,
        ]);
    }

    public function storeManual(Request $request)
    {
        $validated = $request->validate([
            'space_id'    => 'required|exists:spaces,id',
            'user_name'   => 'required|string|max:255',
            'user_email'  => 'required|email',
            'start_time'  => 'required|date',
            'end_time'    => 'nullable|date',
            'asientos'    => 'required|array',
        ]);

        Reservation::create([
            'space_id'   => $validated['space_id'],
            'user_name'  => $validated['user_name'],
            'user_email' => $validated['user_email'],
            'start_time' => $validated['start_time'],
            'end_time'   => $validated['end_time'] ?? now()->addHours(2),
            'status'     => 'confirmada',
            'notes'      => json_encode(['asientos_reservados' => $validated['asientos']]),
        ]);

        return back()->with('message', 'Reserva manual registrada con éxito.');
    }

    public function destroy(Reservation $reservation)
    {
        $reservation->delete();
        return back()->with('message', 'Reserva eliminada. Asientos liberados.');
    }
}
