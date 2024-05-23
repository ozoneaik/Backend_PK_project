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
            $table->char('empqccode',50);
            $table->char('skucode',50);
            $table->char('skuname',100);
            $table->integer('skuqty');
            $table->integer('le_id');
            $table->char('le_name',10);
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
