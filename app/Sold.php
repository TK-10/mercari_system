<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Sold extends Model
{
    protected $fillable = [
        'item_id',
        'mid',
        'item_name',
        'sold_count',
    ];
}
