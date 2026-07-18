<?php

namespace App\Http\Livewire\surgical;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\Models\medical\IntraoperativeAnesthesiaRecord;
use Carbon\Carbon;
class IntraoperativeAnesthesiaRecordForm extends Component
{
    public $surgery_booking_id;

    public $patient_name, $medical_record, $age, $gender, $surgery_datetime;
    public $changed_from_pre_anesthesia, $gcs, $pulse, $bp, $spo2, $npo, $pre_assessment_time;

    public $monitoring_data = [];
    public $anesthesia_technique;

    public $fluid_in, $fluid_out, $urine, $blood_loss, $net_balance;
    public $medications = [
        'bolus' => [],
        'increments' => [],
        'total' => '',
    ];

    public $unusual_event, $post_op_condition, $bp_post, $hr_post, $rr_post;

    public $discharge_to, $signature_datetime, $anesthesiologist_signature;
    public $remarks = [];

    public function mount($surgery_booking_id)
    {
        $this->surgery_booking_id = $surgery_booking_id;
    
        $record = IntraoperativeAnesthesiaRecord::where('surgery_booking_id', $surgery_booking_id)->first();
    
        if ($record) {
            $this->patient_name              = $record->patient_name;
            $this->medical_record            = $record->medical_record;
            $this->age                       = $record->age;
            $this->gender                    = $record->gender;
            $this->surgery_datetime          = $record->surgery_datetime;
    
            $this->changed_from_pre_anesthesia = $record->changed_from_pre_anesthesia;
            $this->gcs                       = $record->gcs;
            $this->pulse                     = $record->pulse;
            $this->bp                        = $record->bp;
            $this->spo2                      = $record->spo2;
            $this->npo                       = $record->npo;
            $this->pre_assessment_time       = $record->pre_assessment_time;
    
            $this->monitoring_data           = json_decode($record->monitoring_data, true) ?? [];
            $this->anesthesia_technique      = $record->anesthesia_technique;
    
            $this->fluid_in                  = $record->fluid_in;
            $this->fluid_out                 = $record->fluid_out;
            $this->urine                     = $record->urine;
            $this->blood_loss                = $record->blood_loss;
            $this->net_balance               = $record->net_balance;
    
            $this->medications               = json_decode($record->medications, true) ?? [
                'bolus' => [],
                'increments' => [],
                'total' => '',
            ];
    
            $this->unusual_event             = $record->unusual_event;
            $this->post_op_condition         = $record->post_op_condition;
            $this->bp_post                   = $record->bp_post;
            $this->hr_post                   = $record->hr_post;
            $this->rr_post                   = $record->rr_post;
    
            $this->discharge_to              = $record->discharge_to;
            $this->signature_datetime        = $record->signature_datetime;
            $this->anesthesiologist_signature = $record->anesthesiologist_signature;
    
            $this->remarks                   = json_decode($record->remarks, true) ?? [];
        }
    }
    

  

    public function save()
    {
        $this->validate([
            'surgery_datetime' => 'nullable|date',
            'gcs' => 'nullable|string',
            'bp' => 'nullable|string',
            'pulse' => 'nullable|string',
            'spo2' => 'nullable|string',
            'npo' => 'nullable|string',
            'pre_assessment_time' => 'nullable',
            'anesthesia_technique' => 'nullable|string',
            'fluid_in' => 'nullable|string',
            'fluid_out' => 'nullable|string',
            'urine' => 'nullable|string',
            'blood_loss' => 'nullable|string',
            'net_balance' => 'nullable|string',
            'discharge_to' => 'nullable|string',
            'signature_datetime' => 'nullable',
            'anesthesiologist_signature' => 'nullable|string',
        ]);
    
        $record = IntraoperativeAnesthesiaRecord::where('surgery_booking_id', $this->surgery_booking_id)->first();
    
        // Format signature_datetime if not null
        $formatted_signature_datetime = $this->signature_datetime
            ? Carbon::parse($this->signature_datetime)->format('Y-m-d H:i:s')
            : null;
    
        $data = [
            'patient_name'                => $this->patient_name,
            'medical_record'             => $this->medical_record,
            'age'                        => $this->age,
            'gender'                     => $this->gender,
            'surgery_datetime'           => $this->surgery_datetime,
    
            'changed_from_pre_anesthesia'=> $this->changed_from_pre_anesthesia ?? 0,
            'gcs'                        => $this->gcs,
            'pulse'                      => $this->pulse,
            'bp'                         => $this->bp,
            'spo2'                       => $this->spo2,
            'npo'                        => $this->npo,
            'pre_assessment_time'        => $this->pre_assessment_time,
    
            'monitoring_data'            => json_encode($this->monitoring_data),
            'anesthesia_technique'       => $this->anesthesia_technique,
    
            'fluid_in'                   => $this->fluid_in,
            'fluid_out'                  => $this->fluid_out,
            'urine'                      => $this->urine,
            'blood_loss'                 => $this->blood_loss,
            'net_balance'                => $this->net_balance,
    
            'medications'                => json_encode($this->medications),
    
            'unusual_event'              => $this->unusual_event,
            'post_op_condition'          => $this->post_op_condition,
            'bp_post'                    => $this->bp_post,
            'hr_post'                    => $this->hr_post,
            'rr_post'                    => $this->rr_post,
    
            'discharge_to'               => $this->discharge_to,
            'signature_datetime'         => $formatted_signature_datetime,
            'anesthesiologist_signature' => $this->anesthesiologist_signature,
    
            'remarks'                    => json_encode($this->remarks),
        ];
    
        if ($record) {
            $record->update($data);
        } else {
            $data['surgery_booking_id'] = $this->surgery_booking_id;
            $data['created_by'] = Auth::id();
            IntraoperativeAnesthesiaRecord::create($data);
        }
    
        session()->flash('success', 'Record saved successfully');
    }
    

    public function render()
    {
        return view('livewire.surgical.intraoperative-anesthesia-record-form');
    }
    
}