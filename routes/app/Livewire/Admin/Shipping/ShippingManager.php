<?php

namespace App\Livewire\Admin\Shipping;

use App\Models\Project;
use App\Models\ShippingCarrier;
use Livewire\Component;

class ShippingManager extends Component
{
    public $project;

    public $carriers;

    public $activeTab = 'methods';

    public function mount()
    {
        $projectCode = request()->route('projectCode');
        $this->project = Project::where('code', $projectCode)->firstOrFail();

        $this->loadData();
    }

    public function loadData()
    {
        $this->carriers = ShippingCarrier::where('project_id', $this->project->id)
            ->with(['rateVersions.rules.conditions'])
            ->get();
    }

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function createCarrier()
    {
        // Sample creation for the demo
        $carrier = ShippingCarrier::create([
            'project_id' => $this->project->id,
            'name' => 'Giao hàng Tiêu chuẩn',
            'code' => 'local_standard',
            'type' => 'local',
            'status' => true,
        ]);

        $version = $carrier->rateVersions()->create([
            'version_name' => 'Bảng giá Mặc định (V1)',
            'is_active' => true,
        ]);

        $this->loadData();
    }

    public function render()
    {
        return view('livewire.admin.shipping.shipping-manager');
    }
}
