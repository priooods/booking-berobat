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
        Schema::create('m_doctors', function (Blueprint $table) {
            $table->integerIncrements('id');
            $table->string('name');
            $table->tinyInteger('is_active')->default(1)->comment('1 = active, 0 = tidak active');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_doctors');
    }
};
