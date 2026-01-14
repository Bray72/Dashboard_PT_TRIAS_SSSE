<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $table = 'departments';

    protected $fillable = [
        'name'
    ];

    /**
     * Relasi: 1 Department punya banyak Near Miss
     */
    public function nearMisses()
    {
        return $this->hasMany(NearMiss::class);
    }
}
