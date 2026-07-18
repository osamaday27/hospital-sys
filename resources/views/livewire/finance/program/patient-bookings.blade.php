<div>
    <div class="card shadow-sm mb-3">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <h4 class="mb-0" style="color: #fff">
                <i class="mdi mdi-calendar-heart mdi-24px me-2"></i>
                {{ __('surgical_trans.operations_bookings') }}
            </h4>
            <button class="btn btn-light btn-sm" wire:click="$refresh">
                <i class="mdi mdi-refresh"></i> {{ __('surgical_trans.refresh') }}
            </button>
        </div>

        <div class="card-body">
            {{-- Filters --}}
            <div class="row g-3 mb-4">
                <div class="col-md-2">
                    <input wire:model.debounce.300ms="search" type="text" class="form-control"
                        placeholder="{{ __('surgical_trans.search_patient') }}">
                </div>

                <div class="col-md-2">
                    <input wire:model="filterDateFrom" type="date" class="form-control">
                </div>

                <div class="col-md-2">
                    <input wire:model="filterDateTo" type="date" class="form-control">
                </div>

                <div class="col-md-2">
                    <input wire:model="filterSurgery" type="text" class="form-control"
                        placeholder="{{ __('surgical_trans.filter_surgery') }}">
                </div>

                <div class="col-md-2">
                    <input wire:model="filterSurgeon" type="text" class="form-control"
                        placeholder="{{ __('surgical_trans.filter_surgeon') }}">
                </div>

                <div class="col-md-2">
                    <select wire:model="filterStatus" class="form-select">
                        <option value="">{{ __('surgical_trans.all_status') }}</option>
                        <option value="booked">{{ __('surgical_trans.status_booked') }}</option>
                        <option value="completed">{{ __('surgical_trans.status_completed') }}</option>
                        <option value="postponed">{{ __('surgical_trans.status_postponed') }}</option>
                        <option value="cancelled">{{ __('surgical_trans.status_cancelled') }}</option>
                    </select>
                </div>

                {{-- ✅ فلتر الدفعات المقدمة --}}
                <div class="col-md-2">
                    <select wire:model="filterAdvance" class="form-select">
                        <option value="">{{ __('finance_trans.all_advance') }}</option>
                        <option value="with">{{ __('finance_trans.with_advance') }}</option>
                        <option value="without">{{ __('finance_trans.without_advance') }}</option>
                    </select>
                </div>
            </div>

            {{-- Table --}}
            <div class="table-responsive rounded">
                <table class="table table-hover table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>{{ __('surgical_trans.patient_name') }}</th>

                            {{-- 👇 دعم تعدد العمليات --}}
                            <th style="min-width:260px;">{{ __('surgical_trans.surgery_name') }}</th>

                            <th>{{ __('surgical_trans.surgeon_name') }}</th>
                            <th>{{ __('surgical_trans.contract_name') }}</th>

                            {{-- 👇 الإجمالي + تفاصيل أسعار العمليات --}}
                            <th style="min-width:200px;">{{ __('surgical_trans.package_price') }}</th>

                            <th>{{ __('finance_trans.advance_payment') }}</th>
                            <th>{{ __('surgical_trans.booking_date') }}</th>
                            <th>{{ __('surgical_trans.room') }}</th>
                            <th>{{ __('surgical_trans.status') }}</th>
                            <th>{{ __('surgical_trans.user') }}</th>
                            <th>{{ __('surgical_trans.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($bookings as $booking)
                            @php
                                $surgeries = $booking->surgeries_list ?? [];
                                $count = count($surgeries);
                                $limit = 1; // نعرض عملية واحدة كبادج، والباقي في Popover
                                $totalPrice = (float) ($booking->package_price_total ?? 0);
                                $surgeon = $booking->medicalTeam->firstWhere('role', 'surgeon');
                            @endphp
                            <tr>
                                <td>{{ ($bookings->currentPage() - 1) * $bookings->perPage() + $loop->iteration }}</td>

                                <td>{{ $booking->patient->full_name ?? '-' }}</td>

                                {{-- العمليات كبادجات + "عرض المزيد" --}}
                                <td>
                                    @if ($count > 0)
                                        @foreach (collect($surgeries)->take($limit) as $srv)
                                            <span
                                                class="badge bg-light text-dark border me-1 mb-1 d-inline-flex align-items-center">
                                                <i class="ri-scissors-cut-line me-1"></i>
                                                {{ $srv['name'] }}
                                                @if (!empty($srv['side']))
                                                    <span class="ms-2 badge bg-soft-primary text-primary">
                                                        {{ __('surgical_trans.' . ($srv['side'] ?? 'not_specified')) }}
                                                    </span>
                                                @endif
                                            </span>
                                        @endforeach

                                        @if ($count > $limit)
                                            @php
                                                // محتوى الـ popover لباقي العمليات
                                                $moreHtml = collect($surgeries)
                                                    ->slice($limit)
                                                    ->map(function ($srv) {
                                                        $nm = e($srv['name']);
                                                        $side = $srv['side']
                                                            ? __('surgical_trans.' . $srv['side'])
                                                            : __('surgical_trans.not_specified');
                                                        return "<div class='mb-1'><i class=\"ri-scissors-cut-line me-1\"></i> {$nm} <span class=\"badge bg-soft-primary text-primary ms-2\">{$side}</span></div>";
                                                    })
                                                    ->implode('');
                                                $moreHtmlAttr = str_replace(['"', "'"], ['&quot;', '&#39;'], $moreHtml);
                                            @endphp
                                            <button type="button" class="btn btn-link p-0 text-secondary"
                                                tabindex="0" role="button" data-bs-toggle="popover"
                                                data-bs-trigger="focus" data-bs-html="true" data-bs-container="body"
                                                title="{{ __('surgical_trans.all_surgicals') }} ({{ $count }})"
                                                data-bs-content="{!! $moreHtmlAttr !!}">
                                                {{ __('surgical_trans.show_more') }} (+{{ $count - $limit }})
                                            </button>
                                        @endif
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>

                                <td>{{ $surgeon?->employee?->full_name ?? '-' }}</td>
                                <td>{{ $booking->contract_name ?? '-' }}</td>

                                {{-- إجمالي السعر + كشف تفصيلي (Popover) --}}
                                <td>
                                    <span class="fw-semibold">{{ number_format($totalPrice, 2) }}</span>

                                    @if ($count > 0)
                                        @php
                                            $priceHtml = collect($surgeries)
                                                ->map(function ($srv) {
                                                    $nm = e($srv['name']);
                                                    $pr = number_format((float) ($srv['price'] ?? 0), 2);
                                                    return "<div class='d-flex justify-content-between'><span>{$nm}</span><span class='ms-3'>{$pr}</span></div>";
                                                })
                                                ->implode('');
                                            $priceHtmlAttr = str_replace(['"', "'"], ['&quot;', '&#39;'], $priceHtml);
                                        @endphp

                                        <button type="button" class="btn btn-sm btn-link text-info p-0 ms-2"
                                            data-bs-toggle="popover" data-bs-trigger="focus" data-bs-html="true"
                                            data-bs-container="body" title="{{ __('surgical_trans.package_price') }}"
                                            data-bs-content="{!! $priceHtmlAttr !!}">
                                            {{ __('surgical_trans.details') }}
                                        </button>
                                    @endif>
                                </td>

                                <td>
                                    @if (($booking->advance_paid ?? 0) > 0)
                                        <span class="badge bg-success">
                                            {{ number_format($booking->advance_paid, 2) }}
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            {{ __('finance_trans.no_advance') }}
                                        </span>
                                    @endif
                                </td>

                                <td>{{ $booking->booking_date }}</td>
                                <td>{{ $booking->room->name_ar ?? '-' }}</td>
                                <td>{{ __('surgical_trans.status_' . $booking->status) }}</td>
                                <td>{{ $booking->user->name ?? '-' }}</td>
                                <td class="text-nowrap">
                                    {{-- عرض تفاصيل الفاتورة للحجز --}}
                                    <a href="{{ route('finance.patient_invoices_details', $booking->id) }}"
                                        class="btn btn-sm btn-outline-primary" title="{{ __('surgical_trans.show') }}">
                                        <i class="mdi mdi-eye-outline"></i>
                                    </a>

                                    {{-- فتح شاشة الدفعة المقدّمة --}}
                                    <a href="{{ route('finance.openAdvancePayment', $booking->id) }}"
                                        class="btn btn-sm btn-outline-success"
                                        title="{{ __('finance_trans.advance_payment') }}">
                                        <i class="mdi mdi-cash-plus"></i>
                                    </a>

                                    {{-- إدارة الفاتورة (يفتح شاشة إدارة فاتورة العملية) --}}
                                    <a href="{{ route('finance.surgical_invoice', $booking->id) }}"
                                        class="btn btn-sm btn-outline-dark"
                                        title="{{ __('surgical_invoice_trans.manage_operation_invoice') }}">
                                        <i class="mdi mdi-file-cog-outline"></i>
                                    </a>
                                </td>

                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="text-center text-muted">
                                    {{ __('surgical_trans.no_records') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            {{ $bookings->links() }}
        </div>
    </div>

    {{-- تهيئة الـ Popovers --}}
    <script>
        document.addEventListener('livewire:load', function() {
            const initPopovers = () => {
                document.querySelectorAll('[data-bs-toggle="popover"]').forEach(el => {
                    const inst = bootstrap.Popover.getInstance(el);
                    if (inst) inst.dispose();
                });
                document.querySelectorAll('[data-bs-toggle="popover"]').forEach(el => {
                    new bootstrap.Popover(el, {
                        container: 'body',
                        html: true,
                        trigger: el.getAttribute('data-bs-trigger') || 'focus',
                        sanitize: false
                    });
                });
            };
            initPopovers();
            Livewire.hook('message.processed', () => initPopovers());
        });
    </script>
</div>
