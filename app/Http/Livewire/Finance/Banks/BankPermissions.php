<?php

namespace App\Http\Livewire\Finance\Banks;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Finance\BankPermission;
use App\Models\User;

class BankPermissions extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $permission_id, $user_id;
    public $can_issue_cheque = false,
           $can_approve_cheque = false,
           $can_clear_cheque = false,
           $can_cancel_cheque = false,
           $can_bounce_cheque = false,
           $can_reconcile_cheque = false,
           $can_view_reports = true;

    public $isEdit = false;

    protected $rules = [
        'user_id'              => 'required|exists:users,id',
        'can_issue_cheque'     => 'boolean',
        'can_approve_cheque'   => 'boolean',
        'can_clear_cheque'     => 'boolean',
        'can_cancel_cheque'    => 'boolean',
        'can_bounce_cheque'    => 'boolean',
        'can_reconcile_cheque' => 'boolean',
        'can_view_reports'     => 'boolean',
    ];

    public function resetFields()
    {
        $this->permission_id = null;
        $this->user_id = null;
        $this->can_issue_cheque = false;
        $this->can_approve_cheque = false;
        $this->can_clear_cheque = false;
        $this->can_cancel_cheque = false;
        $this->can_bounce_cheque = false;
        $this->can_reconcile_cheque = false;
        $this->can_view_reports = true;
        $this->isEdit = false;
    }

    public function save()
    {
        $data = $this->validate();
        BankPermission::create($data);
        session()->flash('success', __('bank_trans.permission_added'));
        $this->resetFields();
    }

    public function edit($id)
    {
        $perm = BankPermission::findOrFail($id);
        $this->fill($perm->toArray());
        $this->permission_id = $id;
        $this->isEdit = true;
    }

    public function update()
    {
        $data = $this->validate();
        BankPermission::findOrFail($this->permission_id)->update($data);
        session()->flash('success', __('bank_trans.permission_updated'));
        $this->resetFields();
    }

    public function delete($id)
    {
        BankPermission::findOrFail($id)->delete();
        session()->flash('success', __('bank_trans.permission_deleted'));
    }

    public function render()
    {
        return view('livewire.finance.banks.bank-permissions', [
            'permissions' => BankPermission::with('user')->paginate(10),
            'users' => User::all(),
        ]);
    }
}