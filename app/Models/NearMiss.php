<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NearMiss extends Model
{
    use HasFactory;

    protected $table = 'near_misses';

    protected $fillable = [
        'company_id',
        'period_id',
        'department_id',
        'date',
        'location',
        'category',
        'severity',
        'likelihood',
        'risk_level',
        'description',
        'action_required',
        'status'
    ];

    public function period()
    {
        return $this->belongsTo(Period::class);
    }
}
