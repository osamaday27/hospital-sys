<div>
    <div id="toast-container" style="position: fixed; top: 20px; right: 20px; z-index: 2000;"></div>

    <!-- 🔍 البحث -->
    <div class="mb-3">
        <input type="text" class="form-control"
               placeholder="{{ trans('surgical_trans.search_member') }}"
               wire:model.debounce.500ms="search">
    </div>

    <div class="row g-3 mb-4">
        <!-- 🩺 الفريق -->
        <div class="col-md-6" wire:ignore>
            <label>{{ trans('surgical_trans.select_medical_team') }}</label>
            <select id="medicalTeamSelect" class="form-select select2">
                <option value="">{{ trans('surgical_trans.select_option') }}</option>
                @foreach($employees as $emp)
                    <option value="{{ $emp->id }}">{{ $emp->full_name }}</option>
                @endforeach
            </select>
            @error('medical_team_id') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <!-- 👨‍⚕️ الدور -->
        <div class="col-md-6" wire:ignore>
            <label>{{ trans('surgical_trans.role') }}</label>
            <select id="roleSelect" class="form-select select2">
                <option value="">{{ trans('surgical_trans.select_option') }}</option>
                <option value="surgeon">{{ trans('surgical_trans.role_surgeon') }}</option>
                <option value="assistant_surgeon">{{ trans('surgical_trans.role_assistant_surgeon') }}</option>
                <option value="anesthesiologist">{{ trans('surgical_trans.role_anesthesiologist') }}</option>
                <option value="nurse">{{ trans('surgical_trans.role_nurse') }}</option>
                <option value="technician">{{ trans('surgical_trans.role_technician') }}</option>
                <option value="other">{{ trans('surgical_trans.role_other') }}</option>
            </select>
            @error('role') <small class="text-danger">{{ $message }}</small> @enderror
        </div>

        <div class="col-12">
            <button class="btn btn-primary" wire:click="save">
                {{ $editingId ? trans('surgical_trans.update') : trans('surgical_trans.add') }}
            </button>
            @if($editingId)
                <button class="btn btn-secondary" wire:click="resetForm">
                    {{ trans('surgical_trans.cancel_edit') }}
                </button>
            @endif
        </div>
    </div>

    <!-- 📋 الجدول -->
    <table class="table table-bordered table-striped">
        <thead class="table-light">
            <tr>
                <th>#</th>
                <th>{{ trans('surgical_trans.member_name') }}</th>
                <th>{{ trans('surgical_trans.role') }}</th>
                <th>{{ trans('surgical_trans.added_by') }}</th>
                <th>{{ trans('surgical_trans.options') }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($teams as $team)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ $team->medical_team->full_name ?? '-' }}</td>
                    <td>{{ trans('surgical_trans.role_' . $team->role) }}</td>
                    <td>{{ $team->user->name ?? '-' }}</td>
                    <td>
                        <button class="btn btn-sm btn-warning" wire:click="edit({{ $team->id }})">
                            {{ trans('surgical_trans.edit') }}
                        </button>
                        <button class="btn btn-sm btn-danger"
                            wire:click="delete({{ $team->id }})"
                            onclick="confirm('{{ trans('surgical_trans.confirm_delete') }}') || event.stopImmediatePropagation()">
                            {{ trans('surgical_trans.delete') }}
                        </button>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="text-center">{{ trans('surgical_trans.no_data') }}</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $teams->links() }}
</div>


<script>
document.addEventListener('livewire:load', function () {

    function initSelect2() {
        $('#medicalTeamSelect').select2({ width: '100%', allowClear: true });
        $('#roleSelect').select2({ width: '100%', allowClear: true });

        $('#medicalTeamSelect').on('change', function (e) {
            @this.set('medical_team_id', $(this).val());
        });
        $('#roleSelect').on('change', function (e) {
            @this.set('role', $(this).val());
        });
    }

    initSelect2();

    Livewire.hook('message.processed', () => initSelect2());

    // 🧹 تفريغ select2 بعد الإضافة
    window.addEventListener('clear-select2', () => {
        $('#medicalTeamSelect').val(null).trigger('change');
        $('#roleSelect').val(null).trigger('change');
    });

    // ✏️ تعبئة select2 عند التعديل
    window.addEventListener('fill-select2', event => {
        $('#medicalTeamSelect').val(event.detail.medical_team_id).trigger('change');
        $('#roleSelect').val(event.detail.role).trigger('change');
    });

    // 🔔 التنبيهات
    window.addEventListener('show-toast', event => {
        const { type, message } = event.detail;
        const toast = document.createElement('div');
        toast.className = `alert alert-${type} alert-dismissible fade show shadow-sm`;
        toast.style.minWidth = '280px';
        toast.innerHTML = `<strong>${message}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>`;
        document.getElementById('toast-container').appendChild(toast);
        setTimeout(() => toast.remove(), 4000);
    });
});
</script>

