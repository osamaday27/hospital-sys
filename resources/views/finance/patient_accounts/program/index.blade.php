@extends('layouts.master')

@section('title')
    {{ trans('patient_accounts_trans.title') }}
@stop

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active">
                            <a href="{{ url()->previous() }}">{{ trans('patient_accounts_trans.title') }}</a>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Button Section -->
    <div class="row">
        <!-- Content Row -->
        <div class="row justify-content-evenly mt-3">

            <!-- Patient Accounts Section -->
            <div class="col-lg-12">
                @include('partials.icon', [
                    'cards' => [
                        [
                            'icon' => 'mdi mdi-file-document-outline',
                            'text' => __('patient_accounts_trans.invoices'),
                            'link' => 'patient_invoices',
                        ],
                        [
                            'icon' => 'mdi mdi-file-check-outline',
                            'text' => __('patient_accounts_trans.claims'), 
                            'link' => 'claims_create',
                        ],
                    
                        [
                            'icon' => 'mdi mdi-file-document-edit-outline',
                            'text' => __('patient_accounts_trans.manage_claims'), 
                            'link' => 'insurance_claims_index',
                        ],
                        
                    ],
                ])
            </div>

        </div>
        <!-- End Content Row -->
    </div>
@endsection
