<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use App\models\medical\m_d_colorvision;
use Illuminate\Support\Facades\Auth;

class ColorVision extends Component
{
    use WithPagination;

    public $od_colors = [],
        $os_colors = [],
        $od_comment,
        $os_comment;
    public $editingId = null,
        $search = '';
    public $id_patient, $id_reservation, $id_visit, $type_visit;
    protected $rules = [
        'od_colors' => 'nullable|array',
        'os_colors' => 'nullable|array',
        'od_comment' => 'nullable|string',
        'os_comment' => 'nullable|string',
    ];

    public function toggleColor($eye, $color)
    {
        if (in_array($color, $this->$eye)) {
            $this->$eye = array_diff($this->$eye, [$color]);
        } else {
            $this->$eye[] = $color;
        }
    }
    public function mount($id_patient, $id_reservation, $type_visit, $id_visit)
    {
        $this->id_patient = $id_patient;
        $this->id_reservation = $id_reservation;
        $this->type_visit = $type_visit;
        $this->id_visit = $id_visit;
    }
    public function save()
    {
        $this->validate();

        $data = [
            'id_patient' => $this->id_patient,
            'id_reservation' => $this->id_reservation,
            'id_visit' => $this->id_visit,
            'type_visit' => $this->type_visit,
            'od_colors' => json_encode($this->od_colors),
            'os_colors' => json_encode($this->os_colors),
            'od_comment' => $this->od_comment,
            'os_comment' => $this->os_comment,
            'created_by' => Auth::id(),
        ];

        if ($this->editingId) {
            m_d_colorvision::find($this->editingId)->update($data);
        } else {
            m_d_colorvision::create($data);
        }

        $this->resetInput();
    }

    public function edit($id)
    {
        $record = m_d_colorvision::find($id);
        $this->editingId = $id;
        $this->od_colors = json_decode($record->od_colors, true);
        $this->os_colors = json_decode($record->os_colors, true);
        $this->od_comment = $record->od_comment;
        $this->os_comment = $record->os_comment;
    }

    public function resetInput()
    {
        $this->reset(['od_colors', 'os_colors', 'od_comment', 'os_comment', 'editingId']);
    }
    public function getColor($color)
    {
        $colors = [
            'washed-red' => '#FF6F61', // لون أحمر فاتح
            'dark-red' => '#8B0000', // لون أحمر داكن
            'blue-yellow' => '#FFD700', // لون أصفر يميل للأزرق
            'red-green' => '#008000', // لون أخضر
        ];

        return $colors[$color] ?? '#ccc'; // لون افتراضي
    }

    public function render()
    {
        return view('livewire.color-vision', [
            'records' => m_d_colorvision::where('id_patient', $this->id_patient)->latest()->get(),
            'getColor' => function ($color) {
                return $this->getColor($color);
            },
        ]);
    }
}