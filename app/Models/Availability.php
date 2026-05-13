<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Availability extends Model
{
    use HasFactory; 

    protected $fillable = [
        'space_id',
        'day_of_week',
        'start_time',
        'end_time',
    ]; 

    /**
     * La disponibilidad pertenece a un espacio (Auditorio).
     */
    public function space(): BelongsTo
    {
        return $this->belongsTo(Space::class);
    }
}
