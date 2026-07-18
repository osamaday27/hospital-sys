@extends('layouts.masterliveware')

@section('title')
    {{ trans('pricing_list.title') }}
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active">
                            <a href="{{ url()->previous() }}">{{ trans('pricing_list.title') }}</a>
                        </li>
                    </ol>
                </div>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('pricing_management.index') }}">{{ trans('pricing_list.title') }}</a>
                        </li>
                        <li class="breadcrumb-item active">{{ trans('pricing_list.pricing_of_services') }}</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Button Section -->
    <div class="row">
        <div class="col-lg-12">
              @livewire('pricing.pricing-of-services', ['price_list_id' => $price_list_id])
        </div>
    </div>


@endsection
