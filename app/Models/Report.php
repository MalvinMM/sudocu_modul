<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    use HasFactory;
    protected $table = 'm_report';
    protected $primaryKey = 'ReportID';

    protected $guarded = [
        'ReportID'
    ];
    public $timestamps = false;

    public function erp()
    {
        return $this->belongsTo(ERP::class, 'ERPID', 'ERPID');
    }

    public function category()
    {
        return $this->belongsTo(ReportCategory::class, 'CategoryID', 'CategoryID');
    }

    public function details()
    {
        return $this->hasMany(DetailReport::class, 'ReportID', 'ReportID');
    }
}
