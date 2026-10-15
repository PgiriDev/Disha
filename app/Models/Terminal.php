<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Terminal extends Model
{
    use HasFactory;

    protected $fillable = [
        'airport_id',
        'name',
        'code',
        'passenger_capacity',
    ];

    public function airport(): BelongsTo
    {
        return $this->belongsTo(Airport::class);
    }

    public function gates(): HasMany
    {
        return $this->hasMany(Gate::class);
    }
}
