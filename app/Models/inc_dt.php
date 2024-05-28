<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class inc_dt extends Model
{
    use HasFactory;
    protected $table = 'inc_dts';
    protected $fillable = [
        'inc_id',
        'empqccode',
        'qcqty',
        'timepermonth',
        'timeperday',
        'grade',
        'le_id',
        'le_name',
        'rate',
        'payamnt',
        'paystatus',
        'payremark',
        'createbycode',
        'updatebycode',
    ];
}
