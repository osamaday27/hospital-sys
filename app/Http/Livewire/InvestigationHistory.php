<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use App\Models\medical\MBInvestigationHistory;
use App\Traits\web\file_storage;

class InvestigationHistory extends Component
{
    use WithPagination, WithFileUploads, file_storage;

    public $investigation,
        $eye = 'OD',
        $forNumber,
        $period = 'D',
        $remarks,
        $file;

    public $id_patient, $id_reservation, $id_visit, $type_visit;
    public $editingId = null;
    public $search = '';
    public $sortColumn = 'created_at';
    public $sortDirection = 'desc';

    protected $rules = [
        'investigation' => 'required|string|max:255',
        'eye' => 'required|string|max:10',
        'forNumber' => 'nullable|integer|min:1',
        'period' => 'required|string|max:2',
        'remarks' => 'nullable|string',
        'file' => 'nullable|image|max:2048',
        'id_patient' => 'required',
        'id_reservation' => 'required',
        'type_visit' => 'required',
        'id_visit' => 'required',
    ];

    public function save()
    {
        $this->validate();

        $filePath = null;
        if ($this->file) {
            // استخدام الـTrait
            $paths = $this->file_storage($this->file, 'investigations');
            $filePath = $paths ?? null;
        }

        $data = [
            'id_patient'    => $this->id_patient,
            'id_reservation'=> $this->id_reservation,
            'id_visit'      => $this->id_visit,
            'type_visit'    => $this->type_visit,
            'investigation' => $this->investigation,
            'eye'           => $this->eye,
            'forNumber'     => $this->forNumber,
            'period'        => $this->period,
            'file_path'     => $filePath,
            'remarks'       => $this->remarks,
            'created_by'    => Auth::id(),
        ];

        if ($this->editingId) {
            MBInvestigationHistory::find($this->editingId)->update($data);
        } else {
            MBInvestigationHistory::create($data);
        }

        $this->resetInput();
    }

    public function delete($id)
    {
        $record = MBInvestigationHistory::find($id);
        if ($record) {
            // حذف الملف من التخزين إذا كان موجود
            if ($record->file_path && \Storage::disk('public')->exists($record->file_path)) {
                \Storage::disk('public')->delete($record->file_path);
            }
            $record->delete();
        }
    }

    public function resetInput()
    {
        $this->investigation = '';
        $this->eye = 'OD';
        $this->forNumber = null;
        $this->period = 'D';
        $this->remarks = '';
        $this->file = null;
        $this->editingId = null;
    }

    public function render()
    {
        return view('livewire.investigation-history', [
            'histories' => MBInvestigationHistory::where('id_patient', $this->id_patient)
                ->orderBy($this->sortColumn, $this->sortDirection)
                ->paginate(10),
        ]);
    }
}