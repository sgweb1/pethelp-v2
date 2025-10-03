<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Rozszerza pole teryt_code z 10 do 15 znaków.
     *
     * Nowe 12-cyfrowe kody jednostek GUS wymagają więcej miejsca
     * (np. '071412865000' dla Warszawy).
     */
    public function up(): void
    {
        Schema::table('population_data', function (Blueprint $table) {
            $table->string('teryt_code', 15)->change();
        });
    }

    /**
     * Przywraca pierwotną długość pola teryt_code.
     */
    public function down(): void
    {
        Schema::table('population_data', function (Blueprint $table) {
            $table->string('teryt_code', 10)->change();
        });
    }
};
