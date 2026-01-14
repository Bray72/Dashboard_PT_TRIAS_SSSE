<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NearMissStatistic extends Model
{
    use HasFactory;

    protected $table = 'near_miss_statistics';

    protected $fillable = [
        'company_id',
        'period_id',
        'total_near_miss',

        'high_risk','medium_risk','low_risk',
        'high_severity','medium_severity','low_severity',
        'high_likelihood','medium_likelihood','low_likelihood',

        'open','closed',
        'near_miss_rate'
    ];

    public function period()
    {
        return $this->belongsTo(Period::class);
    }
}
