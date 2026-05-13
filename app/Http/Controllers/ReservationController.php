<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reservation;
use App\Models\Space;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;

class ReservationController extends Controller
{
    /**
     * Muestra la pantalla de pasarela de pago dummy.
     */
    public function checkout(Request $request)
    {
        $request->validate([
            'space_id' => 'required|exists:spaces,id',
            'seats' => 'required|array|min:1',
            'event_id' => 'nullable|exists:events,id',
        ]);

        $space = Space::findOrFail($request->space_id);

        $event = null;
        $ticketPrice = (float) ($space->price_per_hour ?? 15000);

        if ($request->event_id) {
            $event = \App\Models\Event::find($request->event_id);
            if ($event) {
                $ticketPrice = (float) $event->ticket_price;
            }
        }

        return Inertia::render('Public/Checkout', [
            'space' => $space,
            'seats' => $request->seats,
            'event' => $event ? [
                'id' => $event->id,
                'name' => $event->name,
                'ticket_price' => $ticketPrice,
            ] : null,
            'ticketPrice' => $ticketPrice,
        ]);
    }

    /**
     * Guarda la reserva en la base de datos vinculada al usuario autenticado
     * tras simular el éxito en la pasarela de pago.
     */
    public function store(Request $request)
    {
        // 1. Validamos los datos necesarios
        $request->validate([
            'space_id' => 'required|exists:spaces,id',
            'seats' => 'required|array|min:1',
            'seats.*' => 'required|string',
            'event_id' => 'nullable|exists:events,id',
        ]);

        $user = Auth::user();

        $startTime = Carbon::now()->addHour()->startOfHour();
        $endTime = $startTime->copy()->addHours(2);

        if ($request->event_id) {
            $event = \App\Models\Event::find($request->event_id);
            if ($event) {
                $startTime = Carbon::parse($event->start_time);
                $endTime = Carbon::parse($event->end_time);
            }
        }

        $seatsSelected = $request->seats;
        $notesData = [
            'asientos_reservados' => $seatsSelected,
            'comentario_sistema' => 'Reserva pagada y confirmada a través de la pasarela dummy.',
        ];

        if ($request->event_id) {
            $notesData['event_id'] = $request->event_id;
        }

        $reservation = Reservation::create([
            'space_id' => $request->space_id,
            'event_id' => $request->event_id,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'status' => 'confirmada',
            'user_name' => $user->name,
            'user_email' => $user->email,
            'notes' => json_encode($notesData),
        ]);

        return redirect()->route('reservations.ticket', $reservation->id)
            ->with('message', '¡Pago procesado con éxito! Aquí está tu boleto digital.');
    }

    /**
     * Muestra el historial de boletos digitales del usuario autenticado.
     */
    public function userIndex()
    {
        $user = Auth::user();

        $reservations = Reservation::where('user_email', $user->email)
            ->with('space')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($r) {
                $eventName = null;
                if ($r->event_id) {
                    $event = \App\Models\Event::find($r->event_id);
                    $eventName = $event?->name;
                }
                return [
                    'id' => $r->id,
                    'space' => $r->space,
                    'start_time' => $r->start_time,
                    'end_time' => $r->end_time,
                    'status' => $r->status,
                    'user_name' => $r->user_name,
                    'user_email' => $r->user_email,
                    'notes' => $r->notes,
                    'created_at' => $r->created_at,
                    'event_name' => $eventName,
                ];
            });

        return Inertia::render('Public/MyReservations', [
            'reservations' => $reservations,
        ]);
    }

    /**
     * Muestra el boleto digital individual con su diseño premium.
     */
    public function showTicket(Reservation $reservation)
    {
        if ($reservation->user_email !== Auth::user()->email) {
            abort(403, 'No tienes autorización para ver este boleto.');
        }

        $reservation->load('space');

        $eventName = null;
        if ($reservation->event_id) {
            $event = \App\Models\Event::find($reservation->event_id);
            $eventName = $event?->name;
        }

        return Inertia::render('Public/Ticket', [
            'reservation' => $reservation,
            'eventName' => $eventName,
        ]);
    }
}