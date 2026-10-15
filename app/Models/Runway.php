<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Runway extends Model
{
    use HasFactory;

    protected $fillable = [
        'airport_id',
        'designation',
        'length_m',
        'width_m',
        'surface',
        'status',
    ];

    public function airport(): BelongsTo
    {
        return $this->belongsTo(Airport::class);
    }
}
