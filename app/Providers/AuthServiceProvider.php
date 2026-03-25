<?php

namespace App\Providers;

use App\Models\Device;
use App\Models\MaintenancePlan;
use App\Models\SparePart;
use App\Models\Vendor;
use App\Models\WorkOrder;
use App\Models\WorkOrderAttachment;
use App\Models\WorkOrderPart;
use App\Policies\DevicePolicy;
use App\Policies\MaintenancePlanPolicy;
use App\Policies\SparePartPolicy;
use App\Policies\VendorPolicy;
use App\Policies\WorkOrderAttachmentPolicy;
use App\Policies\WorkOrderPartPolicy;
use App\Policies\WorkOrderPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model-to-policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        WorkOrder::class           => WorkOrderPolicy::class,
        Device::class              => DevicePolicy::class,
        SparePart::class           => SparePartPolicy::class,
        MaintenancePlan::class     => MaintenancePlanPolicy::class,
        Vendor::class              => VendorPolicy::class,
        WorkOrderAttachment::class => WorkOrderAttachmentPolicy::class,
        WorkOrderPart::class       => WorkOrderPartPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
