<?php

namespace App\Http\Livewire\Radiology\Settings\Questions;

use Livewire\Component;
use Livewire\WithPagination;
use App\models\administrative\radiologyquestionssettings;

class Index extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['deleteConfirmed' => 'delete'];

    // الحقول
    public $question = ['ar' => '', 'en' => ''];
    public $answer_type = 'text';
    public $options = [];
    public $active = true;
    public $question_id;
    public $search = '';

    // القواعد
    protected $rules = [
        'question.ar'   => 'required|string|max:255',
        'question.en'   => 'required|string|max:255',
        'answer_type'   => 'required|in:text,options',
        'options'       => 'nullable|array',
        'active'        => 'boolean',
    ];

    protected $messages = [
        'question.ar.required' => '⚠️ يجب إدخال السؤال بالعربية',
        'question.en.required' => '⚠️ يجب إدخال السؤال بالإنجليزية',
        'answer_type.required' => '⚠️ يجب اختيار نوع الإجابة',
    ];

    // حفظ / تعديل
    public function save()
    {
        $this->validate();

        radiologyquestionssettings::updateOrCreate(
            ['id' => $this->question_id],
            [
                'question' => $this->question,
                'answer_type' => $this->answer_type,
                'options' => $this->answer_type === 'options' ? $this->options : null,
                'active' => $this->active,
            ]
        );

        session()->flash('success', $this->question_id ? '✅ تم تعديل السؤال بنجاح' : '✅ تم إضافة السؤال بنجاح');
        $this->resetInput();
    }

    // تعديل
    public function edit($id)
    {
        $q = radiologyquestionssettings::findOrFail($id);
        $this->question_id = $q->id;
        $this->question = $q->getTranslations('question');
        $this->answer_type = $q->answer_type;
        $this->options = $q->options ?? [];
        $this->active = $q->active;
    }

    // حذف
    public function delete($id)
    {
        radiologyquestionssettings::findOrFail($id)->delete();
        session()->flash('success', '✅ تم الحذف بنجاح');
    }

    // إعادة تعيين
    public function resetInput()
    {
        $this->question = ['ar' => '', 'en' => ''];
        $this->answer_type = 'text';
        $this->options = [];
        $this->active = true;
        $this->question_id = null;
    }

    // تفعيل / تعطيل
    public function toggleStatus($id)
    {
        $q = radiologyquestionssettings::findOrFail($id);
        $q->update(['active' => !$q->active]);
    }

    // العرض
    public function render()
    {
        $questions = radiologyquestionssettings::where('question->'.app()->getLocale(), 'like', "%{$this->search}%")
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('livewire.radiology.settings.questions.index', compact('questions'));
    }
}
