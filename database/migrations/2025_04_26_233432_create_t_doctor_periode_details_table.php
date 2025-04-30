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
        Schema::create('t_doctor_periode_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('t_doctor_periodes_id');
            $table->unsignedInteger('m_doctors_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_doctor_periode_details');
    }
};
