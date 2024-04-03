<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ModuleCategory extends Model
{
    use HasFactory;
    protected $table = 'm_module_category';
    protected $primaryKey = 'CategoryID';

    protected $guarded = [
        'CategoryID'
    ];
    public $timestamps = false;

    public function erp()
    {
        return $this->belongsTo(ERP::class, 'ERPID', 'ERPID');
    }
}
