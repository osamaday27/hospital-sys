<div class="tab-pane fade" id="assessmentform" role="tabpanel">
    <div class="row">
        <div class="col-md-12">
            <div class="card border-0 shadow-lg">
                <div class="card-header bg-gradient-primary text-white py-3">
                    <h4 class="mb-0"><i class="fas fa-user-md mr-2"></i>{{ __('doctor_trans.examineing') }}</h4>
                </div>
                <div class="card-body">
                    <style>
                        .form-container {
                            background-color: #f8fafc;
                            padding: 25px;
                            border-radius: 10px;
                            box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
                            margin-bottom: 25px;
                            border: 1px solid #e2e8f0;
                        }

                        .section-title {
                            font-size: 18px;
                            font-weight: 600;
                            color: #2d3748;
                            margin-top: 25px;
                            margin-bottom: 15px;
                            padding-bottom: 8px;
                            border-bottom: 2px solid #4299e1;
                            position: relative;
                        }

                        .section-title:after {
                            content: "";
                            position: absolute;
                            bottom: -2px;
                            left: 0;
                            width: 100px;
                            height: 2px;
                            background: #3182ce;
                        }

                        .form-table {
                            width: 100%;
                            border-collapse: separate;
                            border-spacing: 0;
                            margin-bottom: 20px;
                            border-radius: 8px;
                            overflow: hidden;
                            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
                        }

                        .form-table th {
                            background-color: #4299e1;
                            color: white;
                            font-weight: 500;
                            padding: 12px 15px;
                            text-align: right;
                            width: 25%;
                        }

                        .form-table td {
                            background-color: white;
                            padding: 12px 15px;
                            border: 1px solid #e2e8f0;
                        }

                        .checkbox-group {
                            display: flex;
                            align-items: center;
                            margin-bottom: 8px;
                            padding: 5px;
                            border-radius: 4px;
                            transition: all 0.2s;
                        }

                        .checkbox-group:hover {
                            background-color: #ebf8ff;
                        }

                        .checkbox-group label {
                            margin-right: 8px;
                            cursor: pointer;
                            color: #4a5568;
                        }

                        .checkbox-group input[type="checkbox"] {
                            transform: scale(1.2);
                            margin-left: 8px;
                            cursor: pointer;
                        }

                        .form-control-custom {
                            border: 1px solid #cbd5e0;
                            border-radius: 6px;
                            padding: 8px 12px;
                            transition: all 0.3s;
                            width: 100%;
                        }

                        .form-control-custom:focus {
                            border-color: #4299e1;
                            box-shadow: 0 0 0 3px rgba(66, 153, 225, 0.2);
                            outline: none;
                        }

                        .note {
                            font-size: 13px;
                            color: #718096;
                            font-style: italic;
                            margin-top: 5px;
                        }

                        .card-section {
                            margin-bottom: 25px;
                            border: none;
                            border-radius: 10px;
                            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
                        }

                        .card-section .card-header {
                            background-color: #edf2f7;
                            border-bottom: 1px solid #e2e8f0;
                            font-weight: 600;
                            color: #2d3748;
                            padding: 12px 20px;
                        }

                        .card-section .card-body {
                            padding: 20px;
                        }

                        .form-check-label {
                            color: #4a5568;
                        }

                        .form-check-input {
                            transform: scale(1.1);
                            margin-left: 5px;
                        }

                        .btn-save {
                            background-color: #4299e1;
                            border: none;
                            padding: 10px 25px;
                            font-weight: 500;
                            letter-spacing: 0.5px;
                            transition: all 0.3s;
                        }

                        .btn-save:hover {
                            background-color: #3182ce;
                            transform: translateY(-2px);
                            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                        }

                        h2.form-title {
                            color: #2d3748;
                            font-weight: 600;
                            text-align: center;
                            margin-bottom: 30px;
                            position: relative;
                            padding-bottom: 15px;
                        }

                        h2.form-title:after {
                            content: "";
                            position: absolute;
                            bottom: 0;
                            left: 50%;
                            transform: translateX(-50%);
                            width: 100px;
                            height: 3px;
                            background: linear-gradient(to right, #4299e1, #38b2ac);
                        }

                        .radio-group {
                            display: flex;
                            align-items: center;
                            margin-bottom: 10px;
                        }

                        .radio-group label {
                            margin-left: 10px;
                            margin-right: 20px;
                            cursor: pointer;
                        }

                        .nutrition-table th {
                            background-color: #e2e8f0;
                            color: #2d3748;
                            font-weight: 600;
                        }

                        .nutrition-table td {
                            text-align: center;
                            vertical-align: middle;
                        }

                        .nutrition-table .form-control {
                            border-radius: 4px;
                            border: 1px solid #cbd5e0;
                        }
                    </style>

                    <h2 class="form-title">
                        <i class="fas fa-file-medical-alt mr-2"></i>
                        نموذج الفحص الاجتماعي الاقتصادي والثقافي والنفسي
                    </h2>
                    @livewire('medical.nursing.patient-assessment-tabs', [
                        'patientId'     => $patient->id,
                        'visitId'       => $visit->id ?? null,
                        'reservationId' => $reservation->id ?? null,
                    ])
                    
                </div>
            </div>
        </div>
    </div>
</div>