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
        if (!Schema::hasTable('discounts')) {
            Schema::create('discounts', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->timestamp('started_at')->nullable();
                $table->timestamp('expired_at')->nullable();
                $table->enum('type', ['percentage', 'amount']);
                $table->float('value', 53)->unsigned()->nullable();
                $table->unsignedInteger('usage_limit')->nullable();
                $table->unsignedInteger('times_used')->default(0);
                $table->integer('trial_days')->default(0);
                $table->tinyInteger('status')->default(1);
                $table->integer('discount_month')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discount');
    }
};
