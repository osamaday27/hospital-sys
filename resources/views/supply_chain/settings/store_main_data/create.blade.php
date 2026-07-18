
@extends('layouts.master_doctor')

@php
    $store = $store ?? null;
    $id    = request('id');               // جلب id من الرابط لو موجود
    $tab   = request('tab', 'tab1');      // جلب اسم التبويب المفتوح، الافتراضي tab1
    $store = $id ? \App\Models\supply_chain\storemaindata::find($id) : $store;
@endphp

@section('title', isset($store->id)
    ? __('supply_chain_trans.edit_store')
    : __('supply_chain_trans.create_store')
)

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- العنوان والخبز -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('store_main_data.index') }}">المخازن</a>
                        </li>
                        <li class="breadcrumb-item active">
                            {{ isset($store) ? __('supply_chain_trans.edit_store') : __('supply_chain_trans.create_store') }}
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Toast Container موحّد -->
    <div class="position-fixed top-0 end-0 p-3" style="z-index: 9999">
      <div id="livewire-toast" class="toast align-items-center border-0" role="alert" aria-live="assertive" aria-atomic="true">
        <div class="d-flex">
          <div class="toast-body" id="toast-message"></div>
          <button type="button" class="btn-close me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
      </div>
    </div>

    <!-- التبويبات -->
    <ul class="nav nav-pills nav-fill gap-2 p-1 small bg-light rounded-5 shadow-sm mb-3" id="store-tabs" role="tablist"
        style="--bs-nav-pills-link-active-bg: #0d6efd;">
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $tab == 'tab1' ? 'active' : '' }}"
                    id="tab1-tab" data-bs-toggle="tab" data-bs-target="#tab1"
                    type="button" role="tab" aria-controls="tab1"
                    aria-selected="{{ $tab == 'tab1' ? 'true' : 'false' }}">
                {{ __('supply_chain_trans.store_tab_basic_data') }}
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $tab == 'tab2' ? 'active' : '' }}"
                    id="tab2-tab" data-bs-toggle="tab" data-bs-target="#tab2"
                    type="button" role="tab" aria-controls="tab2"
                    aria-selected="{{ $tab == 'tab2' ? 'true' : 'false' }}">
                {{ __('supply_chain_trans.store_tab_users') }}
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $tab == 'tab3' ? 'active' : '' }}"
                    id="tab3-tab" data-bs-toggle="tab" data-bs-target="#tab3"
                    type="button" role="tab" aria-controls="tab3"
                    aria-selected="{{ $tab == 'tab3' ? 'true' : 'false' }}">
                {{ __('supply_chain_trans.store_tab_products') }}
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $tab == 'tab4' ? 'active' : '' }}"
                    id="tab4-tab" data-bs-toggle="tab" data-bs-target="#tab4"
                    type="button" role="tab" aria-controls="tab4"
                    aria-selected="{{ $tab == 'tab4' ? 'true' : 'false' }}">
                {{ __('supply_chain_trans.store_tab_relations') }}
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $tab == 'tab5' ? 'active' : '' }}"
                    id="tab5-tab" data-bs-toggle="tab" data-bs-target="#tab5"
                    type="button" role="tab" aria-controls="tab5"
                    aria-selected="{{ $tab == 'tab5' ? 'true' : 'false' }}">
                {{ __('supply_chain_trans.store_tab_transactions') }}
            </button>
        </li>
    </ul>

    <!-- محتوى التبويبات -->
    <div class="card">
        <div class="card-body">
            <div class="tab-content" id="store-tabs-content">
                <div class="tab-pane fade {{ $tab == 'tab1' ? 'show active' : '' }}"
                     id="tab1" role="tabpanel" aria-labelledby="tab1-tab">
                    @livewire('supply-chain.settings.store-main-data.store-basic-data', ['storeId' => $store->id ?? null])
                </div>
                <div class="tab-pane fade {{ $tab == 'tab2' ? 'show active' : '' }}"
                     id="tab2" role="tabpanel" aria-labelledby="tab2-tab">
                    @livewire('supply-chain.settings.store-main-data.store-keeper', ['storeId' => $store->id ?? null])
                </div>
                <div class="tab-pane fade {{ $tab == 'tab3' ? 'show active' : '' }}"
                     id="tab3" role="tabpanel" aria-labelledby="tab3-tab">
                    @livewire('supply-chain.settings.store-main-data.store-products', ['storeId' => $store->id ?? null])
                </div>
                <div class="tab-pane fade {{ $tab == 'tab4' ? 'show active' : '' }}"
                     id="tab4" role="tabpanel" aria-labelledby="tab4-tab">
                    @livewire('supply-chain.settings.store-main-data.store-relations', ['storeId' => $store->id ?? null])
                </div>
                <div class="tab-pane fade {{ $tab == 'tab5' ? 'show active' : '' }}"
                     id="tab5" role="tabpanel" aria-labelledby="tab5-tab">
                    @livewire('supply-chain.settings.store-main-data.store-transactions', ['storeId' => $store->id ?? null])
                </div>
            </div>
        </div>
    </div>

    <!-- سكريبت التحكم في التبويبات -->
    <script>
        document.addEventListener('livewire:load', function () {
            Livewire.on('goToTab', (tabId) => {
                const tabTriggerEl = document.querySelector(`#${tabId}-tab`);
                if (tabTriggerEl) {
                    new bootstrap.Tab(tabTriggerEl).show();
                }
            });
        });
    </script>
@endsection

<script>
  document.addEventListener('livewire:load', () => {
    window.addEventListener('toast', event => {
      const toastEl = document.getElementById('livewire-toast');
      const msgEl   = document.getElementById('toast-message');

      // ضع نص الرسالة ونوع الخلفية
      msgEl.innerText = event.detail.message;
      toastEl.className =
        'toast align-items-center border-0 text-white bg-' +
        (event.detail.type || 'success') + ' show';

      // أظهر التوست
      new bootstrap.Toast(toastEl, {
        animation: true,
        autohide: true,
        delay: 3000
      }).show();
    });
  });
</script>

