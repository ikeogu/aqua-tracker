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
            $table->string('feed_size')->nullable()->change();
            $table->string('size')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ponds', function (Blueprint $table) {
            //
            $table->decimal('feed_size', 8,2)->change();
            $table->bigInteger('size')->change();
        });
    }
};
