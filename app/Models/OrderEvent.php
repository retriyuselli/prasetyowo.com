<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class OrderEvent extends Model
{
    protected $fillable = [
        'order_id',
        'type',
        'name',
        'event_date',
        'start_time',
        'end_time',
        'location',
    ];

    protected $casts = [
        'event_date' => 'date',
        'start_time' => 'string',
        'end_time' => 'string',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'order_event_employee')
            ->withPivot(['role', 'notes'])
            ->withTimestamps();
    }
}
