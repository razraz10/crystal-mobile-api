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
        Schema::create('users', function (Blueprint $table) {
            //TODO all fileds must be required.
            $table->id();
            $table->string('name')->nullable();
            $table->string('personal_number')->unique()->nullable();
            $table->string('phone_number')->unique()->nullable();
            $table->string('email')->unique()->nullable();
            ///1 indicate keva, 2 indicate miluim, 3 indicate sadir, 4 indicate oved_tzahal
            $table->integer('employee_type');
            ///foreignKay on the permissions table.
            $table->foreignId('permission_id')->nullable();
            $table->boolean('is_deleted')->default(false);
            $table->rememberToken();
            $table->timestamps();
            ////set relations on others table.
            $table->foreign('permission_id')->references('id')->on('permissions')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
