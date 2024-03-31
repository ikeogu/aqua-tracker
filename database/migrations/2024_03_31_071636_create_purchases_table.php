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
        Schema::create('purchases', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->integer('pieces');
            $table->decimal('price_per_unit', 20, 8);
            $table->decimal('amount', 20, 8);
            $table->string('status');
            $table->decimal('size', 20, 8);
            $table->foreignUuid('farm_id')->constrained('farms')->cascadeOnDelete();
            $table->foreignUuid('harvest_id')->constrained('harvests')->cascadeOnDelete();
            $table->foreignUuid('harvest_customer_id')->constrained('harvest_customers')->cascadeOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchases');
    }
};
