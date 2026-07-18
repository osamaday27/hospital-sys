<?php

namespace App\Http\Livewire\Suppliers\Program;

use Livewire\Component;
use App\Models\Suppliers\suppliersdata;
use App\Models\Suppliers\SupplierInvoice;
use App\Models\Suppliers\SupplierPayment;
use App\Models\Suppliers\SupplierOpeningBalance;
use App\Models\Suppliers\supplieralert;
use App\Models\Suppliers\SupplierDoc;
use App\Models\Suppliers\SupplierMerge;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MergeSuppliers extends Component
{
    public $mainSupplier;   // المورد الأساسي
    public $mergedSupplier; // المورد المكرر

    public function merge()
    {
        $this->validate([
            'mainSupplier'   => 'required|different:mergedSupplier',
            'mergedSupplier' => 'required',
        ]);

        DB::transaction(function () {
            $logData = [];

            // نقل الفواتير
            $countInvoices = SupplierInvoice::where('supplier_id', $this->mergedSupplier)
                ->update(['supplier_id' => $this->mainSupplier]);
            $logData['invoices'] = $countInvoices;

            // نقل الأرصدة الافتتاحية
            $countOpening = SupplierOpeningBalance::where('supplier_id', $this->mergedSupplier)
                ->update(['supplier_id' => $this->mainSupplier]);
            $logData['opening_balances'] = $countOpening;

            // نقل الإشعارات
            $countAlerts = supplieralert::where('supplier_id', $this->mergedSupplier)
                ->update(['supplier_id' => $this->mainSupplier]);
            $logData['alerts'] = $countAlerts;

            // نقل الملفات
            $countDocs = SupplierDoc::where('supplier_id', $this->mergedSupplier)
                ->update(['supplier_id' => $this->mainSupplier]);
            $logData['docs'] = $countDocs;

            // تسجيل الدمج
            SupplierMerge::create([
                'main_supplier_id'   => $this->mainSupplier,
                'merged_supplier_id' => $this->mergedSupplier,
                'user_id'            => Auth::id(),
                'affected_tables'    => $logData,
            ]);

            // عمل Soft Delete للمورد المدموج
            suppliersdata::where('id', $this->mergedSupplier)->delete();
        });

        session()->flash('success', '✅ تم دمج الموردين بنجاح');
        $this->reset(['mainSupplier', 'mergedSupplier']);
    }

    public function render()
    {
        $suppliers = suppliersdata::orderBy('name_ar')->get();
        return view('livewire.suppliers.program.merge-suppliers', compact('suppliers'));
    }
}