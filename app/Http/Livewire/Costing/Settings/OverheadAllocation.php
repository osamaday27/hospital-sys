<?php

namespace App\Http\Livewire\costing\settings;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use App\Models\costing\overheadpool;
use App\Models\costing\overheadallocationrule;
use App\Models\costing\overheadallocationline;
use App\Models\costing\costcenter;
use Carbon\Carbon;
use DB;

class OverheadAllocation extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    // اختيار المجمّع وطريقة التوزيع
    public $pool_id = '';
    public $method = 'percent'; // percent | driver
    public $driver_code = '';   // لو method = driver (مثلاً: area/staff/machine_hours)
    public $notes = '';

    // ملخّصات
    public $pool_amount = 0;
    public $pool_period = '';
    public $status = 'draft';

    // سطور التوزيع (array of arrays)
    public $lines = []; // [['cost_center_id'=>1,'name'=>'..','code'=>'..','percent'=>null,'driver_value'=>null,'allocated'=>0], ...]

    // فلترة القواعد السابقة
    public $perPage = 10;
    public $search = '';

    // مودال
    public $editing_rule_id = null;

    protected $queryString = [
        'pool_id' => ['except' => ''],
        'method'  => ['except' => 'percent'],
        'driver_code' => ['except' => ''],
        'perPage' => ['except' => 10],
        'search' => ['except' => ''],
    ];

    public function mount(): void
    {
        $this->method = 'percent';
        $this->status = 'draft';
    }

    public function rules()
    {
        return [
            'pool_id' => ['required','exists:overhead_pools,id'],
            'method'  => ['required', Rule::in(overheadallocationrule::methods())],
            'driver_code' => ['nullable','string','max:50'],
            'notes'   => ['nullable','string'],
        ];
    }

    public function updatedPoolId()
    {
        $this->loadPool();
        $this->prepareLines();
        $this->recalculate();
    }

    public function updatedMethod()
    {
        // عند تغيير الطريقة نعيد الحساب/تهيئة الحقول
        foreach ($this->lines as &$ln) {
            if ($this->method === 'percent') {
                $ln['driver_value'] = null;
            } else {
                $ln['percent'] = null;
            }
        }
        $this->recalculate();
    }

    public function loadPool()
    {
        $pool = overheadpool::find($this->pool_id);
        if (!$pool) {
            $this->pool_amount = 0;
            $this->pool_period = '';
            return;
        }
        $this->pool_amount = (float) $pool->amount;
        $this->pool_period = sprintf('%s — %s',
            optional($pool->period_start)->format('Y-m-d'),
            optional($pool->period_end)->format('Y-m-d')
        );
    }

    public function prepareLines()
    {
        $this->lines = [];
        $centers = costcenter::orderBy('code')->get();
        foreach ($centers as $c) {
            $this->lines[] = [
                'cost_center_id' => $c->id,
                'code'  => $c->code,
                'name'  => $c->getTranslation('name', app()->getLocale()),
                'percent' => $this->method === 'percent' ? 0 : null,
                'driver_value' => $this->method === 'driver' ? 0 : null,
                'allocated' => 0,
            ];
        }
    }

    public function normalizePercent()
    {
        // يجعل مجموع النسب = 100
        $sum = 0;
        foreach ($this->lines as $ln) {
            $sum += (float) ($ln['percent'] ?? 0);
        }
        if ($sum == 0) return;
        foreach ($this->lines as &$ln) {
            $p = (float) ($ln['percent'] ?? 0);
            $ln['percent'] = $p / $sum * 100;
        }
        $this->recalculate();
    }

    public function recalculate()
    {
        $amount = (float) $this->pool_amount;
        if ($amount <= 0) {
            foreach ($this->lines as &$ln) $ln['allocated'] = 0;
            return;
        }

        if ($this->method === 'percent') {
            $sumPercent = 0;
            foreach ($this->lines as $ln) $sumPercent += (float) ($ln['percent'] ?? 0);
            foreach ($this->lines as &$ln) {
                $p = max(0, (float) ($ln['percent'] ?? 0));
                $ratio = $sumPercent > 0 ? ($p / $sumPercent) : 0;
                $ln['allocated'] = round($amount * $ratio, 2);
            }
        } else { // driver
            $sumDriver = 0;
            foreach ($this->lines as $ln) $sumDriver += (float) ($ln['driver_value'] ?? 0);
            foreach ($this->lines as &$ln) {
                $d = max(0, (float) ($ln['driver_value'] ?? 0));
                $ratio = $sumDriver > 0 ? ($d / $sumDriver) : 0;
                $ln['allocated'] = round($amount * $ratio, 2);
            }
        }
    }

    public function saveRule()
    {
        $this->validate();
        if (!$this->pool_id) return;

        // التحقق من أن مجموع النسب = 100 في حالة percent
        if ($this->method === 'percent') {
            $sum = 0;
            foreach ($this->lines as $ln) $sum += (float) ($ln['percent'] ?? 0);
            if (abs($sum - 100) > 0.01) {
                session()->flash('error', __('allocation_trans.percent_must_be_100'));
                return;
            }
        } else {
            // driver: لازم يكون فيه مجموع > 0
            $sumDriver = 0;
            foreach ($this->lines as $ln) $sumDriver += (float) ($ln['driver_value'] ?? 0);
            if ($sumDriver <= 0) {
                session()->flash('error', __('allocation_trans.driver_sum_must_be_positive'));
                return;
            }
        }

        DB::transaction(function () {
            $pool = overheadpool::findOrFail($this->pool_id);

            $rule = new overheadallocationrule([
                'overhead_pool_id' => $pool->id,
                'method' => $this->method,
                'driver_code' => $this->method === 'driver' ? ($this->driver_code ?: 'custom') : null,
                'total_amount' => $pool->amount,
                'total_percent' => $this->method === 'percent'
                    ? array_sum(array_map(fn($ln) => (float) ($ln['percent'] ?? 0), $this->lines))
                    : 0,
                'total_driver' => $this->method === 'driver'
                    ? array_sum(array_map(fn($ln) => (float) ($ln['driver_value'] ?? 0), $this->lines))
                    : 0,
                'status' => 'approved', // نحفظها معتمدة (تقدر تغيّرها لمسودة لو تحب)
                'notes'  => $this->notes,
                'user_id'=> Auth::id(),
            ]);
            $rule->save();

            foreach ($this->lines as $ln) {
                overheadallocationline::create([
                    'rule_id'        => $rule->id,
                    'cost_center_id' => $ln['cost_center_id'],
                    'percent'        => $this->method === 'percent' ? (float) ($ln['percent'] ?? 0) : null,
                    'driver_value'   => $this->method === 'driver' ? (float) ($ln['driver_value'] ?? 0) : null,
                    'allocated_amount'=> (float) ($ln['allocated'] ?? 0),
                ]);
            }

            $this->editing_rule_id = $rule->id;
        });

        session()->flash('success', __('allocation_trans.saved_success'));
        $this->dispatchBrowserEvent('allocation-saved');
    }

    public function hydrateLine($index, $field, $value)
    {
        // helper للاستخدام من الـ Blade إن حبيت
        $this->lines[$index][$field] = $value;
        $this->recalculate();
    }

    // لعرض قواعد قديمة (تاريخيًا)
    public function getRulesProperty()
    {
        $q = overheadallocationrule::query()->with('pool');

        if ($this->search) {
            $term = '%'.$this->search.'%';
            $q->where(function($qq) use ($term) {
                $qq->where('driver_code','like',$term)
                   ->orWhere('notes','like',$term)
                   ->orWhereHas('pool', function($qp) use ($term) {
                        $qp->where('code','like',$term)
                           ->orWhere('name->ar','like',$term)
                           ->orWhere('name->en','like',$term);
                   });
            });
        }

        return $q->orderByDesc('id')->paginate($this->perPage);
    }

    public function render()
    {
        $pools = overheadpool::whereIn('status',['approved','posted'])
            ->orderByDesc('period_start')->orderBy('code')->get(['id','code','name','period_start','period_end','amount']);

        return view('livewire.costing.settings.overhead-allocation', [
            'pools' => $pools,
            'rules' => $this->rules,
        ])->layout('layouts.masterliveware');
    }
}