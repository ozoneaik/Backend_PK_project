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
            $table->string('empqccode');
            $table->integer('qcqty');
            $table->string('timepermonth');
            $table->string('timeperday');
            $table->string('grade');
            $table->integer('le_id');
            $table->string('le_name');
            $table->double('rate');
            $table->double('payamnt');
            $table->string('paystatus');
            $table->string('payremark');
            $table->string('createbycode');
            $table->string('updatebycode');
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
