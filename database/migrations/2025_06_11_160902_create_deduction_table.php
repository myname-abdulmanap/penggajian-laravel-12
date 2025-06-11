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
        Schema::create('deductions', function (Blueprint $table) {
            $table->id('deduction_id');
            $table->string('name', 100);
            $table->enum('type', ['percentage', 'fixed']); // tambahkan kolom type
            $table->decimal('percentage', 5, 2)->nullable(); // tambahkan kolom percentage
            $table->decimal('amount', 15, 2)->nullable(); // tetap ada amount untuk fixed
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deductions'); // perbaiki nama tabel jadi plural
    }
};
