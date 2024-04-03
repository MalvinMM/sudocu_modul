<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    use HasFactory;
    protected $table = 'm_table';
    protected $primaryKey = 'TableID';

    protected $guarded = [
        'TableID'
    ];
    public $timestamps = false;

    public function erp()
    {
        return $this->belongsTo(ERP::class, 'ERPID', 'ERPID');
    }

    public function fields()
    {
        return $this->hasMany(DetailTable::class, 'TableID', 'TableID');
    }
}
