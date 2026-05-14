<?php

namespace App\Http\Controllers;

use App\Models\Space;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class SpaceController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | MÓDULO PÚBLICO (Cualquier usuario lo ve)
    |--------------------------------------------------------------------------
    */

    /**
     * Muestra el listado público de auditorios.
     */
    public function publicIndex(Request $request)
    {
        $query = Space::where('is_active', true);

        if ($request->has('type') && $request->type != '') {
            $query->where('type', $request->type);
        }

        $spaces = $query->get();

        $spaceIds = $spaces->pluck('id');

        $upcomingReservations = \App\Models\Reservation::whereIn('space_id', $spaceIds)
            ->whereNotIn('status', ['cancelada', 'rechazada'])
            ->where('end_time', '>', now())
            ->get();

        $upcomingEventsMap = [];
        foreach ($upcomingReservations as $res) {
            $notesData = json_decode($res->notes, true);
            if (($notesData['reservation_type'] ?? '') === 'full_lease') {
                $upcomingEventsMap[$res->space_id][] = [
                    'id' => $res->id,
                    'event_name' => $notesData['event_name'] ?? 'Evento',
                    'start_time' => $res->start_time,
                    'end_time' => $res->end_time,
                    'organized_by' => $res->user_name,
                ];
            }
        }

        return Inertia::render('Public/SpacesIndex', [
            'spaces' => $spaces,
            'filters' => $request->only(['type']),
            'upcomingEventsMap' => $upcomingEventsMap,
        ]);
    }

    /**
     * Muestra el detalle de un auditorio específico públicamente por su Slug.
     */
    public function publicShow(Space $space)
    {
        $space->load('availabilities');

        $reservations = \App\Models\Reservation::where('space_id', $space->id)
            ->whereNotIn('status', ['cancelada', 'rechazada'])
            ->get();

        $reservedSeats = [];
        $upcomingEvents = [];

        foreach ($reservations as $reservation) {
            if (!empty($reservation->notes)) {
                $notesData = json_decode($reservation->notes, true);

                $isFullLease = ($notesData['reservation_type'] ?? '') === 'full_lease';

                if ($isFullLease) {
                    $start = \Carbon\Carbon::parse($reservation->start_time);
                    $end = \Carbon\Carbon::parse($reservation->end_time);

                    if ($end > now()) {
                        $upcomingEvents[] = [
                            'id' => $reservation->id,
                            'event_name' => $notesData['event_name'] ?? 'Evento',
                            'start_time' => $reservation->start_time,
                            'end_time' => $reservation->end_time,
                            'organized_by' => $reservation->user_name,
                        ];

                        if ($start <= now()->addHours(3) && $end > now()) {
                            $allSeats = [];
                            for ($i = 1; $i <= ($space->capacity ?? 60); $i++) {
                                $allSeats[] = 'A' . $i;
                            }
                            $reservedSeats = array_merge($reservedSeats, $allSeats);
                        }

                        continue;
                    }
                }

                if (isset($notesData['asientos_reservados']) && is_array($notesData['asientos_reservados'])) {
                    foreach ($notesData['asientos_reservados'] as $seat) {
                        $reservedSeats[] = is_string($seat) ? $seat : ($seat['seat_id'] ?? '');
                    }
                }
            }
        }

        $reservedSeats = array_values(array_unique(array_filter($reservedSeats)));

        return Inertia::render('Public/SpaceShow', [
            'space' => $space,
            'dbReservedSeats' => $reservedSeats,
            'upcomingEvents' => $upcomingEvents,
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | PANEL ADMINISTRATIVO (CRUD Protegido)
    |--------------------------------------------------------------------------
    */

    /**
     * Listado de auditorios en el panel de administración.
     */
    public function index()
{
    // Si el usuario no está logueado o su rol no es 'admin', lo rebota al dashboard público
    if (auth()->user()->role !== 'admin') {
        return redirect()->route('spaces.public.index')
            ->with('error', 'No tienes permisos de administrador.');
    }

    $spaces = Space::all();
    return Inertia::render('Admin/Spaces/Index', [
        'spaces' => $spaces
    ]);
}

    /**
     * Formulario para crear un nuevo auditorio.
     */
    public function create()
    {
        return redirect()->route('admin.spaces.index')
            ->with('message', 'Usa el botón "Nuevo Auditorio" en el panel.');
    }

    /**
     * Guarda un nuevo auditorio en la base de datos.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:spaces,name',
            'type' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'description' => 'required|string',
            'price_per_hour' => 'nullable|numeric|min:0',
            'is_active' => 'required|boolean',
        ]);

        // Generamos el slug automáticamente a partir del nombre
        $validated['slug'] = Str::slug($request->name);

        Space::create($validated);

        return redirect()->route('admin.spaces.index');
    }

    /**
     * Muestra el detalle de un auditorio específico en el panel.
     */
    public function show(Space $space)
    {
        return redirect()->route('admin.spaces.index')
            ->with('message', 'Selecciona un auditorio de la lista para ver su detalle.');
    }

    /**
     * Formulario para editar un auditorio existente.
     */
    public function edit(Space $space)
    {
        return redirect()->route('admin.spaces.index')
            ->with('message', 'Usa el botón "Editar" en la lista de auditorios.');
    }

    /**
     * Actualiza el auditorio en la base de datos.
     */
    public function update(Request $request, Space $space)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:spaces,name,' . $space->id,
            'type' => 'required|string|max:255',
            'capacity' => 'required|integer|min:1',
            'description' => 'required|string',
            'price_per_hour' => 'nullable|numeric|min:0',
            'is_active' => 'required|boolean',
        ]);

        $validated['slug'] = Str::slug($request->name);

        $space->update($validated);

        return redirect()->route('admin.spaces.index');
    }

    /**
     * Elimina un auditorio del sistema.
     */
    public function destroy(Space $space)
    {
        $space->delete();

        return redirect()->route('admin.spaces.index');
    }
}