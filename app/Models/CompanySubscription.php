<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\CompanySubscriptionBilling;

class CompanySubscription extends Model
{
    protected $fillable = [
        'company_id',
        'plan_code',
        'plan_name',
        'plan_price',
        'billing_cycle',
        'usage_reset_at',
        'on_demand_enabled',
        'status',
        'canceled_at',
    ];

    protected $casts = [
        'plan_price' => 'integer',
        'usage_reset_at' => 'datetime',
        'on_demand_enabled' => 'boolean',
        'canceled_at' => 'datetime',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function billings(): HasMany
    {
        return $this->hasMany(CompanySubscriptionBilling::class);
    }
}
