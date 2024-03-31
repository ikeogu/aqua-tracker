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
        Schema::create('ponds', function (Blueprint $table) {
            $table->uuid('id')->primary();

            $table->string("name");
            $table->string("type");
            $table->bigInteger("holding_capacity");
            $table->bigInteger("unit");
            $table->bigInteger("size");
            $table->decimal("feed_size", 20, 8);
            $table->decimal("mortality_rate",20,8);
            $table->foreignUuid("batch_id")->constrained('batches')->cascadeOnDelete();
            $table->foreignUuid("farm_id")->constrained('farms')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ponds');
    }
};
