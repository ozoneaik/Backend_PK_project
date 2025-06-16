<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class inc_serial_details extends Model
{
    use HasFactory;
    protected $fillable = [
        'qc_log_id',
        'datekey',
        'KMonth',
        'empkey',
        'empqc',
        'emp_name',
        'skucode',
        'pname',
        'timeperpcs',
        'levelid',
        'levelname',
        'inc_id'
    ];
    
}
