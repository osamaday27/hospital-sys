<div>

    {{-- ✅ رسائل التنبيه --}}
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm mb-3">
            <i class="mdi mdi-check-circle-outline me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- 📝 النموذج --}}
    <div class="card border-0 shadow-lg rounded-4 stylish-card mb-4">
        <div class="card-header bg-gradient-primary text-black fw-bold">
            <i class="mdi mdi-comment-question-outline me-2"></i> {{ __('questions.add_new') }}
        </div>
        <div class="card-body p-4">
            <form wire:submit.prevent="save" class="row g-4">

                {{-- السؤال بالعربية --}}
                <div class="col-md-6">
                    <label class="form-label fw-bold">
                        <i class="mdi mdi-alphabetical me-1 text-primary"></i> {{ __('questions.question_ar') }}
                    </label>
                    <input type="text" class="form-control stylish-input" wire:model="question.ar" placeholder="أدخل السؤال بالعربية">
                    <small class="text-muted">{{ __('questions.question_ar_desc') }}</small>
                    @if($question['ar'])
                        <div class="preview-box mt-2 p-2 bg-light border rounded shadow-sm">
                            <i class="mdi mdi-eye text-success"></i> <strong>{{ $question['ar'] }}</strong>
                        </div>
                    @endif
                    @error('question.ar') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>

                {{-- السؤال بالإنجليزية --}}
                <div class="col-md-6">
                    <label class="form-label fw-bold">
                        <i class="mdi mdi-alphabetical-variant me-1 text-primary"></i> {{ __('questions.question_en') }}
                    </label>
                    <input type="text" class="form-control stylish-input" wire:model="question.en" placeholder="Enter the question in English">
                    <small class="text-muted">{{ __('questions.question_en_desc') }}</small>
                    @if($question['en'])
                        <div class="preview-box mt-2 p-2 bg-light border rounded shadow-sm">
                            <i class="mdi mdi-eye text-success"></i> <strong>{{ $question['en'] }}</strong>
                        </div>
                    @endif
                    @error('question.en') <span class="text-danger small">{{ $message }}</span> @enderror
                </div>

                {{-- نوع الإجابة --}}
                <div class="col-md-6">
                    <label class="form-label fw-bold">
                        <i class="mdi mdi-format-list-bulleted-type me-1 text-primary"></i> {{ __('questions.answer_type') }}
                    </label>
                    <select class="form-select stylish-input" wire:model="answer_type">
                        <option value="text">{{ __('questions.type_text') }}</option>
                        <option value="options">{{ __('questions.type_options') }}</option>
                    </select>
                </div>

                {{-- ✅ اختيارات جاهزة (CheckBox نعم / لا) --}}
                @if($answer_type === 'options')
                    <div class="col-md-12">
                        <label class="form-label fw-bold">
                            <i class="mdi mdi-check-all me-1 text-primary"></i> {{ __('questions.options') }}
                        </label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="نعم" wire:model="options" id="optionYes">
                            <label class="form-check-label" for="optionYes">نعم</label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="لا" wire:model="options" id="optionNo">
                            <label class="form-check-label" for="optionNo">لا</label>
                        </div>

                        {{-- ✅ Preview --}}
                        @if(!empty($options))
                            <div class="preview-box mt-2 p-2 bg-light border rounded shadow-sm">
                                <i class="mdi mdi-eye text-success"></i>
                                <strong>{{ implode(' , ', $options) }}</strong>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- الحالة --}}
                <div class="col-md-6">
                    <label class="form-label fw-bold">
                        <i class="mdi mdi-toggle-switch me-1 text-primary"></i> {{ __('questions.active') }}
                    </label>
                    <div class="form-check form-switch mt-2">
                        <input class="form-check-input" type="checkbox" wire:model="active">
                        <label class="form-check-label">{{ $active ? '✅ متاح' : '❌ غير متاح' }}</label>
                    </div>
                </div>

                {{-- أزرار الحفظ والإلغاء --}}
                <div class="col-12 d-flex gap-2">
                    <button class="btn btn-success rounded-pill px-4 shadow-sm">
                        <i class="mdi mdi-content-save-outline"></i> {{ __('questions.save') }}
                    </button>
                    <button type="button" wire:click="resetInput" class="btn btn-secondary rounded-pill px-4 shadow-sm">
                        <i class="mdi mdi-cancel"></i> {{ __('questions.cancel') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- 📋 الجدول --}}
    <div class="card border-0 shadow-lg rounded-4 stylish-card">
        <div class="card-header bg-gradient-primary text-black fw-bold">
            <i class="mdi mdi-database me-2"></i> {{ __('questions.list') }}
        </div>
        <div class="card-body p-4">
            <div class="input-group mb-3">
                <span class="input-group-text"><i class="mdi mdi-magnify"></i></span>
                <input type="text" wire:model="search" class="form-control stylish-input" placeholder="{{ __('questions.search') }}">
            </div>

            <table class="table table-hover align-middle shadow-sm">
                <thead class="table-light">
                    <tr>
                        <th>{{ __('questions.question') }}</th>
                        <th>{{ __('questions.answer_type') }}</th>
                        <th>{{ __('questions.status') }}</th>
                        <th>{{ __('questions.actions') }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($questions as $q)
                        <tr>
                            <td>{{ $q->getTranslation('question', app()->getLocale()) }}</td>
                            <td>
                                <span class="badge bg-info text-dark">
                                    {{ $q->answer_type == 'text' ? __('questions.type_text') : __('questions.type_options') }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $q->active ? 'bg-success' : 'bg-danger' }}">
                                    {{ $q->active ? 'متاح' : 'غير متاح' }}
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-primary rounded-pill" wire:click="edit({{ $q->id }})" title="تعديل">
                                    <i class="mdi mdi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-danger rounded-pill" onclick="confirmDelete({{ $q->id }})" title="حذف">
                                    <i class="mdi mdi-delete"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">
                                <i class="mdi mdi-alert-circle-outline"></i> لا توجد بيانات
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{ $questions->links() }}
        </div>
    </div>
</div>

{{-- ✅ SweetAlert2 --}}
<script>
function confirmDelete(id) {
    Swal.fire({
        title: 'تحذير',
        text: '⚠️ هل أنت متأكد أنك تريد حذف هذا السجل؟ لا يمكن التراجع عنه!',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#198754',
        cancelButtonColor: '#dc3545',
        confirmButtonText: 'نعم، احذف',
        cancelButtonText: 'إلغاء'
    }).then((result) => {
        if (result.isConfirmed) {
            Livewire.emit('deleteConfirmed', id);
            Swal.fire('تم الحذف!', '✅ تم الحذف بنجاح.', 'success');
        }
    })
}
</script>
