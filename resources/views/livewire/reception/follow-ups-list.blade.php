<div class="py-4">
    <div class="card shadow-sm border-0">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center flex-wrap">
            <h5 class="mb-0"><i class="mdi mdi-calendar-check"></i> المتابعات المطلوبة من الأطباء</h5>
            <div class="d-flex gap-2 mt-2 mt-md-0">
                <input type="text" class="form-control form-control-sm" placeholder="بحث..." wire:model.debounce.500ms="search">
                <input type="date" class="form-control form-control-sm" wire:model="from_date">
                <input type="date" class="form-control form-control-sm" wire:model="to_date">
                <button class="btn btn-light btn-sm" wire:click="loadRequests">
                    <i class="mdi mdi-refresh"></i> تحديث
                </button>
            </div>
        </div>

        <div class="card-body">
            @if($requests->isEmpty())
                <div class="alert alert-info">لا توجد متابعات مسجلة.</div>
            @else
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>المريض</th>
                                <th>الطبيب</th>
                                <th>العيادة</th>
                                <th>تاريخ الطلب</th>
                                <th>تاريخ المتابعة</th>
                                <th>ملاحظات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($requests as $i => $req)
                                <tr>
                                    <td>{{ $i+1 }}</td>
                                    <td>{{ $req->patient?->full_name ?? '-' }}</td>
                                    <td>{{ $req->doctor?->full_name ?? '-' }}</td>
                                    <td>{{ $req->clinic?->name_ar ?? '-' }}</td>
                                    <td>{{ \Carbon\Carbon::parse($req->request_date)->format('d/m/Y') }}</td>
                                    <td>{{ \Carbon\Carbon::parse($req->followup_date)->format('d/m/Y') }}</td>
                                    <td>{{ $req->notes ?? '-' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
</div>
