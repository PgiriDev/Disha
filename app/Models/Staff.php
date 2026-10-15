<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Staff extends Model
{
    use HasFactory;

    /**
     * "staff" is uncountable, so Eloquent cannot infer the table name.
     */
    protected $table = 'staff';

    protected $fillable = [
        'user_id',
        'department_id',
        'employee_code',
        'first_name',
        'last_name',
        'job_title',
        'phone',
        'email',
        'hire_date',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'hire_date' => 'date',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function recordedOperations(): HasMany
    {
        return $this->hasMany(FlightOperation::class, 'recorded_by');
    }

    public function reportedDelays(): HasMany
    {
        return $this->hasMany(FlightDelay::class, 'reported_by');
    }

    public function registeredCancellations(): HasMany
    {
        return $this->hasMany(FlightCancellation::class, 'cancelled_by');
    }

    public function getFullNameAttribute(): string
    {
        return trim("{$this->first_name} {$this->last_name}");
    }
}
