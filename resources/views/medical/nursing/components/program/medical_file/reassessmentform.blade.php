<div class="tab-pane fade" id="reassessmentform" role="tabpanel">
    <div class="row">
        <!-- فورم الإضافة -->
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary text-white">{{ __('doctor_trans.reassessmentform') }}</div>
                <div class="card-body">
                    <style>
                        h1 {
                            text-align: center;
                            color: #333;
                            margin-bottom: 30px;
                        }

                        .form-container {
                            background-color: #fff;
                            padding: 20px;
                            border-radius: 8px;
                            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
                        }

                        .section-title {
                            font-size: 18px;
                            font-weight: bold;
                            color: #555;
                            margin-top: 20px;
                            margin-bottom: 10px;
                            border-bottom: 2px solid #ddd;
                            padding-bottom: 5px;
                        }

                        table {
                            width: 100%;
                            border-collapse: collapse;
                            margin-bottom: 20px;
                        }

                        th,
                        td {
                            border: 1px solid #ddd;
                            padding: 10px;
                            text-align: right;
                        }

                        th {
                            background-color: #f2f2f2;
                            font-weight: bold;
                        }

                        input[type="text"],
                        input[type="date"],
                        input[type="time"],
                        select {
                            width: 100%;
                            padding: 8px;
                            border: 1px solid #ccc;
                            border-radius: 4px;
                            box-sizing: border-box;
                        }

                        input[type="checkbox"] {
                            margin-left: 10px;
                        }

                        .checkbox-group {
                            display: flex;
                            align-items: center;
                            margin-bottom: 10px;
                        }

                        .checkbox-group label {
                            margin-left: 5px;
                        }

                        .signature-section {
                            margin-top: 30px;
                        }

                        .signature-section h2 {
                            font-size: 16px;
                            color: #444;
                            margin-bottom: 10px;
                        }

                        .signature-section input {
                            width: 200px;
                        }

                        .note {
                            font-size: 14px;
                            color: #777;
                            margin-top: 10px;
                        }
                    </style>
                    <h1>نموذج الفحص الاجتماعي الاقتصادي والثقافي والنفسي</h1>
                  
                    @livewire('medical.nursing.reassessment-tabs', [
                        'patientId'     => $patient->id,
                        'visitId'       => $visit->id ?? null,
                        'reservationId' => $reservation->id ?? null,
                    ])

                </div>
            </div>
        </div>


    </div>
</div>
