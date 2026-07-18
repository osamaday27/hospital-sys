<?php

namespace App\Http\Livewire\Radiology\Settings\Medicalreports;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\administrative\radiologymedicalreports;
use App\Models\administrative\radiologydevicestypessettings;
use App\Models\general\specialties;

class Index extends Component
{
    use WithPagination, WithFileUploads;

    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['deleteConfirmed' => 'delete'];

    // Tabs
    public $activeTab = 'list';

    // Fields
    public $report_id;
    public $title = ['ar' => '', 'en' => ''];
    public $template;
    public $device_type_id;
    public $specialty_id;
    public $status = 'active';

    // عرض التقرير
    public $showReportModal = false;
    public $currentReport;

    protected $rules = [
        'title.ar'   => 'required|string|max:255',
        'title.en'   => 'required|string|max:255',
        'template'   => 'required|string',
        'device_type_id' => 'nullable|exists:rad_s_devicestypes,id',
        'specialty_id'   => 'nullable|exists:specialties,id',
        'status'         => 'required|in:active,inactive',
    ];

    // ✅ إنشاء جديد
    public function createNew()
    {
        $this->resetInput();
        $this->activeTab = 'form';
    }

    // ✅ حفظ / تعديل
    public function save()
    {
        $this->validate();

        radiologymedicalreports::updateOrCreate(
            ['id' => $this->report_id],
            [
                'title'          => $this->title,
                'template'       => $this->template,
                'device_type_id' => $this->device_type_id,
                'specialty_id'   => $this->specialty_id,
                'status'         => $this->status,
            ]
        );

        session()->flash('success', $this->report_id ? '✅ تم تعديل التقرير بنجاح' : '✅ تم إنشاء التقرير بنجاح');
        $this->resetInput();
        $this->activeTab = 'list';
    }

    // ✅ تعديل
    public function edit($id)
    {
        $report = radiologymedicalreports::findOrFail($id);

        $this->report_id    = $id;
        $this->title        = $report->getTranslations('title');
        $this->template     = $report->template;
        $this->device_type_id = $report->device_type_id;
        $this->specialty_id   = $report->specialty_id;
        $this->status         = $report->status;

        $this->activeTab = 'form';
    }

    // ✅ عرض
    public function show($id)
    {
        $this->currentReport = radiologymedicalreports::with(['deviceType','specialty'])->findOrFail($id);
        $this->showReportModal = true;
    }

    // ✅ حذف
    public function delete($id)
    {
        radiologymedicalreports::findOrFail($id)->delete();
        session()->flash('success', '🗑️ تم حذف التقرير بنجاح');
    }

    // ✅ Reset Inputs
    public function resetInput()
    {
        $this->reset([
            'report_id','template','device_type_id','specialty_id','status'
        ]);

        $this->title = ['ar' => '', 'en' => ''];
        $this->status = 'active';
    }

    public function render()
    {
        $reports     = radiologymedicalreports::with(['deviceType','specialty'])->paginate(10);
        $deviceTypes = radiologydevicestypessettings::all();
        $specialties = specialties::all();

        return view('livewire.radiology.settings.medicalreports.index', compact('reports','deviceTypes','specialties'));
    }
}
