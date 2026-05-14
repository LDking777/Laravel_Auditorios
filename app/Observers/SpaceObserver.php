<?php

namespace App\Observers;

use App\Models\Space;
use App\Models\User;
use App\Notifications\SpaceCreatedNotification;

class SpaceObserver
{
    public function created(Space $space): void
    {
        session()->flash('message', "Auditorio \"{$space->name}\" creado exitosamente.");

        $admins = User::where('role', 'admin')->get();
        foreach ($admins as $admin) {
            $admin->notify(new SpaceCreatedNotification($space));
        }
    }

    public function updated(Space $space): void
    {
        session()->flash('message', "Auditorio \"{$space->name}\" actualizado exitosamente.");
    }

    public function deleted(Space $space): void
    {
        session()->flash('message', "Auditorio \"{$space->name}\" eliminado exitosamente.");
    }
}
