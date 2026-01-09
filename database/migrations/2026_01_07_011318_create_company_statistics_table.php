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
        Schema::create('company_statistics', function (Blueprint $table) {
            $table->id();

            $table->foreignId('company_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->foreignId('period_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->integer('man_hours');
            $table->integer('total_employee');
            $table->integer('total_lta');

            $table->timestamps();

            $table->unique(['company_id', 'period_id']); 
            // 1 perusahaan hanya boleh 1 data per periode
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_statistics');
    }
};
