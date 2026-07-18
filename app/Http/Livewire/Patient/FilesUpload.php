<?php

namespace App\Http\Livewire\patient;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\models\administrative\patientfile;
use App\Traits\web\file_storage;

class FilesUpload extends Component
{
    use WithFileUploads, WithPagination, file_storage;

    protected $paginationTheme = 'bootstrap';

    public $patient_id;
    public $visit_id;

    public $file;
    public $file_name;
    public $document_type;
    public $notes;

    // بيانات معاينة الملف
    public $selected_name;
    public $selected_size; // KB
    public $selected_type;

    public $perPage = 10;

    protected function rules()
    {
        return [
            'file' => 'required|file|max:20480', // 20MB
            'file_name' => 'nullable|string|max:255',
            'document_type' => 'nullable|string|max:100',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    public function updatedFile()
    {
        if ($this->file) {
            $this->selected_name = $this->file->getClientOriginalName();
            $this->selected_size = (int) ceil($this->file->getSize() / 1024); // KB
            $this->selected_type = $this->file->getClientMimeType();

            if (empty($this->file_name)) {
                $this->file_name = pathinfo($this->selected_name, PATHINFO_FILENAME);
            }
        } else {
            $this->resetSelected();
        }
    }

    public function resetSelected()
    {
        $this->reset(['file', 'selected_name', 'selected_size', 'selected_type']);
    }

    public function save()
    {
        $this->validate();

        // التخزين باستخدام الترايت الخاص بك (يحفظ عادة تحت storage/app/public ويرجع مسار نسبي)
        $path = $this->file_storage($this->file, 'attachments/patient_files');

        patientfile::create([
            'patient_id'     => $this->patient_id,
            'visit_id'       => $this->visit_id,
            'file_path'      => $path,
            'file_name'      => $this->file_name ?: $this->file->getClientOriginalName(),
            'file_mime'      => $this->file->getClientMimeType(),
            'file_extension' => $this->file->getClientOriginalExtension(),
            'size_in_kb'     => (int) ceil($this->file->getSize() / 1024),
            'document_type'  => $this->document_type,
            'notes'          => $this->notes,
            'user_id'        => Auth::id(),
        ]);

        // تنظيف الحقول
        $this->reset(['file','file_name','notes','document_type','selected_name','selected_size','selected_type']);
        $this->resetPage();

        session()->flash('message', trans('patient_files_trans.file_uploaded_successfully'));
    }

    public function delete($id)
    {
        $file = patientfile::findOrFail($id);

        try {
            // لو الترايت بيحفظ تحت public disk
            if ($file->file_path && Storage::disk('public')->exists($file->file_path)) {
                Storage::disk('public')->delete($file->file_path);
            }
        } catch (\Throwable $e) {
            // تجاهل الخطأ في الحذف من القرص
        }

        $file->delete();

        if ($this->page > 1 && patientfile::where('patient_id', $this->patient_id)->count() <= ($this->page - 1) * $this->perPage) {
            $this->previousPage();
        }
    }

    public function render()
    {
        $files = patientfile::where('patient_id', $this->patient_id)
                    ->latest()
                    ->paginate($this->perPage);

        return view('livewire.patient.files-upload', compact('files'));
    }
}