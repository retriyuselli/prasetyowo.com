<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_event_employee', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_event_id')->constrained('order_events')->onDelete('cascade');
            $table->foreignId('employee_id')->constrained('employees')->onDelete('cascade');
            $table->string('role')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['order_event_id', 'employee_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_event_employee');
    }
};
