<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class addYear extends Model
{
    use HasFactory;
    protected $table = 'add_years';
    protected $fillable = [
        'year'
    ];
}
