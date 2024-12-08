<?php

use App\Enums\MonthEnum;
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
        Schema::create('markets', function (Blueprint $table) {
            $table->id();

            //TODO:: all fileds must be required
            $table->integer('id_num')->nullable();
            $table->text('name_meshek')->nullable();
            $table->longText('comment')->nullable();
            ///deffault is 0. 1- Red, 2 is Yellow, 3 is Green.
            $table->integer('color_comment')->nullable();
            $table->date('expired_agreement')->nullable();
            $table->boolean('is_open');
            $table->integer('year')->nullable();

            $table->foreignId('updated_by')->nullable();
            $table->foreignId('created_by')->nullable();

            $table->foreignId('month_id')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->timestamps();
            ////set relations on others table.
            $table->foreign('month_id')->references('id')->on('months')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('created_by')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('updated_by')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('markets');
    }
};
