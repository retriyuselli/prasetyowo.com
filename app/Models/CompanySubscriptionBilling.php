<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompanySubscriptionBilling extends Model
{
    protected $fillable = [
        'company_subscription_id',
        'name',
        'amount',
        'currency',
        'billed_at',
        'status',
        'invoice_url',
    ];

    protected $casts = [
        'amount' => 'integer',
        'billed_at' => 'datetime',
    ];

    public function subscription(): BelongsTo
    {
        return $this->belongsTo(CompanySubscription::class, 'company_subscription_id');
    }
}

