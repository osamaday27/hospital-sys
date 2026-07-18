<div class="container-fluid py-4">
  <!-- رسالة التنبيه -->
  @if(session()->has('message'))
    <div class="alert alert-success alert-dismissible fade show mb-4">
      <i class="fas fa-check-circle me-2"></i>
      {{ session('message') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('doctor_trans.close') }}"></button>
    </div>
  @endif
  @if(session()->has('error'))
    <div class="alert alert-danger alert-dismissible fade show mb-4">
      {{ session('error') }}
      <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="{{ __('doctor_trans.close') }}"></button>
    </div>
  @endif

  <!-- البحث والفلاتر -->
  <div class="row mb-4 g-3">
    <div class="col-md-4">
      <input type="text"
             wire:model.debounce.500ms="searchTerm"
             class="form-control border-2"
             placeholder="{{ __('doctor_trans.search_placeholder') }}">
    </div>
    <div class="col-md-3">
      <select wire:model="filterStatus" class="form-select border-2">
        <option value="">{{ __('doctor_trans.all_statuses') }}</option>
        <option value="pending">{{ __('doctor_trans.pending') }}</option>
        <option value="in_progress">{{ __('doctor_trans.in_progress') }}</option>
        <option value="finished">{{ __('doctor_trans.finished') }}</option>
        <option value="cancelled">{{ __('doctor_trans.cancelled') }}</option>
      </select>
    </div>
    <div class="col-md-3">
      <select wire:model="filterImportance" class="form-select border-2">
        <option value="">{{ __('doctor_trans.all_importance') }}</option>
        <option value="low">{{ __('doctor_trans.low') }}</option>
        <option value="medium">{{ __('doctor_trans.medium') }}</option>
        <option value="high">{{ __('doctor_trans.high') }}</option>
      </select>
    </div>
  </div>

  <!-- بطاقة إضافة/تعديل تعليمات -->
  <div class="card shadow-sm mb-4 border-0">
    <div class="card-header bg-primary text-white py-3">
      <h5 class="mb-0" style="color: #fff">
        <i class="fas fa-{{ $editId ? 'edit' : 'plus-circle' }} me-2"></i>
        {{ $editId ? __('doctor_trans.edit_instruction') : __('doctor_trans.add_new_instructions') }}
      </h5>
    </div>
    <div class="card-body">
      <form wire:submit.prevent="submit" class="needs-validation" novalidate>
        <div class="row g-4">
          <!-- نص التعليمة -->
          <div class="col-12">
            <label class="form-label fw-bold">{{ __('doctor_trans.instruction_text') }}
              <span class="text-danger">*</span></label>
            <textarea wire:model.defer="instruction"
                      class="form-control border-2"
                      rows="3"
                      placeholder="{{ __('doctor_trans.instruction_placeholder') }}"
                      required></textarea>
            @error('instruction')
              <div class="invalid-feedback d-block">{{ $message }}</div>
            @enderror
          </div>

          <!-- درجة الأهمية -->
          <div class="col-md-4">
            <label class="form-label fw-bold">{{ __('doctor_trans.importance_level') }}</label>
            <select wire:model.defer="importance" class="form-select border-2">
              <option value="low">{{ __('doctor_trans.low') }}</option>
              <option value="medium">{{ __('doctor_trans.medium') }}</option>
              <option value="high">{{ __('doctor_trans.high') }}</option>
            </select>
          </div>

          <!-- موعد محدد -->
          <div class="col-md-4">
            <div class="form-check form-switch mt-4 pt-1">
              <input type="checkbox"
                     wire:model="time_specific"
                     id="time_specific"
                     class="form-check-input">
              <label class="form-check-label fw-bold" for="time_specific">
                {{ __('doctor_trans.specific_time') }}
              </label>
            </div>
          </div>
          @if($time_specific)
            <div class="col-md-4">
              <label class="form-label fw-bold">{{ __('doctor_trans.date_time') }}</label>
              <div class="input-group">
                <span class="input-group-text bg-light">
                  <i class="far fa-calendar-alt"></i>
                </span>
                <input type="datetime-local"
                       wire:model.defer="scheduled_at"
                       class="form-control border-2">
              </div>
            </div>
          @endif

          <!-- إعدادات التكرار -->
          <div class="col-md-4">
            <label class="form-label fw-bold">{{ __('doctor_trans.repeat_count') }}</label>
            <input type="number"
                   wire:model.defer="repeat_count"
                   min="1"
                   class="form-control border-2"
                   placeholder="1">
          </div>

          <div class="col-md-4">
            <label class="form-label fw-bold">{{ __('doctor_trans.repeat_interval') }}</label>
            <div class="input-group">
              <span class="input-group-text bg-light">{{ __('doctor_trans.every') }}</span>
              <input type="text"
                     wire:model.defer="repeat_interval"
                     class="form-control border-2"
                     placeholder="{{ __('doctor_trans.interval_placeholder') }}">
            </div>
          </div>

          <div class="col-md-4">
            <label class="form-label fw-bold">{{ __('doctor_trans.on_repeat_action') }}</label>
            <select wire:model.defer="on_repeat_action" class="form-select border-2">
              <option value="duplicate">{{ __('doctor_trans.duplicate_instruction') }}</option>
              <option value="remind">{{ __('doctor_trans.remind_only') }}</option>
              <option value="skip">{{ __('doctor_trans.skip') }}</option>
            </select>
          </div>

          <!-- زر الحفظ -->
          <div class="col-12 text-end mt-3">
            <button class="btn btn-{{ $editId ? 'warning' : 'primary' }} px-4 py-2 rounded-pill">
              <i class="fas fa-{{ $editId ? 'save' : 'plus' }} me-2"></i>
              {{ $editId ? __('doctor_trans.update') : __('doctor_trans.save_instruction') }}
            </button>
          </div>
        </div>
      </form>
    </div>
  </div>

  <!-- سجل التعليمات -->
  <div class="card shadow-sm border-0">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-hover mb-0">
          <thead class="table-light">
            <tr>
              <th>#</th>
              <th>{{ __('doctor_trans.instruction_text') }}</th>
              <th>{{ __('doctor_trans.importance') }}</th>
              <th>{{ __('doctor_trans.status') }}</th>
              <th>{{ __('doctor_trans.specific_time') }}</th>
              <th>{{ __('doctor_trans.date_time') }}</th>
              <th>{{ __('doctor_trans.repeat_count') }}</th>
              <th>{{ __('doctor_trans.repeat_interval') }}</th>
              <th>{{ __('doctor_trans.on_repeat_action') }}</th>
              <th>{{ __('doctor_trans.notes') }}</th>
              <th>{{ __('doctor_trans.created_at') }}</th>
              <th>{{ __('doctor_trans.actions') }}</th>
            </tr>
          </thead>
          <tbody>
            @forelse($instructions as $ins)
              <tr wire:key="instruction-{{ $ins->id }}">
                <td>{{ $ins->id }}</td>
                <td>{{ $ins->instruction }}</td>
                <td>{{ __('doctor_trans.' . $ins->importance) }}</td>
                <td>{{ __('doctor_trans.' . $ins->status) }}</td>
                <td>
                  @if($ins->time_specific)
                    <i class="fas fa-check text-success"></i>
                  @else
                    <i class="fas fa-times text-danger"></i>
                  @endif
                </td>
                <td>{{ $ins->scheduled_at ? $ins->scheduled_at->format('d/m/Y H:i') : '-' }}</td>
                <td>{{ $ins->repeat_count }}</td>
                <td>{{ $ins->repeat_interval ?? '-' }}</td>
                <td>{{ __('doctor_trans.' . $ins->on_repeat_action) }}</td>
                <td>{{ $ins->notes ?? '-' }}</td>
                <td>{{ $ins->created_at->format('d/m/Y H:i') }}</td>
                <td>
                  <div class="dropdown">
                    <button class="btn btn-sm btn-light dropdown-toggle"
                            type="button" data-bs-toggle="dropdown">
                      <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                      @if($ins->status === 'pending')
                        <li>
                          <button type="button"
                                  class="dropdown-item"
                                  wire:click="edit({{ $ins->id }})">
                            <i class="far fa-edit me-2"></i>{{ __('doctor_trans.edit') }}
                          </button>
                        </li>
                        <li>
                          <button type="button"
                                  class="dropdown-item text-danger"
                                  wire:click="delete({{ $ins->id }})"
                                  onclick="confirm('{{ __('doctor_trans.delete_confirm') }}') || event.stopImmediatePropagation()">
                            <i class="far fa-trash-alt me-2"></i>{{ __('doctor_trans.delete') }}
                          </button>
                        </li>
                      @else
                        <li class="dropdown-item text-muted">
                          {{ __('doctor_trans.no_actions_available') }}
                        </li>
                      @endif
                    </ul>
                  </div>
                </td>
              </tr>
            @empty
              <tr>
                <td colspan="12" class="text-center py-4">
                  <div class="d-flex flex-column align-items-center justify-content-center">
                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">{{ __('doctor_trans.no_instructions') }}</h5>
                    <p class="text-muted mb-0">{{ __('doctor_trans.add_new_instructions_guide') }}</p>
                  </div>
                </td>
              </tr>
            @endforelse
          </tbody>
        </table>
      </div>
    </div>
    <div class="card-footer bg-light py-3">
      <div class="d-flex justify-content-between align-items-center">
        <div class="text-muted">
          {{ __('doctor_trans.showing') }}
          <span class="fw-bold">{{ $instructions->firstItem() }}</span>
          {{ __('doctor_trans.to') }}
          <span class="fw-bold">{{ $instructions->lastItem() }}</span>
          {{ __('doctor_trans.of') }}
          <span class="fw-bold">{{ $instructions->total() }}</span>
          {{ __('doctor_trans.results') }}
        </div>
        <div>
          {{ $instructions->links('pagination::bootstrap-5') }}
        </div>
      </div>
    </div>
  </div>
</div>

<!-- ستايل إضافي -->
<style>
  .card { border-radius: 12px; overflow: hidden; }
  .form-control, .form-select { transition: all 0.3s; }
  .form-control:focus, .form-select:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.25rem rgba(13,110,253,0.1);
  }
  .table { font-size: 0.9rem; }
  .table th { font-weight: 600; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 0.5px; }
  .badge { padding: 0.35em 0.65em; font-weight: 500; }
  .dropdown-menu { font-size: 0.85rem; }
</style>
