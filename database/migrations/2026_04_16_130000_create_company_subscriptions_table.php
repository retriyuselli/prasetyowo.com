<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained()->nullOnDelete();
            $table->string('plan_code')->default('hastana');
            $table->string('plan_name')->default('Anggota Hastana');
            $table->unsignedBigInteger('plan_price')->default(8500000);
            $table->string('billing_cycle')->default('2_years');
            $table->dateTime('usage_reset_at')->nullable();
            $table->boolean('on_demand_enabled')->default(false);
            $table->string('status')->default('active');
            $table->dateTime('canceled_at')->nullable();
            $table->timestamps();

            $table->unique('company_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_subscriptions');
    }
};
