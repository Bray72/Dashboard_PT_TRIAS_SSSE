<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PermitStatistic extends Model
{
    protected $table = 'permit_statistics';

    protected $fillable = [
        'company_id',
        'period_id',
        'permit_type_id',
        'total'
    ];

    public function permitType()
    {
        return $this->belongsTo(PermitType::class);
    }

    public function period()
    {
        return $this->belongsTo(Period::class);
    }
}
