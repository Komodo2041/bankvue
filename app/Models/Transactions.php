<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transactions extends Model
{
    public $fillable = ['import_id', 'transaction_id', 'account_number', 'transaction_date', 'amount', 'currency' ];
}
