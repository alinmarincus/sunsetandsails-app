<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('phone')->nullable()->after('email');
            $table->string('avatar_path')->nullable()->after('phone');
            $table->string('locale', 5)->default('ro')->after('avatar_path');

            // prospect = s-a inscris, nu a navigat inca; member = a fost pe cel putin o croaziera
            $table->string('status', 20)->default('prospect')->after('locale');
            $table->boolean('is_admin')->default(false)->after('status');

            // Wall-ul de poze: privat implicit, public doar daca membrul alege
            $table->boolean('wall_public')->default(false)->after('is_admin');
            $table->string('wall_slug', 32)->nullable()->unique()->after('wall_public');

            // Consimtamant pentru aparitia in pozele croazierei
            $table->timestamp('photo_consent_at')->nullable()->after('wall_slug');

            $table->timestamp('joined_club_at')->nullable()->after('photo_consent_at');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'phone', 'avatar_path', 'locale', 'status', 'is_admin',
                'wall_public', 'wall_slug', 'photo_consent_at', 'joined_club_at',
            ]);
        });
    }
};
