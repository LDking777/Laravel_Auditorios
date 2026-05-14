<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Space extends Model
{
  use HasFactory; 

  protected $fillable = [
    'name',
    'slug',
    'type',
    'capacity',
    'description',
    'price_per_hour',
    'is_active',
  ];

  protected function casts(): array
  {
      return [
          'is_active' => 'boolean',
      ];
  } 

    /**
     * Un espacio tiene muchas disponibilidades semanales.
     */
    public function availabilities(): HasMany
    {
        return $this->hasMany(Availability::class);
    }

    /**
     * Un espacio tiene muchas reservas.
     */
    public function reservations(): HasMany
    {
        return $this->hasMany(Reservation::class);
    }

    /**
     * Un espacio tiene muchos bloqueos de horario.
     */
    public function blockedSlots(): HasMany
    {
        return $this->hasMany(BlockedSlot::class);
    }
}
