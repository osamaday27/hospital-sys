<div class="container-fluid py-3" dir="{{ app()->getLocale() == 'ar' ? 'rtl' : 'ltr' }}">

    <h4 class="fw-bold text-primary mb-3">
        🧾 {{ trans('medical_reports_trans.previous_medical_reports') }}
    </h4>

    {{-- ✅ Reports Table --}}
    @if($selectedReport === null)
        <div class="card shadow-sm">
            <div class="card-body p-0">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-primary">
                        <tr>
                            <th>#</th>
                            <th>{{ trans('medical_reports_trans.surgery_date') }}</th>
                            <th>{{ trans('medical_reports_trans.report_type') }}</th>
                            <th>{{ trans('medical_reports_trans.surgeon') }}</th>
                            <th>{{ trans('medical_reports_trans.procedure') }}</th>
                            <th class="text-center">{{ trans('medical_reports_trans.view') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reports as $index => $rep)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td>{{ $rep->surgery_date ?? 'N/A' }}</td>
                                <td>{{ $rep->report_type ?? 'N/A' }}</td>
                                <td>{{ $rep->surgeon ?? 'N/A' }}</td>
                                <td>{{ $rep->surgical_procedure ?? 'N/A' }}</td>
                                <td class="text-center">
                                    <button 
                                        onclick="window.open('{{ url('/ar/surgical-report/print/' . $rep->id) }}', 
                                        'reportWindow', 
                                        'width=1000,height=800,top=50,left=150,scrollbars=yes,resizable=yes');" 
                                        class="btn btn-sm btn-outline-primary">
                                        <i class="ri-eye-line"></i> {{ trans('medical_reports_trans.print') }}
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-3">
                                    {{ trans('medical_reports_trans.no_reports_found') }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    {{-- ✅ Selected Report --}}
    @if($selectedReport)
        <div class="card shadow-sm mt-4 border-0">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">{{ $selectedReport->report_type ?? trans('medical_reports_trans.medical_report') }}</h5>
                <button wire:click="closeReport" class="btn btn-light btn-sm">
                    <i class="ri-arrow-go-back-line"></i> {{ trans('medical_reports_trans.back') }}
                </button>
            </div>
            <div class="card-body">
                <div class="row mb-2">
                    <div class="col-md-6"><strong>{{ trans('medical_reports_trans.date') }}:</strong> {{ $selectedReport->surgery_date }}</div>
                    <div class="col-md-6"><strong>{{ trans('medical_reports_trans.surgeon') }}:</strong> {{ $selectedReport->surgeon }}</div>
                </div>
                <div class="mb-2"><strong>{{ trans('medical_reports_trans.procedure') }}:</strong> {{ $selectedReport->surgical_procedure }}</div>
                <div class="mb-2"><strong>{{ trans('medical_reports_trans.pre_op_diagnosis') }}:</strong> {{ $selectedReport->preoperative_diagnosis }}</div>
                <div class="mb-2"><strong>{{ trans('medical_reports_trans.post_op_diagnosis') }}:</strong> {{ $selectedReport->postoperative_diagnosis }}</div>

                @php $details = json_decode($selectedReport->operative_details, true); @endphp
                @if($details)
                    <hr>
                    <h6 class="fw-bold text-primary">{{ trans('medical_reports_trans.operative_details') }}</h6>
                    <p class="mb-2">{{ $details['intro'] ?? '' }}</p>
                    <p class="mb-2">{{ $details['details'] ?? '' }}</p>
                @endif

                @if($selectedReport->complications && $selectedReport->complications !== 'None')
                    <div class="alert alert-warning mt-3">
                        <strong>{{ trans('medical_reports_trans.complications') }}:</strong> {{ $selectedReport->complications }}
                    </div>
                @endif

                <div class="text-center mt-3">
                    <a href="{{ route('reports.print', $selectedReport->id) }}" target="_blank" class="btn btn-outline-success px-4">
                        <i class="ri-printer-line"></i> {{ trans('medical_reports_trans.print_report') }}
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>
