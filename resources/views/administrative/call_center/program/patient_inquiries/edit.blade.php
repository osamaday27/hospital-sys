@extends('layouts.master_table')

@section('title')
  {{ trans('general.edit') }} - #{{ $inquiry->id }}
@stop

@section('content')
<div class="row">
  <div class="col-12">
    <div class="page-title-box d-sm-flex align-items-center justify-content-between">
      <h4 class="mb-sm-0 font">{{ trans('general.edit') }} - #{{ $inquiry->id }}</h4>
      <div class="page-title-right">
        <ol class="breadcrumb m-0">
          <li class="breadcrumb-item"><a href="{{ route('patient_inquiries.index') }}">{{ trans('call_center_trans.patient_inquiries') }}</a></li>
          <li class="breadcrumb-item active">{{ trans('general.edit') }}</li>
        </ol>
      </div>
    </div>
  </div>
</div>

<div class="row">
  <div class="col-12">
    @if (session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif

    <form action="{{ route('patient_inquiries.update',$inquiry->id) }}" method="POST">
      @csrf @method('PUT')

      <div class="card">
        <div class="card-header"><h5 class="mb-0">{{ trans('call_center_trans.patient_info') }}</h5></div>
        <div class="card-body">
          @if ($inquiry->is_registered && $patient)
            <div class="row g-3">
              <div class="col-md-4">
                <label class="form-label">{{ trans('call_center_trans.patient_full_name') }}</label>
                <input class="form-control" value="{{ $patient->full_name }}" disabled>
              </div>
              <div class="col-md-4">
                <label class="form-label">{{ trans('call_center_trans.medical_number') }}</label>
                <input class="form-control" value="{{ $patient->medical_number }}" disabled>
              </div>
              <div class="col-md-4">
                <label class="form-label">{{ trans('call_center_trans.inquiry_date') }}</label>
                <input type="date" name="inquiry_date" class="form-control" required
                       value="{{ old('inquiry_date', optional($inquiry->inquiry_date)->format('Y-m-d')) }}">
              </div>
            </div>
          @else
            <div class="row g-3">
              <div class="col-md-4">
                <label class="form-label">{{ trans('call_center_trans.patient_full_name') }}</label>
                <input type="text" name="full_name" class="form-control" required value="{{ old('full_name',$inquiry->full_name) }}">
              </div>
              <div class="col-md-4">
                <label class="form-label">{{ trans('general.gender') }}</label>
                <select name="gender" class="form-control" required>
                  <option value="male" {{ old('gender',$inquiry->gender)==='male'?'selected':'' }}>{{ trans('general.male') }}</option>
                  <option value="female" {{ old('gender',$inquiry->gender)==='female'?'selected':'' }}>{{ trans('general.female') }}</option>
                </select>
              </div>
              <div class="col-md-4">
                <label class="form-label">{{ trans('general.age') }}</label>
                <input type="number" name="age" min="0" max="120" class="form-control" required value="{{ old('age',$inquiry->age) }}">
              </div>
              <div class="col-md-4">
                <label class="form-label">{{ trans('call_center_trans.inquiry_date') }}</label>
                <input type="date" name="inquiry_date" class="form-control" required
                       value="{{ old('inquiry_date', optional($inquiry->inquiry_date)->format('Y-m-d')) }}">
              </div>
            </div>
          @endif
        </div>
      </div>

      <div class="card mt-3">
        <div class="card-header"><h5 class="mb-0">{{ trans('call_center_trans.inquiry_details') }}</h5></div>
        <div class="card-body">
          <div class="mb-3">
            <label class="form-label">{{ trans('call_center_trans.inquiry_text') }}</label>
            <textarea name="inquiry" class="form-control" rows="5" required>{{ old('inquiry',$inquiry->inquiry) }}</textarea>
          </div>

          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label">{{ trans('call_center_trans.doctor') }}</label>
              <select name="doctor_id" id="doctor_select" class="form-control" required style="width:100%">
                @if ($inquiry->doctor)
                  <option value="{{ $inquiry->doctor->id }}" selected>{{ $inquiry->doctor->full_name }}</option>
                @endif
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label">{{ trans('call_center_trans.referral_entity') }}</label>
              <input type="text" name="referral_entity" class="form-control" value="{{ old('referral_entity',$inquiry->referral_entity) }}">
            </div>
            <div class="col-md-6">
              <label class="form-label">{{ trans('call_center_trans.heard_about_us') }}</label>
              <input type="text" name="heard_about_us" class="form-control" value="{{ old('heard_about_us',$inquiry->heard_about_us) }}">
            </div>
            <div class="col-md-6">
              <label class="form-label">{{ trans('general.notes') }}</label>
              <input type="text" name="notes" class="form-control" value="{{ old('notes',$inquiry->notes) }}">
            </div>
          </div>

          <hr>
          <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="mb-0">{{ trans('call_center_trans.contact_methods') }}</h6>
            <button type="button" class="btn btn-warning add-contact">
              <i class="fa fa-plus"></i> {{ trans('call_center_trans.add_another') }}
            </button>
          </div>

          <div id="contacts_container">
            @php
              $contacts = old('contact_type')
                ? collect(old('contact_type'))->map(fn($t,$i)=>['type'=>$t,'value'=>old('contact_value')[$i] ?? ''])
                : collect($inquiry->contact_methods ?? []);
            @endphp

            @forelse ($contacts as $c)
              <div class="row g-2 contact-row mb-2">
                <div class="col-md-4">
                  <select name="contact_type[]" class="form-control" required>
                    @foreach (['phone','whatsapp','email','other'] as $opt)
                      <option value="{{ $opt }}" {{ ($c['type']??'')===$opt?'selected':'' }}>
                        {{ $opt==='email'?trans('call_center_trans.email'):($opt==='phone'?trans('call_center_trans.phone'):ucfirst($opt)) }}
                      </option>
                    @endforeach
                  </select>
                </div>
                <div class="col-md-6">
                  <input name="contact_value[]" class="form-control" required value="{{ $c['value'] ?? '' }}"
                         placeholder="{{ trans('call_center_trans.enter_contact') }}">
                </div>
                <div class="col-md-2 d-flex align-items-center">
                  <button type="button" class="btn btn-danger remove-contact">
                    <i class="fa fa-trash"></i> {{ trans('general.delete') }}
                  </button>
                </div>
              </div>
            @empty
              <div class="row g-2 contact-row mb-2">
                <div class="col-md-4">
                  <select name="contact_type[]" class="form-control" required>
                    <option value="phone">{{ trans('call_center_trans.phone') }}</option>
                    <option value="whatsapp">WhatsApp</option>
                    <option value="email">{{ trans('call_center_trans.email') }}</option>
                    <option value="other">{{ trans('call_center_trans.other') }}</option>
                  </select>
                </div>
                <div class="col-md-6">
                  <input name="contact_value[]" class="form-control" required placeholder="{{ trans('call_center_trans.enter_contact') }}">
                </div>
                <div class="col-md-2 d-flex align-items-center">
                  <button type="button" class="btn btn-danger remove-contact">
                    <i class="fa fa-trash"></i> {{ trans('general.delete') }}
                  </button>
                </div>
              </div>
            @endforelse
          </div>

          <div class="text-center mt-3">
            <a href="{{ route('patient_inquiries.index') }}" class="btn btn-secondary btn-lg">
              <i class="fa fa-arrow-left"></i> {{ trans('general.back') }}
            </a>
            <button type="submit" class="btn btn-success btn-lg">{{ trans('call_center_trans.save') }}</button>
          </div>
        </div>
      </div>
    </form>
  </div>
</div>


<script>
$(function(){
  $('#doctor_select').select2({
    placeholder: "{{ trans('call_center_trans.search_doctor') }}",
    allowClear: true,
    ajax: { url: "{{ route('patient_inquiries.ajax.doctors') }}", data: p => ({ q: p.term }), processResults: d => d }
  });

  $(document).on('click','.add-contact', function(){
    const row = `
    <div class="row g-2 contact-row mb-2">
      <div class="col-md-4">
        <select name="contact_type[]" class="form-control" required>
          <option value="phone">{{ trans('call_center_trans.phone') }}</option>
          <option value="whatsapp">WhatsApp</option>
          <option value="email">{{ trans('call_center_trans.email') }}</option>
          <option value="other">{{ trans('call_center_trans.other') }}</option>
        </select>
      </div>
      <div class="col-md-6">
        <input name="contact_value[]" class="form-control" required placeholder="{{ trans('call_center_trans.enter_contact') }}">
      </div>
      <div class="col-md-2 d-flex align-items-center">
        <button type="button" class="btn btn-danger remove-contact">
          <i class="fa fa-trash"></i> {{ trans('general.delete') }}
        </button>
      </div>
    </div>`;
    $('#contacts_container').append(row);
  });

  $(document).on('click','.remove-contact', function(){
    if ($('.contact-row').length <= 1) return;
    $(this).closest('.contact-row').remove();
  });
});
</script>
@endsection
