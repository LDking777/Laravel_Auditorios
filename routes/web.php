<?php

use App\Http\Controllers\SpaceController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\Staff\StaffManagementController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Illuminate\Http\Request;

// Ruta raíz: redirige al login si no está autenticado
Route::get('/', function (Request $request) {
    if ($request->user()) {
        return redirect()->route('dashboard');
    }
    return redirect()->route('login');
});

/*
|--------------------------------------------------------------------------
| Rutas Protegidas (Solo usuarios logueados)
|--------------------------------------------------------------------------
*/
Route::middleware([
    'auth:sanctum',
    config('jetstream::auth_session'),
    'verified',
])->group(function () {
    
    // 0. Catálogo de Eventos (pantalla principal para usuarios regulares)
    Route::get('/eventos', [EventController::class, 'publicIndex'])->name('events.public.index');
    Route::get('/eventos/{event:slug}', [EventController::class, 'publicShow'])->name('events.public.show');

    // 1. Catálogo de Auditorios
    Route::get('/auditorios', [SpaceController::class, 'publicIndex'])->name('spaces.public.index');

    // 2. Detalle de un Auditorio específico
    Route::get('/auditorios/{space:slug}', [SpaceController::class, 'publicShow'])->name('spaces.public.show');

    // 3. Procesar la reserva de puestos
    Route::post('/reservations', [ReservationController::class, 'store'])->name('reservations.store');

    // 4. Dashboard Redirect
    Route::get('/dashboard', function (Request $request) {
        $user = $request->user();
        if ($user && ($user->role === 'staff' || $user->role === 'admin')) {
            return redirect()->route('spaces.public.index');
        }
        return redirect()->route('events.public.index');
    })->name('dashboard');

    // 5. Flujo de Pago y Tickets
    Route::get('/finalizar-reserva', [ReservationController::class, 'checkout'])->name('reservations.checkout');
    Route::get('/mis-reservas', [ReservationController::class, 'userIndex'])->name('reservations.user.index');
    Route::get('/ticket/{reservation}', [ReservationController::class, 'showTicket'])->name('reservations.ticket');

   /*
    |--------------------------------------------------------------------------
    | PANEL ADMINISTRATIVO Y STAFF (Protegido por Rol)
    |--------------------------------------------------------------------------
    */
    Route::prefix('admin')->name('admin.')->middleware('admin.role')->group(function () {
        
        // Gestión de Usuarios
        Route::get('/users', [\App\Http\Controllers\Admin\UserController::class, 'index'])->name('users.index');
        Route::put('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'update'])->name('users.update');
        Route::delete('/users/{user}', [\App\Http\Controllers\Admin\UserController::class, 'destroy'])->name('users.destroy');

        // Gestión de Auditorios (CRUD)
        Route::resource('spaces', SpaceController::class);

        // Gestión de Reservas y Asientos
        Route::get('/reservations', [\App\Http\Controllers\Admin\AdminReservationController::class, 'index'])->name('reservations.index');
        Route::post('/reservations/manual', [\App\Http\Controllers\Admin\AdminReservationController::class, 'storeManual'])->name('reservations.storeManual');
        Route::delete('/reservations/{reservation}', [\App\Http\Controllers\Admin\AdminReservationController::class, 'destroy'])->name('reservations.destroy');
    });

   /*
    |--------------------------------------------------------------------------
    | STAFF / ORGANIZADOR (Alquiler por tiempo completo)
    |--------------------------------------------------------------------------
    */
    Route::prefix('staff')->name('staff.')->middleware('admin.role')->group(function () {
        Route::get('/checkout', [\App\Http\Controllers\Staff\StaffRentalController::class, 'checkout'])->name('checkout');
        Route::post('/rentals', [\App\Http\Controllers\Staff\StaffRentalController::class, 'store'])->name('rentals.store');
        Route::get('/rentals', [\App\Http\Controllers\Staff\StaffRentalController::class, 'index'])->name('rentals.index');
        Route::get('/rentals/{reservation}', [\App\Http\Controllers\Staff\StaffRentalController::class, 'show'])->name('rentals.show');
        Route::post('/rentals/{reservation}/assign-seat', [\App\Http\Controllers\Staff\StaffRentalController::class, 'assignSeat'])->name('rentals.assign-seat');
        Route::delete('/rentals/{reservation}/remove-seat', [\App\Http\Controllers\Staff\StaffRentalController::class, 'removeSeat'])->name('rentals.remove-seat');

        // Staff event management
        Route::get('/events/{event}/manage', [StaffManagementController::class, 'manage'])->name('events.manage');
        Route::post('/events/{event}/assign-seat', [StaffManagementController::class, 'assignSeat'])->name('events.assign-seat');
        Route::delete('/events/{event}/remove-seat', [StaffManagementController::class, 'removeSeat'])->name('events.remove-seat');
    });
});