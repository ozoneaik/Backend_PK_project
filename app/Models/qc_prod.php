<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class qc_prod extends Model
{

//$table->id();
//$table->integer('ra_id');
//$table->integer('le_id');
//$table->char('grade',10);
//$table->double('rate');
//$table->timestamps();
    use HasFactory;

    protected $fillable = [
      'ra_id',
      'le_id',
      'grade',
      'rate'
    ];
}
