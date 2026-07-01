<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $guarded = [];

    protected $casts = [
        'unit_price' => 'decimal:2',
    ];
}
