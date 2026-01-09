<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyStatistic extends Model
{
    use HasFactory;

    protected $table = 'company_statistics';

    protected $fillable = [
        'company_id',
        'period_id',
        'man_hours',
        'employee',
        'lta',
        'lost_work_days',
        'lost_time',
        'kecelakaan_kerja',
    ];

    // RELATION
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function period()
    {
        return $this->belongsTo(Period::class);
    }
}
