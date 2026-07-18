
@extends('layouts.master_doctor')

@php
$supplier = $supplier ?? null;
$id       = request('id');
$tab      = request('tab', 'tab1');
$supplier = $id
    ? \App\Models\suppliers\suppliersdata::find($id)
    : $supplier;
@endphp

@section('title', isset($supplier->id)
    ? __('suppliers.edit_supplier')
    : __('suppliers.create_supplier')
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
                            <a href="{{ route('suppliers_data.index') }}">{{__('suppliers.title')}}</a>
                        </li>
                        <li class="breadcrumb-item active">
                            {{ isset($supplier) ? __('suppliers.edit_supplier') : __('suppliers.create_supplier') }}
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
    <ul class="nav nav-pills nav-fill gap-2 p-1 small bg-light rounded-5 shadow-sm mb-3" id="supplier-tabs" role="tablist"
        style="--bs-nav-pills-link-active-bg: #0d6efd;">
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $tab == 'tab1' ? 'active' : '' }}"
                    id="tab1-tab" data-bs-toggle="tab" data-bs-target="#tab1"
                    type="button" role="tab" aria-controls="tab1"
                    aria-selected="{{ $tab == 'tab1' ? 'true' : 'false' }}">
                {{ __('suppliers.supplier_tab_basic_data') }}
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $tab == 'tab2' ? 'active' : '' }}"
                    id="tab2-tab" data-bs-toggle="tab" data-bs-target="#tab2"
                    type="button" role="tab" aria-controls="tab2"
                    aria-selected="{{ $tab == 'tab2' ? 'true' : 'false' }}">
                {{ __('suppliers.supplier_tab_representatives') }}
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link {{ $tab == 'tab3' ? 'active' : '' }}"
                    id="tab3-tab" data-bs-toggle="tab" data-bs-target="#tab3"
                    type="button" role="tab" aria-controls="tab3"
                    aria-selected="{{ $tab == 'tab3' ? 'true' : 'false' }}">
                {{ __('suppliers.supplier_tab_docs') }}
            </button>
        </li>

    </ul>
    <!-- محتوى التبويبات -->
    <div class="card">
        <div class="card-body">
            <div class="tab-content" id="supplier-tabs-content">
                <div class="tab-pane fade {{ $tab == 'tab1' ? 'show active' : '' }}"
                     id="tab1" role="tabpanel" aria-labelledby="tab1-tab">
                    @livewire('suppliers.settings.suppliers-data.supplier-basic-data', ['supplierId' => $supplier->id ?? null])
                </div>
                <div class="tab-pane fade {{ $tab == 'tab2' ? 'show active' : '' }}"
                     id="tab2" role="tabpanel" aria-labelledby="tab2-tab">
                    @livewire('suppliers.settings.suppliers-data.supplier-representatives', ['supplierId' => $supplier->id ?? null])
                </div>
                <div class="tab-pane fade {{ $tab == 'tab3' ? 'show active' : '' }}"
                     id="tab3" role="tabpanel" aria-labelledby="tab3-tab">
                    @livewire('suppliers.settings.suppliers-data.supplier-docs', ['supplierId' => $supplier->id ?? null])
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

