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

        
Schema::create('attendances', function (Blueprint $table) {
    $table->bigIncrements('attendance_id');
    $table->unsignedBigInteger('users_id');
    $table->date('date');
    $table->time('check_in')->nullable();
    $table->time('check_out')->nullable();
    $table->string('location')->nullable();
    $table->enum('status', ['hadir', 'terlambat', 'izin', 'sakit', 'alpha'])->default('hadir');
    $table->timestamps();

    $table->foreign('users_id')->references('users_id')->on('users')->onDelete('cascade');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
