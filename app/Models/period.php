<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Period extends Model
{
    use HasFactory;

    protected $fillable = [
        'month',
        'year'
    ];

    public function statistics()
    {
        return $this->hasMany(CompanyStatistic::class);
    }
}
