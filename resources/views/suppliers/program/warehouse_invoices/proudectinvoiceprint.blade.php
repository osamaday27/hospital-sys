@extends('layouts.masterliveware')

@section('title')
    {{ trans('suppliers.title') }}
@stop

<head>
    <meta charset="UTF-8">
    <title>طباعة الفاتورة</title>
    <link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">
    <style>
        body {
            font-size: 14px;
            color: #000;
            background: #fff;
        }

        .invoice-header {
            text-align: center;
            margin-bottom: 25px;
        }

        .table th,
        .table td {
            padding: 6px;
            vertical-align: middle;
        }

        h4,
        h5 {
            margin: 0;
            padding: 0;
        }

        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
@section('content')
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb mb-0">
                                <li class="breadcrumb-item"><a href="#">{{ __('suppliers.home') }}</a></li>
                                <li class="breadcrumb-item"><a href="#">{{ __('suppliers.programs') }}</a></li>
                                <li class="breadcrumb-item active" aria-current="page">
                                    {{ __('suppliers.invoices_service') }}</li>
                            </ol>
                        </nav>
                    </ol>
                </div>
            </div>
        </div>
    </div>
    <!-- Button Section -->
    <div class="row">
        <!-- Content Row -->
        <div class="row justify-content-evenly mt-3">

            <!-- Service Management Section -->
            <div class="col-lg-12">

                <body onload="window.print()">
                    <div class="container my-3">
                    </div>
                </body>
                @livewire('suppliers.program.view-service-invoice-print', ['invoice_id' => $id])
            </div>
        </div>
        <!-- End Content Row -->
    </div>
    <!--end col-->
    </div>
@endsection
