@extends('layouts.master_doctor')

@section('title', trans('supply_chain_trans.store_settings'))

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- العنوان والخبز -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('supply_chain_settings.index') }}">
                                {{ trans('supply_chain_trans.title') }}
                            </a>
                        </li>
                        <li class="breadcrumb-item active">
                            {{ trans('supply_chain_trans.store_settings') }}
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- تبويبات محسّنة -->
    <div class="card">
        <div class="card-body pb-0">
            <ul class="nav nav-pills nav-fill gap-2 p-1 small bg-light rounded-5 shadow-sm mb-3" id="settings-tab" role="tablist"
                style="--bs-nav-pills-link-active-bg: #0d6efd;">
                @php
                    $tabs = [
                        'general' => 'general_settings',
                        'units' => 'unit_data',
                        'company' => 'producing_company',
                        'movements' => 'movement_types',
                        'writeoff' => 'write_off',
                        'storage' => 'storage_conditions',
                        'return' => 'return_reasons',
                        'close' => 'order_close_reasons',
                        'period' => 'financial_period',
                        'year' => 'financial_year',
                    ];
                @endphp

                @foreach ($tabs as $id => $label)
                    <li class="nav-item" role="presentation">
                        <button class="nav-link {{ $loop->first ? 'active' : '' }}" id="{{ $id }}-tab"
                            data-bs-toggle="pill" data-bs-target="#{{ $id }}" type="button" role="tab">
                            {{ trans("supply_chain_trans.$label") }}
                        </button>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>

    <!-- محتوى التبويبات داخل كروت -->
    <div class="card">
        <div class="card-body">
            <div class="tab-content" id="settings-tabContent">
                <div class="tab-pane fade show active" id="general" role="tabpanel">
                    @livewire('supply-chain.settings.general-settings')
                </div>

                <div class="tab-pane fade" id="units" role="tabpanel">
                    @livewire('supply-chain.settings.unit-data')
                </div>

                <div class="tab-pane fade" id="company" role="tabpanel">
                    @livewire('supply-chain.settings.producingcompanydata')
                </div>

                <div class="tab-pane fade" id="movements" role="tabpanel">
                    @livewire('supply-chain.settings.movement-types')
                </div>

                <div class="tab-pane fade" id="writeoff" role="tabpanel">
                    @livewire('supply-chain.settings.write-off')
                </div>

                <div class="tab-pane fade" id="storage" role="tabpanel">
                    @livewire('supply-chain.settings.storage-conditions')
                </div>

                <div class="tab-pane fade" id="return" role="tabpanel">
                    @livewire('supply-chain.settings.return-reasons')
                </div>

                <div class="tab-pane fade" id="close" role="tabpanel">
                    @livewire('supply-chain.settings.orderclosereasons')
                </div>

                <div class="tab-pane fade" id="period" role="tabpanel">
                    @livewire('supply-chain.settings.financial-period')
                </div>

                <div class="tab-pane fade" id="year" role="tabpanel">
                    @livewire('supply-chain.settings.financial-year')
                </div>
            </div>
        </div>
    </div>

@endsection
