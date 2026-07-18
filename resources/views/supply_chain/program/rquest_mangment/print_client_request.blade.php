@extends('layouts.master')

@section('content')
    @include('components.print.print_client_request', [
          'title' => $title,
          'request' => $request
    ])
@endsection
