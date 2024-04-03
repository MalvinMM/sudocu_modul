<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Database extends Model
{
    use HasFactory;

    protected $table = 'm_database';
    protected $primaryKey = 'DBID';
    protected $guarded = ['DBID'];
    public $timestamps = false;


    public function erp()
    {
        return $this->belongsTo(ERP::class, 'ERPID', 'ERPID');
    }
}
