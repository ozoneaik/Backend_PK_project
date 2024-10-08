<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed $pid
 * @property mixed $pname
 * @property mixed $levelid
 * @property mixed|string $timeperpcs
 * @property Carbon|mixed $createdate
 * @property Carbon|mixed $updatedate
 * @property mixed $createby
 * @property mixed $updateby
 * @method static findOrFail($id)
 * @method static find($id)
 */
class qc_prod extends Model
{
    use HasFactory;
    protected $connection = 'mysql_main_qc';
    protected $table = 'qc_prod';

    public $timestamps = false;

    protected static function boot(): void
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
