<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static where(string $string, $year)
 */
class inc_hd extends Model
{
    use HasFactory;

    protected $table = 'inc_hds';
    protected $fillable = [
        'inc_id',             // ID ของ incentive
        'yearkey',
        'monthkey',          // Key ของเดือน
        'paydate',           // วันที่จ่ายเงิน
        'workday',           // จำนวนวันทำงาน
        'status',            // สถานะ
        'numofemp',          // จำนวนพนักงาน
        'totalqcqty',        // จำนวน QC ทั้งหมด
        'totaltimepermonth', // เวลารวมต่อเดือน
        'totaltimeperday',   // เวลารวมต่อวัน
        'gradeteam',         // เกรดของทีม
        'payamntteam',       // จำนวนเงินจ่ายให้ทีม
        'createon',          // เวลาที่สร้างข้อมูล
        'createbycode',      // โค้ดผู้สร้างข้อมูล
        'updateon',          // เวลาที่อัพเดทข้อมูล (nullable)
        'updatebycode'       // โค้ดผู้อัพเดทข้อมูล (nullable)
    ];
}
