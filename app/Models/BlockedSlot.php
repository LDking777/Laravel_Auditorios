<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BlockedSlot extends Model
{
    use HasFactory; 
    
    protected $fillable = [
        'space_id',
        'start_time',
        'end_time',
        'reason',
    ]; 
    /**
     * El bloqueo pertenece a un espacio (Auditorio).
     */
    public function space(): BelongsTo
    {
        return $this->belongsTo(Space::class);
    }
}
