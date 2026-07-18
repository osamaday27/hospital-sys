<div class="container-fluid py-3" dir="rtl">

    {{-- تنبيهات --}}
    @if (session()->has('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session()->has('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    {{-- الفورم --}}
    <div class="card mb-3">
        <div class="card-body row g-3">

            <div class="col-md-3">
                <label class="form-label">نوع الكيان</label>
                <select class="form-select" wire:model="type">
                    <option value="department">إدارة</option>
                    <option value="section">قسم</option>
                    <option value="clinic">عيادة</option>
                    <option value="room">غرفة</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label">بحث</label>
                <input type="text" class="form-control" wire:model.debounce.500ms="search" placeholder="ابحث هنا...">
            </div>

            <div class="col-md-6">
                <label class="form-label">الكيان</label>
                <select class="form-select" wire:model="entity_id">
                    <option value="">اختر الكيان</option>
                    @foreach ($entities as $e)
                        <option value="{{ $e->id }}">#{{ $e->id }} — {{ e((string)($e->name_ar ?? $e->name_en ?? $e->name ?? $e->clinic_code ?? '---')) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">مركز التكلفة</label>
                <select class="form-select" wire:model="cost_center_id">
                    <option value="">اختر</option>
                    @foreach ($costCenters as $cc)
                        <option value="{{ $cc->id }}">{{ e((string) $cc->code) }} — {{ e((string)($cc->name ?? '')) }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label">الحالة</label>
                <select class="form-select" wire:model="status">
                    <option value="active">نشط</option>
                    <option value="inactive">موقوف</option>
                </select>
            </div>

            <div class="col-md-2">
                <label class="form-label">من</label>
                <input type="date" class="form-control" wire:model="effective_from">
            </div>
            <div class="col-md-2">
                <label class="form-label">إلى</label>
                <input type="date" class="form-control" wire:model="effective_to">
            </div>

            <div class="col-md-12">
                <label class="form-label">ملاحظات</label>
                <textarea class="form-control" wire:model="notes"></textarea>
            </div>

            <div class="col-12 text-end">
                <button class="btn btn-success" wire:click="createLink" @if(!$entity_id || !$cost_center_id) disabled @endif>إضافة</button>
            </div>
        </div>
    </div>

    {{-- فلاتر الجدول --}}
    <div class="d-flex gap-2 mb-2">
        <select wire:model="statusFilter" class="form-select w-auto">
            <option value="">كل الحالات</option>
            <option value="active">نشط</option>
            <option value="inactive">موقوف</option>
        </select>
        <input type="date" wire:model="dateFrom" class="form-control w-auto">
        <input type="date" wire:model="dateTo" class="form-control w-auto">
    </div>

    {{-- الجدول --}}
    <div class="card">
        <div class="table-responsive">
            <table class="table table-bordered align-middle">
                <thead>
                <tr>
                    <th wire:click="sortBy('id')">#</th>
                    <th wire:click="sortBy('cost_center_id')">مركز التكلفة</th>
                    <th wire:click="sortBy('assignable_id')">الكيان</th>
                    <th wire:click="sortBy('status')">الحالة</th>
                    <th>الفترة</th>
                    <th>ملاحظات</th>
                    <th>إجراءات</th>
                </tr>
                </thead>
                <tbody>
                @forelse($rows as $row)
                    <tr wire:loading.class="opacity-50">
                        <td>{{ $row->id }}</td>
                        <td>{{ e((string)$row->costcenter->code) }} - {{ e((string)$row->costcenter->name) }}</td>
                        <td>{{ class_basename($row->assignable_type) }} #{{ $row->assignable_id }}</td>
                        <td>
                            @if($row->status === 'active')
                                <span class="badge bg-success">نشط</span>
                            @else
                                <span class="badge bg-secondary">موقوف</span>
                            @endif
                        </td>
                        <td>{{ $row->effective_from }} → {{ $row->effective_to }}</td>
                        <td>{{ e((string)$row->notes) }}</td>
                        <td>
                            <button wire:click="toggleStatus({{ $row->id }})" class="btn btn-sm btn-outline-primary">تبديل</button>
                            <button wire:click="deleteLink({{ $row->id }})" class="btn btn-sm btn-outline-danger">حذف</button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center">لا توجد بيانات</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div class="card-footer">
            {{ $rows->links() }}
        </div>
    </div>

    <div wire:loading>
        <div class="spinner-border text-primary" role="status"></div>
        جاري التحميل...
    </div>
</div>
