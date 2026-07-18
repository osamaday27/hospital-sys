<?php

namespace App\Http\Livewire\Finance\Settings;

use Livewire\Component;
use App\Models\Finance\Bank;
use App\Models\Finance\BankMachine;
use App\Models\General\Branch;
use Illuminate\Support\Facades\Auth;

class BankMachines extends Component
{
    public $machines, $bank_id, $branch_id, $machine_code, $machine_type, $location, $status = 'active';
    public $machine_id;
    public $isEdit = false;

    protected $rules = [
        'bank_id'      => 'required|integer|exists:fin_banks,id',
        'branch_id'    => 'nullable|integer',
        'machine_code' => 'required|string|unique:fin_bank_machines,machine_code',
        'machine_type' => 'required|string|max:50',
        'location'     => 'nullable|string|max:255',
        'status'       => 'required|string',
    ];

    public function mount()
    {
        $this->loadMachines();
    }

    public function loadMachines()
    {
        $this->machines = BankMachine::with(['bank','branch'])->get();
    }

    public function save()
    {
        $this->validate();

        if ($this->isEdit && $this->machine_id) {
            $machine = BankMachine::findOrFail($this->machine_id);
            $machine->update([
                'bank_id'      => $this->bank_id,
                'branch_id'    => $this->branch_id,
                'machine_code' => $this->machine_code,
                'machine_type' => $this->machine_type,
                'location'     => $this->location,
                'status'       => $this->status,
            ]);
        } else {
            BankMachine::create([
                'bank_id'      => $this->bank_id,
                'branch_id'    => $this->branch_id,
                'machine_code' => $this->machine_code,
                'machine_type' => $this->machine_type,
                'location'     => $this->location,
                'status'       => $this->status,
            ]);
        }

        $this->resetInput();
        $this->loadMachines();
        session()->flash('message', 'تم حفظ بيانات الماكينة بنجاح');
    }

    public function edit($id)
    {
        $machine = BankMachine::findOrFail($id);
        $this->machine_id   = $machine->id;
        $this->bank_id      = $machine->bank_id;
        $this->branch_id    = $machine->branch_id;
        $this->machine_code = $machine->machine_code;
        $this->machine_type = $machine->machine_type;
        $this->location     = $machine->location;
        $this->status       = $machine->status;
        $this->isEdit       = true;
    }

    public function delete($id)
    {
        $machine = BankMachine::findOrFail($id);
        $machine->delete();
        $this->loadMachines();
        session()->flash('message', 'تم حذف الماكينة بنجاح');
    }

    public function resetInput()
    {
        $this->machine_id = null;
        $this->bank_id = null;
        $this->branch_id = null;
        $this->machine_code = '';
        $this->machine_type = '';
        $this->location = '';
        $this->status = 'active';
        $this->isEdit = false;
    }

    public function render()
    {
        $banks   = Bank::get();
        $branches= Branch::get();
        return view('livewire.finance.settings.bank-machines', [
            'machines' => $this->machines,
            'banks'    => $banks,
            'branches' => $branches,
        ]);
    }
}