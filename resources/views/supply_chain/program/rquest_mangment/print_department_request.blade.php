@extends('layouts.master')

@section('content')
    @include('components.print.print_department_request', [
          'title' => $title,
          'request' => $request
    ])
@endsection
