@extends('layouts.master')

@section('content')
    @include('components.print.print_all_tran_request', [
          'title' => $title,
          'request' => $request
    ])
@endsection
