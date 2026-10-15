<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'airport_id',
        'description',
    ];

    public function airport(): BelongsTo
    {
        return $this->belongsTo(Airport::class);
    }

    public function staff(): HasMany
    {
        return $this->hasMany(Staff::class);
    }
}
