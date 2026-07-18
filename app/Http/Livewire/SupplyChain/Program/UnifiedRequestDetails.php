<?php

namespace App\Http\Livewire\SupplyChain\Program;

use Livewire\Component;
use App\Models\supply_chain\ClientItemRequest;
use App\Models\supply_chain\EmployeeItemRequest;
use App\Models\supply_chain\DepartmentItemRequest;
use App\Models\supply_chain\StoreTransferRequest;
use App\Models\supply_chain\StoreMovement;
use App\Models\supply_chain\storemaindata;
use App\Models\User;

class UnifiedRequestDetails extends Component
{
    public $requestId;
    public $selectedRequest = null;
    public $selectedType = null;

    public function mount($requestId)
    {
        $this->requestId = $requestId;
        $this->loadRequest();
    }

    public function loadRequest()
    {
        $this->selectedRequest = ClientItemRequest::with([
            'client', 'store', 'items.product', 'items.unit', 'user', 'issuer'
        ])->find($this->requestId);
        if ($this->selectedRequest) {
            $this->selectedType = 'client';
            $this->attachMovements();
            return;
        }

        $this->selectedRequest = EmployeeItemRequest::with([
            'employee', 'store', 'details.product', 'details.unit', 'user', 'issuer'
        ])->find($this->requestId);
        if ($this->selectedRequest) {
            $this->selectedType = 'employee';
            $this->attachMovements();
            return;
        }

        $this->selectedRequest = DepartmentItemRequest::with([
            'department', 'store', 'items.product', 'items.unit', 'creator'
        ])->find($this->requestId);
        if ($this->selectedRequest) {
            $this->selectedType = 'department';
            $this->attachMovements();
            return;
        }

        $this->selectedRequest = StoreTransferRequest::with([
            'store', 'storeTo', 'items.product', 'items.unit', 'creator', 'issuer'
        ])->find($this->requestId);
        if ($this->selectedRequest) {
            $this->selectedType = 'store';
            $this->attachMovements();
            return;
        }
    }

    public function attachMovements()
    {
        $referenceNo = $this->selectedRequest->request_number ?? null;
        if (!$referenceNo) return;

        $movements = StoreMovement::where('reference_no', $referenceNo)
            ->get(['product_id', 'batch_no', 'expiry_date', 'movement_date', 'store_id', 'units_id', 'quantity', 'user_id']);

        $stores = storemaindata::pluck('name_ar', 'id');
        $units = \App\Models\supply_chain\unit::pluck('name_ar', 'id');
        $users = User::pluck('name', 'id');

        $items = ($this->selectedRequest->items ?? $this->selectedRequest->details);

        foreach ($items as $item) {
            $relatedMovements = $movements->where('product_id', $item->product_id);

            $item->issued_quantity = $relatedMovements->sum('quantity');
            $firstMovement = $relatedMovements->first();

            $item->issued_unit_name = $firstMovement ? ($units[$firstMovement->units_id] ?? null) : null;
            $item->issuer_name = $firstMovement ? ($users[$firstMovement->user_id] ?? null) : null;
            $item->store_name = $firstMovement ? ($stores[$firstMovement->store_id] ?? null) : null;
            $item->batch_no = $firstMovement->batch_no ?? null;
            $item->expiry_date = $firstMovement->expiry_date ?? null;
            $item->movement_date = $firstMovement->movement_date ?? null;
        }
    }

    public function render()
    {
        return view('livewire.supply-chain.program.unified-request-details');
    }
}