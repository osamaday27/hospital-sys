<div>

    <form wire:submit.prevent="saveSettings">
        <div class="container">
            <!-- الصف الأول -->
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="cost_policy" class="form-label">{{ trans('supply_chain_trans.cost_policy') }}</label>
                    <select id="cost_policy" wire:model="settings.cost_policy" class="form-select">
                        <option value="">{{ trans('supply_chain_trans.choose_option') }}</option>
                        <option value="last_purchase">{{ trans('supply_chain_trans.last_purchase_price') }}</option>
                        <option value="primary_purchase">{{ trans('supply_chain_trans.primary_purchase_price') }}
                        </option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="sale_policy" class="form-label">{{ trans('supply_chain_trans.sale_policy') }}</label>
                    <select id="sale_policy" wire:model="settings.sale_policy" class="form-select">
                        <option value="">{{ trans('supply_chain_trans.choose_option') }}</option>
                        <option value="last_sale">{{ trans('supply_chain_trans.last_sale_price') }}</option>
                        <option value="primary_sale">{{ trans('supply_chain_trans.primary_sale_price') }}</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="item_coding_method"
                        class="form-label">{{ trans('supply_chain_trans.item_coding_method') }}</label>
                    <select id="item_coding_method" wire:model="settings.item_coding_method" class="form-select">
                        <option value="">{{ trans('supply_chain_trans.choose_option') }}</option>
                        <option value="groups">{{ trans('supply_chain_trans.by_groups') }}</option>
                        <option value="system_serial">{{ trans('supply_chain_trans.system_serial') }}</option>
                        <option value="manual">{{ trans('supply_chain_trans.manual_coding') }}</option>
                    </select>
                </div>
            </div>

            <!-- الصف الثاني -->
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="item_code_length"
                        class="form-label">{{ trans('supply_chain_trans.item_code_length') }}</label>
                    <select id="item_code_length" wire:model="settings.item_code_length" class="form-select">
                        <option value="">{{ trans('supply_chain_trans.choose_option') }}</option>
                        <option value="3">3</option>
                        <option value="5">5</option>
                        <option value="4">4</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="gift_cost" class="form-label">{{ trans('supply_chain_trans.gift_cost') }}</label>
                    <select id="gift_cost" wire:model="settings.gift_cost" class="form-select">
                        <option value="">{{ trans('supply_chain_trans.choose_option') }}</option>
                        <option value="actual">{{ trans('supply_chain_trans.actual_cost') }}</option>
                        <option value="no_cost">{{ trans('supply_chain_trans.no_cost') }}</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="barcode_labels"
                        class="form-label">{{ trans('supply_chain_trans.barcode_labels') }}</label>
                    <select id="barcode_labels" wire:model="settings.barcode_labels" class="form-select">
                        <option value="">{{ trans('supply_chain_trans.choose_option') }}</option>
                        <option value="item_code">{{ trans('supply_chain_trans.by_item_code') }}</option>
                        <option value="balance_serial">{{ trans('supply_chain_trans.balance_serial') }}</option>
                    </select>
                </div>
            </div>

            <!-- الصف الثالث -->
            <div class="row mb-3">
                <div class="col-md-4">
                    <label for="store_user_link"
                        class="form-label">{{ trans('supply_chain_trans.store_user_link') }}</label>
                    <select id="store_user_link" wire:model="settings.store_user_link" class="form-select">
                        <option value="">{{ trans('supply_chain_trans.choose_option') }}</option>
                        <option value="yes">{{ trans('supply_chain_trans.yes') }}</option>
                        <option value="no">{{ trans('supply_chain_trans.no') }}</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="general_serial_method"
                        class="form-label">{{ trans('supply_chain_trans.general_serial_method') }}</label>
                    <select id="general_serial_method" wire:model="settings.general_serial_method" class="form-select">
                        <option value="">{{ trans('supply_chain_trans.choose_option') }}</option>
                        <option value="automatic">{{ trans('supply_chain_trans.automatic') }}</option>
                        <option value="manual">{{ trans('supply_chain_trans.manual') }}</option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="financial_year_code"
                        class="form-label">{{ trans('supply_chain_trans.financial_year_code') }}</label>
                    <select id="financial_year_code" wire:model="settings.financial_year_code" class="form-select">
                        <option value="">{{ trans('supply_chain_trans.choose_year') }}</option>
                        @for ($year = now()->year - 10; $year <= now()->year + 5; $year++)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endfor
                    </select>
                </div>
            </div>

            <!-- قسم "إنشاء طلبات الاستعاضة" والأيام -->
            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="auto_substitution"
                        wire:model="auto_substitution">
                    <label class="form-check-label" for="auto_substitution">
                        {{ trans('supply_chain_trans.auto_substitution') }}
                    </label>
                </div>

                @if ($auto_substitution)
                    <div class="mt-2">
                        <label class="form-label">{{ trans('supply_chain_trans.expenditure_days') }}</label>
                        <div class="row">
                            @php
                                $days = [
                                    'sunday' => trans('supply_chain_trans.sunday'),
                                    'monday' => trans('supply_chain_trans.monday'),
                                    'tuesday' => trans('supply_chain_trans.tuesday'),
                                    'wednesday' => trans('supply_chain_trans.wednesday'),
                                    'thursday' => trans('supply_chain_trans.thursday'),
                                    'friday' => trans('supply_chain_trans.friday'),
                                    'saturday' => trans('supply_chain_trans.saturday'),
                                ];
                            @endphp
                            @foreach ($days as $key => $day)
                                <div class="col-md-3">
                                    <div class="form-check">
                                        <!-- ربط checkboxes بالمصفوفة selectedDays -->
                                        <input class="form-check-input" type="checkbox" id="day_{{ $key }}"
                                            wire:model="selectedDays" value="{{ $key }}">
                                        <label class="form-check-label"
                                            for="day_{{ $key }}">{{ $day }}</label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @error('selectedDays')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                @endif
            </div>

            <!-- حقل عدم تحديث سعر البيع -->
            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="dont_update_sale_price"
                        wire:model="settings.dont_update_sale_price">
                    <label class="form-check-label" for="dont_update_sale_price">
                        {{ trans('supply_chain_trans.dont_update_sale_price') }}
                    </label>
                </div>
            </div>

            <!-- زر الحفظ -->
            <div class="mb-3">
                <button type="submit" class="btn btn-primary">
                    {{ trans('supply_chain_trans.save') }}
                </button>

            </div>
            @if (session()->has('success'))
                <div class="alert alert-success mx-auto d-inline-block">
                    {{ session('success') }}
                </div>
            @endif
        </div>
    </form>
</div>
