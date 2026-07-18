
@extends('layouts.master')
@section('title')
{{ trans('main_trans.title') }}



@stop


@section('content')

<div class="row">
    <div class="col-12">
        <div class="page-title-box d-sm-flex align-items-center justify-content-between">
            <h4 class="mb-sm-0">{{ trans('settings_trans.settings') }}</h4>
            <div class="page-title-right">
                <ol class="breadcrumb m-0">
                    <li class="breadcrumb-item"><a href="javascript: void(0);">{{ trans('settings_trans.settings') }}</a></li>
                    <li class="breadcrumb-item active">{{ trans('settings_trans.nationalities_settings') }}</li>
                </ol>
            </div>
        </div>
    </div>
</div>
        <!-- end page title -->
        <div class="row">
            <div class="col-xxl-12">
                <div class="card">
                    <div class="card-header align-items-center d-flex">
                        <h4 class="card-title mb-0 flex-grow-1">{{ trans('settings_trans.add_clinic') }}</h4>
                        
                    </div><!-- end card header -->

                    <div class="card-body">
                        <div class="live-preview">
                            <form action="{{route('clinic_settings.store')}}" method="post">
                                {{ csrf_field() }}
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="firstNameinput" class="form-label">{{ trans('settings_trans.name_ar') }}</label>
                                            <input type="text" name="name_ar" class="form-control" placeholder="{{ trans('settings_trans.name_ar_enter') }}" id="firstNameinput">
                                        </div>
                                    </div>
                                    <!--end col-->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="lastNameinput" class="form-label">{{ trans('settings_trans.name_en') }}</label>
                                            <input type="text" name="name_en" class="form-control" placeholder="{{ trans('settings_trans.name_en_enter') }}" id="lastNameinput">
                                        </div>
                                    </div>
                                    <!--end col-->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="compnayNameinput" class="form-label">{{ trans('settings_trans.email_clinic') }}</label>
                                            <input type="email" name="email_clinic" class="form-control" placeholder="{{ trans('settings_trans.email_clinic') }}" id="compnayNameinput">
                                        </div>
                                    </div>
                                    <!--end col-->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="phonenumberInput" class="form-label">{{ trans('settings_trans.clinic_phone') }}</label>
                                            <input type="tel" class="form-control" name="clinic_phone" placeholder="{{ trans('settings_trans.clinic_phone') }}" id="phonenumberInput">
                                        </div>
                                    </div>
                                    <!--end col-->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="emailidInput" class="form-label">{{ trans('settings_trans.clinic_number_center') }}</label>
                                            <input type="text" name="clinic_number_center" class="form-control" placeholder="{{ trans('settings_trans.clinic_number_center') }}" id="emailidInput">
                                        </div>
                                    </div>
                                    <!--end col-->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="address1ControlTextarea" class="form-label">{{ trans('settings_trans.clinic_address') }}</label>
                                            <input type="text" name="clinic_address" class="form-control" placeholder="{{ trans('settings_trans.clinic_address') }}" id="address1ControlTextarea">
                                        </div>
                                    </div>
                                    <!--end col-->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="citynameInput" class="form-label">{{ trans('settings_trans.clinic_specialty') }}</label>
                                            <select  class="form-control" name="clinic_specialty" id="choices-single-no-sorting" name="choices-single-no-sorting" data-choices data-choices-sorting-false>
                                                <option selected disabled value="0">{{ trans('patient_trans.select_option') }}</option>
                                                @foreach ($specialty as $item)
                                                   <option value="{{$item->id}}">{{$item->name}}</option>   
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <!--end col-->
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="ForminputState" class="form-label">{{ trans('settings_trans.clinic_branch') }}</label>
                                            <select  class="form-control" name="clinic_branch" id="choices-single-no-sorting" name="choices-single-no-sorting" data-choices data-choices-sorting-false>
                                                <option selected>Choose...</option>
                                                <option value="1">...</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="ForminputState" class="form-label">{{ trans('settings_trans.clinic_manager') }}</label>
                                            <select  class="form-control" name="clinic_manager" id="choices-single-no-sorting" name="choices-single-no-sorting" data-choices data-choices-sorting-false>
                                                <option selected>Choose...</option>
                                                   <option value="1">...</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="emailidInput" class="form-label">{{ trans('settings_trans.clinic_notes') }}</label>
                                            <input type="text" name="clinic_notes" class="form-control" placeholder="{{ trans('settings_trans.clinic_notes') }}" id="emailidInput">
                                        </div>
                                    </div>
                                    <!--end col-->
                                    <div class="col-lg-12">
                                        <div class="text-end">
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                        </div>
                                    </div>
                                    <!--end col-->
                                </div>
                                <!--end row-->
                            </form>
                        </div>
                        
                        </div>
                    </div>
                </div>
            </div> <!-- end col -->
        </div>
    
@endsection
