{{-- Surgery Services / Items / Drugs - Blade View (MDI + Polished UI) --}}
<div class="surgery-services-container">
    <div class="mt-4">
        <div class="card surgical-card">
            <div class="card-header surgical-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="header-title d-flex align-items-center gap-2">
                        <i class="mdi mdi-hospital-bed me-2 fs-4"></i>
                        <h5 style="color: #fff !important;" class="mb-0">
                            {{ __('finance_trans.surgery_services_items') }}
                        </h5>
                    </div>
                    <div class="surgery-badge">
                        <span class="badge d-inline-flex align-items-center gap-1">
                            <i class="mdi mdi-needle me-1"></i>
                            {{ __('Surgery ID') }}: {{ $surgery_id }}
                        </span>
                    </div>
                </div>
            </div>

            <div class="card-body surgical-body">
                <!-- Surgical Services Section -->
                <div class="service-section">
                    <div class="section-header">
                        <div class="header-content">
                            <i class="mdi mdi-clipboard-text-outline me-2"></i>
                            <h6 class="mb-0">{{ __('finance_trans.linked_services') }}</h6>
                            <span class="item-count">{{ count($services) }}</span>
                        </div>
                    </div>

                    <div class="section-content">

                        {{-- ====== نموذج الإضافة أو التعديل (Inline) للخدمات ====== --}}
                        @if($edit_service_row_id)
                            <form wire:submit.prevent="saveServiceEdit" class="service-form">
                                <div class="form-row">
                                    <div class="form-group">
                                        <label>{{ __('finance_trans.service') }}</label>
                                        <select wire:model="edit_service_service_id" class="form-control select2-searchable" data-placeholder="{{ __('finance_trans.choose_service') }}">
                                            <option value="">{{ __('finance_trans.choose_service') }}</option>
                                            @foreach($available_services as $serv)
                                                <option value="{{ $serv->id }}">{{ $serv->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>{{ __('finance_trans.type') }}</label>
                                        <select wire:model="edit_service_type" class="form-control select2-searchable" data-placeholder="{{ __('finance_trans.Type') }}">
                                            <option value="">{{ __('finance_trans.Type') }}</option>
                                            <option value="1">{{ __('finance_trans.Service') }}</option>
                                            <option value="2">{{ __('finance_trans.Analysis') }}</option>
                                            <option value="3">{{ __('finance_trans.Radiology') }}</option>
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>{{ __('finance_trans.notes') }}</label>
                                        <div class="input-with-icon">
                                            <i class="mdi mdi-text-box-outline"></i>
                                            <input type="text" wire:model.defer="edit_service_notes" class="form-control" placeholder="{{ __('Optional notes') }}">
                                        </div>
                                    </div>

                                    <div class="form-group add-btn d-flex gap-2">
                                        <button type="submit" class="btn-add">
                                            <i class="mdi mdi-content-save"></i>
                                            <span>{{ __('Save') }}</span>
                                        </button>
                                        <button type="button" class="btn-cancel" onclick="@this.call('cancelServiceEdit')">
                                            <i class="mdi mdi-close-circle-outline"></i>
                                            <span>{{ __('Cancel') }}</span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        @else
                            <form wire:submit.prevent="addService" class="service-form">
                                <div class="form-row">
                                    <div class="form-group search-group">
                                        <label for="serviceSearch">{{ __('finance_trans.search_services') }}</label>
                                        <div class="input-with-icon">
                                            <i class="mdi mdi-magnify"></i>
                                            <input type="text" id="serviceSearch" wire:model.debounce.500ms="searchService"
                                                   placeholder="{{ __('Type to search...') }}" class="form-control">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="serviceSelect">{{ __('finance_trans.service') }}</label>
                                        <select wire:model="service_id" id="serviceSelect" class="form-control select2-searchable" data-placeholder="{{ __('finance_trans.choose_service') }}">
                                            <option value="">{{ __('finance_trans.choose_service') }}</option>
                                            @foreach($available_services as $serv)
                                                <option value="{{ $serv->id }}">{{ $serv->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="serviceType">{{ __('finance_trans.type') }}</label>
                                        <select wire:model="service_type" id="serviceType" class="form-control select2-searchable" data-placeholder="{{ __('finance_trans.Type') }}">
                                            <option value="">{{ __('finance_trans.Type') }}</option>
                                            <option value="1">{{ __('finance_trans.Service') }}</option>
                                            <option value="2">{{ __('finance_trans.Analysis') }}</option>
                                            <option value="3">{{ __('finance_trans.Radiology') }}</option>
                                        </select>
                                    </div>

                                    <div class="form-group add-btn">
                                        <button type="submit" class="btn-add">
                                            <i class="mdi mdi-plus"></i>
                                            <span>{{ __('finance_trans.add') }}</span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        @endif

                        <div class="service-table-container">
                            <table class="surgical-table">
                                <thead>
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="30%">{{ __('finance_trans.service') }}</th>
                                        <th width="20%">{{ __('finance_trans.type') }}</th>
                                        <th width="35%">{{ __('finance_trans.notes') }}</th>
                                        <th width="10%">{{ __('finance_trans.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($services as $i => $s)
                                        <tr wire:key="service-row-{{ $s->id }}">
                                            <td>{{ $i + 1 }}</td>
                                            <td><strong>{{ optional($s->service)->name }}</strong></td>
                                            <td>
                                                <span class="service-type-badge
                                                    @if($s->service_type == 1) type-service
                                                    @elseif($s->service_type == 2) type-analysis
                                                    @else type-radiology
                                                    @endif">
                                                    @if($s->service_type == 1) {{ __('Service') }}
                                                    @elseif($s->service_type == 2) {{ __('Analysis') }}
                                                    @else {{ __('Radiology') }}
                                                    @endif
                                                </span>
                                            </td>
                                            <td>
                                                @if($s->notes)
                                                    <div class="notes-content">{{ $s->notes }}</div>
                                                @else
                                                    <div class="no-notes d-inline-flex align-items-center gap-1">
                                                        <i class="mdi mdi-text-box-outline"></i>{{ __('No notes') }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="d-flex gap-2">
                                                <button type="button" class="btn-icon-edit"
                                                        wire:click="startEditService({{ $s->id }})"
                                                        title="{{ __('Edit') }}">
                                                    <i class="mdi mdi-pencil-outline"></i>
                                                </button>

                                                <button type="button" class="btn-icon-danger"
                                                        onclick="confirmDelete(() => @this.call('deleteService', {{ $s->id }}))"
                                                        title="{{ __('finance_trans.delete') }}">
                                                    <i class="mdi mdi-trash-can-outline"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr class="empty-row">
                                            <td colspan="5">
                                                <div class="empty-state">
                                                    <i class="mdi mdi-format-list-checkbox"></i>
                                                    <p class="mb-0">{{ __('No services added yet') }}</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Surgical Supplies Section -->
                <div class="supplies-section mt-4">
                    <div class="section-header">
                        <div class="header-content">
                            <i class="mdi mdi-package-variant-closed me-2"></i>
                            <h6 class="mb-0">{{ __('finance_trans.required_items') }}</h6>
                            <span class="item-count">{{ count($items) }}</span>
                        </div>
                    </div>

                    <div class="section-content">

                        {{-- ====== نموذج الإضافة أو التعديل (Inline) للأصناف ====== --}}
                        @if($edit_item_row_id)
                            <form wire:submit.prevent="saveItemEdit" class="supplies-form">
                                <div class="form-row">
                                    <div class="form-group">
                                        <label>{{ __('finance_trans.item') }}</label>
                                        <select wire:model="edit_item_product_id" class="form-control select2-searchable" data-placeholder="{{ __('finance_trans.choose_item') }}">
                                            <option value="">{{ __('finance_trans.choose_item') }}</option>
                                            @foreach($available_items as $it)
                                                <option value="{{ $it->id }}">{{ $it->name_ar }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group quantity-group">
                                        <label>{{ __('finance_trans.quantity') }}</label>
                                        <div class="input-with-icon">
                                            <i class="mdi mdi-counter"></i>
                                            <input type="number" wire:model="edit_item_quantity"
                                                   class="form-control" placeholder="0" min="0" step="1">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label>{{ __('finance_trans.unit') }}</label>
                                        <select wire:model="edit_item_unit" class="form-control select2-searchable" data-placeholder="{{ __('Unit') }}">
                                            <option value="">{{ __('Unit') }}</option>
                                            @foreach($edit_item_units as $u)
                                                @if(!empty($u['id']))
                                                    <option value="{{ $u['name'] }}">{{ $u['name'] }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>{{ __('finance_trans.notes') }}</label>
                                        <div class="input-with-icon">
                                            <i class="mdi mdi-text-box-outline"></i>
                                            <input type="text" wire:model.defer="edit_item_notes" class="form-control" placeholder="{{ __('Optional notes') }}">
                                        </div>
                                    </div>

                                    <div class="form-group add-btn d-flex gap-2">
                                        <button type="submit" class="btn-add">
                                            <i class="mdi mdi-content-save"></i>
                                            <span>{{ __('Save') }}</span>
                                        </button>
                                        <button type="button" class="btn-cancel" onclick="@this.call('cancelItemEdit')">
                                            <i class="mdi mdi-close-circle-outline"></i>
                                            <span>{{ __('Cancel') }}</span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        @else
                            <form wire:submit.prevent="addItem" class="supplies-form">
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="itemSelect">{{ __('finance_trans.item') }}</label>
                                        <select wire:model="item_id" id="itemSelect" class="form-control select2-searchable" data-placeholder="{{ __('finance_trans.choose_item') }}">
                                            <option value="">{{ __('finance_trans.choose_item') }}</option>
                                            @foreach($available_items as $item)
                                                <option value="{{ $item->id }}">{{ $item->name_ar }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group quantity-group">
                                        <label for="itemQuantity">{{ __('finance_trans.quantity') }}</label>
                                        <div class="input-with-icon">
                                            <i class="mdi mdi-counter"></i>
                                            <input type="number" wire:model="item_quantity" id="itemQuantity"
                                                   class="form-control" placeholder="0" min="0" step="1">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="itemUnit">{{ __('finance_trans.unit') }}</label>
                                        <select wire:model="item_unit" id="itemUnit" class="form-control select2-searchable" data-placeholder="{{ __('Unit') }}">
                                            <option value="">{{ __('Unit') }}</option>
                                            @foreach($item_units as $u)
                                                @if(!empty($u['id']))
                                                    <option value="{{ $u['name'] }}">{{ $u['name'] }}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="itemNotes">{{ __('finance_trans.notes') }}</label>
                                        <div class="input-with-icon">
                                            <i class="mdi mdi-text-box-outline"></i>
                                            <input type="text" wire:model="item_notes" id="itemNotes"
                                                   class="form-control" placeholder="{{ __('Optional notes') }}">
                                        </div>
                                    </div>

                                    <div class="form-group add-btn">
                                        <button type="submit" class="btn-add">
                                            <i class="mdi mdi-plus"></i>
                                            <span>{{ __('finance_trans.add') }}</span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        @endif

                        <div class="supplies-table-container">
                            <table class="surgical-table">
                                <thead>
                                    <tr>
                                        <th width="5%" style="text-align: start;">#</th>
                                        <th width="25%" style="text-align: start;">{{ __('finance_trans.item') }}</th>
                                        <th width="12%" style="text-align: start;">{{ __('finance_trans.quantity') }}</th>
                                        <th width="12%" style="text-align: start;">{{ __('finance_trans.unit') }}</th>
                                        <th width="36%" style="text-align: start;">{{ __('finance_trans.notes') }}</th>
                                        <th width="10%" style="text-align: start;">{{ __('finance_trans.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($items as $i => $item)
                                        <tr wire:key="item-row-{{ $item->id }}">
                                            <td>{{ $i + 1 }}</td>
                                            <td><strong>{{ optional($item->item)->name_ar }}</strong></td>
                                            <td class="quantity-cell">{{ $item->quantity }}</td>
                                            <td><span class="unit-badge">{{ $item->unit }}</span></td>
                                            <td>
                                                @if($item->notes)
                                                    <div class="notes-content">{{ $item->notes }}</div>
                                                @else
                                                    <div class="no-notes d-inline-flex align-items-center gap-1">
                                                        <i class="mdi mdi-text-box-outline"></i>{{ __('No notes') }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="d-flex gap-2">
                                                <button type="button" class="btn-icon-edit"
                                                        wire:click="startEditItem({{ $item->id }})"
                                                        title="{{ __('Edit') }}">
                                                    <i class="mdi mdi-pencil-outline"></i>
                                                </button>

                                                <button type="button" class="btn-icon-danger"
                                                        onclick="confirmDelete(() => @this.call('deleteItem', {{ $item->id }}))"
                                                        title="{{ __('finance_trans.delete') }}">
                                                    <i class="mdi mdi-trash-can-outline"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr class="empty-row">
                                            <td colspan="6">
                                                <div class="empty-state">
                                                    <i class="mdi mdi-archive-outline"></i>
                                                    <p class="mb-0">{{ __('No items added yet') }}</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Medications Section -->
                <div class="medications-section mt-4">
                    <div class="section-header">
                        <div class="header-content">
                            <i class="mdi mdi-pill me-2"></i>
                            <h6 class="mb-0">{{ __('finance_trans.required_drugs') }}</h6>
                            <span class="item-count">{{ count($drugs) }}</span>
                        </div>
                    </div>

                    <div class="section-content">

                        {{-- ====== نموذج الإضافة أو التعديل (Inline) للأدوية ====== --}}
                        @if($edit_drug_row_id)
                            <form wire:submit.prevent="saveDrugEdit" class="medications-form">
                                <div class="form-row">
                                    <div class="form-group">
                                        <label>{{ __('finance_trans.drug') }}</label>
                                        <select wire:model="edit_drug_product_id" class="form-control select2-searchable" data-placeholder="{{ __('finance_trans.choose_drug') }}">
                                            <option value="">{{ __('finance_trans.choose_drug') }}</option>
                                            @foreach($available_drugs as $d)
                                                <option value="{{ $d->id }}">{{ $d->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label>{{ __('finance_trans.dose') }}</label>
                                        <div class="input-with-icon">
                                            <i class="mdi mdi-eyedropper-variant"></i>
                                            <input type="number" wire:model="edit_drug_dose"
                                                   class="form-control" placeholder="0" min="0" step="0.01">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label>{{ __('finance_trans.unit') }}</label>
                                        <div class="input-with-icon">
                                            <i class="mdi mdi-scale-balance"></i>
                                            <input type="text" wire:model="edit_drug_unit"
                                                   class="form-control" placeholder="{{ __('Unit') }}">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label>{{ __('finance_trans.notes') }}</label>
                                        <div class="input-with-icon">
                                            <i class="mdi mdi-text-box-outline"></i>
                                            <input type="text" wire:model.defer="edit_drug_notes"
                                                   class="form-control" placeholder="{{ __('Optional notes') }}">
                                        </div>
                                    </div>

                                    <div class="form-group add-btn d-flex gap-2">
                                        <button type="submit" class="btn-add">
                                            <i class="mdi mdi-content-save"></i>
                                            <span>{{ __('Save') }}</span>
                                        </button>
                                        <button type="button" class="btn-cancel" onclick="@this.call('cancelDrugEdit')">
                                            <i class="mdi mdi-close-circle-outline"></i>
                                            <span>{{ __('Cancel') }}</span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        @else
                            <form wire:submit.prevent="addDrug" class="medications-form">
                                <div class="form-row">
                                    <div class="form-group">
                                        <label for="drugSelect">{{ __('finance_trans.drug') }}</label>
                                        <select wire:model="drug_id" id="drugSelect" class="form-control select2-searchable" data-placeholder="{{ __('finance_trans.choose_drug') }}">
                                            <option value="">{{ __('finance_trans.choose_drug') }}</option>
                                            @foreach($available_drugs as $drug)
                                                <option value="{{ $drug->id }}">{{ $drug->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="form-group">
                                        <label for="drugDose">{{ __('finance_trans.dose') }}</label>
                                        <div class="input-with-icon">
                                            <i class="mdi mdi-eyedropper-variant"></i>
                                            <input type="number" wire:model="drug_dose" id="drugDose"
                                                   class="form-control" placeholder="0" min="0" step="0.01">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="drugUnit">{{ __('finance_trans.unit') }}</label>
                                        <div class="input-with-icon">
                                            <i class="mdi mdi-scale-balance"></i>
                                            <input type="text" wire:model="drug_unit" id="drugUnit"
                                                   class="form-control" placeholder="{{ __('Unit') }}">
                                        </div>
                                    </div>

                                    <div class="form-group">
                                        <label for="drugNotes">{{ __('finance_trans.notes') }}</label>
                                        <div class="input-with-icon">
                                            <i class="mdi mdi-text-box-outline"></i>
                                            <input type="text" wire:model="drug_notes" id="drugNotes"
                                                   class="form-control" placeholder="{{ __('Optional notes') }}">
                                        </div>
                                    </div>

                                    <div class="form-group add-btn">
                                        <button type="submit" class="btn-add">
                                            <i class="mdi mdi-plus"></i>
                                            <span>{{ __('finance_trans.add') }}</span>
                                        </button>
                                    </div>
                                </div>
                            </form>
                        @endif

                        <div class="medications-table-container">
                            <table class="surgical-table">
                                <thead>
                                    <tr>
                                        <th width="5%">#</th>
                                        <th width="28%">{{ __('finance_trans.drug') }}</th>
                                        <th width="18%">{{ __('finance_trans.dose') }}</th>
                                        <th width="18%">{{ __('finance_trans.unit') }}</th>
                                        <th width="21%">{{ __('finance_trans.notes') }}</th>
                                        <th width="10%">{{ __('finance_trans.actions') }}</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($drugs as $i => $drug)
                                        <tr wire:key="drug-row-{{ $drug->id }}">
                                            <td>{{ $i + 1 }}</td>
                                            <td><strong>{{ optional($drug->drug)->name }}</strong></td>
                                            <td class="dose-cell">{{ $drug->dose }}</td>
                                            <td><span class="unit-badge">{{ $drug->unit }}</span></td>
                                            <td>
                                                @if($drug->notes)
                                                    <div class="notes-content">{{ $drug->notes }}</div>
                                                @else
                                                    <div class="no-notes d-inline-flex align-items-center gap-1">
                                                        <i class="mdi mdi-text-box-outline"></i>{{ __('No notes') }}
                                                    </div>
                                                @endif
                                            </td>
                                            <td class="d-flex gap-2">
                                                <button type="button" class="btn-icon-edit"
                                                        wire:click="startEditDrug({{ $drug->id }})"
                                                        title="{{ __('Edit') }}">
                                                    <i class="mdi mdi-pencil-outline"></i>
                                                </button>

                                                <button type="button" class="btn-icon-danger"
                                                        onclick="confirmDelete(() => @this.call('deleteDrug', {{ $drug->id }}))"
                                                        title="{{ __('finance_trans.delete') }}">
                                                    <i class="mdi mdi-trash-can-outline"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr class="empty-row">
                                            <td colspan="6">
                                                <div class="empty-state">
                                                    <i class="mdi mdi-bottle-tonic-plus-outline"></i>
                                                    <p class="mb-0">{{ __('No drugs added yet') }}</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div><!-- /Medications -->
            </div>
        </div>
    </div>
</div>

<style>
:root{
    --primary-color:#2a5bd7;
    --primary-light:#e8f0fe;
    --secondary-color:#6c757d;
    --success-color:#28a745;
    --info-color:#17a2b8;
    --warning-color:#ffc107;
    --danger-color:#dc3545;
    --light-color:#f8f9fa;
    --dark-color:#343a40;
    --border-radius:12px;
    --box-shadow:0 8px 20px rgba(0,0,0,.08);
    --transition:all .25s ease;
}

.surgery-services-container{font-family:'Segoe UI',Roboto,'Helvetica Neue',Arial,sans-serif;color:#333}
.surgical-card{border:0;border-radius:var(--border-radius);overflow:hidden;box-shadow:var(--box-shadow)}
.surgical-header{background:linear-gradient(135deg,var(--primary-color),#1a4bc3);color:#fff;padding:1.1rem 1.25rem;border-bottom:0}
.surgery-badge .badge{background:rgba(255,255,255,.18);color:#fff;font-weight:600;padding:.45rem .75rem;border-radius:999px;font-size:.84rem}
.surgical-body{padding:0}
.section-header{background:var(--primary-light);color:var(--primary-color);padding:.75rem 1.1rem;border-bottom:1px solid rgba(0,0,0,.05)}
.header-content{display:flex;align-items:center;gap:.5rem}
.header-content h6{margin:0;font-weight:700;font-size:1rem}
.item-count{background:var(--primary-color);color:#fff;font-size:.73rem;padding:.18rem .5rem;border-radius:10px;font-weight:700}
.section-content{padding:1.1rem}
.form-row{display:flex;gap:1rem;margin-bottom:1.2rem;flex-wrap:wrap}
.form-group{flex:1;min-width:220px}
.form-group label{display:block;margin-bottom:.45rem;font-size:.85rem;font-weight:600;color:var(--secondary-color)}
.form-control{width:100%;padding:.625rem .75rem;border:1px solid #e2e6ea;border-radius:10px;font-size:.92rem;transition:var(--transition);background:#fff}
.form-control:focus{border-color:var(--primary-color);box-shadow:0 0 0 .2rem rgba(42,91,215,.18);outline:0}
.input-with-icon{position:relative}
.input-with-icon i{position:absolute;left:.75rem;top:50%;transform:translateY(-50%);color:var(--secondary-color);font-size:1rem}
.input-with-icon input{padding-left:2.25rem}
.add-btn{display:flex;align-items:flex-end}
.btn-add{background:var(--primary-color);color:#fff;border:0;padding:.62rem 1.1rem;border-radius:12px;font-weight:700;font-size:.95rem;cursor:pointer;transition:var(--transition);display:inline-flex;align-items:center;gap:.5rem;height:44px;box-shadow:0 6px 14px rgba(42,91,215,.25)}
.btn-add:hover{background:#1a4bc3;transform:translateY(-1px)}
.btn-add:active{transform:translateY(0)}
.btn-cancel{background:#adb5bd;color:#fff;border:0;padding:.62rem 1.1rem;border-radius:12px;font-weight:700;font-size:.95rem;cursor:pointer;transition:var(--transition);display:inline-flex;align-items:center;gap:.5rem;height:44px}
.btn-cancel:hover{background:#8f9aa3;transform:translateY(-1px)}

.surgical-table{width:100%;border-collapse:separate;border-spacing:0;margin-top:.6rem}
.surgical-table thead{background:#f6f8fb}
.surgical-table th{padding:.75rem 1rem;text-align:left;font-size:.82rem;font-weight:800;color:#5b6370;text-transform:uppercase;letter-spacing:.4px;border-bottom:2px solid #e6ebf2}
.surgical-table td{padding:1rem;border-bottom:1px solid #ecf0f4;vertical-align:middle;font-size:.93rem}
.surgical-table tr:last-child td{border-bottom:0}
.surgical-table tr:hover{background:#fafbff}

.service-type-badge{display:inline-flex;align-items:center;gap:.35rem;padding:.35rem .7rem;border-radius:999px;font-size:.78rem;font-weight:800}
.type-service{background:rgba(40,167,69,.1);color:var(--success-color)}
.type-analysis{background:rgba(23,162,184,.1);color:var(--info-color)}
.type-radiology{background:rgba(255,193,7,.12);color:#d39e00}

.unit-badge{display:inline-block;padding:.32rem .65rem;border-radius:999px;font-size:.78rem;font-weight:800;background:rgba(108,117,125,.1);color:var(--secondary-color)}
.quantity-cell,.dose-cell{font-weight:800;color:var(--dark-color)}
.notes-content{color:#6c7a89;font-size:.88rem;line-height:1.5}
.no-notes{color:#adb5bd;font-style:italic;font-size:.86rem}
.empty-row td{padding:2rem 1rem;text-align:center}
.empty-state{display:flex;flex-direction:column;align-items:center;gap:.5rem;color:#adb5bd}
.empty-state i{font-size:2rem;margin-bottom:.25rem}
.empty-state p{margin:0;font-size:.92rem}

/* Action buttons */
.btn-icon-danger,.btn-icon-edit{background:transparent;border:1px solid transparent;cursor:pointer;padding:6px 10px;border-radius:10px;transition:var(--transition)}
.btn-icon-danger i{color:var(--danger-color);font-size:1.15rem;line-height:1}
.btn-icon-danger:hover{background:rgba(220,53,69,.08);border-color:rgba(220,53,69,.18);transform:translateY(-1px)}
.btn-icon-edit i{color:#0d6efd;font-size:1.15rem;line-height:1}
.btn-icon-edit:hover{background:rgba(13,110,253,.08);border-color:rgba(13,110,253,.18);transform:translateY(-1px)}

/* Select2 Custom Styling */
.select2-searchable.select2-container .select2-selection--single{height:44px;border:1px solid #e2e6ea;border-radius:10px}
.select2-searchable.select2-container--default .select2-selection--single .select2-selection__rendered{line-height:44px;padding-left:15px;color:#495057}
.select2-searchable.select2-container--default .select2-selection--single .select2-selection__arrow{height:42px}
.select2-searchable.select2-container--default .select2-search--dropdown .select2-search__field{border:1px solid #ddd;border-radius:10px;padding:6px 12px}
.select2-searchable.select2-container--default .select2-results__option--highlighted[aria-selected]{background:var(--primary-color);color:#fff}
.select2-searchable.select2-container--default .select2-results__option[aria-selected=true]{background:#f8f9fa;color:var(--dark-color)}
.select2-searchable.select2-container--open .select2-dropdown{border-color:var(--primary-color);box-shadow:var(--box-shadow);border-radius:10px}

/* Responsive */
@media (max-width: 992px){.form-group{min-width:calc(50% - .5rem)}}
@media (max-width: 768px){
    .form-group{min-width:100%}
    .form-row{gap:.75rem}
    .surgical-table th,.surgical-table td{padding:.75rem .5rem}
}
</style>

<script>
document.addEventListener('livewire:load', function () {
    function initSelect2(element) {
        $(element).select2({
            width: '100%',
            dropdownAutoWidth: true,
            placeholder: $(element).data('placeholder') || $(element).attr('placeholder') || 'Select an option',
            allowClear: true,
            minimumResultsForSearch: 5,
            dropdownParent: $(element).closest('.form-group')
        });
        $(element).on('change', function () {
            @this.set($(this).attr('wire:model'), $(this).val());
        });
    }

    // init all
    $('.select2-searchable').each(function () { initSelect2(this); });

    // before update destroy to avoid leaks
    Livewire.hook('message.received', () => {
        $('.select2-searchable').each(function () {
            if ($(this).hasClass('select2-hidden-accessible')) { $(this).select2('destroy'); }
        });
    });

    // after update re-init
    Livewire.hook('message.processed', () => {
        $('.select2-searchable').each(function () { initSelect2(this); });
    });

    // autofocus
    document.getElementById('serviceSearch')?.focus();
});

// Delete confirmation (SweetAlert then fallback)
function confirmDelete(cb) {
    if (window.Swal) {
        Swal.fire({
            title: '{{ __("Are you sure?") }}',
            text: '{{ __("This action cannot be undone.") }}',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: '{{ __("Yes, delete") }}',
            cancelButtonText: '{{ __("Cancel") }}'
        }).then((result) => { if (result.isConfirmed) cb(); });
    } else {
        if (confirm('{{ __("Are you sure?") }}')) cb();
    }
}
</script>
