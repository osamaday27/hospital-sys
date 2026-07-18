<?php
namespace App\Http\Controllers\finance\lockers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class invoicepaymentcontroller extends Controller
{
    public function invoicepayment($invoice_id)
    {
        $invoice_id = $invoice_id;
        // Return the view for cancellation request with the invoice ID
        return view('finance.lockers.program.invoicepayment', compact('invoice_id'));
    }

    public function invoicedetails($invoice_id)
    {
        $invoice_id = $invoice_id;
        // Return the view for cancellation request with the invoice ID
        return view('finance.lockers.program.invoicedetails', compact('invoice_id'));
    }

    public function invoice_payment_print($invoice_id)
    {
        // ✅ أضفنا تحميل الخدمات المرتبطة بالفاتورة عشان يظهر جدول الخدمات وتحسب المجاميع صح
        $receipt = \App\Models\finance\receipts::with([
            'invoice.patient',
            'invoice.reservations.doctor',
            'invoice.reservations.clinic',
            'invoice.reservations.service',
            'invoice.services.service', // <-- NEW: load services on the invoice
            'locker'
        ])->where('invoices_id', $invoice_id)->latest()->firstOrFail();

        return view('finance.lockers.program.print_invoice_payment', [
            'title' => 'إيصال دفع نقدية',
            'receipt' => $receipt,
        ]);
    }

    public function invoice_payment_printthermal($invoice_id)
    {
        // ✅ نفس التحميل للخدمات
        $receipt = \App\Models\finance\receipts::with([
            'invoice.patient',
            'invoice.reservations.doctor',
            'invoice.reservations.clinic',
            'invoice.reservations.service',
            'invoice.services.service', // <-- NEW
            'locker'
        ])->where('invoices_id', $invoice_id)->latest()->firstOrFail();

        return view('finance.lockers.program.print_invoice_paymentthermal', [
            'title' => 'إيصال دفع نقدية',
            'receipt' => $receipt,
        ]);
    }
}