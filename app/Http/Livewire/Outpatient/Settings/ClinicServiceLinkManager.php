<?php

namespace App\Http\Livewire\Outpatient\Settings;

use Livewire\Component;
use App\Models\administrative\ClinicServiceLink;
use App\Models\administrative\admsclinic;
use App\Models\finance\service;

class ClinicServiceLinkManager extends Component
{
    public $clinic_id, $service_id, $service_type = 'examination', $status = 'active';
    public $is_consultant = false, $is_doctor = false;
    public $links;

    public function mount()
    {
        $this->refreshLinks();
    }

    public function refreshLinks()
    {
        $this->links = ClinicServiceLink::with(['clinic', 'service'])->get();
    }

    public function save()
    {
        $this->validate([
            'clinic_id' => 'required|exists:adm_s_clinics,id',
            'service_id' => 'required|exists:fin_services,id',
            'service_type' => 'required',
            'status' => 'required'
        ]);

        ClinicServiceLink::create([
            'clinic_id' => $this->clinic_id,
            'service_id' => $this->service_id,
            'is_consultant' => $this->is_consultant ? 1 : 0,
            'is_doctor' => $this->is_doctor ? 1 : 0,
            'service_type' => $this->service_type,
            'status' => $this->status,
            'user_id' => auth()->id(),
        ]);

        $this->reset(['clinic_id','service_id','is_consultant','is_doctor','service_type','status']);
        $this->service_type = 'examination';
        $this->status = 'active';

        $this->refreshLinks();
        session()->flash('success', 'تم إضافة الخدمة للعيادة بنجاح');
    }

    public function delete($id)
    {
        ClinicServiceLink::findOrFail($id)->delete();
        $this->refreshLinks();
    }

    public function render()
    {
        return view('livewire.outpatient.settings.clinic-service-link-manager', [
            'clinics' => admsclinic::all(),
            'services' => service::all(),
        ]);
    }
}