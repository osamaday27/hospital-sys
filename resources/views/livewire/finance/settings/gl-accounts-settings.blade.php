<div class="container-fluid">
    <!-- عنوان الصفحة مع مسار التنقل -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2 class="fw-bold text-info mb-0">
                <i class="ri-account-pin-box-fill me-2"></i> {{ __('glaccounts_trans.settings_title') }}
            </h2>

        </div>

    </div>

    <!-- رسائل التنبيه -->
    @if (session()->has('success'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
            <i class="ri-checkbox-circle-fill me-2 fs-4"></i>
            <div class="flex-grow-1">{{ session('success') }}</div>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- بطاقة نموذج الإدخال -->
    <div class="card mb-4 border-0 shadow-sm">
        <div
            class="card-header bg-primary bg-opacity-10 border-bottom-0 py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 text-primary">
                <i class="ri-add-circle-line me-2"></i>
                {{ $account_id ? __('glaccounts_trans.edit_account') : __('glaccounts_trans.add_account') }}
            </h5>
            @if ($account_id)
                <span class="badge bg-primary">ID: {{ $account_id }}</span>
            @endif
        </div>
        <div class="card-body">
            <form wire:submit.prevent="save">
                <div class="row g-3">
                    <!-- الصف الأول -->
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">{{ __('glaccounts_trans.code') }} <span
                                class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="ri-hashtag text-muted"></i></span>
                            <input wire:model.defer="code" type="text" class="form-control" placeholder="001">
                        </div>
                        @error('code')
                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold">{{ __('glaccounts_trans.name_ar') }} <span
                                class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="ri-translate text-muted"></i></span>
                            <input wire:model.defer="name_ar" type="text" class="form-control"
                                placeholder="اسم الحساب">
                        </div>
                        @error('name_ar')
                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold">{{ __('glaccounts_trans.name_en') }} <span
                                class="text-danger">*</span></label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="ri-global-line text-muted"></i></span>
                            <input wire:model.defer="name_en" type="text" class="form-control"
                                placeholder="Account Name">
                        </div>
                        @error('name_en')
                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold">{{ __('glaccounts_trans.description') }}</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i class="ri-file-text-line text-muted"></i></span>
                            <input wire:model.defer="description" type="text" class="form-control"
                                placeholder="{{ __('glaccounts_trans.description_placeholder') }}">
                        </div>
                    </div>

                    <!-- الصف الثاني -->
                    <div class="col-md-2">
                        <label class="form-label fw-semibold">{{ __('glaccounts_trans.type') }} <span
                                class="text-danger">*</span></label>
                        <select wire:model.defer="type" class="form-select">
                            <option value="">{{ __('glaccounts_trans.choose') }}</option>
                            <option value="asset">{{ __('glaccounts_trans.asset') }}</option>
                            <option value="liability">{{ __('glaccounts_trans.liability') }}</option>
                            <option value="equity">{{ __('glaccounts_trans.equity') }}</option>
                            <option value="income">{{ __('glaccounts_trans.income') }}</option>
                            <option value="expense">{{ __('glaccounts_trans.expense') }}</option>
                        </select>
                        @error('type')
                            <small class="text-danger d-block mt-1">{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-md-2">
                        <label class="form-label fw-semibold">{{ __('glaccounts_trans.category') }}</label>
                        <select wire:model.defer="category" class="form-select">
                            <option value="">{{ __('glaccounts_trans.choose') }}</option>
                            <option value="cash">{{ __('glaccounts_trans.cash') }}</option>
                            <option value="bank">{{ __('glaccounts_trans.bank') }}</option>
                            <option value="customer">{{ __('glaccounts_trans.customer') }}</option>
                            <option value="supplier">{{ __('glaccounts_trans.supplier') }}</option>
                            <option value="tax">{{ __('glaccounts_trans.tax') }}</option>
                            <option value="other">{{ __('glaccounts_trans.other') }}</option>
                        </select>
                    </div>

                    <div class="col-md-2 position-relative">
                        <label class="form-label fw-semibold">{{ __('glaccounts_trans.parent_account') }}</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i
                                    class="ri-account-box-line text-muted"></i></span>
                            <input type="text" wire:model.debounce.300ms="parent_name" class="form-control"
                                placeholder="{{ __('glaccounts_trans.parent_account_placeholder') }}"
                                wire:click="showAllParents" />

                        </div>

                        @if (!empty($filteredParents))
                            <div class="position-absolute w-100 z-3 mt-1">
                                <div class="card shadow-sm border">
                                    <div class="card-body p-0" style="z-index: 1000;">
                                        <ul class="list-group list-group-flush"
                                            style="max-height: 200px; overflow-y:auto;z-index: 1050;">
                                            @foreach ($filteredParents as $item)
                                                <li class="list-group-item list-group-item-action py-2"
                                                    wire:click="selectParent({{ $item->id }}, '{{ addslashes($item->fullName()) }}')">
                                                    <div class="d-flex align-items-center">
                                                        <i class="ri-folder-2-fill text-warning me-2"></i>
                                                        <span>{{ $item->fullName() }}</span>
                                                    </div>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <div class="col-md-2">
                        <label class="form-label fw-semibold">{{ __('glaccounts_trans.is_group') }}</label>
                        <div class="form-check form-switch">
                            <input wire:model.defer="is_group" class="form-check-input" type="checkbox"
                                role="switch" id="isGroupSwitch">
                            <label class="form-check-label"
                                for="isGroupSwitch">{{ __('glaccounts_trans.is_group_account') }}</label>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label fw-semibold">{{ __('glaccounts_trans.balance_type') }}</label>
                        <select wire:model.defer="balance_type" class="form-select">
                            <option value="debit">{{ __('glaccounts_trans.debit') }}</option>
                            <option value="credit">{{ __('glaccounts_trans.credit') }}</option>
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label fw-semibold">{{ __('glaccounts_trans.opening_balance') }}</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light"><i
                                    class="ri-money-dollar-circle-line text-muted"></i></span>
                            <input wire:model.defer="opening_balance" type="number" class="form-control"
                                step="0.01" placeholder="0.00">
                        </div>
                    </div>
                </div>

                <!-- أزرار الإجراءات -->
                <div class="d-flex justify-content-end mt-4 gap-2">
                    <button type="button" wire:click="resetFields" class="btn btn-outline-secondary">
                        <i class="ri-close-line me-1"></i> {{ __('glaccounts_trans.cancel') }}
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="ri-save-line me-1"></i>
                        {{ $account_id ? __('glaccounts_trans.update') : __('glaccounts_trans.save') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- بطاقة عرض شجرة الحسابات -->
    <div class="card border-0 shadow-sm">
        <div class="card-header bg-primary bg-opacity-10 border-bottom-0 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 text-primary">
                    <i class="ri-account-tree-line me-2"></i> {{ __('glaccounts_trans.chart_of_accounts') }}
                </h5>
                <div class="w-25">
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="ri-search-line text-muted"></i></span>
                        <input wire:model.debounce.300ms="search" type="text" class="form-control"
                            placeholder="{{ __('glaccounts_trans.search') }}">
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th width="10%">{{ __('glaccounts_trans.code') }}</th>
                            <th width="25%">{{ __('glaccounts_trans.name') }}</th>
                            <th width="15%">{{ __('glaccounts_trans.type') }}</th>
                            <th width="15%">{{ __('glaccounts_trans.category') }}</th>
                            <th width="15%">{{ __('glaccounts_trans.balance') }}</th>
                            <th width="20%">{{ __('glaccounts_trans.actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            function renderAccountsTree($accounts, $level = 0, $expandedAccounts = [])
                            {
                                foreach ($accounts as $acc) {
                                    $indent = str_repeat('&nbsp;&nbsp;&nbsp;&nbsp;', $level);
                                    $hasChildren = $acc->children->count() > 0;
                                    $isExpanded = in_array($acc->id, $expandedAccounts);

                                    echo '<tr class="' . ($isExpanded ? 'bg-light' : '') . '">';
                                    echo '<td class="align-middle">' . $indent;

                                    if ($hasChildren) {
                                        $icon = $isExpanded
                                            ? 'ri-folder-open-fill text-warning'
                                            : 'ri-folder-fill text-warning';
                                        echo '<a wire:click.prevent="toggleAccount(' .
                                            $acc->id .
                                            ')" href="#" class="me-1"><i class="ri ' .
                                            $icon .
                                            '"></i></a>';
                                    }

                                    echo e($acc->code) . '</td>';
                                    echo '<td class="align-middle">';
                                    echo '<div class="d-flex align-items-center">';
                                    echo $acc->is_group
                                        ? '<i class="ri-folder-2-fill text-warning me-2"></i>'
                                        : '<i class="ri-file-text-line text-info me-2"></i>';
                                    echo e($acc->fullName());
                                    echo '</div></td>';
                                    echo '<td class="align-middle"><span class="badge bg-' .
                                        e($acc->type_color) .
                                        '-subtle text-' .
                                        e($acc->type_color) .
                                        '">' .
                                        e($acc->type_translated) .
                                        '</span></td>';
                                    echo '<td class="align-middle">' . e($acc->category_translated ?? '-') . '</td>';
                                    echo '<td class="align-middle"><span class="fw-semibold text-' .
                                        ($acc->balance_type === 'debit' ? 'success' : 'danger') .
                                        '">' .
                                        number_format($acc->calculated_balance, 2) .
                                        '</span></td>';

                                    echo '<td class="align-middle">';
                                    echo '<div class="d-flex gap-2">';
                                    echo '<button wire:click="edit(' .
                                        $acc->id .
                                        ')" class="btn btn-sm btn-outline-primary" title="' .
                                        __('glaccounts_trans.edit') .
                                        '"><i class="ri-edit-line"></i></button>';
                                    echo '<button wire:click="viewDetails(' .
                                        $acc->id .
                                        ')" class="btn btn-sm btn-outline-info" title="' .
                                        __('glaccounts_trans.view_details') .
                                        '" data-bs-toggle="modal" data-bs-target="#accountDetailsModal"><i class="ri-eye-line"></i></button>';
                                    if (
                                        $acc->children->count() == 0 &&
                                        $acc->debit_entries_count == 0 &&
                                        $acc->credit_entries_count == 0
                                    ) {
                                        echo '<button wire:click="confirmDelete(' .
                                            $acc->id .
                                            ')" class="btn btn-sm btn-outline-danger" title="' .
                                            __('glaccounts_trans.delete') .
                                            '"><i class="ri-delete-bin-line"></i></button>';
                                    }
                                    echo '</div></td>';
                                    echo '</tr>';

                                    if ($isExpanded) {
                                        renderAccountsTree($acc->children, $level + 1, $expandedAccounts);
                                    }
                                }
                            }
                        @endphp

                        @php
                            renderAccountsTree($accounts, 0, $expandedAccounts);
                        @endphp

                        @if ($accounts->isEmpty())
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <i class="ri-database-2-line fs-3"></i>
                                    <p class="mt-2">{{ __('glaccounts_trans.no_accounts_found') }}</p>
                                </td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>

            <!-- التقسيم الصفحي -->
            @if ($accounts->hasPages())
                <div class="card-footer bg-light border-top-0 py-3 d-flex justify-content-center">
                    {{ $accounts->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- مودال عرض التفاصيل -->
    <div wire:ignore.self class="modal fade" id="accountDetailsModal" tabindex="-1"
        aria-labelledby="accountDetailsLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-primary text-white" style="background-color: #093960 !important;">
                    <h5 class="modal-title" style="color: #fff;margin-bottom: 15px;">
                        <i class="ri-information-line me-2"></i> {{ __('glaccounts_trans.account_details') }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label
                                    class="form-label fw-semibold text-muted">{{ __('glaccounts_trans.code') }}</label>
                                <p class="form-control-plaintext fw-bold">{{ $code }}</p>
                            </div>

                            <div class="mb-3">
                                <label
                                    class="form-label fw-semibold text-muted">{{ __('glaccounts_trans.name_ar') }}</label>
                                <p class="form-control-plaintext">{{ $name_ar }}</p>
                            </div>

                            <div class="mb-3">
                                <label
                                    class="form-label fw-semibold text-muted">{{ __('glaccounts_trans.name_en') }}</label>
                                <p class="form-control-plaintext">{{ $name_en }}</p>
                            </div>

                            <div class="mb-3">
                                <label
                                    class="form-label fw-semibold text-muted">{{ __('glaccounts_trans.description') }}</label>
                                <p class="form-control-plaintext">{{ $description ?: '-' }}</p>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="mb-3">
                                <label
                                    class="form-label fw-semibold text-muted">{{ __('glaccounts_trans.type') }}</label>
                                <p class="form-control-plaintext">
                                    <span
                                        class="badge bg-{{ $this->getTypeColor($type) }}-subtle text-{{ $this->getTypeColor($type) }}">
                                        {{ $type }}
                                    </span>
                                </p>
                            </div>

                            <div class="mb-3">
                                <label
                                    class="form-label fw-semibold text-muted">{{ __('glaccounts_trans.category') }}</label>
                                <p class="form-control-plaintext">{{ $category ?: '-' }}</p>
                            </div>

                            <div class="mb-3">
                                <label
                                    class="form-label fw-semibold text-muted">{{ __('glaccounts_trans.balance_type') }}</label>
                                <p class="form-control-plaintext">
                                    <span
                                        class="badge bg-{{ $balance_type === 'debit' ? 'success' : 'danger' }}-subtle text-{{ $balance_type === 'debit' ? 'success' : 'danger' }}">
                                        {{ $balance_type }}
                                    </span>
                                </p>
                            </div>

                            <div class="mb-3">
                                <label
                                    class="form-label fw-semibold text-muted">{{ __('glaccounts_trans.current_balance') }}</label>
                                <p
                                    class="form-control-plaintext fw-bold fs-5 text-{{ $balance_type === 'debit' ? 'success' : 'danger' }}">
                                    {{ number_format($current_balance, 2) }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="ri-close-line me-1"></i> {{ __('glaccounts_trans.close') }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('livewire:load', function() {
        // إعادة تحميل المودال عند تغيير البيانات
        Livewire.on('showDetailsModal', () => {
            var modal = new bootstrap.Modal(document.getElementById('accountDetailsModal'));
            modal.show();
        });

        // تأكيد الحذف
        window.addEventListener('swal:confirm', event => {
            Swal.fire({
                title: event.detail.title,
                text: event.detail.text,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: event.detail.confirmText,
                cancelButtonText: event.detail.cancelText
            }).then((result) => {
                if (result.isConfirmed) {
                    Livewire.emit(event.detail.event);
                }
            });
        });

        // إظهار رسالة نجاح
        window.addEventListener('swal:success', event => {
            Swal.fire({
                title: event.detail.title,
                text: event.detail.text,
                icon: 'success',
                confirmButtonColor: '#3085d6',
            });
        });
    });
</script>
