<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Bifele din lista de bagaj.
 *
 * Important: bifa e legata si de croaziera, nu doar de membru si element.
 * Altfel, cu liste refolosite pe mai multe iesiri, cine bifa "Pasaport" la
 * o croaziera il gasea bifat si la urmatoarea.
 *
 * Inlocuieste tabelele checklist_items / checklist_user, ale caror elemente
 * traiesc acum in content_items.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('checklist_ticks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('trip_id')->constrained()->cascadeOnDelete();
            $table->foreignId('content_item_id')->constrained()->cascadeOnDelete();
            $table->timestamp('checked_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'trip_id', 'content_item_id'], 'bifa_unica');
            $table->index(['trip_id', 'user_id']);
        });

        Schema::dropIfExists('checklist_user');
        Schema::dropIfExists('checklist_items');
    }

    public function down(): void
    {
        Schema::dropIfExists('checklist_ticks');

        Schema::create('checklist_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->nullable()->constrained()->cascadeOnDelete();
            $table->json('label');
            $table->json('hint')->nullable();
            $table->unsignedSmallInteger('position')->default(0);
            $table->timestamps();
        });

        Schema::create('checklist_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('checklist_item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamp('checked_at')->nullable();
            $table->timestamps();
            $table->unique(['checklist_item_id', 'user_id']);
        });
    }
};
