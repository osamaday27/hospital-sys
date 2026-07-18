<?php

namespace App\Http\Livewire\Reception;

use Livewire\Component;
use Carbon\Carbon;
use App\Models\administrative\follow_up_request;

class FollowUpsList extends Component
{
    public $requests = [];

    // فلاتر
    public $search = '';
    public $from_date;
    public $to_date;

    public function mount()
    {
        $this->from_date = Carbon::today()->toDateString();
        $this->loadRequests();
    }

    public function updatedSearch()
    {
        $this->loadRequests();
    }

    public function updatedFromDate()
    {
        $this->loadRequests();
    }

    public function updatedToDate()
    {
        $this->loadRequests();
    }

    public function loadRequests()
    {
        $query = follow_up_request::with(['patient', 'doctor', 'clinic']);

        // فلترة بالبحث
        if (!empty($this->search)) {
            $query->whereHas('patient', function ($q) {
                $q->where('full_name', 'like', '%' . $this->search . '%');
            })->orWhereHas('doctor', function ($q) {
                $q->where('full_name', 'like', '%' . $this->search . '%');
            })->orWhereHas('clinic', function ($q) {
                $q->where('name_ar', 'like', '%' . $this->search . '%');
            });
        }

        // فلترة بالتاريخ
        if (!empty($this->from_date)) {
            $query->whereDate('followup_date', '>=', $this->from_date);
        }
        if (!empty($this->to_date)) {
            $query->whereDate('followup_date', '<=', $this->to_date);
        }

        $this->requests = $query->orderBy('followup_date')->get();
    }

    public function render()
    {
        return view('livewire.reception.follow-ups-list')
            ->extends('layouts.masterliveware')
            ->section('content');
    }
}