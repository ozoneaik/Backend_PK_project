<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property mixed|string $skuname
 * @property mixed $skucode
 * @property mixed $year
 * @property mixed $month
 */
class ProductNotFound extends Model
{
    use HasFactory;
    protected $table = 'product_not_founds';
    protected $fillable = ['skucode','skuname'];
}

