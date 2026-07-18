@extends('layouts.master_doctor')

@section('title', isset($product) ? trans('supply_chain_trans.edit_product') : trans('supply_chain_trans.create_product'))

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- العنوان والخبز -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('products_main_data.index') }}">{{ trans('supply_chain_trans.products') }}</a>
                        </li>
                        <li class="breadcrumb-item active">
                            {{ isset($product) ? trans('supply_chain_trans.edit_product') : trans('supply_chain_trans.create_product') }}
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <ul class="nav nav-pills nav-fill gap-2 p-1 small bg-light rounded-5 shadow-sm mb-3" id="product-tabs" role="tablist"
    style="--bs-nav-pills-link-active-bg: #0d6efd;">
    <li class="nav-item" role="presentation">
        <button class="nav-link active" id="tab1-tab" data-bs-toggle="tab" data-bs-target="#tab1"
            type="button" role="tab" aria-controls="tab1" aria-selected="true">
            {{ trans('supply_chain_trans.product_data') }}
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="tab2-tab" data-bs-toggle="tab" data-bs-target="#tab2"
            type="button" role="tab" aria-controls="tab2" aria-selected="false">
            {{ trans('supply_chain_trans.product_units') }}
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link" id="tab3-tab" data-bs-toggle="tab" data-bs-target="#tab3"
            type="button" role="tab" aria-controls="tab3" aria-selected="false">
            {{ trans('supply_chain_trans.Suppliers_conditions') }}
        </button>
    </li>
</ul>


    <!-- محتوى التبويبات -->
    <div class="card">
        <div class="card-body">
            <div class="tab-content" id="product-tabs-content">
                <!-- تبويب البيانات الأساسية -->
                <div class="tab-pane fade show active" id="tab1" role="tabpanel" aria-labelledby="tab1-tab">
                    @livewire('supply-chain.settings.products-data.basic-data', ['productId' => $product->id ?? null])
                </div>

                <!-- تبويب التفاصيل الإضافية -->
                <div class="tab-pane fade" id="tab2" role="tabpanel" aria-labelledby="tab2-tab">
                    @livewire('supply-chain.settings.products-data.product-units', ['productId' => $product->id ?? null])
                </div>

                <!-- تبويب الموردين وظروف التخزين -->
                <div class="tab-pane fade" id="tab3" role="tabpanel" aria-labelledby="tab3-tab">
                    @livewire('supply-chain.settings.products-data.suppliers-conditions', ['productId' => $product->id ?? null])
                </div>
            </div>
        </div>
    </div>

    <!-- سكريبت التحكم في التبويبات -->
    <script>
        document.addEventListener('livewire:load', function () {
            // التنقل البرمجي من Livewire
            Livewire.on('goToTab', tabId => {
                const tabTriggerEl = document.querySelector(`#${tabId}-tab`);
                if (tabTriggerEl) {
                    const tab = new bootstrap.Tab(tabTriggerEl);
                    tab.show();
                }
            });

            // منع التنقل اليدوي بالتبويبات نهائيًا
            const links = document.querySelectorAll('#product-tabs .nav-link');
            links.forEach(link => {
                link.addEventListener('click', function (event) {
                    // منع التنقل اليدوي دائمًا حتى لو الضغط بالماوس أو الكيبورد
                    event.stopImmediatePropagation();
                    event.preventDefault();
                    return false;
                });
            });
        });
    </script>


@endsection
