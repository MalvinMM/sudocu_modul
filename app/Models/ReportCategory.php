<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportCategory extends Model
{
    use HasFactory;
    protected $table = 'm_report_category';
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
