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
        Schema::create('subscribed_plans', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('tenant_id')->constrained('tenants')->onDelete('cascade');
            $table->string('status');
            $table->foreignUuid('subscription_plan_id')->constrained('subscription_plans')->onDelete('cascade');
            $table->string('reference');
            $table->string('payment_method')->nullable();
            $table->bigInteger('amount');
            $table->integer('no_of_months');
            $table->timestamp('start_date')->nullable();
            $table->timestamp('end_date')->default(now());
            $table->string('type')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('subscribed_plans');
    }
};
