<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Event extends Model
{
    use HasFactory;

    protected $fillable = [
        'reservation_id',
        'space_id',
        'name',
        'slug',
        'banner_image',
        'ticket_price',
        'description',
        'start_time',
        'end_time',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    protected static function booted()
    {
        static::creating(function ($event) {
            if (!$event->slug) {
                $event->slug = Str::slug($event->name) . '-' . uniqid();
            }
        });
    }

    public function reservation()
    {
        return $this->belongsTo(Reservation::class);
    }

    public function space()
    {
        return $this->belongsTo(Space::class);
    }

    public function tickets()
    {
        return $this->hasMany(Reservation::class, 'event_id');
    }
}
