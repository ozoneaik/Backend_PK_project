<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QcMain extends Model
{
    use HasFactory;
    protected $connection = 'mysql_main_qc';
}
