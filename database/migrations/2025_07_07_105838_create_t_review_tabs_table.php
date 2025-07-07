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
        Schema::create('t_review_tabs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('users_id');
            $table->unsignedBigInteger('t_antrian_tabs_id');
            $table->integer('start')->default(0);
            $table->string('description');
            $table->timestamps();
            $table->foreign('t_antrian_tabs_id')->references('id')->on('t_antrians')->cascadeOnDelete();
            $table->foreign('users_id')->references('id')->on('users')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('t_review_tabs');
    }
};
