<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static orderBy(string $string, string $string1)
 */
class qc_time extends Model
{
    use HasFactory;

    protected $table = 'qc_times';
    protected $fillable = [
        'ti_id',
        'time',
        'grade'
    ];
}
