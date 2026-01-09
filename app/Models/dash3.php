<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class dash3 extends Model
{
    use HasFactory;
 
    protected $table = 'monthly_reports_c';

    protected $fillable = [
        'period',
        'total_man_hours',
        'total_employee',
        'total_lta'
    ];
}
