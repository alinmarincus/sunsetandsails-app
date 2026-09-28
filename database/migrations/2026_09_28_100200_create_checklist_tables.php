<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Lista cu necesarul. trip_id null = sablon global, copiat la croaziere noi.
        Schema::create('checklist_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->nullable()->constrained()->cascadeOnDelete();
            $table->json('label');
            $table->json('hint')->nullable();
            $table->unsignedSmallInteger('position')->default(0);
            $table->timestamps();

            $table->index(['trip_id', 'position']);
        });

        // Bifele sunt personale: fiecare participant isi vede doar lista lui.
        Schema::create('checklist_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('checklist_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('checked_at')->nullable();
            $table->timestamps();

            $table->unique(['checklist_item_id', 'user_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('checklist_user');
        Schema::dropIfExists('checklist_items');
    }
};
