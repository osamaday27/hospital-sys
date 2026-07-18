@props(['selectedTooth' => null])

@php
    // ترتيب الأسنان الدائمة من اليمين لليسار
    $upperRight = [18,17,16,15,14,13,12,11];
    $upperLeft  = [21,22,23,24,25,26,27,28];
    $lowerLeft  = [38,37,36,35,34,33,32,31];
    $lowerRight = [41,42,43,44,45,46,47,48];
@endphp

<div class="max-w-7xl mx-auto p-4">
    <h2 class="text-xl font-bold mb-4 text-center">🦷 Dental Map (Permanent Teeth)</h2>
 {{-- الفك العلوي --}}
    <div class="flex justify-center gap-2 overflow-x-auto mb-4" style="display: block ruby;">
        @foreach([18,17,16,15,14,13,12,11,21,22,23,24,25,26,27,28] as $tooth)
            <x-single-tooth :tooth="$tooth" :selectedTooth="$selectedTooth" />
        @endforeach
    </div>

    <div class="border-t border-gray-400 w-2/3 mx-auto my-4"></div>

    {{-- الفك السفلي --}}
    <div class="flex justify-center gap-2 overflow-x-auto mt-4" style="display: block ruby;">
        @foreach([48,47,46,45,44,43,42,41,31,32,33,34,35,36,37,38] as $tooth)
            <x-single-tooth :tooth="$tooth" :selectedTooth="$selectedTooth" />
        @endforeach
    </div>

    {{-- الأسنان المختارة --}}
    @if($selectedTooth)
        <div class="mt-4 p-3 bg-gray-100 rounded text-center">
            <span class="font-bold">الأسنان المختارة:</span> {{ implode(', ', $selectedTooth) }}
        </div>
    @endif
</div>
