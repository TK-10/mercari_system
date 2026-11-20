<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
        'item_id',
        'item_name',
        'item_price',
        'stock',
        'detail',
        'del_flg',
    ];
}
