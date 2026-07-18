<?php

namespace App\Http\Livewire;

use Livewire\WithFileUploads;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\models\medical\m_d_eyeexamination;
use App\Traits\web\file_storage;
use App\Models\User;

class EyeExamination extends Component
{
    use WithFileUploads, WithPagination, file_storage;

    public $activeTab = 'input'; // input, chart, history

    public $base64Image;
    public $external_file;
    public $id_patient, $id_reservation, $id_visit, $type_visit;
    public $sections = [];
    public $right_eye = [];
    public $left_eye = [];
    public $comment = [];
    public $editingId = null,
        $search = '',
        $showDeleteModal = false;
    public $showDetailsModal = false,
        $selectedBatchId = null,
        $selectedBatchRecords = [];
    public $eom_options = [];
    public $uploaded_eom_image;
    public $fundus_comment = '';
    public $fundus_base64_image;
    public $historySearch = '';

    public $right_eom = ['SR' => '', 'IO' => '', 'IR' => '', 'SO' => '', 'LR' => '', 'MR' => ''];
    public $left_eom = ['SR' => '', 'IO' => '', 'IR' => '', 'SO' => '', 'LR' => '', 'MR' => ''];
    public $eom_values = ['SR' => '', 'IO' => '', 'IR' => '', 'SO' => '', 'LR' => '', 'MR' => ''];

    // هنستخدم الباجينيشن للهيستوري
    protected $paginationTheme = 'bootstrap';

    // Datalist options
    public $anterior_vitreous_options = ['Normal', 'Cells 1+', 'Cells 2+', 'Cells 3+', 'Cells 4+'];
    public $anterior_chamber_options = ['Normal depth', 'no abnormal contents', 'Flare 1+', 'Flare 2+', 'Flare 3+', 'Flare 4+', 'Cells 1+', 'Cells 2+', 'Cells 3+', 'Cells 4+'];
    public $eye_lids = ['Normal position', 'Ptosis'];
    public $Cornea = ['Clear', 'pannus', 'siccus', 'Macula', 'Nebula', 'Leukoma'];
    public $IRIS = ['Normal shape and pattern', 'Muddy', 'Koeppe nodule', 'Busacca nodule '];
    public $Pupil = ['RRR', 'RAPD', 'Posterior synechiae', 'Festooned pupil'];
    public $Lens = ['Clear', 'Cataract'];
    public $Anterior_Vitreous = ['Normal', 'Cells 1+', 'Cells 2+', 'Cells 3+', 'Cells 4+'];
    public $Optic_Disc = ['Rim', 'Cup/ disc'];
    protected $listeners = ['reloadBatches' => 'loadBatches', 'saveFundusImage'];
    public $UCVA = ['20/20', '20/40', '20/60', '2.00', '1.5', '1.2', '1.00', '0.9', '0.8', '0.7', '0.6', '0.5', '0.3', '0.2', '0.15', '0.1', '0.05', 'CF 2m', 'CF 1.5m', 'CF 1m', 'CF 75cm', 'CF 50cm', 'CF 20cm', 'CF 10cm', 'HM- GP', 'HM- BP', 'PL- GP', 'PL- BP', 'No PL', 'Can Fix and Follow', 'Cannot be assessed'];
    public $BCVA = ['20/20', '20/40', '20/60', '2.00', '1.5', '1.2', '1.00', '0.9', '0.8', '0.7', '0.6', '0.5', '0.3', '0.2', '0.15', '0.1', '0.05', 'CF 2m', 'CF 1.5m', 'CF 1m', 'CF 75cm', 'CF 50cm', 'CF 20cm', 'CF 10cm', 'HM- GP', 'HM- BP', 'PL- GP', 'PL- BP', 'No PL', 'Can Fix and Follow', 'Cannot be assessed'];

    protected $rules = [
        'id_patient' => 'required',
        'id_reservation' => 'required',
        'id_visit' => 'required',
        'type_visit' => 'required',
        'sections.*' => 'required|string|max:255',
        'right_eye.*.*' => 'nullable|string|max:255',
        'left_eye.*.*' => 'nullable|string|max:255',
        'comment.*.*' => 'nullable|string',
        'uploaded_eom_image' => 'nullable|image|mimes:jpeg,png,jpg|max:10240',
        'external_file' => 'nullable|file|mimes:jpeg,png,jpg,pdf,csv,xlsx,json|max:10240',
    ];

    public function mount($id_patient, $id_reservation, $type_visit, $id_visit)
    {
        $this->id_patient = $id_patient;
        $this->id_reservation = $id_reservation;
        $this->type_visit = $type_visit;
        $this->id_visit = $id_visit;

        $this->initializeSections();
        $this->generateEomOptions();
        $this->fillWithLastRecord(); // 👈 تحميل آخر بيانات محفوظة بالفيلدات
    }

    /**
     * تعبئة الفيلدات بآخر بيانات محفوظة للمريض
     */
    protected function fillWithLastRecord()
    {
        // نجيب آخر batch_id للمريض
        $lastBatch = m_d_eyeexamination::where('id_patient', $this->id_patient)->orderByDesc('created_at')->value('batch_id');

        if ($lastBatch) {
            $records = m_d_eyeexamination::where('batch_id', $lastBatch)->where('id_patient', $this->id_patient)->get();

            // نفرغ الفيلدات ونبنيها بالبيانات
            foreach ($this->sections as $section) {
                $this->right_eye[$section] = [];
                $this->left_eye[$section] = [];
                $this->comment[$section] = [];
            }

            foreach ($records as $record) {
                $this->right_eye[$record->section][] = $record->right_eye ?? '';
                $this->left_eye[$record->section][] = $record->left_eye ?? '';
                $this->comment[$record->section][] = $record->comment ?? '';
            }
        }
    }

    public function viewDetails($batchId)
    {
        $this->selectedBatchId = $batchId;
        $this->selectedBatchRecords = m_d_eyeexamination::where('batch_id', $batchId)->get();
        $this->showDetailsModal = true;
    }

    public function exportBatch($batchId)
    {
        $records = m_d_eyeexamination::where('batch_id', $batchId)->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=eye_examination_$batchId.csv",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0',
        ];

        $callback = function () use ($records) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Section', 'Right Eye', 'Left Eye', 'Comment', 'Created At']);
            foreach ($records as $record) {
                fputcsv($file, [$record->section, $record->right_eye, $record->left_eye, $record->comment, optional($record->created_at)->format('Y-m-d H:i')]);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function confirmDelete($batchId)
    {
        $this->selectedBatchId = $batchId;
        $this->showDeleteModal = true;
    }

    public function deleteBatch()
    {
        if ($this->selectedBatchId) {
            m_d_eyeexamination::where('batch_id', $this->selectedBatchId)->delete();
            session()->flash('message', 'Batch deleted successfully.');
            $this->showDeleteModal = false;
            $this->showDetailsModal = false;
            $this->emit('reloadBatches');
        }
    }

    // علشان زرار إزالة الملف يشتغل
    public function removeFile()
    {
        $this->external_file = null;
    }

    protected function initializeSections()
    {
        $this->sections = ['External Examination', 'Visual Acuity (UCVA)', 'Visual Acuity (BCVA)', 'Cycloplegic refraction', 'IOP(mm Hg)', 'ocular motility', 'Eye Lids', 'Lacrimal', 'Orbit', 'Conjunctiva', 'sclera', 'Cornea', 'Anterior chamber', 'AS Angle', 'IRIS', 'Pupil', 'Lens', 'Anterior Vitreous', 'Macula', 'Retina', 'Optic Disc', 'Fundus', 'Comment'];

        foreach ($this->sections as $section) {
            if ($section == 'Fundus') {
                $this->right_eye[$section] = ['Rim'];
                $this->left_eye[$section] = ['Rim'];
            } elseif (in_array($section, ['External Examination', 'Cycloplegic refraction', 'IOP(mm Hg)', 'ocular motility', 'Lacrimal', 'Orbit', 'Optic Disc', 'Visual Acuity (UCVA)', 'Visual Acuity (BCVA)'])) {
                $this->right_eye[$section] = ['normal'];
                $this->left_eye[$section] = ['normal'];
            } elseif (in_array($section, ['Retina', 'Macula', 'Anterior Vitreous', 'sclera', 'Conjunctiva'])) {
                $this->right_eye[$section] = ['Normal'];
                $this->left_eye[$section] = ['Normal'];
            } elseif ($section == 'Lens') {
                $this->right_eye[$section] = ['Clear'];
                $this->left_eye[$section] = ['Clear'];
            } elseif ($section == 'Pupil') {
                $this->right_eye[$section] = ['RRR'];
                $this->left_eye[$section] = ['RRR'];
            } elseif ($section == 'IRIS') {
                $this->right_eye[$section] = ['Normal shape and pattern'];
                $this->left_eye[$section] = ['Normal shape and pattern'];
            } elseif ($section == 'AS Angle' || $section == 'Anterior chamber') {
                $this->right_eye[$section] = ['Normal depth'];
                $this->left_eye[$section] = ['Normal depth'];
            } elseif ($section == 'Cornea') {
                $this->right_eye[$section] = ['Clear'];
                $this->left_eye[$section] = ['Clear'];
            } elseif ($section == 'Eye Lids') {
                $this->right_eye[$section] = ['Normal position'];
                $this->left_eye[$section] = ['Normal position'];
            } else {
                $this->right_eye[$section] = [''];
                $this->left_eye[$section] = [''];
            }

            $this->comment[$section] = [''];
        }
    }

    public function generateEomOptions()
    {
        $negatives = $positives = [];
        for ($i = 1; $i <= 4; $i++) {
            $negatives[] = -$i;
            $positives[] = $i;
        }
        sort($negatives);
        sort($positives);
        $this->eom_options = array_merge(array_map(fn($v) => number_format($v, 2), $negatives), ['0'], array_map(fn($v) => '+' . number_format($v, 2), $positives));
    }

    // هنخلي الlistener يعيد ضبط الصفحة (هيؤدي لإعادة الريندر وبالتالي تحميل الهيستوري)
    public function loadBatches()
    {
        $this->resetPage();
    }

    public function save()
    {
        $this->validate();

        $batchId = now()->timestamp . Auth::id();
        foreach ($this->sections as $section) {
            foreach ($this->right_eye[$section] as $index => $value) {
                if (!empty($value) || !empty($this->left_eye[$section][$index]) || !empty($this->comment[$section][$index])) {
                    m_d_eyeexamination::create([
                        'id_patient' => $this->id_patient,
                        'id_reservation' => $this->id_reservation,
                        'id_visit' => $this->id_visit,
                        'type_visit' => $this->type_visit,
                        'section' => $section,
                        'right_eye' => $value,
                        'left_eye' => $this->left_eye[$section][$index],
                        'comment' => $this->comment[$section][$index],
                        'batch_id' => $batchId,
                        'created_by' => Auth::id(),
                    ]);
                }
            }
        }
        $this->resetInputs();
        session()->flash('message', 'تم حفظ البيانات بنجاح');
        $this->emit('reloadBatches');
    }

    protected function resetInputs()
    {
        $this->reset(['right_eye', 'left_eye', 'comment', 'external_file', 'fundus_base64_image', 'fundus_comment']);
        $this->initializeSections();
    }

    public function copyValue($section, $index)
    {
        $this->left_eye[$section][$index] = $this->right_eye[$section][$index] ?? '';
    }

    public function addRow($section)
    {
        $this->right_eye[$section][] = '';
        $this->left_eye[$section][] = '';
        $this->comment[$section][] = '';
    }

    public function removeRow($section, $index)
    {
        unset($this->right_eye[$section][$index], $this->left_eye[$section][$index], $this->comment[$section][$index]);
        $this->right_eye[$section] = array_values($this->right_eye[$section]);
        $this->left_eye[$section] = array_values($this->left_eye[$section]);
        $this->comment[$section] = array_values($this->comment[$section]);
    }

    public function render()
    {
        // أقسام بنستخرج منها ملخصات (يمين/شمال)
        // NOTE: لو عايز تزود/تنقص الأقسام، عدّل الليست دي
        $summarySections = ['Visual Acuity (UCVA)', 'Visual Acuity (BCVA)', 'Cornea', 'Lens', 'Macula', 'Retina', 'Optic Disc'];

        // نجمع الهيستوري على مستوى الدُفعات
        $batches = m_d_eyeexamination::query()
            ->where('id_patient', $this->id_patient)
            ->when($this->historySearch, function ($q) {
                $term = '%' . $this->historySearch . '%';
                $q->where(function ($qq) use ($term) {
                    $qq->where('batch_id', 'like', $term)->orWhere('right_eye', 'like', $term)->orWhere('left_eye', 'like', $term)->orWhere('comment', 'like', $term)->orWhere('section', 'like', $term);
                });
            })
            ->selectRaw(
                "
                batch_id,
                MIN(created_by) as created_by,
                MAX(created_at) as created_at,
                GROUP_CONCAT(
                    CASE WHEN section IN ('Visual Acuity (UCVA)','Visual Acuity (BCVA)','Cornea','Lens','Macula','Retina','Optic Disc')
                    THEN CONCAT(section, ': ', COALESCE(NULLIF(TRIM(right_eye),''),'—'))
                    END SEPARATOR ' | '
                ) as right_eye_summary,
                GROUP_CONCAT(
                    CASE WHEN section IN ('Visual Acuity (UCVA)','Visual Acuity (BCVA)','Cornea','Lens','Macula','Retina','Optic Disc')
                    THEN CONCAT(section, ': ', COALESCE(NULLIF(TRIM(left_eye),''),'—'))
                    END SEPARATOR ' | '
                ) as left_eye_summary
            ",
            )
            ->groupBy('batch_id')
            ->orderByDesc('created_at')
            ->paginate(10);

        // نجيب أسماء المستخدمين مرة واحدة
        $userMap = User::whereIn('id', collect($batches->items())->pluck('created_by')->filter()->unique())->pluck('name', 'id');

        return view('livewire.eye-examination', compact('batches', 'userMap'));
    }
}