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
        Schema::create('inc_hds', function (Blueprint $table) {
            $table->id();
            $table->integer('yearkey');
            $table->integer('monthkey');
            $table->string('paydate');
            $table->integer('workday');
            $table->string('status');
            $table->integer('numofemp');
            $table->integer('totalqcqty');
            $table->string('totaltimepermonth');
            $table->string('totaltimeperday');
            $table->string('gradeteam');
            $table->double('payamntteam');
            $table->string('createbycode');
            $table->string('updatebycode');
            $table->dateTime('caldate')->nullable();
            $table->dateTime('confirmdate')->nullable();
            $table->dateTime('confirmapprove')->nullable();
            $table->string('confirmapprovebycode')->nullable();
            $table->string('confirmpaydatebycode')->nullable();
            $table->dateTime('confirmpaydate')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('inc_hds');
    }
};
