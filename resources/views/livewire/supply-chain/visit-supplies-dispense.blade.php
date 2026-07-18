<div class="card border-0 shadow-sm">
    <div class="card-header bg-gradient-primary text-white d-flex justify-content-between align-items-center p-3">
        <h5 class="mb-0 fw-bold">
            <i class="ri-archive-line me-2"></i> صرف مستلزمات الزيارة
        </h5>
        <a href="" class="btn btn-info btn-sm fw-bold">
            <i class="ri-arrow-go-back-line me-1"></i> العودة
        </a>
    </div>
    <div class="card-body">
        @if(session()->has('success'))
            <div class="alert alert-success">
                <i class="ri-checkbox-circle-line me-1"></i> {{ session('success') }}
            </div>
        @endif

        {{-- تفاصيل الزيارة --}}
        <div class="mb-4">
            <h6 class="mb-3 fw-bold text-primary">
                <i class="ri-information-line me-1"></i> تفاصيل الزيارة
            </h6>
            <div class="row gy-2">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">رقم الحجز:</label>
                    <div class="form-control bg-light">{{ $reservationId }}</div>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">المريض:</label>
                    <div class="form-control bg-light">{{ optional(App\Models\administrative\medical_record::find($patientId))->full_name }}</div>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">رقم الزيارة:</label>
                    <div class="form-control bg-light">{{ $visitId }}</div>
                </div>
            </div>
        </div>

        {{-- إدخال الصرف --}}
        <form wire:submit.prevent="addSupply" class="row g-3 mb-4">
            <div class="col-md-3">
                <label class="form-label">المخزن</label>
                <select wire:model="store_id" class="form-select form-select-sm @error('store_id') is-invalid @enderror">
                    <option value="">اختر المخزن</option>
                    @foreach($stores as $st)
                        <option value="{{ $st->id }}">{{ $st->name_ar }} ({{ $st->manual_code }})</option>
                    @endforeach
                </select>
                @error('store_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-3">
                <label class="form-label">المستحضر</label>
                <select wire:model="product_id" class="form-select form-select-sm @error('product_id') is-invalid @enderror">
                    <option value="">اختر المستحضر</option>
                    @foreach($products as $prod)
                        <option value="{{ $prod->id }}">{{ $prod->name_ar }}</option>
                    @endforeach
                </select>
                @error('product_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-2">
                <label class="form-label">الوحدة</label>
                <select wire:model="unit_id" class="form-select form-select-sm @error('unit_id') is-invalid @enderror">
                    <option value="">اختر الوحدة</option>
                    @foreach($units as $u)
                        <option value="{{ $u['id'] }}">{{ app()->getLocale()=='ar' ? $u['name_ar'] : $u['name_en'] }}</option>
                    @endforeach
                </select>
                @error('unit_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-1">
                <label class="form-label">الرصيد</label>
                <div class="form-control-plaintext">{{ number_format($balance,2) }}</div>
            </div>
            <div class="col-md-1">
                <label class="form-label">الكمية</label>
                <input type="number" step="0.01" wire:model="quantity" class="form-control form-control-sm @error('quantity') is-invalid @enderror">
                @error('quantity')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-1">
                <label class="form-label">السعر</label>
                <input type="number" step="0.01" wire:model="price" class="form-control form-control-sm @error('price') is-invalid @enderror">
                @error('price')<div class="invalid-feedback">{{ $message }}</div>@enderror
            </div>
            <div class="col-md-1 d-grid">
                <button type="submit" class="btn btn-success btn-sm">صرف</button>
            </div>
        </form>

        {{-- جدول الصرفيات --}}
        <div class="table-responsive">
            <table class="table table-bordered table-striped align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th><th>مخزن</th><th>مستحضر</th><th>وحدة</th><th>كمية</th><th>سعر</th><th>إجمالي</th><th>تاريخ</th><th>حذف</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($supplies as $item)
                        <tr>
                            <td>{{ $item->id }}</td>
                            <td>{{ optional($item->store)->name_ar }}</td>
                            <td>{{ optional($item->product)->name_ar }}</td>
                            <td>{{ optional($item->unit)->name_ar }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>{{ number_format($item->price,2) }}</td>
                            <td>{{ number_format($item->quantity*$item->price,2) }}</td>
                            <td>{{ $item->created_at->format('Y-m-d H:i') }}</td>
                            <td class="text-center">
                                <button wire:click="removeSupply({{ $item->id }})" class="btn btn-outline-danger btn-sm" onclick="confirm('هل أنت متأكد؟')||event.stopImmediatePropagation()">حذف</button>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="9" class="text-center">لا توجد بيانات</td></tr>
                    @endforelse
                </tbody>
            </table>
            <div class="mt-3">{{ $supplies->links() }}</div>
        </div>
    </div>
</div>

<style>
.card { border-radius: .75rem; }
.table th { font-weight:600; font-size:.85rem; color:#6c757d; }
.form-label { font-size:.9rem; }
</style>