<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('data_pribadis', function (Blueprint $table) {
            $table->string('bank_name')->nullable()->after('no_rekening');
        });
    }

    public function down(): void
    {
        Schema::table('data_pribadis', function (Blueprint $table) {
            $table->dropColumn('bank_name');
        });
    }
};

