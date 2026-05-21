<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Productivity extends Model
{
    /**
     *
     * @var array
     */
    protected $fillable = [
        'product_line',
        'production_unit',
        'produced_quantity',
        'defect_count',
        'production_date',
    ];
}
