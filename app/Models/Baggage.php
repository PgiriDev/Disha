<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Baggage extends Model
{
    use HasFactory;

    /**
     * "baggage" is uncountable, so Eloquent cannot infer the table name.
     */
    protected $table = 'baggage';

    protected $fillable = [
        'ticket_id',
        'tag_number',
        'weight_kg',
        'type',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'weight_kg' => 'decimal:2',
        ];
    }

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class);
    }
}
