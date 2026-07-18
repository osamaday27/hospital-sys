@extends('layouts.masterpatient')

@section('title')
    {{ trans('patient_trans.info_patient') }}
@stop
<meta name="csrf-token" content="{{ csrf_token() }}">

@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active">
                            <a href="{{ url()->previous() }}">{{ trans('patient_trans.info_patient') }}</a>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <!-- عرض جميع الأخطاء في الأعلى -->
                @if ($errors->any())
                    <div class="alert alert-danger">
                        <ul>
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @if (session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif
                <div class="card-body">
                  
                    
                        <div class="row">
                            <div class="accordion custom-accordionwithicon custom-accordion-border accordion-border-box accordion-secondary"
                                id="accordionBordered">
                                <div class="accordion-item shadow bordersys">
                                    <h2 class="accordion-header" id="accordionborderedExample1">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#accor_borderedExamplecollapse1" aria-expanded="true"
                                            aria-controls="accor_borderedExamplecollapse1">
                                            {{ trans('patient_trans.name') }}
                                        </button>
                                    </h2>
                                    <div id="accor_borderedExamplecollapse1" class="accordion-collapse collapse show"
                                        aria-labelledby="accordionborderedExample1" data-bs-parent="#accordionBordered">
                                        <div class="accordion-body">
                                            @include('administrative.medical_record.master_data_info_show.secssion_name')
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div style="top: 10px;"
                                class="accordion custom-accordionwithicon custom-accordion-border accordion-border-box accordion-secondary"
                                id="gender_age">
                                <div class="accordion-item shadow bordersys">
                                    <h2 class="accordion-header" id="gender_ageExample1">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#gender_ageExamplecollapse1" aria-expanded="true"
                                            aria-controls="gender_ageExamplecollapse1">
                                            {{ trans('patient_trans.gender_age') }}
                                        </button>
                                    </h2>
                                    <div id="gender_ageExamplecollapse1" class="accordion-collapse collapse show"
                                        aria-labelledby="gender_ageExample1" data-bs-parent="#gender_age">
                                        <div class="accordion-body">
                                            <div class="row">
                                                @include('administrative.medical_record.master_data_info_show.gender_age')
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div style="top:23px;"
                                class="accordion custom-accordionwithicon custom-accordion-border accordion-border-box accordion-secondary"
                                id="contact_information">
                                <div class="accordion-item shadow bordersys">
                                    <h2 class="accordion-header" id="contact_informationExample1">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#contact_informationExamplecollapse1" aria-expanded="true"
                                            aria-controls="contact_informationExamplecollapse1">
                                            {{ trans('patient_trans.contact_information') }}
                                        </button>
                                    </h2>
                                    <div id="contact_informationExamplecollapse1" class="accordion-collapse collapse show"
                                        aria-labelledby="contact_informationExample1" data-bs-parent="#contact_information">
                                        <div class="accordion-body">
                                            <div class="row">
                                                @include('administrative.medical_record.master_data_info_show.contact_information')
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div style="top: 33px;"
                                class="accordion custom-accordionwithicon custom-accordion-border accordion-border-box accordion-secondary"
                                id="address_information">
                                <div class="accordion-item shadow bordersys">
                                    <h2 class="accordion-header" id="address _informationExample1">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse"
                                            data-bs-target="#address_informationExamplecollapse1" aria-expanded="true"
                                            aria-controls="address_informationExamplecollapse1">
                                            {{ trans('patient_trans.address_information') }}
                                        </button>
                                    </h2>
                                    <div id="address_informationExamplecollapse1" class="accordion-collapse collapse show"
                                        aria-labelledby="address_informationExample1" data-bs-parent="#address_information">
                                        <div class="accordion-body">
                                            <div class="row">
                                                @include('administrative.medical_record.master_data_info_show.address_information')
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-12" style="margin-top: 44px;">
                            <div class="card">
                                <div class="card-body">
                                    <div class="d-flex align-items-center mb-5">
                                        <div class="flex-grow-1">
                                            <h5 class="card-title mb-0">{{ trans('patient_trans.complete_profile') }}</h5>
                                        </div>
                                    </div>
                                    <div class="progress animated-progress custom-progress progress-label">
                                        <div id="wi" class="progress-bar bg-danger" role="progressbar"
                                            style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                                            <div class="label" id="pr">0%</div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                          
                        </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        document.getElementById('myForm').addEventListener('input', get);

        function calculateCompletionPercentage(formId) {
            const form = document.getElementById(formId);
            const inputFields = form.querySelectorAll('select, input');
            const totalFields = inputFields.length;
            let count = 0;

            inputFields.forEach((input) => {
                if (input.value.trim() !== '' && input.value.trim() !== '0' && input.value.trim() !== null) {
                    count++;
                }
            });
            return (count / totalFields) * 100;
        }

        function get() {
            const formId = 'myForm';
            const completionPercentage = calculateCompletionPercentage(formId);
            const roundedNumber = Math.round(completionPercentage);
            document.getElementById('wi').setAttribute('aria-valuenow', roundedNumber);
            document.getElementById('wi').style.width = `${roundedNumber}%`;
            document.getElementById('pr').textContent = `${roundedNumber}%`;
            updateProgressBar(roundedNumber);
        }

        function updateProgressBar(percentage) {
            const progressBar = document.getElementById('wi');
            progressBar.className = 'progress-bar'; // Reset class
            if (percentage >= 75) {
                progressBar.classList.add('bg-success');
            } else if (percentage >= 50) {
                progressBar.classList.add('bg-warning');
            } else {
                progressBar.classList.add('bg-danger');
            }
        }
    </script>
@endsection
