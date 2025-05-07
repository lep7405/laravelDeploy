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
        Schema::create('discount', function (Blueprint $table) {
            $table->increments('id')->unsigned();
            $table->string('shop', 255)->index();
            $table->string('name', 255);
            $table->timestamp('started_at')->nullable();
            $table->timestamp('expired_at')->nullable();
            $table->enum('type', ["percentage", "amount"]);
            $table->float('value')->unsigned()->nullable();
            $table->integer('usage_limit')->unsigned()->nullable();
            $table->integer('times_used')->unsigned()->default(0);
            $table->integer('trial_days')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('discount');
    }
};
