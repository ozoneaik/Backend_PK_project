<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static where(string $string, $year)
 */
class qc_workday extends Model
{
    use HasFactory;

    protected $fillable = [
        'wo_year',
        'wo_month',
        'workday'
    ];
}
