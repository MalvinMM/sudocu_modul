<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DBView extends Model
{
    use HasFactory;
    protected $table = 'db_views';

    protected $guarded = ['DBID'];
    public $timestamps = false;

    public function erp()
    {
        return $this->belongsTo(ERP::class, 'ERPID', 'ERPID');
    }
}
