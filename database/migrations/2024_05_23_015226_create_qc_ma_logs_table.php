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
            $table->char('ml_name',50);
            $table->char('ml_before',50);
            $table->char('ml_after',50);
            $table->time('createon');
            $table->char('createbycode',10);
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
