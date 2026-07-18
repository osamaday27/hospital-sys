<?php

namespace App\Http\Livewire\Administrative;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\administrative\AbsenceReason;

class AbsenceReasons extends Component
{
    use WithPagination;

    public $name_ar, $name_en, $status = true, $reasonId;
    public $updateMode = false;

    protected $rules = [
        'name_ar' => 'required|string|max:255',
        'name_en' => 'nullable|string|max:255',
        'status'  => 'boolean',
    ];

    public function render()
    {
        return view('livewire.administrative.absence-reasons', [
            'reasons' => AbsenceReason::latest()->paginate(10)
        ])->extends('layouts.masterliveware')
          ->section('content');
    }

    public function resetInput()
    {
        $this->name_ar = '';
        $this->name_en = '';
        $this->status = true;
        $this->reasonId = null;
        $this->updateMode = false;
    }

    public function store()
    {
        $this->validate();
        AbsenceReason::create([
            'name_ar' => $this->name_ar,
            'name_en' => $this->name_en,
            'status'  => $this->status,
        ]);
        session()->flash('success', 'تمت إضافة السبب بنجاح');
        $this->resetInput();
    }

    public function edit($id)
    {
        $reason = AbsenceReason::findOrFail($id);
        $this->reasonId = $reason->id;
        $this->name_ar = $reason->name_ar;
        $this->name_en = $reason->name_en;
        $this->status = $reason->status;
        $this->updateMode = true;
    }

    public function update()
    {
        $this->validate();
        $reason = AbsenceReason::findOrFail($this->reasonId);
        $reason->update([
            'name_ar' => $this->name_ar,
            'name_en' => $this->name_en,
            'status'  => $this->status,
        ]);
        session()->flash('success', 'تم تعديل السبب بنجاح');
        $this->resetInput();
    }

    public function delete($id)
    {
        AbsenceReason::findOrFail($id)->delete();
        session()->flash('success', 'تم حذف السبب');
    }
}