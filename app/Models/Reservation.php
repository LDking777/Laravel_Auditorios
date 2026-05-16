<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'space_id',
        'event_id',
        'start_time',
        'end_time',
        'status',
        'user_name',
        'user_email',
        'notes',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function space()
    {
        return $this->belongsTo(Space::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_email', 'email');
    }

    public function event()
    {
        return $this->belongsTo(Event::class);
    }
}