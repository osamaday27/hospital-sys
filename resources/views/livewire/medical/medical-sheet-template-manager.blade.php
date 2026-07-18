<div dir="rtl">

    {{-- ✅ التنبيهات --}}
    @if (session()->has('success'))
        <div class="alert alert-success rounded-4 shadow-sm p-3 mb-3 text-center">
            {{ session('success') }}
        </div>
    @endif

    {{-- 🧾 رأس الصفحة --}}
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h4 class="fw-bold text-primary"><i class="ri-file-list-3-line"></i> إدارة الشيتات الطبية</h4>
        <input type="text" wire:model="search" placeholder="🔍 بحث..." class="form-control w-25 rounded-3">
    </div>

    {{-- 🧱 النموذج --}}
    <div class="card shadow-lg rounded-4 mb-4">
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label>اسم الشيت</label>
                    <input type="text" wire:model="template_name" class="form-control rounded-3">
                </div>

                <div class="col-md-4">
                    <label>التخصص</label>
                    <select wire:model="speciality_id" class="form-select rounded-3">
                        <option value="">اختر التخصص</option>
                        @foreach ($specialities as $sp)
                            <option value="{{ $sp->id }}">{{ $sp->name_ar }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label>الطبيب</label>
                    <select wire:model="doctor_id" class="form-select rounded-3">
                        <option value="">عام</option>
                        @foreach ($doctors as $doc)
                            <option value="{{ $doc->id }}">{{ $doc->full_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label>الموديول</label>
                    <input type="text" wire:model="model_name" class="form-control rounded-3"
                        placeholder="Surgery, Visit...">
                </div>

                <div class="col-md-2">
                    <label>الترتيب</label>
                    <input type="number" wire:model="display_order" class="form-control rounded-3">
                </div>

                <div class="col-md-2">
                    <label>الرؤية</label>
                    <select wire:model="visibility" class="form-select rounded-3">
                        <option value="global">عام</option>
                        <option value="private">خاص</option>
                    </select>
                </div>

                <div class="col-md-2">
                    <label>الحالة</label>
                    <select wire:model="status" class="form-select rounded-3">
                        <option value="active">نشط</option>
                        <option value="inactive">معطل</option>
                    </select>
                </div>

                <div class="col-md-3">
                    <label>الفئة</label>
                    <input type="text" wire:model="category" class="form-control rounded-3"
                        placeholder="Cataract, Retina...">
                </div>

                <div class="col-md-2">
                    <label>الإصدار</label>
                    <input type="text" wire:model="version" class="form-control rounded-3" placeholder="1.0">
                </div>

                <div class="col-md-12">
                    <label>الوصف</label>
                    <textarea wire:model="description" class="form-control rounded-3" rows="2" placeholder="وصف الشيت ووظيفته"></textarea>
                </div>
            </div>

            <div class="mt-3">
                <button class="btn btn-success px-4" wire:click="save"><i class="ri-save-3-line"></i> حفظ</button>
                <button class="btn btn-secondary px-3" wire:click="resetInput">إلغاء</button>
            </div>
        </div>
    </div>

    {{-- 📋 جدول القوالب --}}
    <div class="card shadow-lg rounded-4">
        <div class="card-body p-0">
            <table class="table table-hover mb-0 align-middle">
                <thead class="bg-light">
                    <tr>
                        <th>#</th>
                        <th>الاسم</th>
                        <th>التخصص</th>
                        <th>الطبيب</th>
                        <th>الموديول</th>
                        <th>الترتيب</th>
                        <th>الرؤية</th>
                        <th>الحالة</th>
                        <th>عمليات</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($templates as $t)
                        <tr>
                            <td>{{ $t->id }}</td>
                            <td>{{ $t->template_name }}</td>
                            <td>{{ $t->speciality->name ?? '-' }}</td>
                            <td>{{ $t->doctor->name ?? 'عام' }}</td>
                            <td>{{ $t->model_name ?? '-' }}</td>
                            <td>{{ $t->display_order }}</td>
                            <td>
                                <span class="badge bg-{{ $t->visibility == 'global' ? 'info' : 'warning' }}">
                                    {{ $t->visibility == 'global' ? 'عام' : 'خاص' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-{{ $t->status == 'active' ? 'success' : 'secondary' }}">
                                    {{ $t->status == 'active' ? 'نشط' : 'معطل' }}
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-primary"
                                    wire:click="edit({{ $t->id }})">تعديل</button>
                                <button class="btn btn-sm btn-danger"
                                    wire:click="delete({{ $t->id }})">حذف</button>
                                <button class="btn btn-sm btn-outline-info"
                                    onclick="window.location='{{ route('medical.templates.builder', $t->id) }}'">
                                    تصميم الشيت
                                </button>

                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-3">لا توجد شيتات حالياً</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $templates->links() }}
    </div>
</div>
