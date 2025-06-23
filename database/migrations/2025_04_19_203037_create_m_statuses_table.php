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
        Schema::create('m_statuses', function (Blueprint $table) {
            $table->smallIncrements('id');
            $table->string('title');
        });

        DB::table('m_status_tabs')->insert(
            array(
                ['title' => 'DRAFT'],
                ['title' => 'DI AJUKAN'],
                ['title' => 'SEDANG BEROBAT'],
                ['title' => 'SELESAI BEROBAT'],
                ['title' => 'DI BATALKAN'],
                ['title' => 'DI TOLAK']
            )
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_statuses');
    }
};
