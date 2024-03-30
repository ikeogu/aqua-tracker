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
        Schema::create('batches', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->foreignUuid('farm_id')->constrained('farms')->cascadeOnDelete();
            $table->bigInteger('unit_purchase');
            $table->decimal('price_per_unit',20,8);
            $table->decimal('amount_spent',20,8);
            $table->string('fish_specie');
            $table->string('fish_type');
            $table->mediumText('vendor');
            $table->string('status');
            $table->string('date_purchased');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('batches');
    }
};
