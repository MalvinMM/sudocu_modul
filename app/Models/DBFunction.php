<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DBFunction extends Model
{
    use HasFactory;
    protected $table = 'db_function';
    protected $guarded = ['id'];
    public $timestamps = false;

    public function erp()
    {
        return $this->belongsTo(ERP::class, 'ERPID', 'ERPID');
    }
}
