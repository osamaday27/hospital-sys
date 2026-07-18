<?php 

namespace App\Models\Suppliers;

use Illuminate\Database\Eloquent\Model;

class SupplierPaymentInstallment extends Model
{
    protected $table = 'supplier_payment_installments';

    protected $fillable = [
        'supplier_invoice_id',
        'supplier_payment_id',
        'method',
        'amount',
        'reference_no',
        'bank_id',
        'bank_account_id',
        'cheque_book_id',
        'cheque_leaf_id',
        'locker_id',
        'due_date',
        'paid_date',
        'status',
        'notes',
    ];

    // 🔗 الفاتورة
    public function invoice()
    {
        return $this->belongsTo(SupplierInvoice::class, 'supplier_invoice_id');
    }

    // 🔗 البنك
    public function bank()
    {
        return $this->belongsTo(\App\Models\Finance\Bank::class, 'bank_id');
    }

    // 🔗 حساب البنك
    public function bankAccount()
    {
        return $this->belongsTo(\App\Models\Finance\BankAccount::class, 'bank_account_id');
    }

    // 🔗 الخزنة
    public function locker()
    {
        return $this->belongsTo(\App\Models\Finance\slocker::class, 'locker_id');
    }

    // 🔗 دفتر الشيك
    public function chequeBook()
    {
        return $this->belongsTo(\App\Models\Finance\ChequeBook::class, 'cheque_book_id');
    }

    // 🔗 ورقة الشيك
    public function chequeLeaf()
    {
        return $this->belongsTo(\App\Models\Finance\ChequeLeaf::class, 'cheque_leaf_id');
    }
}