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
        Schema::create('salaries', function (Blueprint $table) {
            $table->id('salary_id');
            $table->unsignedBigInteger('user_id');
            $table->date('period');
            $table->decimal('base_salary', 15, 2);
            $table->decimal('allowance', 15, 2);
            $table->decimal('overtime', 15, 2);
            $table->decimal('deduction', 15, 2);
            $table->decimal('net_salary', 15, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salaries');
    }
};
