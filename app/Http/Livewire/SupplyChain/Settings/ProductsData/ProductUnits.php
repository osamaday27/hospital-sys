<?php

namespace App\Http\Livewire\SupplyChain\Settings\ProductsData;

use Livewire\Component;
use App\Models\supply_chain\ProductUnit;
use App\Models\supply_chain\Unit;

class ProductUnits extends Component
{
    public $productId;
    public $deleteUnitId = null;            // لتخزين معرّف الوحدة التي سيتم حذفها

    public $selected_unit_id;
    public $conversion_rate;
    public $cost_price;
    public $sale_price;
    public $unit_type;

    public $units       = [];
    public $isEditMode  = false;
    public $editingUnitId = null;

    protected $rules = [
        'selected_unit_id' => 'required|exists:units,id',
        'conversion_rate'  => 'required|numeric|not_in:0',
        'cost_price'       => 'nullable|numeric|min:0',
        'sale_price'       => 'nullable|numeric|min:0',
        'unit_type'        => 'required|in:purchase,sale,both,conversion',
    ];

    protected $listeners = [
        'productSaved'    => 'setProductId',
        'deleteConfirmed1' => 'deleteUnit',   // يستدعى عندما يؤكد المستخدم الحذف في المودال
    ];

    public function mount($productId = null)
    {
        $this->productId = $productId;
    }

    public function setProductId($id)
    {
        $this->productId = $id;
    }

    protected function messages()
    {
        return [
            'conversion_rate.not_in' => trans('supply_chain_trans.conversion_rate_not_zero'),
        ];
    }

    public function saveUnit()
    {
        $this->validate();

        if ($this->sale_price !== null
         && $this->cost_price !== null
         && $this->sale_price < $this->cost_price
        ) {
            $this->addError('sale_price', trans('supply_chain_trans.sale_price_less_than_cost'));
            return;
        }

        if ($this->isEditMode && $this->editingUnitId) {
            // تعديل الوحدة الحالية
            $unit = ProductUnit::findOrFail($this->editingUnitId);

            $isLastUnit = ProductUnit::where('product_id', $unit->product_id)
                                      ->orderByDesc('id')
                                      ->first()->id === $unit->id;

            if (! $isLastUnit) {
                $this->dispatchBrowserEvent('show-toast', [
                    'message' => 'لا يمكن تعديل هذه الوحدة لأنها في منتصف سلسلة التحويل.',
                    'type'    => 'error',
                ]);
                $this->resetForm();
                return;
            }

            $isFirstUnit = $unit->unit_large_id === null;

            $unit->update([
                'unit_large_id'   => $isFirstUnit ? $this->selected_unit_id : $unit->unit_large_id,
                'unit_small_id'   => $isFirstUnit ? null : $this->selected_unit_id,
                'conversion_rate' => $this->conversion_rate,
                'cost_price'      => $this->cost_price,
                'sale_price'      => $this->sale_price,
                'unit_type'       => $this->unit_type,
            ]);

            $toastMessage = trans('general.updated_successfully');
        } else {
            // إضافة وحدة جديدة
            $lastUnit = ProductUnit::where('product_id', $this->productId)
                                   ->orderByDesc('id')
                                   ->first();

            if (! $lastUnit) {
                $unit_large_id = $this->selected_unit_id;
                $unit_small_id = null;
            } elseif ($lastUnit->unit_small_id === null) {
                $unit_large_id = $lastUnit->unit_large_id;
                $unit_small_id = $this->selected_unit_id;
            } else {
                $unit_large_id = $lastUnit->unit_small_id;
                $unit_small_id = $this->selected_unit_id;
            }

            ProductUnit::create([
                'product_id'      => $this->productId,
                'unit_large_id'   => $unit_large_id,
                'unit_small_id'   => $unit_small_id,
                'conversion_rate' => $this->conversion_rate,
                'cost_price'      => $this->cost_price,
                'sale_price'      => $this->sale_price,
                'unit_type'       => $this->unit_type,
            ]);

            $toastMessage = trans('supply_chain_trans.unit_added_successfully');
        }

        $this->dispatchBrowserEvent('show-toast', [
            'message' => $toastMessage,
            'type'    => 'success',
        ]);

        $this->resetForm();
    }

    public function editUnit($id)
    {
        $unit = ProductUnit::findOrFail($id);

        $this->editingUnitId    = $unit->id;
        $this->selected_unit_id = $unit->unit_large_id === null
                                   ? $unit->unit_small_id
                                   : $unit->unit_large_id;
        $this->conversion_rate  = $unit->conversion_rate;
        $this->cost_price       = $unit->cost_price;
        $this->sale_price       = $unit->sale_price;
        $this->unit_type        = $unit->unit_type;
        $this->isEditMode       = true;
    }

    public function cancelEdit()
    {
        $this->resetForm();
        $this->dispatchBrowserEvent('show-toast', [
            'message' => 'تم إلغاء التعديل.',
            'type'    => 'info',
        ]);
    }

    public function resetForm()
    {
        $this->reset([
            'selected_unit_id',
            'conversion_rate',
            'cost_price',
            'sale_price',
            'unit_type',
            'isEditMode',
            'editingUnitId',
        ]);
    }

    /**
     * يُخزن معرّف الوحدة التي يريد المستخدم حذفها
     * ثم يفتح مودال التأكيد
     */
    public function confirmDelete($id)
    {
        $this->deleteUnitId = $id;
        $this->dispatchBrowserEvent('open-delete-modal1');
    }

    /**
     * يستدعى بعد أن يضغط المستخدم على "نعم، احذف"
     */
    public function deleteUnit()
    {
        $id = $this->deleteUnitId;
        $unit = ProductUnit::find($id);

        if (! $unit) {
            $this->dispatchBrowserEvent('close-delete-modal1');
            return;
        }

        $isFirstUnit = $unit->unit_large_id === null;
        $isLastUnit  = ProductUnit::where('product_id', $unit->product_id)
                                  ->orderByDesc('id')
                                  ->first()->id === $unit->id;

        if ($isFirstUnit || ! $isLastUnit) {
            $this->dispatchBrowserEvent('close-delete-modal1');
            $this->dispatchBrowserEvent('show-toast', [
                'message' => $isFirstUnit
                    ? 'لا يمكن حذف أول وحدة لأنها تعتبر الأساس.'
                    : 'لا يمكن حذف هذه الوحدة لأنها في منتصف سلسلة التحويل.',
                'type'    => 'error',
            ]);
            return;
        }

        $unit->delete();

        $this->dispatchBrowserEvent('close-delete-modal1');
        $this->dispatchBrowserEvent('show-toast', [
            'message' => __('general.deleted_successfully'),
            'type'    => 'success',
        ]);
    }

    public function render()
    {
        $this->units = Unit::all();

        $productUnits = $this->productId
            ? ProductUnit::with(['unitLarge', 'unitSmall'])
                ->where('product_id', $this->productId)
                ->get()
            : collect();

        return view('livewire.supply-chain.settings.products-data.product-units', compact('productUnits'));
    }
}
