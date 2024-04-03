<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailModule extends Model
{
    use HasFactory;
    protected $table = 'd_module';
    protected $primaryKey = 'ModuleDetailID';

    protected $guarded = [
        'ModuleDetailID'
    ];
    public $timestamps = false;

    public function module()
    {
        return $this->belongsTo(Module::class, 'ModuleID', 'ModuleID');
    }
}
