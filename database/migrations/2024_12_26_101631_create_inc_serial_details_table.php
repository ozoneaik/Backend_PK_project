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
        Schema::create('inc_serial_details', function (Blueprint $table) {
            $table->id();
            $table->integer('qc_log_id')->comment('QC Log ID');
            $table->date('datekey')->comment('datekey');
            $table->text('KMonth')->comment('KMonth');
            $table->text('empkey')->comment('empkey');
            $table->text('empqc')->comment('รหัสพนักงาน');
            $table->text('emp_name')->comment('ชื่อพนักงาน');
            $table->text('skucode')->nullable()->comment('รหัสสินค้า');
            $table->text('pname')->nullable()->comment('ชื่อสินค้า');
            $table->time('timeperpcs')->nullable()->comment('TimePerPcs');
            $table->text('levelid')->nullable()->comment('รหัสระดับ QC');
            $table->text('levelname')->nullable()->comment('ระดับ QC');
            $table->integer('inc_id')->comment('รหัสอ้างอิง INC');
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
        Schema::dropIfExists('inc_serial_details');
    }
};
