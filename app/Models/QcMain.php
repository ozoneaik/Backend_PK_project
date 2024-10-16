<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static select(string $string, string $string1)
 */
class QcMain extends Model
{
    use HasFactory;

    protected $connection = 'mysql_main_qc';
    protected $table = 'qc_log_data';
}
