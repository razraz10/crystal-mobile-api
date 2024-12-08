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
        Schema::create('inhibits', function (Blueprint $table) {
            $table->id();
            //TODO:: all fileds must be required
            $table->text('inhibit_ta')->nullable();
            $table->text('inhibit_mrahs')->nullable();
            $table->text('activ_required')->nullable();
            $table->text('impacted_tasks')->nullable();
            $table->longText('comment')->nullable();
            ///deffault is 0. 1- Red, 2 is Yellow, 3 is Green.
            $table->integer('color_comment')->nullable();
            $table->integer('year')->nullable();
            $table->integer('month')->nullable();

            $table->foreignId('updated_by')->nullable();
            $table->foreignId('created_by')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->timestamps();
            ////set relations on others table.
            $table->foreign('created_by')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('updated_by')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('inhibits');
    }
};
