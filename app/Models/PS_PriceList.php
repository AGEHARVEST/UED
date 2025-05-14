<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PS_PriceList extends Model
{
    use HasFactory;

    protected $table='ps_price_list';

     public $timestamps = false;

    protected $fillable=[
        'id_currency',
        'name'
    ];
}
