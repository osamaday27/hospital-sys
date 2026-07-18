<?php

namespace App\Http\Livewire\Finance\Settings;

use Livewire\Component;
use App\Models\finance\GLAccount;
use Livewire\WithPagination;

class GlAccountsSettings extends Component
{
    use WithPagination;

    public $parents;
    public $account_id;
    public $code, $name_ar, $name_en, $description;
    public $type, $category, $is_group = false;
    public $opening_balance = 0, $current_balance = 0, $balance_type = 'debit';
    public $status = true;
    public $search = '';
    public $expandedAccounts = [];

    public $parent_name = '';
    public $parent_id = null;
    public $filteredParents = [];

    public function mount()
    {
        $this->loadParents();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedParentName($value)
    {
        $this->filteredParents = $this->parents
            ->filter(function ($item) use ($value) {
                return stripos($item->fullName(), $value) !== false;
            })
            ->take(10)
            ->values();
    }

    public function selectParent($id, $name)
    {
        $this->parent_id = $id;
        $this->parent_name = $name;
        $this->filteredParents = [];
    }

    public function toggleAccount($accountId)
    {
        if (in_array($accountId, $this->expandedAccounts)) {
            $this->expandedAccounts = array_diff($this->expandedAccounts, [$accountId]);
        } else {
            $this->expandedAccounts[] = $accountId;
        }
    }
    public function showAllParents()
    {
        $this->filteredParents = $this->parents->take(10)->values();
    }
    
    public function hideSuggestions()
    {
        // لتأخير الإخفاء بعد الضغط على العنصر
        usleep(200000); // 200ms
        $this->filteredParents = [];
    }
    
    public function getTypeColor($type)
    {
        return match ($type) {
            'asset' => 'success',
            'liability' => 'danger',
            'equity' => 'info',
            'income' => 'primary',
            'expense' => 'warning',
            default => 'secondary',
        };
    }

    public function loadParents()
    {
        $this->parents = GLAccount::where('is_group', true)->get();
    }

    public function save()
    {
        // التحقق من صحة الحساب الأب المختار
        if ($this->parent_name && !$this->parent_id) {
            $matchedParent = $this->parents->firstWhere('fullName', $this->parent_name);
            if ($matchedParent) {
                $this->parent_id = $matchedParent->id;
            } else {
                $this->addError('parent_name', 'الحساب الرئيسي غير موجود بالقائمة.');
                return;
            }
        }

        $this->validate([
            'code' => 'required|unique:fin_s_gl_accounts,code,' . $this->account_id,
            'name_ar' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'type' => 'required|in:asset,liability,equity,income,expense',
            'balance_type' => 'required|in:debit,credit',
            'category' => 'nullable|in:cash,bank,customer,supplier,tax,other',
            'parent_id' => 'nullable|exists:fin_s_gl_accounts,id',
            'opening_balance' => 'numeric|min:0',
        ]);

        $account = GLAccount::updateOrCreate(
            ['id' => $this->account_id],
            [
                'code' => $this->code,
                'name_ar' => $this->name_ar,
                'name_en' => $this->name_en,
                'description' => $this->description,
                'type' => $this->type,
                'category' => $this->category,
                'parent_id' => $this->parent_id,
                'is_group' => $this->is_group,
                'opening_balance' => $this->opening_balance,
                'current_balance' => $this->opening_balance,
                'balance_type' => $this->balance_type,
                'status' => $this->status,
                'created_by' => auth()->id(),
            ],
        );

        session()->flash('success', __('تم حفظ الحساب بنجاح'));
        $this->resetFields();
        $this->loadParents();
    }

    public function edit($id)
    {
        $acc = GLAccount::findOrFail($id);
        $this->account_id = $acc->id;
        $this->code = $acc->code;
        $this->name_ar = $acc->name_ar;
        $this->name_en = $acc->name_en;
        $this->description = $acc->description;
        $this->type = $acc->type;
        $this->category = $acc->category;
        $this->parent_id = $acc->parent_id;
        $this->parent_name = optional($acc->parent)->fullName();
        $this->is_group = $acc->is_group;
        $this->opening_balance = $acc->opening_balance;
        $this->current_balance = $acc->current_balance;
        $this->balance_type = $acc->balance_type;
        $this->status = $acc->status;
    }

    public function resetFields()
    {
        $this->reset([
            'account_id', 'code', 'name_ar', 'name_en', 'description',
            'type', 'category', 'parent_id', 'is_group', 'opening_balance',
            'current_balance', 'balance_type', 'status', 'parent_name'
        ]);
    }

    public function viewDetails($id)
    {
        $acc = GLAccount::findOrFail($id);
        $this->account_id = $acc->id;
        $this->code = $acc->code;
        $this->name_ar = $acc->name_ar;
        $this->name_en = $acc->name_en;
        $this->description = $acc->description;
        $this->type = $acc->type;
        $this->category = $acc->category;
        $this->balance_type = $acc->balance_type;
        $this->current_balance = $acc->current_balance;
    }

    public function confirmDelete($id)
    {
        $account = GLAccount::with('children')->findOrFail($id);
        if ($account->children->count() > 0) {
            session()->flash('error', 'لا يمكن حذف حساب يحتوي على حسابات فرعية.');
            return;
        }

        $account->delete();
        session()->flash('success', 'تم حذف الحساب بنجاح.');
    }

public function render()
{
    $normalizedSearch = $this->normalizeSearch($this->search);
    $searchTerm = '%' . str_replace(' ', '%', $normalizedSearch) . '%';

    $accounts = \App\Models\finance\GLAccount::with([
            'children' => function ($q) {
                $q->withSum('debitEntries as debit_total', 'debit')
                  ->withSum('creditEntries as credit_total', 'credit')
                  ->withCount(['debitEntries as debit_entries_count','creditEntries as credit_entries_count'])
                  ->with(['children' => function ($q2) {
                      $q2->withSum('debitEntries as debit_total', 'debit')
                         ->withSum('creditEntries as credit_total', 'credit')
                         ->withCount(['debitEntries as debit_entries_count','creditEntries as credit_entries_count']);
                  }]);
            },
            'debitEntries', 'creditEntries',
        ])
        ->withSum('debitEntries as debit_total', 'debit')
        ->withSum('creditEntries as credit_total', 'credit')
        ->withCount(['debitEntries as debit_entries_count','creditEntries as credit_entries_count'])
        ->whereNull('parent_id')
        ->where(function ($q) use ($searchTerm) {
            $q->whereRaw("LOWER(REPLACE(name_ar, 'أ', 'ا')) LIKE ?", [$searchTerm])
              ->orWhereRaw("LOWER(REPLACE(name_en, 'أ', 'a')) LIKE ?", [$searchTerm])
              ->orWhereRaw("LOWER(code) LIKE ?", [$searchTerm]);
        })
        ->orderByRaw("CASE 
            WHEN code LIKE ? THEN 1 
            WHEN name_ar LIKE ? THEN 2 
            WHEN name_en LIKE ? THEN 3 
            ELSE 4 
        END", [$searchTerm, $searchTerm, $searchTerm])
        ->paginate(10);

    return view('livewire.finance.settings.gl-accounts-settings', [
        'accounts' => $accounts,
        'parents' => $this->parents,
        'expandedAccounts' => $this->expandedAccounts,
    ]);
}

    
    /**
     * Normalize Arabic search: remove diacritics, normalize letters, etc.
     */
    protected function normalizeSearch($string)
    {
        $string = trim($string);
        $string = preg_replace('/\s+/', ' ', $string); // حذف المسافات الزائدة
        $string = str_replace(['أ', 'إ', 'آ', 'ى', 'ئ', 'ؤ'], ['ا', 'ا', 'ا', 'ي', 'ي', 'و'], $string); // توحيد الحروف
        $string = mb_strtolower($string); // تحويل إلى lowercase
        return $string;
    }
    
    
}