<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('data_pribadis', function (Blueprint $table) {
            $table->string('no_rekening')->nullable()->after('nomor_telepon');
            $table->text('no_rekening_encrypted')->nullable()->after('no_rekening');
        });
    }

    public function down(): void
    {
        Schema::table('data_pribadis', function (Blueprint $table) {
            $table->dropColumn([
                'no_rekening_encrypted',
                'no_rekening',
            ]);
        });
    }
};

