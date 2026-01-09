<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class cr extends Model
{
    use HasFactory;

    protected $fillable = [
        'period',
        'total_man_hours',
        'total_employee',
        'total_lta'
    ];
}
