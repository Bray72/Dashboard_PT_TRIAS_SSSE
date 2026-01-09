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
         Schema::create('monthly_reports', function (Blueprint $table) {
            $table->id();
            $table->string('period'); // contoh: 2025-01
            $table->integer('total_man_hours');
            $table->integer('total_employee');
            $table->integer('total_lta');
            $table->timestamps();
        });

        Schema::create('monthly_reports_b', function (Blueprint $table) {
            $table->id();
            $table->string('period'); // contoh: 2025-01
            $table->integer('total_man_hours');
            $table->integer('total_employee');
            $table->integer('total_lta');
            $table->timestamps();
        });

        Schema::create('monthly_reports_c', function (Blueprint $table) {
            $table->id();
            $table->string('period'); // contoh: 2025-01
            $table->integer('total_man_hours');
            $table->integer('total_employee');
            $table->integer('total_lta');
            $table->timestamps();
        });
    }
    

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('monthly_reports');
    }
};
