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
        Schema::create('months', function (Blueprint $table) {
            $table->id();

            ///deffault is 0. 1- Red, 2 is Yellow, 3 is Green.
            //TODO:: all fileds must be required
            $table->integer('JAN')->nullable();
            $table->integer('FEB')->nullable();
            $table->integer('MAR')->nullable();
            $table->integer('APR')->nullable();
            $table->integer('MAY')->nullable();
            $table->integer('JUN')->nullable();
            $table->integer('JUL')->nullable();
            $table->integer('AUG')->nullable();
            $table->integer('SEP')->nullable();
            $table->integer('OCT')->nullable();
            $table->integer('NOV')->nullable();
            $table->integer('DEC')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('months');
    }
};
