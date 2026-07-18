<?php

namespace App\Http\Livewire\Surgical;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\administrative\surgicaldatasetting;
use App\Models\finance\service;
use App\Models\finance\FinAsset;
use Illuminate\Support\Facades\DB;

class CopySurgicalTools extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $source_id;           // العملية المصدر
    public $target_ids = [];     // العمليات الهدف
    public $tools_preview = [];  // عرض الأدوات من الأصل
    public $surgerySearch = '';  // البحث عن العمليات الهدف
    public $sourceList = [];     // العمليات اللي فيها أدوات فعليًا

    public function mount()
    {
        // ✅ عرض فقط العمليات اللي فيها أدوات مسجلة فعلاً
        $this->sourceList = surgicaldatasetting::whereNotNull('surgical_tools')
            ->where('surgical_tools', '!=', '')
            ->where('surgical_tools', '!=', '[]')
            ->select('id', 'name_ar', 'name_en', 'surgical_tools', 'service_id')
            ->get();
    }

    // 🔹 تحويل القيمة إلى Array حتى لو كانت String أو JSON
    private function parseTools($value)
    {
        if (is_array($value)) {
            return $value;
        }

        $decoded = json_decode($value, true);
        if (is_array($decoded)) {
            return $decoded;
        }

        if (is_string($value) && str_contains($value, ',')) {
            $split = array_filter(explode(',', $value));
            return array_values($split);
        }

        return [];
    }

    public function updatedSourceId()
    {
        $this->tools_preview = [];

        if ($this->source_id) {
            $record = surgicaldatasetting::find($this->source_id);

            if ($record && $record->surgical_tools) {
                // ✅ استخراج IDs وتحويلها إلى Array
                $toolIds = $this->parseTools($record->surgical_tools);

                // ✅ جلب الأدوات من الأصول
                $this->tools_preview = FinAsset::whereIn('id', $toolIds)
                    ->select('id', 'name_ar', 'name_en')
                    ->get()
                    ->toArray();
            }
        }
    }

    public function copyTools()
    {
        $this->validate([
            'source_id' => 'required|exists:adm_s_surgicaldatasettings,id',
            'target_ids' => 'required|array|min:1',
        ]);

        $source = surgicaldatasetting::findOrFail($this->source_id);
        $toolIds = $this->parseTools($source->surgical_tools);

        if (empty($toolIds)) {
            session()->flash('error', '⚠️ لا توجد أدوات مسجلة في العملية المصدر.');
            return;
        }

        DB::beginTransaction();
        try {
            foreach ($this->target_ids as $target_service_id) {

                // ✅ جلب بيانات العملية من جدول الخدمات
                $service = service::find($target_service_id);
                if (!$service) continue;

                // ✅ التحقق أو إنشاء سجل الإعدادات بناءً على اسم العملية
                $target = surgicaldatasetting::firstOrCreate(
                    ['service_id' => $target_service_id],
                    [
                        'name_ar' => $service->getTranslation('name', 'ar'),
                        'name_en' => $service->getTranslation('name', 'en'),
                        'surgical_tools' => '',
                    ]
                );

                // ✅ نسخ الأدوات من المصدر
                $target->update([
                    'surgical_tools' => implode(',', $toolIds),
                ]);
            }

            DB::commit();
            session()->flash('success', '✅ تم إنشاء أو تحديث الإعدادات ونسخ الأدوات بنجاح إلى العمليات المحددة.');
            $this->reset(['source_id', 'target_ids', 'tools_preview']);
            $this->mount();
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'حدث خطأ أثناء النسخ: ' . $e->getMessage());
        }
    }

    public function render()
    {
        // ✅ جلب العمليات الهدف من الخدمات (level_type = operations)
        $services = service::whereHas('sublevel.level1', function ($query) {
            $query->where('level_type', 'operations');
        })
            ->when($this->surgerySearch, function ($q) {
                $q->where('name->ar', 'like', "%{$this->surgerySearch}%")
                    ->orWhere('name->en', 'like', "%{$this->surgerySearch}%");
            })
            ->select('id', 'name')
            ->paginate(10);

        return view('livewire.surgical.copy-surgical-tools', [
            'services' => $services,
        ]);
    }
}