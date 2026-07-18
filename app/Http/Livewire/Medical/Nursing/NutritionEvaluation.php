<?php

namespace App\Http\Livewire\Medical\Nursing;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Models\Medical\NutritionalScreening;

class NutritionEvaluation extends Component
{
    use WithPagination;

    public $patient_id, $visit_id, $reservation_id;
    public $special_diet;
    public $sugar_high = false,
    $sugar_low  = false,
    $sugar_normal = false;

    public $salt_high   = false,
    $salt_low    = false,
    $salt_normal = false;

public $protein_high   = false,
    $protein_low    = false,
    $protein_normal = false;

// وهكذا ...

    public $fat_high=false; 
    public  $fat_low=false;    
    public $fat_normal=false;

    public $nutrition_normal=false, $nutrition_nausea=false, $nutrition_vomiting=false,
           $nutrition_weight_loss=false, $nutrition_dysphagia=false;

    public $mouth_normal=false, $mouth_dry=false, $mouth_inflammation=false;
    public $teeth_normal=false, $teeth_loss=false, $teeth_denture=false;
    public $women_na=false, $pregnant=false, $breastfeeding=false;
    public $elderly_na=false, $elderly_over70=false;
    public $risk_low=false, $risk_high=false;

    public $notify_doctor_yes=false, $notify_doctor_no=false;

    public $selectedRecord;

    protected $rules = [
        'special_diet' => 'nullable|string|max:255',
    ];

    public function mount($patientId, $visitId=null, $reservationId=null)
    {
        $this->patient_id     = $patientId;
        $this->visit_id       = $visitId;
        $this->reservation_id = $reservationId;
    }

    public function save()
    {
        $this->validate();

        NutritionalScreening::create(array_merge([
            'patient_id'=>$this->patient_id,
            'visit_id'=>$this->visit_id,
            'reservation_id'=>$this->reservation_id,
            'user_add'=>Auth::id(),
        ], array_filter([
            'special_diet'=>$this->special_diet,
            'sugar_high'=>$this->sugar_high,'sugar_low'=>$this->sugar_low,'sugar_normal'=>$this->sugar_normal,
            'salt_high'=>$this->salt_high,'salt_low'=>$this->salt_low,'salt_normal'=>$this->salt_normal,
            'protein_high'=>$this->protein_high,'protein_low'=>$this->protein_low,'protein_normal'=>$this->protein_normal,
            'fat_high'=>$this->fat_high,'fat_low'=>$this->fat_low,'fat_normal'=>$this->fat_normal,
            'nutrition_normal'=>$this->nutrition_normal,'nutrition_nausea'=>$this->nutrition_nausea,
            'nutrition_vomiting'=>$this->nutrition_vomiting,'nutrition_weight_loss'=>$this->nutrition_weight_loss,
            'nutrition_dysphagia'=>$this->nutrition_dysphagia,
            'mouth_normal'=>$this->mouth_normal,'mouth_dry'=>$this->mouth_dry,'mouth_inflammation'=>$this->mouth_inflammation,
            'teeth_normal'=>$this->teeth_normal,'teeth_loss'=>$this->teeth_loss,'teeth_denture'=>$this->teeth_denture,
            'women_na'=>$this->women_na,'pregnant'=>$this->pregnant,'breastfeeding'=>$this->breastfeeding,
            'elderly_na'=>$this->elderly_na,'elderly_over70'=>$this->elderly_over70,
            'risk_low'=>$this->risk_low,'risk_high'=>$this->risk_high,
            'notify_doctor_yes'=>$this->notify_doctor_yes,'notify_doctor_no'=>$this->notify_doctor_no,
        ], function($v){ return !is_null($v); })));

        session()->flash('message','تم الحفظ بنجاح');
        $this->cancelEdit();
    }

    public function viewRecord($id)
    {
        $r = NutritionalScreening::findOrFail($id);
        $this->selectedRecord = $r;

        foreach([
            'special_diet','sugar_high','sugar_low','sugar_normal',
            'salt_high','salt_low','salt_normal','protein_high','protein_low','protein_normal',
            'fat_high','fat_low','fat_normal','nutrition_normal','nutrition_nausea',
            'nutrition_vomiting','nutrition_weight_loss','nutrition_dysphagia',
            'mouth_normal','mouth_dry','mouth_inflammation','teeth_normal','teeth_loss','teeth_denture',
            'women_na','pregnant','breastfeeding','elderly_na','elderly_over70','risk_low','risk_high',
            'notify_doctor_yes','notify_doctor_no'
        ] as $fld) {
            $this->{$fld} = $r->{$fld};
        }
    }

    public function update()
    {
        $this->validate();

        $this->selectedRecord->update([
            'special_diet'=>$this->special_diet,
            'sugar_high'=>$this->sugar_high,'sugar_low'=>$this->sugar_low,'sugar_normal'=>$this->sugar_normal,
            'salt_high'=>$this->salt_high,'salt_low'=>$this->salt_low,'salt_normal'=>$this->salt_normal,
            'protein_high'=>$this->protein_high,'protein_low'=>$this->protein_low,'protein_normal'=>$this->protein_normal,
            'fat_high'=>$this->fat_high,'fat_low'=>$this->fat_low,'fat_normal'=>$this->fat_normal,
            'nutrition_normal'=>$this->nutrition_normal,'nutrition_nausea'=>$this->nutrition_nausea,
            'nutrition_vomiting'=>$this->nutrition_vomiting,'nutrition_weight_loss'=>$this->nutrition_weight_loss,
            'nutrition_dysphagia'=>$this->nutrition_dysphagia,
            'mouth_normal'=>$this->mouth_normal,'mouth_dry'=>$this->mouth_dry,'mouth_inflammation'=>$this->mouth_inflammation,
            'teeth_normal'=>$this->teeth_normal,'teeth_loss'=>$this->teeth_loss,'teeth_denture'=>$this->teeth_denture,
            'women_na'=>$this->women_na,'pregnant'=>$this->pregnant,'breastfeeding'=>$this->breastfeeding,
            'elderly_na'=>$this->elderly_na,'elderly_over70'=>$this->elderly_over70,
            'risk_low'=>$this->risk_low,'risk_high'=>$this->risk_high,
            'notify_doctor_yes'=>$this->notify_doctor_yes,'notify_doctor_no'=>$this->notify_doctor_no,
        ]);

        session()->flash('message','تم التعديل بنجاح');
        $this->cancelEdit();
    }

    public function cancelEdit()
    {
        $this->selectedRecord=null;
        $this->reset([
            'special_diet','sugar_high','sugar_low','sugar_normal',
            'salt_high','salt_low','salt_normal','protein_high','protein_low','protein_normal',
            'fat_high','fat_low','fat_normal','nutrition_normal','nutrition_nausea',
            'nutrition_vomiting','nutrition_weight_loss','nutrition_dysphagia',
            'mouth_normal','mouth_dry','mouth_inflammation','teeth_normal','teeth_loss','teeth_denture',
            'women_na','pregnant','breastfeeding','elderly_na','elderly_over70','risk_low','risk_high',
            'notify_doctor_yes','notify_doctor_no'
        ]);
    }

    public function render()
    {
        $records = NutritionalScreening::with('user')->latest()->paginate(10);
        return view('livewire.medical.nursing.nutrition-evaluation',compact('records'));
    }
}