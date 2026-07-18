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
                <div class="overflow-auto">
                    <ul class="nav nav-pills animation-nav nav-justified gap-2 mb-3 flex-nowrap" id="bookingTabs"
                        role="tablist" style="flex-wrap: nowrap;">
                        @foreach ([
            // 🟦 البداية - الأساسيات
            'details' => ['icon' => 'mdi-information-outline', 'label' => trans('surgical_trans.operation_details')],

            // 🟦 عوامل أمان قبل العملية
            'allargy' => ['icon' => 'mdi-file-alert', 'label' => trans('surgical_trans.allargy')],
            'prescriptions' => ['icon' => 'mdi-pill', 'label' => trans('surgical_trans.prescriptions')],
            'timeout' => ['icon' => 'mdi-time', 'label' => trans('surgical_trans.timeout')],

            // 🟦 الفحوصات والنتائج
            'examinations' => ['icon' => 'mdi-stethoscope', 'label' => trans('surgical_trans.examinations')],

            // 🟦 التوثيق الجراحي
            'OperativeProcedureReport' => ['icon' => 'mdi-stethoscope', 'label' => trans('surgical_trans.OperativeProcedureReport')],

            // 🟦 ما بعد العملية
            'dischargesummary' => ['icon' => 'mdi-stethoscope', 'label' => trans('surgical_trans.dischargesummary')],
            'postoperativeplan' => ['icon' => 'mdi-stethoscope', 'label' => trans('surgical_trans.postoperativeplan')],
            'sheets' => ['icon' => 'mdi-file-table-outline', 'label' => 'Surgical Sheets'],

            // 🟦 التوثيق والدعم
            'medical_reports' => ['icon' => 'mdi-file-document-multiple-outline', 'label' => trans('surgical_trans.medical_reports')],
            'notes2' => ['icon' => 'mdi-note-text-outline', 'label' => trans('surgical_trans.notes')],
            'documents' => ['icon' => 'mdi-file-document-outline', 'label' => trans('surgical_trans.documents')],
        ] as $id => $tab)
                            <li class="nav-item" style="flex: 0 0 auto;">
                                <button class="nav-link {{ $loop->first ? 'active' : '' }}" data-bs-toggle="tab"
                                    data-bs-target="#{{ $id }}" type="button" role="tab">
                                    <i class="mdi {{ $tab['icon'] }} me-1"></i>{{ $tab['label'] }}
                                </button>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>

            <div class="card-body">
                <div class="tab-content">
                    <!-- ✅ Operation Details -->
                    <div class="tab-pane fade show active" id="details" role="tabpanel">
                        <livewire:surgical.surgical-doctorditiel :booking="$booking" />
                    </div>

                    <!-- ✅ Documents -->
                    <div class="tab-pane fade" id="documents" role="tabpanel">
                        @livewire('surgical.files-upload', ['surgery_booking_id' => $booking->id])
                    </div>

                    <!-- ✅ Notes -->
                    <div class="tab-pane fade" id="notes2" role="tabpanel">
                        @livewire('surgical.notes', ['surgery_booking_id' => $booking->id])
                    </div>
                    <div class="tab-pane fade" id="sheets" role="tabpanel">
                        @livewire(
                            'medical.sheets.sheet-loader',
                            [
                                'patient_id' => $booking->patient->id,
                                'reservationId' => $booking->id,
                            ],
                            key('sheet-loader-' . $booking->id)
                        )
                    </div>


                    <!-- ✅ Examinations -->
                    <div class="tab-pane fade" id="examinations" role="tabpanel">
                        @livewire('doctor-order-eye', [
                            'patientId' => $booking->patient->id,
                            'reservationId' => $booking->id,
                            'type_visit' => 'surgical',
                            'visitId' => $booking->visit_id ?? null,
                        ])
                    </div>
                    <div class="tab-pane fade" id="OperativeProcedureReport" role="tabpanel">
                        @livewire('surgical.operative-procedure-form', [
                            'patient_id' => $booking->patient->id,
                            'reservationId' => $booking->id,
                            'type_visit' => 'surgical',
                            'visitId' => $booking->visit_id ?? null,
                        ])
                    </div>
                    <div class="tab-pane fade" id="dischargesummary" role="tabpanel">
                        @livewire('surgical.discharge-summary-form', [
                            'patient_id' => $booking->patient->id,
                            'reservationId' => $booking->id,
                            'type_visit' => 'surgical',
                            'visitId' => $booking->visit_id ?? null,
                        ])
                    </div>
                    <div class="tab-pane fade" id="postoperativeplan" role="tabpanel">
                        @livewire('surgical.post-operative-plan', [
                            'patient_id' => $booking->patient->id,
                            'reservationId' => $booking->id,
                            'type_visit' => 'surgical',
                            'visitId' => $booking->visit_id ?? null,
                        ])
                    </div>
                    <div class="tab-pane fade" id="timeout" role="tabpanel">
                        <livewire:surgical.surgery-timeout-form :booking_id="$booking->id" />

                    </div>
                    <div class="tab-pane fade" id="allargy" role="tabpanel">
                        @livewire('allergy', ['id_patient' => $patient->id, 'id_reservation' => $booking->id, 'type_visit' => '3', 'id_visit' => $booking->visit_id ?? null])

                    </div>
                    <!-- ✅ Prescriptions -->
                    <div class="tab-pane fade" id="prescriptions" role="tabpanel">
                        @livewire('doctor-prescription', [
                            'patientId' => $booking->patient->id,
                            'reservationId' => $booking->id,
                            'type_visit' => 'surgical',
                            'id_visit' => $booking->visit_id ?? null,
                        ])
                    </div>

                    <!-- ✅ Medical Reports -->
                    <div class="tab-pane fade" id="medical_reports" role="tabpanel">
                        @livewire('medical-report-form', [
                            'id_patient' => $booking->patient->id,
                            'id_reservation' => $booking->id,
                            'type_visit' => 'surgical',
                            'id_visit' => $booking->visit_id ?? null,
                        ])
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
