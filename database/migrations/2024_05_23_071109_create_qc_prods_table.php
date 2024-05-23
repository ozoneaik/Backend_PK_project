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
        Schema::create('qc_prods', function (Blueprint $table) {
            $table->id();
            $table->string('pid');
            $table->string('pname');
            $table->integer('le_id');
            $table->string('timeperpcs');
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
        Schema::dropIfExists('qc_prods');
    }
};
