<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenanceType extends Model
{
    use HasFactory;

    protected $fillable = ['name_ar', 'name_en', 'description'];

    public function maintenancePlans()
    {
        return $this->hasMany(MaintenancePlan::class);
    }
}
