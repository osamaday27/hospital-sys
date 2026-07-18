<section class="prescription-container shadow-lg rounded-4 bg-white p-4 mb-5">
    <div class="prescription-header text-center mb-4 position-relative">
        <div class="clinic-stamp position-absolute top-0 start-0 opacity-75">
            <i class="fas fa-stamp fa-3x text-primary"></i>
        </div>
        <h3 class="text-primary fw-bold mb-1">🧾 {{ __('doctor_trans.prescription') }}</h3>
        <div class="prescription-id badge bg-primary fs-6 fw-light mb-3">#{{ $pres->id }}</div>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="info-card p-3 rounded-3 bg-light">
                <p class="mb-2"><strong class="text-secondary">{{ __('doctor_trans.date') }}:</strong> 
                <span class="text-dark">{{ $pres->prescription_date }}</span></p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-card p-3 rounded-3 bg-light">
                <p class="mb-2"><strong class="text-secondary">{{ __('doctor_trans.doctor') }}:</strong> 
                <span class="text-dark">{{ $pres->doctor?->name }}</span></p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="info-card p-3 rounded-3 bg-light">
                <p class="mb-2"><strong class="text-secondary">{{ __('doctor_trans.patient') }}:</strong> 
                <span class="text-dark">{{ $pres->patient?->name }}</span></p>
            </div>
        </div>
    </div>

    <hr class="my-4 border-primary opacity-50">

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle prescription-table">
            <thead class="table-primary">
                <tr>
                    <th class="text-center">{{ __('doctor_trans.drug') }}</th>
                    <th class="text-center">{{ __('doctor_trans.dose') }}</th>
                    <th class="text-center">{{ __('doctor_trans.frequency') }}</th>
                    <th class="text-center">{{ __('doctor_trans.unit') }}</th>
                    <th class="text-center">{{ __('doctor_trans.duration') }}</th>
                    <th class="text-center">{{ __('doctor_trans.route') }}</th>
                    <th class="text-center">{{ __('doctor_trans.eye') }}</th>
                    <th class="text-center">{{ __('doctor_trans.notes') }}</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pres->items as $item)
                    <tr>
                        <td class="fw-medium">{{ $item->medication?->getTranslation('name', app()->getLocale()) ?? $item->medication_text }}</td>
                        <td class="text-center">{{ $item->quantity }}</td>
                        <td class="text-center">{{ $item->timer }}</td>
                        <td class="text-center">{{ $item->unit?->getTranslation('name', app()->getLocale()) ?? $item->unit_text }}</td>
                        <td class="text-center">{{ $item->duration?->getTranslation('name', app()->getLocale()) ?? $item->duration_text }}</td>
                        <td class="text-center">{{ $item->taken?->getTranslation('name', app()->getLocale()) ?? $item->taken_text }}</td>
                        <td class="text-center">
                            @if($item->eye)
                                <span class="badge bg-info">{{ $item->eye }}</span>
                            @endif
                        </td>
                        <td class="text-muted small">{{ $item->notes }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div class="signature-section mt-5 pt-4 text-center">
        <div class="signature-line mx-auto mb-2" style="width: 250px; border-bottom: 2px dotted #6c757d;"></div>
        <p class="text-secondary mb-0">{{ __('doctor_trans.doctor_signature') }}</p>
        <p class="text-muted small mt-1">{{ $pres->doctor?->name }}</p>
    </div>
</section>

<style>
    .prescription-container {
        border: 1px solid #e3e6f0;
        background-image: linear-gradient(to bottom, #f8f9ff, #ffffff);
    }
    .prescription-table th {
        font-weight: 600;
        padding: 12px 8px;
    }
    .prescription-table td {
        padding: 10px 8px;
    }
    .info-card {
        transition: all 0.3s ease;
        border: 1px solid #e3e6f0;
    }
    .info-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.05);
    }
    @media (max-width: 768px) {
        .prescription-table {
            font-size: 0.85rem;
        }
    }
</style>