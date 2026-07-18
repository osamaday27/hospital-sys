<div class="container-fluid">
  @php
    use Illuminate\Support\Carbon;

    // تنسيق آمن للتاريخ
    $dateStr = '';
    if (!empty($inquiry->inquiry_date)) {
        try {
            $dateStr = Carbon::parse($inquiry->inquiry_date)->format('Y-m-d');
        } catch (\Throwable $e) {
            $dateStr = (string) $inquiry->inquiry_date; // fallback كنص
        }
    }

    // ترجمة النوع عند الحالات غير المسجلة
    $genderLabel = $inquiry->gender === 'male'
        ? trans('general.male')
        : ($inquiry->gender === 'female' ? trans('general.female') : '');

    // التأكد من أن contact_methods مصفوفة
    $contacts = $inquiry->contact_methods ?? [];
    if (!is_array($contacts)) {
        $contacts = (array) $contacts;
    }
  @endphp

  <div class="row g-3">
    <div class="col-md-3">
      <div class="border p-2">
        <div class="text-muted small">{{ trans('general.id') }}</div>
        <div class="fw-semibold">#{{ e($inquiry->id) }}</div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="border p-2">
        <div class="text-muted small">{{ trans('call_center_trans.inquiry_date') }}</div>
        <div class="fw-semibold">{{ e($dateStr) }}</div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="border p-2">
        <div class="text-muted small">{{ trans('call_center_trans.status') }}</div>
        <div class="fw-semibold">
          {{ $inquiry->status === 'new' ? trans('call_center_trans.status_new') : trans('call_center_trans.status_reviewed') }}
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="border p-2">
        <div class="text-muted small">{{ trans('call_center_trans.created_by') }}</div>
        <div class="fw-semibold">{{ e(optional($inquiry->creator)->name) }}</div>
      </div>
    </div>

    <div class="col-12">
      <div class="border p-2">
        <div class="text-muted small">{{ trans('call_center_trans.patient_full_name') }}</div>
        <div class="fw-semibold">
          @if ($inquiry->is_registered && $inquiry->patient)
            {{ e($inquiry->patient->full_name) }}
            @php $mn = $inquiry->patient->medical_number; @endphp
            @if(!empty($mn)) ({{ e($mn) }}) @endif
          @else
            {{ e($inquiry->full_name) }}
            @if($genderLabel) - {{ $genderLabel }} @endif
            @if(!is_null($inquiry->age)) - {{ e($inquiry->age) }} @endif
          @endif
        </div>
      </div>
    </div>

    <div class="col-12">
      <div class="border p-2">
        <div class="text-muted small">{{ trans('call_center_trans.inquiry_text') }}</div>
        <div class="fw-semibold white-space-prewrap">{{ e($inquiry->inquiry) }}</div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="border p-2">
        <div class="text-muted small">{{ trans('call_center_trans.doctor') }}</div>
        <div class="fw-semibold">{{ e(optional($inquiry->doctor)->full_name) }}</div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="border p-2">
        <div class="text-muted small">{{ trans('call_center_trans.referral_entity') }}</div>
        <div class="fw-semibold">{{ e($inquiry->referral_entity) }}</div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="border p-2">
        <div class="text-muted small">{{ trans('call_center_trans.heard_about_us') }}</div>
        <div class="fw-semibold">{{ e($inquiry->heard_about_us) }}</div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="border p-2">
        <div class="text-muted small">{{ trans('general.notes') }}</div>
        <div class="fw-semibold">{{ e($inquiry->notes) }}</div>
      </div>
    </div>

    <div class="col-12">
      <div class="border p-2">
        <div class="text-muted small">{{ trans('call_center_trans.contact_methods') }}</div>
        @if (!empty($contacts))
          <ul class="mb-0">
            @foreach ($contacts as $c)
              @php $c = (array)$c; @endphp
              <li>
                <strong>{{ e($c['type'] ?? '') }}:</strong>
                {{ e($c['value'] ?? '') }}
              </li>
            @endforeach
          </ul>
        @else
          <div class="text-muted">{{ trans('general.none') }}</div>
        @endif
      </div>
    </div>
  </div>
</div>
