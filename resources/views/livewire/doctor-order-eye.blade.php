<div class="card shadow border-0 rounded-4">
    <div class="card-body">

        {{-- إشعارات الجلسة --}}
        @if (session()->has('success'))
            <div class="alert alert-success text-center fw-bold">{{ session('success') }}</div>
        @endif
        @if (session()->has('error'))
            <div class="alert alert-danger text-center fw-bold">{{ session('error') }}</div>
        @endif

        <input type="hidden" wire:model="patient_id">
        <input type="hidden" wire:model="patient_visits_id">

        <div class="row g-4">

            {{-- ✅ فحوصات العين --}}
            <div class="col-md-4">
                <div class="card border-0 shadow-sm bg-white rounded-4 h-100">
                    <div class="card-header bg-primary text-white fw-bold rounded-top-4 d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-eye me-2"></i> فحوصات العين</span>
                        <button wire:click="toggleSelectAll('eye')" class="btn btn-sm btn-light text-primary fw-bold" style="color: #1373c3 !important;">تحديد الكل</button>
                    </div>
                    <div class="card-body p-2">
                        <input type="text" wire:model="searchEye" class="form-control form-control-sm mb-2" placeholder="ابحث عن فحص...">
                        <div class="table-responsive" style="max-height: 300px; overflow-y:auto;">
                            <table class="table table-sm table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>اسم الفحص</th>
                                        <th>العين</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($filteredEyeTests as $index => $exam)
                                        <tr>
                                            <td><input type="checkbox" wire:model="selectedexTests.{{ $index }}" value="{{ $exam->id }}"></td>
                                            <td>{{ $exam->name }}</td>
                                            <td>
                                                <select wire:model="selectedEyeexTests.{{ $index }}" class="form-select form-select-sm">
                                                    <option value="right">اليمنى</option>
                                                    <option value="left">اليسرى</option>
                                                    <option value="both">كليهما</option>
                                                </select>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-center mt-2">
                            {{ $filteredEyeTests->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- ✅ اختبارات المعمل --}}
            <div class="col-md-4">
                <div class="card border-0 shadow-sm bg-white rounded-4 h-100">
                    <div class="card-header bg-success text-white fw-bold rounded-top-4 d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-vials me-2"></i> اختبارات المعمل</span>
                        <button wire:click="toggleSelectAll('lab')" class="btn btn-sm btn-light text-success fw-bold">تحديد الكل</button>
                    </div>
                    <div class="card-body p-2">
                        <input type="text" wire:model="searchLab" class="form-control form-control-sm mb-2" placeholder="ابحث عن اختبار...">
                        <div class="table-responsive" style="max-height: 300px; overflow-y:auto;">
                            <table class="table table-sm table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>الاختبار</th>
                                        <th>العين</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($filteredLabTests as $index => $lab)
                                        <tr>
                                            <td><input type="checkbox" wire:model="selectedLab.{{ $index }}" value="{{ $lab->id }}"></td>
                                            <td>{{ $lab->name }}</td>
                                            <td>
                                                <select wire:model="selectedEyeLab.{{ $index }}" class="form-select form-select-sm">
                                                    <option value="right">اليمنى</option>
                                                    <option value="left">اليسرى</option>
                                                    <option value="both">كليهما</option>
                                                </select>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-center mt-2">
                            {{ $filteredLabTests->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- ✅ اختبارات الأشعة --}}
            <div class="col-md-4">
                <div class="card border-0 shadow-sm bg-white rounded-4 h-100">
                    <div class="card-header bg-warning text-dark fw-bold rounded-top-4 d-flex justify-content-between align-items-center">
                        <span><i class="fas fa-x-ray me-2"></i> اختبارات الأشعة</span>
                        <button wire:click="toggleSelectAll('rad')" class="btn btn-sm btn-light text-dark fw-bold">تحديد الكل</button>
                    </div>
                    <div class="card-body p-2">
                        <input type="text" wire:model="searchRad" class="form-control form-control-sm mb-2" placeholder="ابحث عن اختبار...">
                        <div class="table-responsive" style="max-height: 300px; overflow-y:auto;">
                            <table class="table table-sm table-bordered align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>#</th>
                                        <th>الاختبار</th>
                                        <th>العين</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($filteredRadTests as $index => $rad)
                                        <tr>
                                            <td><input type="checkbox" wire:model="selectedRad.{{ $index }}" value="{{ $rad->id }}"></td>
                                            <td>{{ $rad->name }}</td>
                                            <td>
                                                <select wire:model="selectedEyeRad.{{ $index }}" class="form-select form-select-sm">
                                                    <option value="right">اليمنى</option>
                                                    <option value="left">اليسرى</option>
                                                    <option value="both">كليهما</option>
                                                </select>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-center mt-2">
                            {{ $filteredRadTests->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>

        </div>

        {{-- زر الحفظ --}}
        <div class="text-end mt-4">
            <button wire:click="save" wire:loading.attr="disabled" class="btn btn-lg btn-success rounded-pill px-4">
                <span wire:loading.remove>💾 حفظ</span>
                <span wire:loading><i class="fas fa-spinner fa-spin me-1"></i> جارٍ الحفظ...</span>
            </button>
        </div>

        {{-- ✅ الطلبات السابقة --}}
        <div class="mt-5">
            <h5 class="fw-bold text-primary mb-3">🗂️ الطلبات السابقة</h5>
            <div class="table-responsive">
                <table class="table table-striped table-hover align-middle border">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>النوع</th>
                            <th>الاختبارات</th>
                            <th>العين</th>
                            <th>التاريخ</th>
                            <th>الحالة</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($previousOrders as $order)
                            @php
                                $services = json_decode($order->service_id, true) ?? [];
                                $testIds = collect($services)->pluck('test_id')->toArray();
                                $testNames = \App\models\Finance\Service::whereIn('id', $testIds)->pluck('name', 'id');
                            @endphp
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $order->request_type == 'lab' ? 'معمل' : ($order->request_type == 'xray' ? 'أشعة' : 'فحص') }}</td>
                                <td>
                                    @foreach ($services as $service)
                                        <span class="badge bg-info text-white">{{ $testNames[$service['test_id']] ?? 'غير معروف' }}</span>
                                    @endforeach
                                </td>
                                <td>
                                    @foreach ($services as $service)
                                        <span class="badge bg-secondary">
                                            {{ $service['eye'] == 'right' ? 'اليمنى' : ($service['eye'] == 'left' ? 'اليسرى' : 'كليهما') }}
                                        </span>
                                    @endforeach
                                </td>
                                <td>{{ $order->request_date }}</td>
                                <td>
                                    <span class="badge {{ $order->status == 'pending' ? 'bg-warning text-dark' : 'bg-success' }}">
                                        {{ $order->status == 'pending' ? 'معلق' : 'مكتمل' }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">لا توجد طلبات سابقة حالياً.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>
