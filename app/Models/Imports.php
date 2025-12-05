<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Imports extends Model
{
    public $fillable = ['file_name', 'total_records', 'successful_records', 'failed_records', 'status'];

    public function transactions() {
       return $this->hasMany(Transactions::class, "import_id");
    }

    public function importlogs() {
       return $this->hasMany(Importslogs::class, "import_id");
    }

}
 