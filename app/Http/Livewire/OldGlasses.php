<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\models\medical\m_d_oldglasses;

class OldGlasses extends Component
{
    use WithPagination;

    public $od_bcva, $od_comment;
     public $os_sph = '0', $os_cyl = '0', $os_axis = '0', $os_near_add = '0',  $os_bcva, $os_comment;
    public $duration, $duration_unit;
    public $id_patient, $id_reservation, $id_visit, $type_visit;
    public $editingId = null;
    public $search = '';
    public $sortColumn = 'created_at';
    public $sortDirection = 'desc';
    public $od_sph = '0'; // القيمة الافتراضية
    public $sph_options = [];
    public $od_cyl = '0'; // القيمة الافتراضية
    public $cyl_options = [];
    public $od_axis = '0'; // القيمة الافتراضية
    public $axis_options = [];
    public $od_near_add = '0'; // القيمة الافتراضية
    public $near_add_options = [];
    public $bcva_options = ['20/20', '20/40', '20/60', '2.00', '1.5', '1.2', '1.00', '0.9', '0.8', '0.7', '0.6', '0.5', '0.3', '0.2', '0.15', '0.1', '0.05', 'CF 2m', 'CF 1.5m', 'CF 1m', 'CF 75cm', 'CF 50cm', 'CF 20cm', 'CF 10cm', 'HM- GP', 'HM- BP', 'PL- GP', 'PL- BP', 'No PL', 'Can Fix and Follow', 'Cannot be assessed'];

    protected $rules = [
        'od_sph' => 'nullable|string|max:10',
        'od_cyl' => 'nullable|string|max:10',
        'od_axis' => 'nullable|string|max:10',
        'od_near_add' => 'nullable|string|max:10',
        'od_bcva' => 'nullable|string|max:10',
        'od_comment' => 'nullable|string',
        'os_sph' => 'nullable|string|max:10',
        'os_cyl' => 'nullable|string|max:10',
        'os_axis' => 'nullable|string|max:10',
        'os_near_add' => 'nullable|string|max:10',
        'os_bcva' => 'nullable|string|max:10',
        'os_comment' => 'nullable|string',
        'duration' => 'nullable|integer|min:0',
        'duration_unit' => 'nullable|string|max:10',
        'id_patient' => 'required',
        'id_reservation' => 'required',
        'type_visit' => 'required',
        'id_visit' => 'required',
    ];

    public function mount($id_patient, $id_reservation, $type_visit, $id_visit)
    {
        $this->id_patient = $id_patient;
        $this->id_reservation = $id_reservation;
        $this->type_visit = $type_visit;
        $this->id_visit = $id_visit;
        $this->od_sph = '0';
        // ← القيمة الافتراضية
        $this->generateCylOptions();
        $this->generateSphOptions();
        $this->generateAxisOptions();
        $this->generateNearAddOptions();
    }
    public function generateNearAddOptions()
    {
        $values = [];

        for ($i = 0; $i <= 5; $i += 0.25) {
            $values[] = number_format($i, 2);
        }

        $this->near_add_options = $values;
    }
    public function generateAxisOptions()
    {
        $values = [];

        for ($i = 0; $i <= 180; $i += 5) {
            $values[] = $i;
        }

        $this->axis_options = array_map(function ($val) {
            return str_pad($val, 3, '0', STR_PAD_LEFT); // مثل: 005 - 010 - 180
        }, $values);
    }
    public function generateSphOptions()
    {
        $negatives = [];
        $positives = [];

        for ($i = 0.25; $i <= 8; $i += 0.25) {
            $negatives[] = -$i;
            $positives[] = $i;
        }

        for ($i = 8.5; $i <= 20; $i += 0.5) {
            $negatives[] = -$i;
            $positives[] = $i;
        }

        for ($i = 21; $i <= 45; $i++) {
            $negatives[] = -$i;
            $positives[] = $i;
        }

        sort($positives); // ترتيب تنازلي
        sort($negatives); // ترتيب تصاعدي

        $values = array_merge(array_map(fn($val) => number_format($val, 2), $negatives), ['0'], array_map(fn($val) => '+' . number_format($val, 2), $positives));

        $this->sph_options = $values;
    }

    public function generateCylOptions()
    {
        $negatives = [];
        $positives = [];

        for ($i = 0.25; $i <= 6; $i += 0.25) {
            $negatives[] = -$i;
            $positives[] = $i;
        }

        for ($i = 6.5; $i <= 11; $i += 0.5) {
            $negatives[] = -$i;
            $positives[] = $i;
        }

        for ($i = 12; $i <= 15; $i++) {
            $negatives[] = -$i;
            $positives[] = $i;
        }

        sort($positives); // ترتيب تنازلي
        sort($negatives); // ترتيب تصاعدي

        $values = array_merge(array_map(fn($val) => number_format($val, 2), $negatives), ['0'], array_map(fn($val) => '+' . number_format($val, 2), $positives));

        $this->cyl_options = $values;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'id_patient' => $this->id_patient,
            'id_reservation' => $this->id_reservation,
            'id_visit' => $this->id_visit,
            'type_visit' => $this->type_visit,
            'od_sph' => $this->od_sph,
            'od_cyl' => $this->od_cyl,
            'od_axis' => $this->od_axis,
            'od_near_add' => $this->od_near_add,
            'od_bcva' => $this->od_bcva,
            'od_comment' => $this->od_comment,
            'os_sph' => $this->os_sph,
            'os_cyl' => $this->os_cyl,
            'os_axis' => $this->os_axis,
            'os_near_add' => $this->os_near_add,
            'os_bcva' => $this->os_bcva,
            'os_comment' => $this->os_comment,
            'duration' => $this->duration,
            'duration_unit' => $this->duration_unit,
            'created_by' => Auth::id(),
        ];

        if ($this->editingId) {
            m_d_oldglasses::find($this->editingId)->update($data);
        } else {
            m_d_oldglasses::create($data);
        }

        $this->resetInput();
    }
    public function edit($id)
    {
        $record = m_d_oldglasses::findOrFail($id);

        $this->editingId = $id;

        // OD
        $this->od_sph = $record->od_sph;
        $this->od_cyl = $record->od_cyl;
        $this->od_axis = $record->od_axis;
        $this->od_near_add = $record->od_near_add;
        $this->od_bcva = $record->od_bcva;
        $this->od_comment = $record->od_comment;

        // OS
        $this->os_sph = $record->os_sph;
        $this->os_cyl = $record->os_cyl;
        $this->os_axis = $record->os_axis;
        $this->os_near_add = $record->os_near_add;
        $this->os_bcva = $record->os_bcva;
        $this->os_comment = $record->os_comment;
    }

    public function delete($id)
    {
        m_d_oldglasses::findOrFail($id)->delete();
    }

    public function resetInput()
    {
        $this->od_sph = $this->od_cyl = $this->od_axis = $this->od_near_add = $this->od_bcva = $this->od_comment = '';
        $this->os_sph = $this->os_cyl = $this->os_axis = $this->os_near_add = $this->os_bcva = $this->os_comment = '';
        $this->duration = null;
        $this->duration_unit = '';
        $this->editingId = null;
    }

    public function render()
    {
        return view('livewire.old-glasses', [
            'glasses_records' => m_d_oldglasses::where('id_patient', $this->id_patient)->orderBy($this->sortColumn, $this->sortDirection)->paginate(10),
        ]);
    }
}