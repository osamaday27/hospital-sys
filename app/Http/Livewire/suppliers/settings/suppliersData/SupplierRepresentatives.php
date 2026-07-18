<?php

namespace App\Http\Livewire\Suppliers\Settings\SuppliersData;

use Livewire\Component;
use App\Models\Suppliers\Representative;

class SupplierRepresentatives extends Component
{
    public $supplierId;

    // form fields
    public $name_ar;
    public $name_en;
    public $job_title;
    public $email;
    public $phone;
    public $status = 'active';

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
            $this->dispatchBrowserEvent('notify', [
                'type'    => 'warning',
                'message' => __('suppliers.please_enter_supplier_data_first'),
            ]);
            return;
        }

        $this->validate([
            'name_ar'   => 'required|string',
            'name_en'   => 'required|string',
            'job_title' => 'nullable|string',
            'email'     => 'nullable|email',
            'phone'     => 'nullable|string',
            'status'    => 'required|in:active,inactive',
        ]);

        Representative::updateOrCreate(
            ['id' => $this->edit_id],
            [
                'supplier_id' => $this->supplierId,
                'name_ar'     => $this->name_ar,
                'name_en'     => $this->name_en,
                'job_title'   => $this->job_title,
                'email'       => $this->email,
                'phone'       => $this->phone,
                'status'      => $this->status,
            ]
        );

        $this->resetForm();

        $this->dispatchBrowserEvent('notify', [
            'type'    => 'success',
            'message' => __('suppliers.representative_saved_successfully'),
        ]);
    }

    // <-- هذه الدالة أضفناها لتعمل مع زر التعديل -->
    public function edit($id)
    {
        $rep = Representative::findOrFail($id);

        $this->edit_id    = $rep->id;
        $this->name_ar    = $rep->name_ar;
        $this->name_en    = $rep->name_en;
        $this->job_title  = $rep->job_title;
        $this->email      = $rep->email;
        $this->phone      = $rep->phone;
        $this->status     = $rep->status;
    }

    public function confirmDelete($id)
    {
        if (! $this->supplierId) {
            $this->dispatchBrowserEvent('notify', [
                'type'    => 'warning',
                'message' => __('suppliers.please_enter_supplier_data_first'),
            ]);
            return;
        }

        $this->delete_id = $id;
        $this->dispatchBrowserEvent('show-delete-modal');
    }

    public function delete()
    {
        Representative::findOrFail($this->delete_id)->delete();
        $this->delete_id = null;
        $this->dispatchBrowserEvent('hide-delete-modal');

        $this->dispatchBrowserEvent('notify', [
            'type'    => 'success',
            'message' => __('suppliers.representative_deleted_successfully'),
        ]);
    }

    private function resetForm()
    {
        $this->reset([
            'name_ar', 'name_en', 'job_title',
            'email', 'phone', 'status', 'edit_id',
        ]);

        $this->status = 'active';
    }

    public function render()
    {
        return view('livewire.suppliers.settings.suppliers-data.supplier-representatives', [
            'representatives' => Representative::where('supplier_id', $this->supplierId)->get(),
        ]);
    }
}
