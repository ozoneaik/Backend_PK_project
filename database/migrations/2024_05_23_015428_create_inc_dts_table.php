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
        Schema::create('inc_dts', function (Blueprint $table) {
            $table->id();
            $table->integer('inc_id');
            $table->char('empqccode',50);
            $table->integer('qcqty');
            $table->time('timepermonth');
            $table->time('timeperday');
            $table->char('grade',2);
            $table->integer('le_id');
            $table->char('le_name',10);
            $table->double('rate');
            $table->double('payamnt');
            $table->char('paystatus',10);
            $table->char('payremark',50);
            $table->time('createon');
            $table->char('createbycode',50);
            $table->time('updateon');
            $table->char('updatebycode',50);
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
        Schema::dropIfExists('inc_dts');
    }
};
