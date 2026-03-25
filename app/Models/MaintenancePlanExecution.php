<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MaintenancePlanExecution extends Model
{
    use HasFactory;

    protected $fillable = [
        'maintenance_plan_id',
        'scheduled_date',
        'executed_date',
        'executed_by_user_id',
        'status',   // PENDING | DONE | SKIPPED
        'notes',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'executed_date'  => 'date',
    ];

    public function maintenancePlan()
    {
        return $this->belongsTo(MaintenancePlan::class);
    }

    public function executedBy()
    {
        return $this->belongsTo(User::class, 'executed_by_user_id');
    }
}
