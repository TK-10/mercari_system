<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class History extends Model
{
    protected $fillable = [
        'item_id',
        'item_name',
        'item_price',
        'site_name',
        'status',
        'stock',
        'user_name',
    ];
}
