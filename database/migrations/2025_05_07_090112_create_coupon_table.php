<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        if (!Schema::hasTable('coupon')) {
            Schema::create('coupon', function (Blueprint $table) {
                $table->id();
                $table->string('code', 128);
                $table->string('shop', 255)->nullable();
                $table->unsignedInteger('discount_id');
                $table->unsignedInteger('times_used')->nullable();
                $table->boolean('status')->default(true);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupon');
    }
};
