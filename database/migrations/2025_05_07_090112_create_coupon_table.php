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
        Schema::create('coupon', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code', 128)->index();
            $table->string('shop', 255)->nullable()->index();
            $table->integer('discount_id')->unsigned()->index();
            $table->integer('times_used')->unsigned()->nullable();
            $table->boolean ( 'status' )->default ( true );
            $table->timestamps();
            $table->foreign('discount_id')->references('id')->on('discounts');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupon');
    }
};
