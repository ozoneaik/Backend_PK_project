<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class qc_level extends Model
{
    use HasFactory;

    protected $fillable = [
      'le_id',
      'le_code',
        'le_name'
    ];
}
