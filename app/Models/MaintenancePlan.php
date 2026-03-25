<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenancePlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'device_id',
        'maintenance_type_id',
        'frequency_type',   // DAILY | WEEKLY | MONTHLY | QUARTERLY | ANNUAL
        'frequency_value',
        'last_done_date',
        'next_due_date',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'last_done_date' => 'date',
        'next_due_date'  => 'date',
        'is_active'      => 'boolean',
    ];

    public function device()
    {
        return $this->belongsTo(Device::class);
    }

    public function maintenanceType()
    {
        return $this->belongsTo(MaintenanceType::class);
    }

    public function executions()
    {
        return $this->hasMany(MaintenancePlanExecution::class);
    }
}
