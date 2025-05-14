<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PS_PriceProduct extends Model
{
    use HasFactory;

    protected $table='ps_price_product';

     public $timestamps = false;

     protected $fillable=[
        'id_pricelist',
        'id_product',
        'price',
     ];
}
