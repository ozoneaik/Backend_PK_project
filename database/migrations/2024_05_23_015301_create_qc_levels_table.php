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
        Schema::create('qc_levels', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('le_id');
            $table->string('le_code',50);
            $table->string('le_name',10);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('qc_levels');
    }
};
