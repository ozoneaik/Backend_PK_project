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
            $table->integer('inc_id');
            $table->integer('monthkey');
            $table->date('paydate');
            $table->integer('workday');
            $table->char('status',50);
            $table->integer('numofemp');
            $table->integer('totalqcqty');
            $table->time('totaltimepermonth');
            $table->time('totaltimeperday');
            $table->char('gradeteam',2);
            $table->double('payamntteam');
            $table->time('createon');
            $table->char('createbycode',50);
            $table->time('updateon')->nullable();
            $table->char('updatebycode',50)->nullable();
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
