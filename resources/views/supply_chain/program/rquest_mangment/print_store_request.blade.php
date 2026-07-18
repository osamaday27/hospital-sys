@extends('layouts.master')

@section('content')
    @include('components.print.print_store_request', [
          'title' => $title,
          'request' => $request
    ])
@endsection
