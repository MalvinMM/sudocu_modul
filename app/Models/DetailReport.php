<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailReport extends Model
{
    use HasFactory;
    protected $table = 'd_report';
    protected $primaryKey = 'ReportDetailID';

    protected $guarded = [
        'ReportDetailID'
    ];
    public $timestamps = false;

    public function module()
    {
        return $this->belongsTo(Report::class, 'ReportID', 'ReportID');
    }
}
