<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('qc_ma_logs', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('ml_id');
            $table->string('ml_name');
            $table->string('ml_before');
            $table->string('ml_after');
            $table->string('createbycode');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('qc_ma_logs');
    }
};
