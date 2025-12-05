<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Importslogs extends Model
{
    public $fillable = ['transaction_id', 'import_id', 'error_message'];
    public $table = "importlogs";
}
 