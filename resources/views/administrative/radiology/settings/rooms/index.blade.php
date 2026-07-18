@extends('layouts.masterliveware')

@section('title', __('radiology_rooms.title'))

@section('content')
<div class="container-fluid py-4">

    {{-- 🏥 مناداة Livewire Component --}}
    @livewire('radiology.settings.rooms.index')

</div>
@endsection
