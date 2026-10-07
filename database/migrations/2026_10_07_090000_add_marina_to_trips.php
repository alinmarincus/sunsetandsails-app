<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * De unde se pleaca si ce trebuie stiut despre marina. Datele vin de pe
 * boarding pass-ul companiei de charter: marina de intrare si de iesire,
 * orele de imbarcare si debarcare, contactul de la baza si urgentele.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->string('departure_marina')->nullable()->after('boat');
            $table->string('return_marina')->nullable()->after('departure_marina');
            $table->string('marina_address')->nullable()->after('return_marina');
            $table->string('marina_map_url')->nullable()->after('marina_address');

            $table->time('boarding_time')->nullable()->after('marina_map_url');
            $table->time('disembark_time')->nullable()->after('boarding_time');

            $table->string('base_contact')->nullable()->after('disembark_time');
            $table->string('emergency_phone')->nullable()->after('base_contact');

            $table->json('getting_there')->nullable()->after('emergency_phone');
            $table->string('boarding_pass_path')->nullable()->after('getting_there');
        });
    }

    public function down(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->dropColumn([
                'departure_marina', 'return_marina', 'marina_address', 'marina_map_url',
                'boarding_time', 'disembark_time', 'base_contact', 'emergency_phone',
                'getting_there', 'boarding_pass_path',
            ]);
        });
    }
};
