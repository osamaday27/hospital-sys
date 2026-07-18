@extends('layouts.masterliveware')

@section('title', trans('surgical_trans.booking_details'))

@section('content')
    <div class="container-fluid py-4">

        <!-- 🟢 Patient Info Card -->
        <div class="card border-primary mb-4">
            <div class="card-header bg-info text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0" style="color: #fff;">
                    <i class="mdi mdi-account-heart-outline me-2"></i>{{ trans('surgical_trans.patient_info') }}
                </h4>
             
            </div>
            <div class="col-md-4">
                @livewire('alertallergy', ['id_patient' => $patient->id])
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <!-- Personal Info -->
                    <div class="col-md-4">
                        <div class="info-item">
                            <i class="mdi mdi-account-outline text-primary me-2"></i>
                            <strong>{{ trans('patient_trans.name') }}:</strong>
                            <span>{{ $booking->patient->full_name }}</span>
                        </div>
                        <div class="info-item">
                            <i class="mdi mdi-identifier text-primary me-2"></i>
                            <strong>{{ trans('patient_trans.medical_number') }}:</strong>
                            <span>{{ $booking->patient->medical_number }}</span>
                        </div>
                        <div class="info-item">
                            <i class="mdi mdi-numeric text-primary me-2"></i>
                            <strong>{{ trans('surgical_trans.booking_number') }}:</strong>
                            <span>#{{ $booking->id }}</span>
                        </div>
                        
                    </div>

                    <!-- Demographics -->
                    <div class="col-md-4">
                        <div class="info-item">
                            <i class="mdi mdi-cake-variant text-primary me-2"></i>
                            <strong>{{ trans('patient_trans.age') }}:</strong>
                            <span>{{ $booking->patient->age }} {{ trans('main_trans.years') }}</span>
                        </div>
                        <div class="info-item">
                            <i class="mdi mdi-gender-male-female text-primary me-2"></i>
                            <strong>{{ trans('patient_trans.gender') }}:</strong>
                            <span>{{ $booking->patient->gender == 'male' ? trans('patient_trans.male') : trans('patient_trans.female') }}</span>
                        </div>
                        <div class="info-item">
                            <i class="mdi mdi-card-account-details-outline text-primary me-2"></i>
                            <strong>{{ trans('patient_trans.id_number') }}:</strong>
                            <span>{{ $booking->patient->national_id }}</span>
                        </div>
                    </div>

                    <!-- Contact Info -->
                    <div class="col-md-4">
                        <div class="info-item">
                            <i class="mdi mdi-phone-outline text-primary me-2"></i>
                            <strong>{{ trans('patient_trans.phone') }}:</strong>
                            <span>{{ $booking->patient->phone_number ?? trans('main_trans.not_available') }}</span>
                        </div>
                        <div class="info-item">
                            <i class="mdi mdi-file-document-outline text-primary me-2"></i>
                            <strong>{{ trans('patient_trans.contract_number') }}:</strong>
                            <span>{{ $booking->patient->contract->name ?? trans('main_trans.not_available') }}</span>
                        </div>
                        <div class="info-item">
                            <i class="mdi mdi-map-marker-outline text-primary me-2"></i>
                            <strong>{{ trans('patient_trans.address') }}:</strong>
                            <span>{{ $booking->patient->full_address ?? trans('main_trans.not_available') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 🟢 Tabs -->
        <div class="card">
            <div class="card-header pb-0">
                <ul class="nav nav-pills animation-nav nav-justified gap-2 mb-3" id="bookingTabs" role="tablist">
                    @foreach ([
            'details' => ['icon' => 'mdi-information-outline', 'label' => trans('surgical_trans.operation_details')],
            'Anesthesia_consent' => ['icon' => 'mdi-information-outline', 'label' => trans('surgical_trans.Anesthesia_consent')],
            'Pre_Anesthesia_Assessment' => ['icon' => 'mdi-information-outline', 'label' => trans('surgical_trans.Pre_Anesthesia_Assessment')],
            'Anesthesia_Plan' => ['icon' => 'mdi-information-outline', 'label' => trans('surgical_trans.Anesthesia_Plan')],
            'Intraoperative_Anesthesia_Record' => ['icon' => 'mdi-information-outline', 'label' => trans('surgical_trans.Intraoperative_Anesthesia_Record')],
            'Post_Anesthesia' => ['icon' => 'mdi-information-outline', 'label' => trans('surgical_trans.Post_Anesthesia')],
           // 'allargy' => ['icon' => 'mdi-file-alert', 'label' => trans('surgical_trans.allargy')],

            //'vitals' => ['icon' => 'mdi-file-document-outline', 'label' => trans('surgical_trans.vitals')],
            'documents' => ['icon' => 'mdi-file-document-outline', 'label' => trans('surgical_trans.documents')],
            'notes1' => ['icon' => 'mdi-note-text-outline', 'label' => trans('surgical_trans.notes')],
        ] as $id => $tab)
                        <li class="nav-item">
                            <button class="nav-link {{ $loop->first ? 'active' : '' }}" data-bs-toggle="tab"
                                data-bs-target="#{{ $id }}" type="button" role="tab">
                                <i class="mdi {{ $tab['icon'] }} me-1"></i>{{ $tab['label'] }}
                            </button>
                        </li>
                    @endforeach
                </ul>
            </div>

            <div class="card-body">
                <div class="tab-content">
                    <!-- ✅ Operation Details -->
                    <div class="tab-pane fade show active" id="details" role="tabpanel">
                        <livewire:surgical.surgicalnursingdetails :booking="$booking" />
                    </div>

                    <!-- ✅ Medical Team -->
                    <div class="tab-pane fade" id="team" role="tabpanel">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="text-primary mb-0">
                                <i class="mdi mdi-account-group-outline me-1"></i>
                                {{ trans('surgical_trans.medical_team') }}
                            </h5>

                        </div>

                        <div class="card-body">
                            @livewire('surgical.surgerysedicalteam', ['booking_id' => $booking->id])
                        </div>
                    </div>


                    <!-- ✅ surgical Instruments -->
                    <div class="tab-pane fade" id="instruments" role="tabpanel">
                        @livewire('surgical.item-count-form', ['booking_id' => $booking->id])
                    </div>
                    <div class="tab-pane fade" id="Intraoperative_Anesthesia_Record" role="tabpanel">
                        @livewire('surgical.intraoperative-anesthesia-record-form', ['surgery_booking_id' => $booking->id])
                    </div>
                    <div class="tab-pane fade" id="Post_Anesthesia" role="tabpanel">
                        @livewire('surgical.post-anesthesia-recovery-form', ['surgery_booking_id' => $booking->id])
                    </div>
                    <div class="tab-pane fade" id="Anesthesia_consent" role="tabpanel">
                        @livewire('surgical.anesthesia-approval', ['booking_id' => $booking->id])
                    </div>
                    <div class="tab-pane fade" id="Anesthesia_Plan" role="tabpanel">
                        @livewire('surgical.anaesthesia-plan-form', ['surgery_booking_id' => $booking->id])
                    </div>
                    <!-- ✅ Medications & Supplies -->
                    <div class="tab-pane fade" id="Pre_Anesthesia_Assessment" role="tabpanel">
                        @livewire('surgical.pre-anesthesia-assessment-form', ['surgery_booking_id' => $booking->id])

                    </div>
                    <!-- ✅ Medications & Supplies -->
                    <div class="tab-pane fade" id="timeout" role="tabpanel">
                        <livewire:surgical.surgery-timeout-form :booking_id="$booking->id" />

                    </div>
                    <div class="tab-pane fade" id="preoperative_checklist" role="tabpanel">
                        <livewire:surgical.preoperative-checklist-form :booking_id="$booking->id" />
                    </div>
                    <div class="tab-pane fade" id="vitals" role="tabpanel">
                        @livewire('vital-signs.form', ['patient_id' => $patient->id,'age'=>$age, 'patient_visits_id' => $patient_visits->id ?? null])
                    </div>
                    <div class="tab-pane fade" id="allargy" role="tabpanel">
                        @livewire('allergy', ['id_patient' => $patient->id, 'id_reservation' => $booking->id, 'type_visit' => '3', 'id_visit' => $booking->visit_id ?? null])

                    </div>

                    <!-- ✅ Documents -->
                    <div class="tab-pane fade" id="documents" role="tabpanel">
                        @livewire('surgical.files-upload', ['surgery_booking_id' => $booking->id])

                    </div>

                    <!-- ✅ Notes -->
                    <div class="tab-pane fade" id="notes1" role="tabpanel">
                        @livewire('surgical.notes', ['surgery_booking_id' => $booking->id])
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .info-item {
            display: flex;
            align-items: center;
            margin-bottom: 12px;
        }

        .detail-card {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            overflow: hidden;
            margin-bottom: 20px;
        }

        .detail-header {
            background-color: #f8f9fa;
            padding: 12px 15px;
            border-bottom: 1px solid #e0e0e0;
            display: flex;
            align-items: center;
        }

        .detail-body {
            padding: 15px;
        }

        .detail-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px dashed #f0f0f0;
        }

        .detail-item:last-child {
            border-bottom: none;
        }

        .document-upload-area {
            background-color: #f8f9fa;
            border: 2px dashed #dee2e6;
            transition: all 0.3s;
        }

        .document-upload-area:hover {
            border-color: #0d6efd;
            background-color: #f0f7ff;
        }

        .timeline {
            position: relative;
            padding-left: 20px;
        }

        .timeline-item {
            position: relative;
            padding-bottom: 15px;
        }

        .timeline-point {
            position: absolute;
            left: -20px;
            top: 4px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background-color: #0d6efd;
        }

        .timeline-content {
            padding-left: 10px;
        }
    </style>
@endsection
