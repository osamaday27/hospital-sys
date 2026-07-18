<?php

namespace App\Http\Livewire\Suppliers\Program;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Suppliers\SupplierPaymentInstallment;
use App\Models\Suppliers\SupplierPaymentAlert;

class PaymentAlerts extends Component
{
    use WithPagination;

    public $supplier_name;
    public $invoice_status;
    public $payment_status;
    public $from_date;
    public $to_date;
    public $review_status;

    protected $paginationTheme = 'bootstrap';

    public function updating($field)
    {
        $this->resetPage();
    }

    // ✅ تحديث حالة المراجعة في جدول alerts
    public function toggleReviewed($id)
    {
        $alert = SupplierPaymentAlert::find($id);
        if ($alert) {
            $alert->reviewed = !$alert->reviewed;
            $alert->save();
        }
    }

    public function render()
    {
        $query = SupplierPaymentAlert::with(['installment.invoice.supplier'])
            ->whereHas('installment', function ($q) {
                $q->where('status', '!=', 'paid'); // نخفي المدفوعة
            });

        // فلترة المورد
        if ($this->supplier_name) {
            $query->whereHas('installment.invoice.supplier', function ($q) {
                $q->where('name_ar', 'like', '%' . $this->supplier_name . '%');
            });
        }

        // فلترة حالة الفاتورة
        if ($this->invoice_status) {
            $query->whereHas('installment.invoice', function ($q) {
                $q->where('status', $this->invoice_status);
            });
        }

        // فلترة حالة الدفعة
        if ($this->payment_status) {
            $query->whereHas('installment', function ($q) {
                $q->where('status', $this->payment_status);
            });
        }

        // فلترة بالمراجعة
        if ($this->review_status == 'reviewed') {
            $query->where('reviewed', true);
        } elseif ($this->review_status == 'not_reviewed') {
            $query->where('reviewed', false);
        }

        // فلترة بالتاريخ
        if ($this->from_date && $this->to_date) {
            $query->whereHas('installment', function ($q) {
                $q->whereBetween('due_date', [$this->from_date, $this->to_date]);
            });
        }

        $alerts = $query->orderBy('alert_date', 'asc')->paginate(10);

        return view('livewire.suppliers.program.payment-alerts', [
            'alerts' => $alerts
        ]);
    }
}