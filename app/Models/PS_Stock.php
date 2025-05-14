<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PS_Stock extends Model
{
    use HasFactory;

    protected $table='ps_stock';

    public $timestamps = false;

    protected $fillable=[
        'id_warehouse',
        'id_product',
        'id_product_atribute',
        'reference',
        'physical_quantity',
        'usable_quantity',
        'price_te'
    ];
}
