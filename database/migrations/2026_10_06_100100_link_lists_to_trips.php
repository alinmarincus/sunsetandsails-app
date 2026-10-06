<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Fiecare croaziera poate avea cate o lista din fiecare tip. Aceeasi lista
 * poate fi folosita de mai multe croaziere — de aceea legatura sta aici,
 * nu invers.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->foreignId('packing_list_id')->nullable()->after('status')
                  ->constrained('content_lists')->nullOnDelete();

            $table->foreignId('menu_list_id')->nullable()->after('packing_list_id')
                  ->constrained('content_lists')->nullOnDelete();

            $table->foreignId('info_list_id')->nullable()->after('menu_list_id')
                  ->constrained('content_lists')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('trips', function (Blueprint $table) {
            $table->dropConstrainedForeignKey('packing_list_id');
            $table->dropConstrainedForeignKey('menu_list_id');
            $table->dropConstrainedForeignKey('info_list_id');
        });
    }
};
