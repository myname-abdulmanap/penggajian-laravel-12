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
            $table->unsignedBigInteger('salary_id')->primary();
            $table->unsignedBigInteger('users_id');
            $table->foreign('users_id')->references('users_id')->on('users')->onDelete('cascade');

            $table->date('period');
            $table->decimal('base_salary', 15, 2)->default(0);
            $table->decimal('overtime', 15, 2)->default(0);
            $table->decimal('net_salary', 15, 2)->default(0);
            $table->timestamps();
        });

        Schema::create('salary_allowance', function (Blueprint $table) {
            $table->unsignedBigInteger('salary_id');
            $table->unsignedBigInteger('allowance_id');

            $table->foreign('salary_id')->references('salary_id')->on('salaries')->onDelete('cascade');
            $table->foreign('allowance_id')->references('allowance_id')->on('allowances')->onDelete('cascade');

            $table->primary(['salary_id', 'allowance_id']);
        });

        Schema::create('salary_deduction', function (Blueprint $table) {
            $table->unsignedBigInteger('salary_id');
            $table->unsignedBigInteger('deduction_id');

            $table->foreign('salary_id')->references('salary_id')->on('salaries')->onDelete('cascade');
            $table->foreign('deduction_id')->references('deduction_id')->on('deductions')->onDelete('cascade');

            $table->primary(['salary_id', 'deduction_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_deduction');
        Schema::dropIfExists('salary_allowance');
        Schema::dropIfExists('salaries');
    }



};
