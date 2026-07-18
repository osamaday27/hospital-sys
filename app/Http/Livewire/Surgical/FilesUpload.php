<?php

namespace App\Http\Livewire\surgical;

use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\models\administrative\surgicalfile;
use App\Traits\web\file_storage;

class FilesUpload extends Component
{
    use WithFileUploads, WithPagination, file_storage;

    protected $paginationTheme = 'bootstrap';

    public $surgery_booking_id;
    public $file;
    public $file_name;
    public $notes;

    // بيانات معاينة الملف المختار قبل الحفظ
    public $selected_name;
    public $selected_size; // بالكيلوبايت
    public $selected_type;

    public $perPage = 10;

    protected function rules()
    {
        return [
            'file' => 'required|file|max:10240', // 10MB
            'file_name' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ];
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    // ✅ لما المستخدم يختار ملف: جهّز بيانات المعاينة واضبط اسم الملف الافتراضي
    public function updatedFile()
    {
        if ($this->file) {
            $this->selected_name = $this->file->getClientOriginalName();
            $this->selected_size = (int) ceil($this->file->getSize() / 1024); // KB
            $this->selected_type = $this->file->getClientMimeType();

            if (empty($this->file_name)) {
                // اسم افتراضي بدون الامتداد
                $this->file_name = pathinfo($this->selected_name, PATHINFO_FILENAME);
            }
        } else {
            $this->resetSelected();
        }
    }

    // زر إلغاء اختيار الملف قبل الحفظ
    public function resetSelected()
    {
        $this->reset(['file', 'selected_name', 'selected_size', 'selected_type']);
    }

    public function save()
    {
        $this->validate();

        // نخزّن في public/attachments... (حسب الترايت الحالي عندك)
        $path = $this->file_storage($this->file, 'attachments/surgical_files');

        surgicalfile::create([
            'surgery_booking_id' => $this->surgery_booking_id,
            'file_path' => $path, // مثال: attachments/surgical_files/2025/08/1206496.pdf
            'file_name' => $this->file_name ?: $this->file->getClientOriginalName(),
            'file_type' => $this->file->getClientMimeType(),
            'notes' => $this->notes,
            'user_id' => Auth::id(),
        ]);

        // تنظيف
        $this->reset(['file', 'file_name', 'notes', 'selected_name', 'selected_size', 'selected_type']);

        // ارجع لأول صفحة
        $this->resetPage();

        session()->flash('message', 'تم رفع الملف بنجاح.');
    }

    public function delete($id)
    {
        $file = surgicalfile::findOrFail($id);

        try {
            if ($file->file_path && Storage::disk('public')->exists($file->file_path)) {
                Storage::disk('public')->delete($file->file_path);
            }
        } catch (\Throwable $e) {
            // تجاهل
        }

        $file->delete();

        if ($this->page > 1 && surgicalfile::where('surgery_booking_id', $this->surgery_booking_id)->count() <= ($this->page - 1) * $this->perPage) {
            $this->previousPage();
        }
    }

    public function render()
    {
        $files = surgicalfile::where('surgery_booking_id', $this->surgery_booking_id)
                    ->latest()
                    ->paginate($this->perPage);

        return view('livewire.surgical.files-upload', compact('files'));
    }
}