<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use App\models\medical\m_d_medicalreporttemplate;
use App\models\User;
use App\models\general\specialties;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class medicalReportTemplateForm extends Component
{
    // خصائص النموذج الأساسية
    public $name;
    public $content;
    public $description; // وصف القالب
    public $is_global = false;
    public $is_active = true; // حالة القالب (نشط/غير نشط)
    public $category; // تصنيف القالب
    
    // العلاقات
    public $selected_specialties = [];
    public $selected_users = [];
    public $specialty_id; // التخصص الافتراضي
    
    // البيانات المرجعية
    public $specialties = [];
    public $users = [];
    public $allTemplates = [];
    public $categories = []; // فئات القوالب
    
    // إدارة التحرير
    public $editMode = false;
    public $editingTemplateId = null;
    
    // البحث والفلترة
    public $search = '';
    public $filterBySpecialty = '';
    public $filterByCategory = '';
    public $filterByStatus = '';
    
    // الترتيب والصفحات
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $perPage = 10;
    
    // متغيرات إضافية
    public $showDeleteModal = false;
    public $templateToDelete = null;
    public $showPreviewModal = false;
    public $previewContent = '';
    public $previewName = '';

    protected $listeners = [
        'refreshTemplates' => 'loadTemplates',
        'templateDeleted' => 'handleTemplateDeleted'
    ];

    protected $rules = [
        'name' => 'required|string|max:255|min:3',
        'content' => 'required|string|min:10',
        'description' => 'nullable|string|max:500',
        'category' => 'nullable|string|max:100',
        'is_global' => 'boolean',
        'is_active' => 'boolean',
        'specialty_id' => 'nullable|exists:specialties,id',
        'selected_specialties' => 'nullable|array',
        'selected_specialties.*' => 'exists:specialties,id',
        'selected_users' => 'nullable|array',
        'selected_users.*' => 'exists:users,id',
    ];

    protected $validationAttributes = [
        'name' => 'اسم القالب',
        'content' => 'محتوى القالب',
        'description' => 'الوصف',
        'category' => 'التصنيف',
        'specialty_id' => 'التخصص',
    ];

    protected $messages = [
        'name.required' => 'يجب إدخال اسم القالب',
        'name.min' => 'اسم القالب يجب أن يكون 3 أحرف على الأقل',
        'content.required' => 'يجب إدخال محتوى القالب',
        'content.min' => 'محتوى القالب قصير جداً',
    ];

    public function mount()
    {
        $this->initializeData();
        $this->loadTemplates();
    }

    private function initializeData()
    {
        // تحميل البيانات المرجعية
        $this->specialties = specialties::where('status','active')
            ->orderBy('name_ar')
            ->get();
        
        $this->users = User::where('Status','enabled')
            ->select('id', 'name', 'email')
          
            ->orderBy('name')
            ->get();

        // تحميل الفئات من القوالب الموجودة
        $this->categories = m_d_medicalreporttemplate::select('category')
            ->distinct()
            ->whereNotNull('category')
            ->pluck('category')
            ->toArray();

        // إضافة فئات افتراضية
        $defaultCategories = [
            'فحص عام',
            'تقرير أشعة',
            'تقرير مختبر',
            'تقرير عملية',
            'تقرير متابعة',
            'شهادة طبية',
            'تقرير خروج',
        ];

        $this->categories = array_unique(array_merge($this->categories, $defaultCategories));
        sort($this->categories);

        // تعيين التخصص الافتراضي للمستخدم الحالي
        $this->specialty_id = Auth::user()->specialty_id;
    }

    public function save()
    {
        $this->validate();

        try {
            DB::beginTransaction();

            $data = [
                'name' => trim($this->name),
                'content' => $this->content,
                'description' => $this->description,
                'category' => $this->category,
                'is_global' => $this->is_global,
                'is_active' => $this->is_active,
                'specialty_id' => $this->specialty_id,
                'updated_by' => Auth::id(),
            ];

            if ($this->editMode && $this->editingTemplateId) {
                // تحديث قالب موجود
                $template = m_d_medicalreporttemplate::findOrFail($this->editingTemplateId);
                
                // التحقق من الصلاحيات
                if (!$this->canEditTemplate($template)) {
                    $this->addError('permission', 'ليس لديك صلاحية لتعديل هذا القالب');
                    DB::rollBack();
                    return;
                }

                $template->update($data);
                $message = 'تم تحديث القالب بنجاح';
            } else {
                // إنشاء قالب جديد
                $data['user_id'] = Auth::id();
                $data['created_by'] = Auth::id();
                $template = m_d_medicalreporttemplate::create($data);
                $message = 'تم إنشاء القالب بنجاح';
            }

            // مزامنة العلاقات
            $template->specialties()->sync($this->selected_specialties ?? []);
            $template->users()->sync($this->selected_users ?? []);

            DB::commit();

            $this->resetForm();
            $this->loadTemplates();
            
            session()->flash('message', $message);
            $this->dispatchBrowserEvent('template-saved', ['message' => $message]);
            
        } catch (\Exception $e) {
            DB::rollBack();
            $this->addError('save', 'حدث خطأ أثناء حفظ القالب: ' . $e->getMessage());
        }
    }

 public function edit($id)
{
    try {
        $template = m_d_medicalreporttemplate::with(['specialties', 'users', 'specialty'])
            ->findOrFail($id);

        // ✅ التحقق من الصلاحيات
        if (!$this->canEditTemplate($template)) {
            session()->flash('error', 'ليس لديك صلاحية لتعديل هذا القالب');
            return;
        }

        // ✅ تعبئة البيانات في الخصائص
        $this->editingTemplateId = $id;
        $this->editMode = true;
        $this->name = $template->name;
        $this->content = $template->content;
        $this->description = $template->description;
        $this->category = $template->category;
        $this->is_global = (bool) $template->is_global;
        $this->is_active = (bool) $template->is_active;
        $this->specialty_id = $template->specialty_id;
        $this->selected_specialties = $template->specialties->pluck('id')->toArray();
        $this->selected_users = $template->users->pluck('id')->toArray();

        // ✅ إشعار الواجهة لتحديث المحرر بعد التأكد من جاهزيته
        $this->dispatchBrowserEvent('refreshEditor'); // لو المحرر مش موجود يعاد تهيئته
        $this->dispatchBrowserEvent('setEditorContent', ['content' => $this->content]);
        $this->dispatchBrowserEvent('scroll-to-form');

    } catch (\Exception $e) {
        \Log::error('خطأ أثناء تحميل القالب: ' . $e->getMessage());
        session()->flash('error', 'حدث خطأ أثناء تحميل القالب');
    }
}


    public function duplicate($id)
    {
        try {
            $template = m_d_medicalreporttemplate::with(['specialties', 'users'])
                ->findOrFail($id);

            $this->resetForm();
            $this->name = $template->name . ' (نسخة)';
            $this->content = $template->content;
            $this->description = $template->description;
            $this->category = $template->category;
            $this->is_global = false; // النسخة المكررة ليست عامة
            $this->is_active = true;
            $this->specialty_id = $template->specialty_id;
            $this->selected_specialties = $template->specialties->pluck('id')->toArray();
            $this->selected_users = [];

            $this->dispatchBrowserEvent('setEditorContent', ['content' => $this->content]);
            $this->dispatchBrowserEvent('scroll-to-form');
            
            session()->flash('info', 'تم تحميل القالب للنسخ. قم بتعديل الاسم والمحتوى ثم احفظ');
            
        } catch (\Exception $e) {
            session()->flash('error', 'حدث خطأ أثناء نسخ القالب');
        }
    }

    public function confirmDelete($id)
    {
        $template = m_d_medicalreporttemplate::find($id);
        
        if (!$template) {
            session()->flash('error', 'القالب غير موجود');
            return;
        }

        if (!$this->canDeleteTemplate($template)) {
            session()->flash('error', 'ليس لديك صلاحية لحذف هذا القالب');
            return;
        }

        $this->templateToDelete = $id;
        $this->showDeleteModal = true;
        $this->dispatchBrowserEvent('show-delete-modal');
    }

    public function delete()
    {
        try {
            if (!$this->templateToDelete) {
                return;
            }

            $template = m_d_medicalreporttemplate::findOrFail($this->templateToDelete);

            if (!$this->canDeleteTemplate($template)) {
                session()->flash('error', 'ليس لديك صلاحية لحذف هذا القالب');
                return;
            }

            DB::beginTransaction();

            // حذف العلاقات
            $template->specialties()->detach();
            $template->users()->detach();

            // حذف القالب
            $template->delete();

            DB::commit();

            $this->templateToDelete = null;
            $this->showDeleteModal = false;
            $this->loadTemplates();
            
            session()->flash('message', 'تم حذف القالب بنجاح');
            $this->dispatchBrowserEvent('template-deleted');
            
        } catch (\Exception $e) {
            DB::rollBack();
            session()->flash('error', 'حدث خطأ أثناء حذف القالب');
        }
    }

    public function preview($id)
    {
        try {
            $template = m_d_medicalreporttemplate::findOrFail($id);
            
            $this->previewName = $template->name;
            $this->previewContent = $template->content;
            $this->showPreviewModal = true;
            
            $this->dispatchBrowserEvent('show-preview-modal', [
                'name' => $this->previewName,
                'content' => $this->previewContent
            ]);
            
        } catch (\Exception $e) {
            session()->flash('error', 'حدث خطأ أثناء معاينة القالب');
        }
    }

    public function toggleStatus($id)
    {
        try {
            $template = m_d_medicalreporttemplate::findOrFail($id);

            if (!$this->canEditTemplate($template)) {
                session()->flash('error', 'ليس لديك صلاحية لتعديل حالة هذا القالب');
                return;
            }

            $template->is_active = !$template->is_active;
            $template->save();

            $this->loadTemplates();
            
            $status = $template->is_active ? 'مفعّل' : 'معطّل';
            session()->flash('message', "تم تغيير حالة القالب إلى: {$status}");
            
        } catch (\Exception $e) {
            session()->flash('error', 'حدث خطأ أثناء تغيير حالة القالب');
        }
    }

    public function resetForm()
    {
        $this->reset([
            'name',
            'content',
            'description',
            'category',
            'is_global',
            'selected_specialties',
            'selected_users',
            'editMode',
            'editingTemplateId'
        ]);
        
        $this->is_active = true;
        $this->specialty_id = Auth::user()->specialty_id;
        
        $this->dispatchBrowserEvent('reset-editor');
        $this->resetErrorBag();
        $this->resetValidation();
    }

    public function loadTemplates()
    {
        $query = m_d_medicalreporttemplate::with([
            'specialties:id,name_ar',
            'users:id,name',
            'specialty:id,name_ar',
            'creator:id,name'
        ]);

        // الفلترة حسب الصلاحيات
       

        // البحث
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('description', 'like', '%' . $this->search . '%')
                  ->orWhere('category', 'like', '%' . $this->search . '%');
            });
        }

        // الفلترة حسب التخصص
        if (!empty($this->filterBySpecialty)) {
            $query->where(function ($q) {
                $q->where('specialty_id', $this->filterBySpecialty)
                  ->orWhereHas('specialties', fn($q) => $q->where('specialties.id', $this->filterBySpecialty));
            });
        }

        // الفلترة حسب التصنيف
        if (!empty($this->filterByCategory)) {
            $query->where('category', $this->filterByCategory);
        }

        // الفلترة حسب الحالة
        if ($this->filterByStatus !== '') {
            $query->where('is_active', (bool) $this->filterByStatus);
        }

        // الترتيب
        $query->orderBy($this->sortBy, $this->sortDirection);

        $this->allTemplates = $query->get();
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }

        $this->loadTemplates();
    }

    public function clearFilters()
    {
        $this->reset(['search', 'filterBySpecialty', 'filterByCategory', 'filterByStatus']);
        $this->loadTemplates();
    }

    // دوال التحقق من الصلاحيات
    private function canEditTemplate($template)
    {
        $user = Auth::user();
        
        if ($user->hasRole('admin')) {
            return true;
        }

        return $template->user_id === $user->id || 
               $template->created_by === $user->id;
    }

    private function canDeleteTemplate($template)
    {
        $user = Auth::user();
        
        if ($user->hasRole('admin')) {
            return true;
        }

        // لا يمكن حذف القوالب العامة إلا من قبل المسؤولين
        if ($template->is_global) {
            return false;
        }

        return $template->user_id === $user->id || 
               $template->created_by === $user->id;
    }

    // دوال مساعدة للعرض
    public function getTemplateStatusBadge($template)
    {
        return $template->is_active 
            ? '<span class="badge bg-success">نشط</span>' 
            : '<span class="badge bg-secondary">معطل</span>';
    }

    public function getTemplateTypeBadge($template)
    {
        return $template->is_global 
            ? '<span class="badge bg-primary">عام</span>' 
            : '<span class="badge bg-info">خاص</span>';
    }

    // Updated properties
    public function updatedSearch()
    {
        $this->loadTemplates();
    }

    public function updatedFilterBySpecialty()
    {
        $this->loadTemplates();
    }

    public function updatedFilterByCategory()
    {
        $this->loadTemplates();
    }

    public function updatedFilterByStatus()
    {
        $this->loadTemplates();
    }

    public function render()
    {
        return view('livewire.medical-report-template-form', [
            'templates' => $this->allTemplates,
            'templatesCount' => count($this->allTemplates),
        ]);
    }
}