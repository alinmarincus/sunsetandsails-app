<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Liste refolosibile: necesar de bagaj, meniu, informatii utile.
 *
 * Toate trei au aceeasi structura — sectiuni, subsectiuni si elemente —
 * si difera doar prin ce campuri folosesc. O lista poate fi atribuita mai
 * multor croaziere, si poate fi duplicata ca sa fie adaptata.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('content_lists', function (Blueprint $table) {
            $table->id();
            $table->string('type', 20);            // packing | menu | info
            $table->json('name');
            $table->json('intro')->nullable();     // text scurt, afisat sus
            $table->timestamps();

            $table->index('type');
        });

        Schema::create('content_sections', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_list_id')->constrained()->cascadeOnDelete();

            // Subsectiune = sectiune cu parinte. O singura treapta de adancime.
            $table->foreignId('parent_id')->nullable()
                  ->constrained('content_sections')->cascadeOnDelete();

            $table->json('title');
            $table->json('note')->nullable();
            $table->unsignedSmallInteger('position')->default(0);
            $table->timestamps();

            $table->index(['content_list_id', 'parent_id', 'position']);
        });

        Schema::create('content_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('content_section_id')->constrained()->cascadeOnDelete();

            $table->json('title');
            $table->json('body')->nullable();
            $table->string('image_path')->nullable();

            // Link — la necesar duce unde se cumpara, la info poate duce oriunde
            $table->string('link_url')->nullable();
            $table->json('link_label')->nullable();

            // Pregatit pentru shopul propriu: cand va exista, linkul se face
            // catre produs, nu catre o adresa scrisa de mana.
            $table->unsignedBigInteger('product_id')->nullable();

            $table->string('video_url')->nullable();

            $table->unsignedSmallInteger('position')->default(0);
            $table->timestamps();

            $table->index(['content_section_id', 'position']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('content_items');
        Schema::dropIfExists('content_sections');
        Schema::dropIfExists('content_lists');
    }
};
