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
        Schema::create('inc_details', function (Blueprint $table) {
            $table->id();
            $table->integer('inc_id');
            $table->integer('monthkey');
            $table->string('empqccode');
            $table->string('skucode');
            $table->string('skuname');
            $table->integer('skuqty');
            $table->integer('le_id');
            $table->string('le_name');
            $table->time('timeperpcs');
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
        Schema::dropIfExists('inc_details');
    }
};
