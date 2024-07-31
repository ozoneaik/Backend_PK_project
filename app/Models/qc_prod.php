<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class qc_prod extends Model
{
    use HasFactory;
    protected $connection = 'mysql_main_qc';
    protected $table = 'qc_prod';

    public $timestamps = false;

    protected static function boot()
    {
        parent::boot();

        // Adding a before creating event listener
        static::creating(function ($model) {
            $existingRecord = static::where('pid', $model->pid)->first();
            if ($existingRecord) {
                throw new \Exception('มีรหัสสินค้านี้ในฐานข้อมูลแล้ว');
            }
        });
    }
}
