<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->foreignId('payment_method_secondary_id')
                ->nullable()
                ->constrained('payment_methods')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropForeign(['payment_method_secondary_id']);
            $table->dropColumn('payment_method_secondary_id');
        });
    }
};

