<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    use HasFactory;
    protected $table = 'm_module';
    protected $primaryKey = 'ModuleID';

    protected $guarded = [
        'ModuleID'
    ];
    public $timestamps = false;

    public function erp()
    {
        return $this->belongsTo(ERP::class, 'ERPID', 'ERPID');
    }

    public function category()
    {
        return $this->belongsTo(ModuleCategory::class, 'CategoryID', 'CategoryID');
    }

    public function details()
    {
        return $this->hasMany(DetailModule::class, 'ModuleID', 'ModuleID');
    }
}
