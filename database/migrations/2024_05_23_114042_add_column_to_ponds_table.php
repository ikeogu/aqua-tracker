<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('ponds', function (Blueprint $table) {
            //
            $table->string('unit_size')->nullable();
            $table->decimal('size')->change();
            $table->decimal('feed_size')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ponds', function (Blueprint $table) {
            $table->dropColumn('unit_size');
        });
    }
};
