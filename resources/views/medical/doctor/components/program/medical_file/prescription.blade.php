<div class="tab-pane fade " id="prescription2" role="tabpanel">
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-4">
                    <form class="mt-3" id="form-add_medication_to_patient">

                        <input type="text" id="patient_id" name="patient_id" hidden value="">
                        <input type="text" id="id_reservation" name="id_reservation" hidden
                            value="">
                        <div class="row">
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="lastNameinput" class="form-label">
                                        {{ trans('doctor_trans.choose_medicine') }}
                                    </label>
                                    <select class="js-example-basic-multiple" name="medication" id="medication">
                                    </select>
                                </div>
                            </div>
                            <!--end col-->
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="lastNameinput"
                                        class="form-label">{{ trans('doctor_trans.quantity') }}</label>
                                    <input type="text" class="form-control"
                                        placeholder="{{ trans('doctor_trans.quantity') }}" id="quantity"
                                        name="quantity">
                                </div>
                            </div><!--end col-->
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="lastNameinput"
                                        class="form-label">{{ trans('doctor_trans.unit') }}</label>
                                    <select class="js-example-basic-multiple" name="medication_unit" id="medication_unit">
                                    </select>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="lastNameinput"
                                        class="form-label">{{ trans('doctor_trans.duration') }}</label>
                                    <select class="js-example-basic-multiple" name="medication_duration"
                                        id="medication_duration">
                                    </select>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="lastNameinput" class="form-label">
                                        {{ trans('doctor_trans.take') }}
                                    </label>
                                    <select class="js-example-basic-multiple" name="medication_taken"
                                        id="medication_taken">
                                    </select>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="lastNameinput"
                                        class="form-label">{{ trans('doctor_trans.timer') }}</label>
                                    <input type="text" class="form-control"
                                        placeholder="{{ trans('doctor_trans.timer') }}" id="timer" name="timer">
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="address1ControlTextarea"
                                        class="form-label">{{ trans('doctor_trans.notes') }}</label>
                                    <input type="text" class="form-control" name="notes" id="notes"
                                        placeholder="{{ trans('doctor_trans.notes') }}" id="address1ControlTextarea">
                                </div>
                            </div>
                            <div class="col-6 mb-3">
                                <button type="submit"
                                    class="btn btn-success submit-form-add_medication_patient"style="width: 100%;">
                                    <script src="{{ URL::asset('assets/images/icon/lordicon.js') }}"></script>
                                    <lord-icon class="label-icon align-middle fs-16 me-2"
                                        src="{{ URL::asset('assets/images/icon/pdsourfn.json') }}" trigger="loop"
                                        delay="1500" state="in-dynamic" style="width:30px;height:30px;">
                                    </lord-icon>
                                    {{ trans('doctor_trans.save') }}
                                </button>

                            </div>
                            <div class="col-6 mb-3">
                                <a target="_block"
                                    class="btn btn-info"style="width: 100%;">
                                    <script src="{{ URL::asset('assets/images/icon/lordicon.js') }}"></script>
                                    <lord-icon class="label-icon align-middle fs-16 me-2"
                                        src="{{ URL::asset('assets/images/icon/vhyuhmbl.json') }}" trigger="loop"
                                        delay="1500" state="in-dynamic" style="width:30px;height:30px;">
                                    </lord-icon>
                                    {{ trans('doctor_trans.print_prescription') }}
                                </a>

                            </div>

                            <div class="row">
                                <div class="col-3">
                                    <button type="button" class="btn btn-info btn-label waves-effect waves-light"
                                        data-bs-toggle="modal" data-bs-target="#add_medication">
                                        <script src="{{ URL::asset('assets/images/icon/lordicon2.js') }}"></script>
                                        <lord-icon class="label-icon align-middle fs-16 me-2"
                                            src="{{ URL::asset('assets/images/icon/zfzufhzk.json') }}" trigger="loop"
                                            delay="1500" state="in-dynamic"
                                            style="width:30px;height:30px;left: -10px !important;top: -10px !important;">
                                        </lord-icon>
                                        {{ trans('doctor_trans.add_medication') }}
                                    </button>
                                </div>
                                <div class="col-3">
                                    <button type="button" class="btn btn-danger btn-label waves-effect waves-light"
                                        data-bs-toggle="modal" data-bs-target="#add_medication_unit">
                                        <script src="{{ URL::asset('assets/images/icon/lordicon2.js') }}"></script>
                                        <lord-icon class="label-icon align-middle fs-16 me-2"
                                            src="{{ URL::asset('assets/images/icon/zfzufhzk.json') }}" trigger="loop"
                                            delay="1500" state="in-dynamic"
                                            style="width:30px;height:30px;left: -10px !important;top: -10px !important;">
                                        </lord-icon>
                                        {{ trans('doctor_trans.add_unit') }}
                                    </button>
                                </div>
                                <div class="col-3">
                                    <button type="button"
                                        class="btn btn-secondary btn-label waves-effect waves-light"
                                        data-bs-toggle="modal" data-bs-target="#add_medication_duration">
                                        <script src="{{ URL::asset('assets/images/icon/lordicon2.js') }}"></script>
                                        <lord-icon class="label-icon align-middle fs-16 me-2"
                                            src="{{ URL::asset('assets/images/icon/zfzufhzk.json') }}" trigger="loop"
                                            delay="1500" state="in-dynamic"
                                            style="width:30px;height:30px;left: -10px !important;top: -10px !important;">
                                        </lord-icon>
                                        {{ trans('doctor_trans.add_duration') }}
                                    </button>
                                </div>
                                <div class="col-3">
                                    <button type="button" class="btn btn-warning btn-label waves-effect waves-light"
                                        data-bs-toggle="modal" data-bs-target="#add_medication_taken">
                                        <script src="{{ URL::asset('assets/images/icon/lordicon2.js') }}"></script>
                                        <lord-icon class="label-icon align-middle fs-16 me-2"
                                            src="{{ URL::asset('assets/images/icon/zfzufhzk.json') }}" trigger="loop"
                                            delay="1500" state="in-dynamic"
                                            style="width:30px;height:30px;left: -10px !important;top: -10px !important;">
                                        </lord-icon>
                                        {{ trans('doctor_trans.add_take') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="col-8">
                    <div id="prescription_pat"></div>

                </div>
            </div>
            <!--end row-->
        </div>
        <!--end card-body-->
    </div>
    <!--end card-->
</div>




<div class="modal fade" id="add_medication" tabindex="-1" aria-labelledby="exampleModalgridLabel"
    aria-modal="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalgridLabel">{{ trans('doctor_trans.add_new_medication') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="mt-3" id="form-add_medication">
                    @csrf
                    <div class="form-group">
                        <label for="firstName"
                            class="form-label">{{ trans('doctor_trans.add_new_medication_d') }}</label>
                        <input type="text" class="form-control" id="name_add_medication"
                            name="name_add_medication"
                            placeholder="{{ trans('doctor_trans.enter_add_medication') }}">
                    </div>
                    <div class="col-lg-12">
                        <div class="hstack gap-2 justify-content-end">
                            <button type="button" class="btn btn-light"
                                data-bs-dismiss="modal">{{ trans('doctor_trans.close') }}</button>
                            <button type="button" class="btn btn-success submit-form-add_medication"
                                style="background:green" id="create_new">{{ trans('doctor_trans.save') }}</button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="add_medication_unit" tabindex="-1" aria-labelledby="exampleModalgridLabel"
    aria-modal="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalgridLabel">
                    {{ trans('doctor_trans.add_new_medication_unit') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="mt-3" id="form-add_medication_unit">
                    @csrf
                    <div class="form-group">
                        <label for="firstName"
                            class="form-label">{{ trans('doctor_trans.add_new_medication_unit_d') }}</label>
                        <input type="text" class="form-control" id="name_add_medication_unit"
                            name="name_add_medication_unit"
                            placeholder="{{ trans('doctor_trans.enter_add_medication_unit') }}">
                    </div>
                    <div class="col-lg-12">
                        <div class="hstack gap-2 justify-content-end">
                            <button type="button" class="btn btn-light"
                                data-bs-dismiss="modal">{{ trans('doctor_trans.close') }}</button>
                            <button type="button" class="btn btn-success submit-form-add_medication_unit"
                                style="background:green" id="create_new">{{ trans('doctor_trans.save') }}</button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="add_medication_duration" tabindex="-1" aria-labelledby="exampleModalgridLabel"
    aria-modal="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalgridLabel">
                    {{ trans('doctor_trans.add_new_medication_duration') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="mt-3" id="form-add_medication_duration">
                    @csrf
                    <div class="form-group">
                        <label for="firstName"
                            class="form-label">{{ trans('doctor_trans.add_new_medication_duration_d') }}</label>
                        <input type="text" class="form-control" id="name_add_medication_duration"
                            name="name_add_medication_duration"
                            placeholder="{{ trans('doctor_trans.enter_add_medication_duration') }}">
                    </div>
                    <div class="col-lg-12">
                        <div class="hstack gap-2 justify-content-end">
                            <button type="button" class="btn btn-light"
                                data-bs-dismiss="modal">{{ trans('doctor_trans.close') }}</button>
                            <button type="button" class="btn btn-success submit-form-add_medication_duration"
                                style="background:green" id="create_new">{{ trans('doctor_trans.save') }}</button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="add_medication_taken" tabindex="-1" aria-labelledby="exampleModalgridLabel"
    aria-modal="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalgridLabel">
                    {{ trans('doctor_trans.add_new_medication_taken') }}
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form class="mt-3" id="form-add_medication_taken">
                    @csrf
                    <div class="form-group">
                        <label for="firstName"
                            class="form-label">{{ trans('doctor_trans.add_new_medication_taken_d') }}</label>
                        <input type="text" class="form-control" id="name_add_medication_taken"
                            name="name_add_medication_taken"
                            placeholder="{{ trans('doctor_trans.enter_add_medication_taken') }}">
                    </div>
                    <div class="col-lg-12">
                        <div class="hstack gap-2 justify-content-end">
                            <button type="button" class="btn btn-light"
                                data-bs-dismiss="modal">{{ trans('doctor_trans.close') }}</button>
                            <button type="button" class="btn btn-success submit-form-add_medication_taken"
                                style="background:green" id="create_new">{{ trans('doctor_trans.save') }}</button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</div>
