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
        Schema::create('t_doctor_schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('t_doctor_periode_details_id');
            $table->unsignedInteger('m_polis_id');
            $table->time('doctor_schedule_start');
            $table->time('doctor_schedule_end');
            $table->date('doctor_schedule_dates');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_doctor_schedules');
    }
};
