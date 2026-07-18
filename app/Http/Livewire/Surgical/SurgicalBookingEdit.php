<?php
namespace App\Http\Livewire\Surgical;

use Livewire\Component;
use App\Models\Finance\Service;
use App\Models\General\adm_s_rooms_surgical;
use App\Models\Administrative\adm_b_surgery_bookings;
use App\Models\Administrative\surgery_medical_team;
use App\Models\Medical\ms_diseases_list;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use App\models\hr\employee;

class SurgicalBookingEdit extends Component
{
    public $booking;
    public $services;
    public $rooms;
    public $diagnoses;
    public $roles = ['surgeon', 'assistant_surgeon', 'anesthesiologist', 'nurse', 'other'];
    public $team_members = [];  // Declare the team_members property

    public $surgical_id, $id_room, $procedure_side, $booking_date, $time_slots, $diagnoses_ids, $custom_diagnosis;
    public $availableEmployees = [];  // Declare available employees to populate select dropdown

    public function mount($encryptedBookingId)
    {
        $this->booking = adm_b_surgery_bookings::findOrFail(Crypt::decryptString($encryptedBookingId));
        $this->services = Service::all();
        $this->rooms = adm_s_rooms_surgical::where('status', 'available')->get();
        $this->diagnoses = ms_diseases_list::all();

        // Set initial data from the booking
        $this->surgical_id = $this->booking->surgical_id;
        $this->id_room = $this->booking->room_id;
        $this->procedure_side = $this->booking->procedure_side;
        $this->booking_date = $this->booking->booking_date;
        $this->time_slots = explode(',', $this->booking->scheduled_from);
        $this->diagnoses_ids = $this->booking->diagnoses()->pluck('m_s_diseases_list.id')->toArray();
        $this->custom_diagnosis = $this->booking->custom_diagnosis;

        // Fetch available employees (you can modify this based on your logic)
        $this->availableEmployees = employee::all(); // Fetch all available employees for roles

        // Initialize team_members for each role
        foreach ($this->roles as $role) {
            $this->team_members[$role] = surgery_medical_team::where('surgery_bookings_id', $this->booking->id)
                ->where('role', $role)
                ->pluck('medical_team_id')
                ->toArray();
        }
    }

    public function render()
    {
        return view('livewire.surgical.surgical-booking-edit');
    }
}