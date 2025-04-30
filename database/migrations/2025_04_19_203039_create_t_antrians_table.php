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
        Schema::create('t_antrians', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('m_statuses_id');
            $table->integer('antrian');
            $table->string('number_ktp');
            $table->string('name');
            $table->unsignedBigInteger('user_id');
            $table->unsignedInteger('m_polis_id');
            $table->char('gender', 1);
            $table->date('birthday');
            $table->string('phone');
            $table->string('address');
            $table->string('diagnosa');
            $table->date('date_treatment');
            $table->tinyInteger('payment')->comment('0 = cash, 1 = bpjs');
            $table->string('no_bpjs')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_antrians');
    }
};
