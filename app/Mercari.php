<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Mercari extends Model
{
    protected $fillable = [
        'mid',
        'item_name',
        'item_price',
        'buyer_name',
        'shorikubun',
    ];
}
