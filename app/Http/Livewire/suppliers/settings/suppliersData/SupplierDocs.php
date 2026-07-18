<?php

namespace App\Http\Livewire\Suppliers\Settings\SuppliersData;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use App\Traits\web\file_storage;
use App\Models\Suppliers\SupplierDoc;

class SupplierDocs extends Component
{
    use WithFileUploads, file_storage;

    public $supplierId;
    public $file_type;
    public $document_name;
    public $file_upload;

    public $edit_id   = null;
    public $delete_id = null;

    protected $listeners = [
        'supplierIdUpdated' => 'updateSupplierId',
    ];

    public function mount($supplierId = null)
    {
        $this->supplierId = $supplierId;
    }

    public function updateSupplierId($id)
    {
        $this->supplierId = $id;
    }

    public function save()
    {
        if (! $this->supplierId) {
            $this->dispatchBrowserEvent('toast', [
                'message' => __('suppliers.please_enter_supplier_data_first'),
                'type'    => 'warning',
            ]);
            return;
        }

        $this->validate([
            'file_type'     => 'required|in:tax_card,commercial_registration,vat_certificate,other',
            'document_name' => 'required|string',
            'file_upload'   => 'required|file|max:5120', // 5MB
        ]);

        // إذا كنا في وضع التعديل، احصل على المسار القديم وحذفه
        if ($this->edit_id) {
            $old = SupplierDoc::findOrFail($this->edit_id)->file_path;
            Storage::disk('public')->delete($old);
        }

        // رفع الملف الجديد
        $paths = $this->file_storage($this->file_upload, 'supplierdocs');
        $path  = $paths[0] ?? null;

        $data = [
            'supplier_id' => $this->supplierId,
            'file_type'   => $this->file_type,
            'file_name'   => $this->document_name,
            'file_path'   => $path,
        ];

        if ($this->edit_id) {
            SupplierDoc::findOrFail($this->edit_id)->update($data);
        } else {
            SupplierDoc::create($data);
        }

        $this->reset(['file_type','document_name','file_upload','edit_id']);

        $this->dispatchBrowserEvent('toast', [
            'message' => __('suppliers.doc_saved_successfully'),
            'type'    => 'success',
        ]);
    }

    public function edit($id)
    {
        $doc = SupplierDoc::findOrFail($id);
        $this->edit_id       = $doc->id;
        $this->file_type     = $doc->file_type;
        $this->document_name = $doc->file_name;
        // لا نحمل $file_upload للتعديل
    }

    public function confirmDelete($id)
    {
        $this->delete_id = $id;
        $this->dispatchBrowserEvent('show-delete-modal2');
    }

    public function delete()
    {
        $doc = SupplierDoc::findOrFail($this->delete_id);
        // حذف الملف من التخزين
        Storage::disk('public')->delete($doc->file_path);
        $doc->delete();

        $this->delete_id = null;
        $this->dispatchBrowserEvent('hide-delete-modal2');

        $this->dispatchBrowserEvent('toast', [
            'message' => __('suppliers.doc_deleted_successfully'),
            'type'    => 'success',
        ]);
    }

    public function render()
    {
        return view('livewire.suppliers.settings.suppliers-data.supplier-docs', [
            'supplierDocs' => SupplierDoc::where('supplier_id', $this->supplierId)->get(),
        ]);
    }
}
