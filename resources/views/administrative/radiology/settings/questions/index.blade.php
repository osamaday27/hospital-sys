@extends('layouts.masterliveware')

@section('title', __('radiology_trans.questions_settings'))

@section('content')
<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item active">
                        <a href="{{ url()->previous() }}">{{ __('radiology_trans.questions_settings') }}</a>
                    </li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="row mt-3">
    <div class="col-lg-12">
        {{-- ✅ مناداة مكون لايف واير --}}
        @livewire('radiology.settings.questions.index')
    </div>
</div>
@endsection
