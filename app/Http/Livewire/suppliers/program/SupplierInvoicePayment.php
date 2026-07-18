<?php

namespace App\Http\Livewire\Suppliers\Program;

use Livewire\Component;
use App\Models\Finance\slocker;
use App\Models\Suppliers\SupplierInvoice;
use App\Models\Suppliers\SupplierPaymentInstallment;
use App\Models\Finance\Bank;
use App\Models\Finance\BankAccount;
use App\Models\Finance\ChequeBook;
use App\Models\Finance\ChequeLeaf;
use App\Models\Finance\BankTransaction;
use App\Models\Finance\BankCheque;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class SupplierInvoicePayment extends Component
{
    public $supplier_invoice_id; // رقم الفاتورة
    public $invoice; // بيانات الفاتورة
    public $lockers; // الخزن المتاحة
    public $banks = []; // البنوك المتاحة
    public $notes; // ملاحظات عامة على الدفعات

    // بيانات الأقساط (الدفعات)
    public $installments = [
        [
            'uuid' => '', // معرف فريد لكل دفعة
            'amount' => '', // المبلغ
            'method' => 'cash', // طريقة الدفع (كاش - شيك - تحويل - فيزا)
            'locker_id' => '', // الخزنة في حالة الدفع كاش
            'due_date' => '', // تاريخ الاستحقاق
            'reference_no' => '', // رقم المرجع
            'bank_id' => '', // البنك
            'bankAccounts' => [], // حسابات البنك المرتبطة
            'bank_account_id' => '', // الحساب البنكي
            'chequeBooks' => [], // دفاتر الشيكات
            'cheque_book_id' => '', // دفتر الشيك
            'chequeLeaves' => [], // أوراق الشيكات
            'cheque_leaf_id' => '', // ورقة الشيك
        ],
    ];

    // قواعد التحقق من البيانات
    protected $rules = [
        'installments.*.amount' => 'required|numeric|min:1',
        'installments.*.method' => 'required|string|in:cash,cheque,bank_transfer,visa,other',
        'installments.*.locker_id' => 'required_if:installments.*.method,cash|exists:fin_s_locker,id',
        'installments.*.bank_id' => 'nullable|exists:fin_banks,id',
        'installments.*.bank_account_id' => 'nullable|exists:fin_bank_accounts,id',
        'installments.*.cheque_book_id' => 'nullable|exists:fin_cheque_books,id',
        'installments.*.cheque_leaf_id' => 'nullable|exists:fin_cheque_leaves,id',
        'installments.*.due_date' => 'nullable|date',
        'installments.*.reference_no' => 'nullable|string|max:255',
        'notes' => 'nullable|string|max:500',
    ];

    // تحميل البيانات الأساسية عند فتح الكمبوننت
    public function mount($supplier_invoice_id)
    {
        $this->supplier_invoice_id = $supplier_invoice_id;

        // تحميل الفاتورة مع بيانات المورد
        $this->invoice = SupplierInvoice::with('supplier')->findOrFail($supplier_invoice_id);

        // تحميل الخزن النشطة
        $this->lockers = slocker::where('stauts', 'active')->get();

        // تحميل البنوك النشطة
        $this->banks = Bank::where('status', 'active')->get();

        // إضافة UUID لأول دفعة
        $this->installments[0]['uuid'] = (string) Str::uuid();
    }

    // تحديث الحسابات والبنوك والشيكات عند تغيير الاختيارات
    public function updatedInstallments($value, $key)
    {
        $parts = explode('.', $key);
        if (count($parts) < 2) {
            return;
        }

        $index = $parts[0];
        $field = $parts[1];

        // تحديث الحسابات عند اختيار البنك
        if ($field === 'bank_id') {
            $this->installments[$index]['bankAccounts'] = [];
            $this->installments[$index]['bank_account_id'] = '';
            $this->installments[$index]['chequeBooks'] = [];
            $this->installments[$index]['cheque_book_id'] = '';
            $this->installments[$index]['chequeLeaves'] = [];
            $this->installments[$index]['cheque_leaf_id'] = '';

            if ($value) {
                $this->installments[$index]['bankAccounts'] = BankAccount::where('bank_id', $value)->where('status', 'active')->get()->toArray();
            }
        }

        // تحديث دفاتر الشيكات عند اختيار الحساب البنكي
        if ($field === 'bank_account_id') {
            $this->installments[$index]['chequeBooks'] = [];
            $this->installments[$index]['cheque_book_id'] = '';
            $this->installments[$index]['chequeLeaves'] = [];
            $this->installments[$index]['cheque_leaf_id'] = '';

            if ($value) {
                $this->installments[$index]['chequeBooks'] = ChequeBook::where('bank_account_id', $value)->where('status', 'active')->get()->toArray();
            }
        }

        // تحديث أوراق الشيكات عند اختيار دفتر شيكات
        if ($field === 'cheque_book_id') {
            $this->installments[$index]['chequeLeaves'] = [];
            $this->installments[$index]['cheque_leaf_id'] = '';

            if ($value) {
                $this->installments[$index]['chequeLeaves'] = ChequeLeaf::where('cheque_book_id', $value)->where('status', 'available')->get()->toArray();
            }
        }
    }

    // إضافة دفعة جديدة
    public function addInstallment()
    {
        $this->installments[] = [
            'uuid' => (string) Str::uuid(),
            'amount' => '',
            'method' => 'cash',
            'locker_id' => '',
            'due_date' => '',
            'reference_no' => '',
            'bank_id' => '',
            'bankAccounts' => [],
            'bank_account_id' => '',
            'chequeBooks' => [],
            'cheque_book_id' => '',
            'chequeLeaves' => [],
            'cheque_leaf_id' => '',
        ];
    }

    // حذف دفعة
    public function removeInstallment($index)
    {
        unset($this->installments[$index]);
        $this->installments = array_values($this->installments);
    }

    // حفظ الدفعات
    public function save()
    {
        $this->validate();

        $invoice = $this->invoice->fresh();
        $totalInstallments = collect($this->installments)->sum('amount');

        // منع إدخال مبلغ أكبر من المتبقي
        if ($totalInstallments > $invoice->remaining_amount) {
            $this->addError('installments', __('suppliers.amount_exceeds_remaining'));
            return;
        }

        foreach ($this->installments as $inst) {
            // حفظ بيانات القسط في جدول الأقساط
            $installment = SupplierPaymentInstallment::create([
                'supplier_invoice_id' => $this->supplier_invoice_id,
                'method' => $inst['method'],
                'amount' => $inst['amount'],
                'locker_id' => $inst['method'] === 'cash' ? $inst['locker_id'] : null,
                'due_date' => $inst['due_date'],
                'reference_no' => $inst['reference_no'],
                'bank_id' => $inst['bank_id'] ?: null,
                'bank_account_id' => $inst['bank_account_id'] ?: null,
                'cheque_book_id' => $inst['cheque_book_id'] ?: null, // ✅ جديد
                'cheque_leaf_id' => $inst['cheque_leaf_id'] ?: null, // ✅ جديد
                'notes' => $this->notes,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // ✅ لو طريقة الدفع شيك
            if ($inst['method'] === 'cheque' && !empty($inst['cheque_leaf_id'])) {
                $leaf = ChequeLeaf::where('id', $inst['cheque_leaf_id'])->where('status', 'available')->first();

                if ($leaf) {
                    // إنشاء شيك جديد
                    $cheque = BankCheque::create([
                        'bank_account_id' => $inst['bank_account_id'],
                        'cheque_number' => $leaf->cheque_number,
                        'amount' => $inst['amount'],
                        'issue_date' => now()->toDateString(),
                        'due_date' => $inst['due_date'],
                        'payee' => $this->invoice->supplier->name_ar ?? 'Supplier',
                        'notes' => $this->notes,
                        'status' => 'issued',
                        'created_by' => Auth::id(),
                    ]);

                    // تحديث ورقة الشيك
                    $leaf->update([
                        'status' => 'issued',
                        'linked_cheque_id' => $cheque->id,
                    ]);
                }
            }

            // ✅ لو طريقة الدفع تحويل بنكي أو فيزا
            if (in_array($inst['method'], ['bank_transfer', 'visa']) && !empty($inst['bank_account_id'])) {
                BankTransaction::create([
                    'bank_id' => $inst['bank_id'],
                    'account_id' => $inst['bank_account_id'],
                    'transaction_date' => $inst['due_date'] ?: now(),
                    'type' => 'withdrawal', // سحب من الحساب البنكي
                    'amount' => $inst['amount'],
                    'reference' => $inst['reference_no'],
                    'related_invoice_id' => $invoice->id,
                    'notes' => 'Supplier Payment Installment',
                    'user_id' => Auth::id(),
                ]);
            }
        }

        // تحديث بيانات الفاتورة
        $invoice->paid_amount += $totalInstallments;
        $invoice->remaining_amount = $invoice->total_amount - $invoice->paid_amount;
        $invoice->status = $invoice->remaining_amount > 0 ? 'partial' : 'paid';
        $invoice->save();

        // رسالة نجاح
        session()->flash('success', __('suppliers.payment_success'));

        // إعادة ضبط النموذج
        $this->reset(['notes', 'installments']);
        $this->installments = [
            [
                'uuid' => (string) Str::uuid(),
                'amount' => '',
                'method' => 'cash',
                'locker_id' => '',
                'due_date' => '',
                'reference_no' => '',
                'bank_id' => '',
                'bankAccounts' => [],
                'bank_account_id' => '',
                'chequeBooks' => [],
                'cheque_book_id' => '',
                'chequeLeaves' => [],
                'cheque_leaf_id' => '',
            ],
        ];

        // إشعار بإنه تم إضافة دفعة
        $this->emit('paymentAdded');
    }

    public function render()
    {
        return view('livewire.suppliers.program.supplier-invoice-payment', [
            'lockers' => $this->lockers,
            'invoice' => $this->invoice,
            'banks' => $this->banks,
        ]);
    }
}