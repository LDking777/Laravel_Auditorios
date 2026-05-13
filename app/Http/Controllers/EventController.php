<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Space;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EventController extends Controller
{
    public function publicIndex()
    {
        $events = Event::with('space:id,name,type,slug', 'reservation')
            ->where('end_time', '>', now())
            ->orderBy('start_time')
            ->get()
            ->map(function ($e) {
                return [
                    'id' => $e->id,
                    'name' => $e->name,
                    'slug' => $e->slug,
                    'banner_image' => $e->banner_image,
                    'ticket_price' => $e->ticket_price,
                    'description' => $e->description,
                    'start_time' => $e->start_time,
                    'end_time' => $e->end_time,
                    'space_name' => $e->space?->name,
                    'space_type' => $e->space?->type,
                    'space_slug' => $e->space?->slug,
                    'organizer_name' => $e->reservation?->user_name ?? 'Staff Universitario',
                ];
            });

        return Inertia::render('Public/EventsIndex', [
            'events' => $events,
        ]);
    }

    public function publicShow(Event $event)
    {
        $event->load('space');

        $reservations = \App\Models\Reservation::where('space_id', $event->space_id)
            ->where(function ($q) use ($event) {
                $q->where('event_id', $event->id)
                  ->orWhere(function ($sub) {
                      $sub->where('notes', 'like', '%"reservation_type":"full_lease"%');
                  });
            })
            ->whereNotIn('status', ['cancelada', 'rechazada'])
            ->get();

        $reservedSeats = [];
        foreach ($reservations as $r) {
            $notes = json_decode($r->notes, true);
            if (isset($notes['asientos_reservados']) && is_array($notes['asientos_reservados'])) {
                foreach ($notes['asientos_reservados'] as $seat) {
                    $reservedSeats[] = is_string($seat) ? $seat : ($seat['seat_id'] ?? '');
                }
            }
        }

        $reservedSeats = array_values(array_unique(array_filter($reservedSeats)));

        return Inertia::render('Public/EventShow', [
            'event' => [
                'id' => $event->id,
                'name' => $event->name,
                'slug' => $event->slug,
                'banner_image' => $event->banner_image,
                'ticket_price' => (float) $event->ticket_price,
                'description' => $event->description,
                'start_time' => $event->start_time,
                'end_time' => $event->end_time,
                'space' => [
                    'id' => $event->space->id,
                    'name' => $event->space->name,
                    'type' => $event->space->type,
                    'slug' => $event->space->slug,
                    'capacity' => $event->space->capacity,
                ],
            ],
            'dbReservedSeats' => $reservedSeats,
        ]);
    }
}
