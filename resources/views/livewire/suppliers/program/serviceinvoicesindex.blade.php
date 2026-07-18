<div class="card shadow-sm" dir="rtl">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
            <i class="mdi mdi-file-document me-2"></i>
            <h5 class="mb-0 text-primary">{{ __('suppliers.supplier_service_invoices') }}</h5>
        </div>
        <a href="{{ route('service_invoices') }}" class="btn btn-success btn-sm">
            <i class="mdi mdi-plus-circle-outline me-1"></i> {{ __('suppliers.add_invoice') }}
        </a>
    </div>

    <div class="card-body">
        <!-- الفلاتر -->
        <div class="row g-3 mb-3">
            <div class="col-md-2">
                <input type="text" wire:model.debounce.500ms="search" class="form-control form-control-sm"
                    placeholder="🔍 {{ __('suppliers.search') }}">
            </div>

            <div class="col-md-2">
                <div wire:ignore>
                    <select wire:model="branch_id" id="branch_id" class="form-select select2">
                        <option value="">{{ __('suppliers.choose_branch') }}</option>
                        @foreach ($branches as $branch)
                            <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="col-md-2">
                <select wire:model="filter_status" class="form-select form-select-sm">
                    <option value="">{{ __('suppliers.all_status') }}</option>
                    <option value="new">جديد</option>
                    <option value="approved">معتمد</option>
                    <option value="cancelled">ملغي</option>
                </select>
            </div>

            <div class="col-md-2">
                <select wire:model="filter_payment_status" class="form-select form-select-sm">
                    <option value="">{{ __('suppliers.all_payment_status') }}</option>
                    <option value="paid">مدفوع</option>
                    <option value="partial">مدفوع جزئي</option>
                    <option value="unpaid">غير مدفوع</option>
                </select>
            </div>


            <div class="col-md-2">
                <select wire:model="filter_payment_method" class="form-select form-select-sm">
                    <option value="">{{ __('suppliers.all_payment_methods') }}</option>
                    <option value="cash"><i class="mdi mdi-cash"></i> كاش</option>
                    <option value="card"><i class="mdi mdi-credit-card-outline"></i> بطاقة</option>
                    <option value="bank"><i class="mdi mdi-bank"></i> تحويل بنكي</option>
                </select>
            </div>

            <div class="col-md-2 d-flex gap-2">
                <input type="date" wire:model="filter_date_from" class="form-control form-control-sm">
                <input type="date" wire:model="filter_date_to" class="form-control form-control-sm">
            </div>
        </div>

        <!-- جدول الفواتير -->
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th><i class="mdi mdi-file-document-outline"></i> {{ __('suppliers.invoice_no') }}</th>
                        <th><i class="mdi mdi-account"></i> {{ __('suppliers.supplier') }}</th>
                        <th><i class="mdi mdi-office-building"></i> {{ __('suppliers.branch') }}</th>
                        <th><i class="mdi mdi-calendar"></i> {{ __('suppliers.invoice_date') }}</th>
                        <th><i class="mdi mdi-cash"></i> {{ __('suppliers.total_amount') }}</th>
                        <th><i class="mdi mdi-sale"></i> {{ __('suppliers.discount') }}</th>
                        <th><i class="mdi mdi-percent"></i> {{ __('suppliers.tax') }}</th>
                        <th><i class="mdi mdi-cash-check"></i> {{ __('suppliers.paid_amount') }}</th>
                        <th><i class="mdi mdi-cash-remove"></i> {{ __('suppliers.remaining_amount') }}</th>
                        <th><i class="mdi mdi-credit-card"></i> {{ __('suppliers.payment_method') }}</th>
                        <th><i class="mdi mdi-information"></i> {{ __('suppliers.status') }}</th>
                        <th><i class="mdi mdi-cog"></i> {{ __('suppliers.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($invoices as $inv)
                        <tr>
                            <td>{{ $inv->id }}</td>
                            <td>{{ $inv->invoice_no }}</td>
                            <td>{{ $inv->supplier->name_ar ?? '-' }}</td>
                            <td>{{ $inv->branch->name ?? '-' }}</td>
                            <td>{{ $inv->invoice_date }}</td>
                            <td class="fw-bold text-success">{{ number_format($inv->total_amount, 2) }} ج.م</td>
                            <td class="text-danger">{{ number_format($inv->discount, 2) }}</td>
                            <td class="text-warning">{{ number_format($inv->tax, 2) }}</td>
                            <td class="text-success">{{ number_format($inv->paid_amount, 2) }}</td>
                            <td class="text-danger">{{ number_format($inv->remaining_amount, 2) }}</td>
                            <td>
                                @if ($inv->payment_method == 'cash')
                                    <i class="mdi mdi-cash text-success"></i> كاش
                                @elseif($inv->payment_method == 'card')
                                    <i class="mdi mdi-credit-card-outline text-primary"></i> بطاقة
                                @else
                                    <i class="mdi mdi-bank text-info"></i> بنك
                                @endif
                            </td>


                            <td>
                                @if ($inv->status == 'new')
                                    <span class="badge bg-warning">جديد</span>
                                @elseif($inv->status == 'approved')
                                    <span class="badge bg-success">معتمد</span>
                                 @elseif($inv->status == 'cancelled')
                                    <span class="badge bg-danger">ملغي</span>
                                
                                @endif
                                @if ($inv->remaining_amount == 0)
                                    <span class="badge bg-success">مدفوع</span>
                                @elseif($inv->paid_amount > 0 && $inv->remaining_amount > 0)
                                    <span class="badge bg-warning">مدفوع جزئي</span>
                                @else
                                    <span class="badge bg-danger">غير مدفوع</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <a href="{{ route('supplier_invoices.show', $inv->id) }}" class="btn btn-sm btn-info">
                                    <i class="mdi mdi-eye"></i>
                                </a>
                                <a href="{{ route('supplier_invoices.print', $inv->id) }}" target="_blank"
                                    class="btn btn-success btn-sm">
                                    <i class="mdi mdi-printer"></i> 
                                </a>

                                <a href="{{ route('supplier_invoices.edit', $inv->id) }}"
                                    class="btn btn-sm btn-warning" title="{{ __('suppliers.update_invoice') }}">
                                    <i class="mdi mdi-pencil"></i>
                                </a>

                                @if ($inv->status == 'new')
                                    <button type="button" class="btn btn-sm btn-success"
                                        wire:click="approve({{ $inv->id }})">
                                        <i class="mdi mdi-check"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-danger"
                                        wire:click="confirmDelete({{ $inv->id }})">
                                        <i class="mdi mdi-delete"></i>
                                    </button>
                                @endif

                                @if ($inv->remaining_amount > 0 && $inv->status == 'approved' || $inv->status == 'partial')
                                    <a href="{{ route('supplier_invoices.payment', $inv->id) }}"
                                        class="btn btn-sm btn-primary" title="تسجيل دفعة">
                                        <i class="mdi mdi-cash-plus"></i>
                                    </a>
                                @endif


                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-3">
                {{ $invoices->links() }}
            </div>
        </div>
    </div>
</div>
<!-- تأكيد الحذف -->
<script>
    window.addEventListener('show-delete-confirmation', event => {
        Swal.fire({
            title: 'هل أنت متأكد؟',
            text: "لن تستطيع التراجع عن هذا الإجراء!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'نعم، احذف!',
            cancelButtonText: 'إلغاء'
        }).then((result) => {
            if (result.isConfirmed) {
                Livewire.emit('deleteConfirmed');
            }
        });
    });
    window.addEventListener('deleted', event => {
        Swal.fire('تم الحذف!', '', 'success');
    });
</script>
