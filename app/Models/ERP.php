<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ERP extends Model
{
    use HasFactory;
    protected $table = 'm_erp';
    protected $primaryKey = 'ERPID';

    protected $guarded = [
        'ERPID'
    ];
    public $timestamps = false;

    public function db()
    {
        return $this->hasMany(Database::class, 'ERPID', 'ERPID');
    }

    public function tables()
    {
        return $this->hasMany(Table::class, 'ERPID', 'ERPID');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_erp', 'ERPID', 'UserID');
    }

    public function moduleCategories()
    {
        return $this->belongsToMany(ModuleCategory::class, 'ERPID', 'ERPID');
    }

    public function modules()
    {
        return $this->hasMany(Module::class, 'ERPID', 'ERPID');
    }

    public function reports()
    {
        return $this->hasMany(Report::class, 'ERPID', 'ERPID');
    }

    public function views()
    {
        return $this->hasMany(DBView::class, 'ERPID', 'ERPID');
    }

    public function functions()
    {
        return $this->hasMany(DBFunction::class, 'ERPID', 'ERPID');
    }

    public function storeProcs()
    {
        return $this->hasMany(DBStoreProc::class, 'ERPID', 'ERPID');
    }
}
