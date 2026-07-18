<?php

namespace App\Http\Livewire\Finance\Settings;

use Livewire\Component;
use App\Models\Finance\Bank;
use App\Models\General\Branch;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class BankSett extends Component
{
    public $banks, $name_ar, $name_en, $account_number, $iban, $swift_code, $currency, $balance, $status = 'active', $branch_id;
    public $bank_id;
    public $isEdit = false;
protected function rules()
{
    return [
        'name_ar'        => 'required|string|max:255',
        'name_en'        => 'nullable|string|max:255',
        'account_number' => [
            'required',
            'string',
            Rule::unique('fin_banks', 'account_number')->ignore($this->bank_id),
        ],
        'iban'           => 'nullable|string|max:50',
        'swift_code'     => 'nullable|string|max:50',
        'currency'       => 'required|string|max:10',
        'balance'        => 'nullable|numeric',
        'status'         => 'required|string',
        'branch_id'      => 'nullable|integer',
    ];
}

    public function mount()
    {
        $this->loadBanks();
    }

    public function loadBanks()
    {
        $this->banks = Bank::with('branch')->get();
    }

  public function save()
{
    $this->validate();

    if ($this->isEdit && $this->bank_id) {
        $bank = Bank::findOrFail($this->bank_id);
        $bank->update([
            'name_ar'        => $this->name_ar,
            'name_en'        => $this->name_en,
            'account_number' => $this->account_number,
            'iban'           => $this->iban,
            'swift_code'     => $this->swift_code,
            'currency'       => $this->currency,
            'balance'        => $this->balance,
            'status'         => $this->status,
            'branch_id'      => $this->branch_id,
        ]);
    } else {
        Bank::create([
            'branch_id'      => $this->branch_id,
            'name_ar'        => $this->name_ar,
            'name_en'        => $this->name_en,
            'account_number' => $this->account_number,
            'iban'           => $this->iban,
            'swift_code'     => $this->swift_code,
            'currency'       => $this->currency,
            'balance'        => $this->balance,
            'status'         => $this->status,
        ]);
    }

    $this->resetInput();
    $this->loadBanks();

    session()->flash('message', trans('bank_trans.success'));

    // ✅ إغلاق المودال
    $this->dispatchBrowserEvent('close-modal');
}

    public function edit($id)
    {
        $bank = Bank::findOrFail($id);
        $this->bank_id       = $bank->id;
        $this->name_ar       = $bank->name_ar;
        $this->name_en       = $bank->name_en;
        $this->account_number= $bank->account_number;
        $this->iban          = $bank->iban;
        $this->swift_code    = $bank->swift_code;
        $this->currency      = $bank->currency;
        $this->balance       = $bank->balance;
        $this->status        = $bank->status;
        $this->branch_id     = $bank->branch_id;
        $this->isEdit = true;
    }

    public function delete($id)
    {
        $bank = Bank::findOrFail($id);
        $bank->delete();
        $this->loadBanks();
        session()->flash('message', 'تم حذف البنك بنجاح');
    }

    public function resetInput()
    {
        $this->bank_id = null;
        $this->name_ar = '';
        $this->name_en = '';
        $this->account_number = '';
        $this->iban = '';
        $this->swift_code = '';
        $this->currency = 'EGP';
        $this->balance = 0;
        $this->status = 'active';
        $this->branch_id = null;
        $this->isEdit = false;
    }

    public function render()
    {
        $branches = Branch::get();
        return view('livewire.finance.settings.bank-sett', [
            'banks'    => $this->banks,
            'branches' => $branches,
        ]);
    }
}