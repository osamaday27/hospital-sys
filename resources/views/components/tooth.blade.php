@props(['tooth', 'selectedTooth' => [], 'toothData' => [], 'age' => null])

@php
    $isSelected = in_array($tooth, $selectedTooth);
    $toothStatus = $toothData[$tooth]['status'] ?? 'none';
    $problems = $toothData[$tooth]['problems'] ?? [];
    $treatment = $toothData[$tooth]['treatment'] ?? '';

    $fillColors = [
        'class1' => '#a7c7e7',
        'class2' => '#77dd77',
        'class3' => '#fdfd96',
        'gold_crown' => '#ffd700',
        'porcelain_crown' => '#e6e6fa',
        'veneer' => '#ffffff',
        'root_canal' => '#ff6961',
        'fracture' => '#ffb347',
        'abscess' => '#ff0000',
        'post_core' => '#836953',
        'diastema' => '#cfcfc4',
        'fixed_bridge' => '#b19cd9',
        'impacted' => '#779ecb',
        'extracted' => '#000000',
        'missing' => '#d3d3d3',
        'drifting' => '#ffb6c1'
    ];

    $fill = $fillColors[$toothStatus] ?? '#f3f4f6';
    $stroke = $isSelected ? '#000000' : '#6b7280';
    $strokeWidth = $isSelected ? '1.5' : '1';

    $toothSize = '100px';
    if (in_array($tooth, [11, 12, 21, 22, 31, 32, 41, 42])) {
        $toothSize = '90px';
    } elseif (in_array($tooth, [13, 23, 33, 43])) {
        $toothSize = '100px';
    } elseif (in_array($tooth, [14, 15, 24, 25, 34, 35, 44, 45])) {
        $toothSize = '95px';
    } else {
        $toothSize = '110px';
    }

    $borderStyle = '';
    if ($toothStatus === 'fracture') {
        $borderStyle = 'stroke-dasharray: 5, 3;';
    } elseif ($toothStatus === 'abscess') {
        $borderStyle = 'stroke-dasharray: 2, 2;';
    }

    $isDeciduous = $age && $age < 6 && in_array($tooth, [51, 52, 53, 54, 55, 61, 62, 63, 64, 65, 71, 72, 73, 74, 75, 81, 82, 83, 84, 85]);
@endphp

@if(!$age || !$isDeciduous || ($isDeciduous && $age < 12))
<div class="tooth-wrapper text-center" wire:click="$emit('openToothModal', {{ $tooth }})" title="سن {{ $tooth }}">
    @if (in_array($tooth, [11, 21, 31, 41]))
        @include('components.teeth-svg.central-incisor', compact('fill', 'stroke', 'strokeWidth', 'borderStyle', 'toothSize'))
    @elseif (in_array($tooth, [12, 22, 32, 42]))
        @include('components.teeth-svg.lateral-incisor', compact('fill', 'stroke', 'strokeWidth', 'borderStyle', 'toothSize'))
    @elseif (in_array($tooth, [13, 23, 33, 43]))
        @include('components.teeth-svg.canine', compact('fill', 'stroke', 'strokeWidth', 'borderStyle', 'toothSize'))
    @elseif (in_array($tooth, [14, 15, 24, 25, 34, 35, 44, 45]))
        @include('components.teeth-svg.premolar', compact('fill', 'stroke', 'strokeWidth', 'borderStyle', 'toothSize'))
    @elseif (in_array($tooth, [16, 17, 18, 26, 27, 28, 36, 37, 38, 46, 47, 48]))
        @include('components.teeth-svg.molar', compact('fill', 'stroke', 'strokeWidth', 'borderStyle', 'toothSize'))
    @endif

    @if(!empty($problems))
        @foreach($problems as $index => $problem)
            @php
                $severityClass = 'problem-' . ($problem['severity'] == 3 ? 'high' : ($problem['severity'] == 2 ? 'medium' : 'low'));
                $position = ['top' => 10 + ($index * 10), 'left' => 10 + ($index * 10)];
            @endphp
            <div class="tooth-problem {{ $severityClass }}"
                 style="top: {{ $position['top'] }}%; left: {{ $position['left'] }}%;"
                 title="{{ $problem['diagnosis'] }}">
                {{ $index + 1 }}
            </div>
        @endforeach
    @endif

    @if($treatment)
        <div class="position-absolute bottom-0 start-0 end-0 text-center bg-dark text-white small">
            {{ substr($treatment, 0, 3) }}
        </div>
    @endif

    <div class="tooth-number fw-bold {{ $isSelected ? 'text-primary' : 'text-secondary' }}">
        {{ $tooth }}
    </div>
</div>
@endif

<style>
    .tooth-wrapper {
        display: inline-flex;
        flex-direction: column;
        align-items: center;
        cursor: pointer;
        transition: transform 0.2s ease;
        margin: 0.5rem;
        position: relative;
        min-width: 70px;
    }
    .tooth-wrapper:hover {
        transform: scale(1.05);
    }
    .tooth-number {
        font-size: 0.9rem;
        margin-top: 0.3rem;
    }
    .tooth-problem {
        position: absolute;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 10px;
        color: white;
        font-weight: bold;
    }
    .problem-high {
        background-color: #dc3545;
        border: 1px solid #a71d2a;
    }
    .problem-medium {
        background-color: #ffc107;
        border: 1px solid #d39e00;
        color: #000;
    }
    .problem-low {
        background-color: #28a745;
        border: 1px solid #1e7e34;
    }
</style>
