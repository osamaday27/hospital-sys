<?php
namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use App\models\Patient;
use App\models\Visit;
use App\models\AngleExaminationImage;

class AngleExamination extends Component
{
    use WithFileUploads;

    public $image;
    public $patient_id;
    public $visit_id;

    protected $rules = [
        'image' => 'required|image|max:2048', // الحد الأقصى 2 ميغابايت
        'patient_id' => 'required|exists:patients,id',
        'visit_id' => 'required|exists:visits,id'
    ];

    public function saveImage()
    {
        $this->validate();

        $imagePath = $this->image->store('angle_examinations', 'public');

        AngleExaminationImage::create([
            'patient_id' => $this->patient_id,
            'visit_id' => $this->visit_id,
            'image_path' => $imagePath
        ]);

        session()->flash('message', 'Image saved successfully!');
    }

    public function render()
    {
        return view('livewire.angle-examination');
    }
}