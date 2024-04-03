<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DetailTable extends Model
{
    use HasFactory;
    protected $table = 'd_table';
    protected $primaryKey = 'FieldID';

    protected $guarded = [
        'FieldID'
    ];
    public $timestamps = false;

    public function table()
    {
        return $this->belongsTo(Table::class, 'TableID', 'TableID');
    }
}
