<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('company_subscription_billings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_subscription_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->unsignedBigInteger('amount')->default(0);
            $table->string('currency')->default('IDR');
            $table->dateTime('billed_at')->nullable();
            $table->string('status')->default('paid');
            $table->string('invoice_url')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('company_subscription_billings');
    }
};

