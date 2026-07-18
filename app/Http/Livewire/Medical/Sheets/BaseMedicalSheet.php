<?php

namespace App\Http\Livewire\Medical\Sheets;

use Livewire\Component;
use App\Models\Medical\MedicalSheet;
use Illuminate\Support\Facades\Auth;

class BaseMedicalSheet extends Component
{
    public $sheet_type;
    public $data = [];

    public function mount($sheet_type)
    {
        $this->sheet_type = $sheet_type;
    }

    public function save()
    {
        $validatedData = $this->validate([
            'data' => 'array'
        ]);

        MedicalSheet::create([
            'sheet_type' => $this->sheet_type,
            'data' => json_encode($this->data),
            'user_id' => Auth::id(),
        ]);

        session()->flash('success', "{$this->sheet_type} sheet saved successfully!");
    }

    public function render()
    {
        $viewName = strtolower(str_replace(' ', '-', $this->sheet_type));
        return view("livewire.medical.sheets.$viewName");
    }
}