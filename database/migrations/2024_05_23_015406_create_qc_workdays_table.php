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
        Schema::create('qc_workdays', function (Blueprint $table) {
            $table->id();
            $table->integer('wo_id');
            $table->integer('wo_year');
            $table->integer('wo_month');
            $table->integer('workday');
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
        Schema::dropIfExists('qc_workdays');
    }
};
