<?php
// app/Http/Livewire/surgical/PostAnesthesiaRecoveryForm.php

namespace App\Http\Livewire\surgical;

use Livewire\Component;
use App\Models\medical\PostAnesthesiaRecovery;
use Illuminate\Support\Facades\Auth;

class PostAnesthesiaRecoveryForm extends Component
{
    public $surgery_booking_id;
    public $record_id;

    // كل الحقول
    public $procedure_done, $recovery_time_start, $recovery_time_end;
    public $surgeon_name, $anesthesiologist_name;
    public $anesthesia_type = [], $sedation_type;
    public $status_vitals = [], $level_of_consciousness;
    public $lines_connected, $lines_description;
    public $nurse_followup = [], $medications = [], $fluids = [];
    public $unusual_events;
    public $transfer_status = [];
    public $discharge_criteria_met, $discharge_location, $discharge_time;
    public $aldrete_score = [], $followup_instructions = [];

    protected $rules = [
        'procedure_done' => 'nullable|string',
        'recovery_time_start' => 'nullable',
        'recovery_time_end' => 'nullable',
        'surgeon_name' => 'nullable|string',
        'anesthesiologist_name' => 'nullable|string',
        'anesthesia_type' => 'nullable|array',
        'sedation_type' => 'nullable|string',
        'status_vitals' => 'nullable|array',
        'level_of_consciousness' => 'nullable|string',
        'lines_connected' => 'nullable|boolean',
        'lines_description' => 'nullable|string',
        'nurse_followup' => 'nullable|array',
        'medications' => 'nullable|array',
        'fluids' => 'nullable|array',
        'unusual_events' => 'nullable|string',
        'transfer_status' => 'nullable|array',
        'discharge_criteria_met' => 'nullable|boolean',
        'discharge_location' => 'nullable|string',
        'discharge_time' => 'nullable',
        'aldrete_score' => 'nullable|array',
        'followup_instructions' => 'nullable|array',
    ];

    private function initializeArrays()
    {
        // Initialize arrays with empty values
        $this->status_vitals = array_fill(0, 4, [
            'time' => '',
            'hr' => '',
            'bp' => '',
            'rr' => '',
            'temp' => '',
            'spo2' => ''
        ]);

        $this->nurse_followup = array_fill(0, 3, [
            'time' => '',
            'hr' => '',
            'bp' => '',
            'rr' => '',
            'temp' => '',
            'spo2' => '',
            'pain_score' => '',
            'consciousness' => ''
        ]);

        $this->medications = array_fill(0, 3, [
            'name' => '',
            'dose' => '',
            'route' => '',
            'physician' => '',
            'time' => '',
            'double_check' => '',
            'nurse' => ''
        ]);

        $this->fluids = array_fill(0, 2, [
            'name' => '',
            'volume' => '',
            'additive' => '',
            'rate' => '',
            'physician' => '',
            'start_time' => '',
            'double_check' => '',
            'nurse' => '',
            'end_time' => ''
        ]);

        $this->transfer_status = array_fill(0, 2, [
            'time' => '',
            'hr' => '',
            'bp' => '',
            'rr' => '',
            'temp' => '',
            'spo2' => ''
        ]);

        $this->aldrete_score = [
            'activity' => 0,
            'respiration' => 0,
            'circulation' => 0,
            'consciousness' => 0,
            'color' => 0
        ];

        $this->followup_instructions = [
            'npo' => '',
            'vitals' => '',
            'oxygen' => '',
            'no_standing' => 0,
            'no_driving' => 0,
            'emergency_contact' => '',
            'anesthesiologist_name' => '',
            'anesthesiologist_date' => '',
            'anesthesiologist_time' => '',
            'nurse_name' => '',
            'nurse_date' => '',
            'nurse_time' => ''
        ];
    }

    private function loadRecord($record)
    {
        // Load array fields directly since they are already cast in the model
        $this->anesthesia_type = $record->anesthesia_type ?? [];
        $this->status_vitals = $record->status_vitals ?? $this->status_vitals;
        $this->nurse_followup = $record->nurse_followup ?? $this->nurse_followup;
        $this->medications = $record->medications ?? $this->medications;
        $this->fluids = $record->fluids ?? $this->fluids;
        $this->transfer_status = $record->transfer_status ?? $this->transfer_status;
        $this->aldrete_score = $record->aldrete_score ?? $this->aldrete_score;
        $this->followup_instructions = $record->followup_instructions ?? $this->followup_instructions;

        // Fill other fields
        $this->procedure_done = $record->procedure_done;
        $this->recovery_time_start = $record->recovery_time_start;
        $this->recovery_time_end = $record->recovery_time_end;
        $this->surgeon_name = $record->surgeon_name;
        $this->anesthesiologist_name = $record->anesthesiologist_name;
        $this->sedation_type = $record->sedation_type;
        $this->level_of_consciousness = $record->level_of_consciousness;
        $this->lines_connected = $record->lines_connected;
        $this->lines_description = $record->lines_description;
        $this->unusual_events = $record->unusual_events;
        $this->discharge_criteria_met = $record->discharge_criteria_met;
        $this->discharge_location = $record->discharge_location;
        $this->discharge_time = $record->discharge_time;
    }

    public function mount($surgery_booking_id, $record_id = null)
    {
        $this->surgery_booking_id = $surgery_booking_id;
        $this->record_id = $record_id;

        // Initialize arrays with empty values
        $this->initializeArrays();

        if ($record_id) {
            // Load specific record
            $record = PostAnesthesiaRecovery::find($record_id);
            if ($record) {
                $this->loadRecord($record);
            }
        } else {
            // Load latest record for this surgery booking
            $latestRecord = PostAnesthesiaRecovery::where('surgery_booking_id', $surgery_booking_id)
                ->latest()
                ->first();
            
            if ($latestRecord) {
                $this->record_id = $latestRecord->id;
                $this->loadRecord($latestRecord);
            }
        }
    }

    public function save()
    {
        $data = $this->validate();

        // Convert arrays to JSON for storage
        $data['anesthesia_type'] = json_encode($this->anesthesia_type);
        $data['status_vitals'] = json_encode($this->status_vitals);
        $data['nurse_followup'] = json_encode($this->nurse_followup);
        $data['medications'] = json_encode($this->medications);
        $data['fluids'] = json_encode($this->fluids);
        $data['transfer_status'] = json_encode($this->transfer_status);
        $data['aldrete_score'] = json_encode($this->aldrete_score);
        $data['followup_instructions'] = json_encode($this->followup_instructions);

        $data['surgery_booking_id'] = $this->surgery_booking_id;

        if ($this->record_id) {
            PostAnesthesiaRecovery::where('id', $this->record_id)->update($data);
            session()->flash('success', 'Record updated successfully');
        } else {
            PostAnesthesiaRecovery::create($data);
            session()->flash('success', 'Record saved successfully');
        }
    }

    public function render()
    {
        return view('livewire.surgical.post-anesthesia-recovery-form');
    }
}