<?php

namespace App\Http\Livewire\Suppliers\Program;

use Livewire\Component;
use App\Models\Suppliers\SupplierMerge;
use App\Models\Suppliers\suppliersdata;
use App\Models\Suppliers\SupplierInvoice;
use App\Models\Suppliers\SupplierOpeningBalance;
use App\Models\Suppliers\supplieralert;
use App\Models\Suppliers\SupplierDoc;

class MergeLog extends Component
{
    public function revert($mergeId)
    {
        $merge = SupplierMerge::findOrFail($mergeId);

        if ($merge->is_reverted) {
            session()->flash('error', '❌ تم التراجع بالفعل.');
            return;
        }

        // استرجاع المورد المدموج
        $mergedSupplier = suppliersdata::withTrashed()->find($merge->merged_supplier_id);
        if ($mergedSupplier) {
            $mergedSupplier->restore();
        }

        // استرجاع العمليات
        SupplierInvoice::where('supplier_id', $merge->main_supplier_id)
            ->where('created_at', '<=', $merge->created_at)
            ->update(['supplier_id' => $merge->merged_supplier_id]);

        SupplierOpeningBalance::where('supplier_id', $merge->main_supplier_id)
            ->update(['supplier_id' => $merge->merged_supplier_id]);

        supplieralert::where('supplier_id', $merge->main_supplier_id)
            ->update(['supplier_id' => $merge->merged_supplier_id]);

        SupplierDoc::where('supplier_id', $merge->main_supplier_id)
            ->update(['supplier_id' => $merge->merged_supplier_id]);

        $merge->update(['is_reverted' => true]);

        session()->flash('success', '✅ تم التراجع عن الدمج');
    }

    public function render()
    {
        $merges = SupplierMerge::with(['mainSupplier', 'mergedSupplier', 'user'])->latest()->get();
        return view('livewire.suppliers.program.merge-log', compact('merges'));
    }
}