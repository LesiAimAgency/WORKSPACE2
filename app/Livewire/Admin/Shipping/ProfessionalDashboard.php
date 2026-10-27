<?php

namespace App\Livewire\Admin\Shipping;

use App\Models\Project;
use App\Models\ShippingCarrier;
use App\Models\ShippingRule;
use App\Models\ShippingRuleCondition;
use Livewire\Component;

class ProfessionalDashboard extends Component
{
    public $project;

    // Local Rules state
    public $localCarrier;

    // Rule Form State
    public $showRuleModal = false;

    public $ruleForm = [
        'name' => '',
        'action_type' => 'override',
        'fee' => 0,
        'is_surcharge' => false,
        'priority' => 10,
        'condition_type' => 'distance',
        'operator' => '<=',
        'value_1' => '',
    ];

    public function mount()
    {
        $projectCode = request()->route('projectCode');
        $this->project = Project::where('code', $projectCode)->firstOrFail();

        $this->loadLocalCarrier();
    }

    public function loadLocalCarrier()
    {
        $this->localCarrier = ShippingCarrier::where('project_id', $this->project->id)
            ->where('code', 'local')
            ->with(['rateVersions' => function ($q) {
                $q->where('is_active', true)->with('rules.conditions');
            }])
            ->first();

        // Create default local carrier if it doesn't exist
        if (! $this->localCarrier) {
            $this->localCarrier = ShippingCarrier::create([
                'project_id' => $this->project->id,
                'name' => 'Tự giao hàng',
                'code' => 'local',
                'type' => 'local',
                'status' => true,
            ]);
            $this->localCarrier->rateVersions()->create([
                'version_name' => 'Bảng giá mặc định',
                'is_active' => true,
            ]);
            $this->loadLocalCarrier();
        }
    }

    public function openRuleModal()
    {
        $this->reset('ruleForm');
        $this->ruleForm = [
            'name' => '',
            'action_type' => 'override',
            'fee' => 0,
            'is_surcharge' => false,
            'priority' => 10,
            'condition_type' => 'distance',
            'operator' => '<=',
            'value_1' => '',
        ];
        $this->showRuleModal = true;
    }

    public function saveRule()
    {
        $this->validate([
            'ruleForm.name' => 'required|string|max:255',
            'ruleForm.fee' => 'required|numeric',
        ]);

        $rateVersion = $this->localCarrier->rateVersions->first();
        if (! $rateVersion) {
            return;
        }

        $rule = ShippingRule::create([
            'shipping_rate_version_id' => $rateVersion->id,
            'name' => $this->ruleForm['name'],
            'action_type' => $this->ruleForm['action_type'],
            'fee' => $this->ruleForm['fee'],
            'is_surcharge' => $this->ruleForm['is_surcharge'],
            'priority' => $this->ruleForm['priority'],
            'status' => true,
        ]);

        if ($this->ruleForm['value_1'] !== '') {
            ShippingRuleCondition::create([
                'shipping_rule_id' => $rule->id,
                'condition_type' => $this->ruleForm['condition_type'],
                'operator' => $this->ruleForm['operator'],
                'value_1' => $this->ruleForm['value_1'],
            ]);
        }

        $this->showRuleModal = false;
        $this->loadLocalCarrier();
        session()->flash('message', 'Đã thêm quy tắc thành công!');
    }

    public function deleteRule($ruleId)
    {
        ShippingRule::where('id', $ruleId)->delete();
        $this->loadLocalCarrier();
        session()->flash('message', 'Đã xóa quy tắc thành công!');
    }

    public function render()
    {
        return view('livewire.admin.shipping.professional-dashboard');
    }
}
