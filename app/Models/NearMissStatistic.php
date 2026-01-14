<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NearMissStatistic extends Model
{
    protected $fillable = [
        'company_id',
        'period_id',
        'total_near_miss',
        'high_risk',
        'medium_risk',
        'low_risk',
        'open',
        'closed',
        'high_severity',
        'medium_severity',
        'low_severity',
        'high_likelihood',
        'medium_likelihood',
        'low_likelihood',
        'near_miss_rate'
    ];
}
