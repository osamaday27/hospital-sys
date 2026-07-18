@extends('layouts.masterliveware')

@section('title')
    {{ trans('namedictionary_trans.title') }}
@stop

@section('content')
    <!-- عنوان الصفحة -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item active">
                            <a href="{{ url()->previous() }}">
                                {{ trans('namedictionary_trans.title') }}
                            </a>
                        </li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- قسم الأزرار -->
    <div class="row">
        <!-- محتوى الصفحة -->
        <div class="row justify-content-evenly mt-3">

            <!-- قسم إدارة الأسماء -->
            <div class="col-lg-12">
                @livewire('patient-name-review')
            </div>
        </div>
        <!-- نهاية محتوى الصفحة -->
    </div>
@endsection
