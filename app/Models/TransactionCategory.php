<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransactionCategory extends Model
{
    protected $guarded = [];

    public const KINDS = ['income', 'expense'];
}
