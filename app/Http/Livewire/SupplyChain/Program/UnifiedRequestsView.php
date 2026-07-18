<?php

namespace App\Http\Livewire\SupplyChain\Program;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\supply_chain\{
    ClientItemRequest,
    departmentItemRequest,
    EmployeeItemRequest,
    storeItemRequest
};
use Carbon\Carbon;
use Illuminate\Pagination\LengthAwarePaginator;

class UnifiedRequestsView extends Component
{
    use WithPagination;

    public $filter_type = '';
    public $search = '';
    public $perPage = 10;
    public $from_date;
    public $to_date;
    public $tab = 'open'; // 'open' or 'closed'

    public function mount()
    {
        $this->from_date = now()->subMonth()->format('Y-m-d');
        $this->to_date = now()->format('Y-m-d');
    }

    public function render()
    {
        $requests = collect();

        $filterByStatus = $this->tab === 'open'
            ? ['new', 'partial']
            : ['processed', 'closed'];

        $dateFrom = Carbon::parse($this->from_date)->startOfDay();
        $dateTo = Carbon::parse($this->to_date)->endOfDay();

        // Clients
        if ($this->filter_type === '' || $this->filter_type === 'client') {
            $client = ClientItemRequest::with(['store', 'client'])
                ->whereIn('status', $filterByStatus)
                ->whereBetween('request_date', [$dateFrom, $dateTo])
                ->whereHas('client', fn($q) => $q->where('full_name', 'like', "%{$this->search}%"))
                ->get()
                ->map(fn($r) => [
                    'id' => $r->id,
                    'number' => $r->request_number,
                    'date' => $r->request_date,
                    'type' => __('supply_chain_trans.client'),
                    'key' => 'client',
                    'name' => $r->client->full_name ?? '-',
                    'store' => $r->store->name_ar ?? '-',
                    'status' => $r->status,
                ]);
            $requests = $requests->merge($client);
        }

        // Employees
        if ($this->filter_type === '' || $this->filter_type === 'employee') {
            $employee = EmployeeItemRequest::with(['store', 'employee'])
                ->whereIn('status', $filterByStatus)
                ->whereBetween('request_date', [$dateFrom, $dateTo])
                ->whereHas('employee', fn($q) => $q->where('full_name', 'like', "%{$this->search}%"))
                ->get()
                ->map(fn($r) => [
                    'id' => $r->id,
                    'number' => $r->request_number,
                    'date' => $r->request_date,
                    'type' => __('supply_chain_trans.employee'),
                    'key' => 'employee',
                    'name' => $r->employee->full_name ?? '-',
                    'store' => $r->store->name_ar ?? '-',
                    'status' => $r->status,
                ]);
            $requests = $requests->merge($employee);
        }

        // Departments
        if ($this->filter_type === '' || $this->filter_type === 'department') {
            $department = departmentItemRequest::with(['store', 'department'])
                ->whereIn('status', $filterByStatus)
                ->whereBetween('request_date', [$dateFrom, $dateTo])
                ->whereHas('department', fn($q) => $q->where('name_ar', 'like', "%{$this->search}%"))
                ->get()
                ->map(fn($r) => [
                    'id' => $r->id,
                    'number' => $r->request_number,
                    'date' => $r->request_date,
                    'type' => __('supply_chain_trans.department'),
                    'key' => 'department',
                    'name' => $r->department->name_ar ?? '-',
                    'store' => $r->store->name_ar ?? '-',
                    'status' => $r->status,
                ]);
            $requests = $requests->merge($department);
        }

        // Store Transfers
        if ($this->filter_type === '' || $this->filter_type === 'store') {
            $store = storeItemRequest::with(['store', 'store_to'])
                ->whereIn('status', $filterByStatus)
                ->whereBetween('request_date', [$dateFrom, $dateTo])
                ->get()
                ->map(fn($r) => [
                    'id' => $r->id,
                    'number' => $r->request_number,
                    'date' => $r->request_date,
                    'type' => __('supply_chain_trans.replenishment'),
                    'key' => 'store',
                    'name' => optional($r->store_to)->name_ar ?? '-',
                    'store' => optional($r->store)->name_ar ?? '-',
                    'status' => $r->status,
                ]);
            $requests = $requests->merge($store);
        }

        // Sort & Paginate
        $requests = $requests->sortByDesc('date')->values();

        $openCount = $requests->filter(fn($r) => in_array($r['status'], ['new', 'partial']))->count();

        $hasActiveFilters = $this->filter_type || $this->search ||
            $this->from_date !== now()->subMonth()->format('Y-m-d') ||
            $this->to_date !== now()->format('Y-m-d');

        $currentPage = $this->page;
        $items = $requests->forPage($currentPage, $this->perPage)->values();

        $paginated = new LengthAwarePaginator(
            $items,
            $requests->count(),
            $this->perPage,
            $currentPage,
            ['path' => request()->url(), 'query' => request()->query()]
        );

        return view('livewire.supply-chain.program.unified-requests-view', [
            'requests' => $paginated,
            'total' => $requests->count(),
            'openCount' => $openCount,
            'hasActiveFilters' => $hasActiveFilters,
        ]);
    }

    public function processRequest($id, $type)
    {
        return match ($type) {
            __('supply_chain_trans.client') => redirect()->route('client_request.process', $id),
            __('supply_chain_trans.employee') => redirect()->route('employee_request.process', $id),
            __('supply_chain_trans.department') => redirect()->route('department_request.process', $id),
            __('supply_chain_trans.replenishment') => redirect()->route('store_request.process', $id),
            default => null,
        };
    }

    public function closeRequest($id, $type)
    {
        match ($type) {
            __('supply_chain_trans.client') => ClientItemRequest::where('id', $id)->update(['status' => 'closed']),
            __('supply_chain_trans.employee') => EmployeeItemRequest::where('id', $id)->update(['status' => 'closed']),
            __('supply_chain_trans.department') => departmentItemRequest::where('id', $id)->update(['status' => 'closed']),
            __('supply_chain_trans.replenishment') => storeItemRequest::where('id', $id)->update(['status' => 'closed']),
            default => null,
        };

        session()->flash('success', __('supply_chain_trans.request_closed'));
    }

    public function printRequest($id, $type)
    {
        return match ($type) {
            __('supply_chain_trans.client') => redirect()->route('client_request.print', $id),
            __('supply_chain_trans.employee') => redirect()->route('employee_request.print', $id),
            __('supply_chain_trans.department') => redirect()->route('department_request.print', $id),
            __('supply_chain_trans.replenishment') => redirect()->route('store_request.print', $id),
            default => null,
        };
    }

    public function updatedTab($value)
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['filter_type', 'search', 'from_date', 'to_date']);
        $this->from_date = now()->subMonth()->format('Y-m-d');
        $this->to_date = now()->format('Y-m-d');
    }

    public function viewDetails($id, $type)
    {
        // يمكن تنفيذ استعراض التفاصيل هنا إذا لزم
    }
}