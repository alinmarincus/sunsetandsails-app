<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trips', function (Blueprint $table) {
            $table->id();
            $table->string('slug')->unique();

            // Campuri traductibile — JSON {"ro": "...", "en": "..."}
            $table->json('title');
            $table->json('destination')->nullable();
            $table->json('summary')->nullable();
            $table->json('description')->nullable();

            $table->string('cover_image')->nullable();
            $table->date('start_date');
            $table->date('end_date');
            $table->unsignedSmallInteger('capacity')->nullable();
            $table->string('boat')->nullable();

            // Folderul din Google Drive cu pozele croazierei (sync ulterior)
            $table->string('drive_folder_id')->nullable();

            $table->string('status', 20)->default('draft');   // draft | published
            $table->timestamps();

            $table->index(['status', 'start_date']);
        });

        // Itinerar, zi cu zi
        Schema::create('trip_days', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('day_number');
            $table->date('date')->nullable();
            $table->json('port');
            $table->json('note')->nullable();
            $table->timestamps();

            $table->index(['trip_id', 'day_number']);
        });

        // Participarea unui membru la o croaziera
        Schema::create('trip_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('status', 20)->default('confirmed'); // confirmed | cancelled
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['trip_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trip_user');
        Schema::dropIfExists('trip_days');
        Schema::dropIfExists('trips');
    }
};
