@props([
    'tooth', 
    'selected' => false,
    'primary' => false,
    'treatments' => []
])

@php
    // تحديد الألوان حسب حالة السن
    $baseColor = $primary ? '#93c5fd' : '#e5e5e5';
    $strokeColor = $selected ? '#065f46' : '#555';
    $strokeWidth = $selected ? '1.5' : '1';
    
    // تحديد الرمز حسب نوع السن
    $toothSymbol = $tooth['supernumerary'] ? '⨁' : $tooth['number'];
    
    // تحديد الفئة حسب موقع السن
    $toothClass = '';
    if (in_array($tooth['number'], ['1', '16', '17', '32'])) {
        $toothClass = 'molar';
    } elseif (in_array($tooth['number'], ['3', '14', '19', '30'])) {
        $toothClass = 'canine';
    } elseif (in_array($tooth['number'], ['6', '11', '22', '27'])) {
        $toothClass = 'incisor';
    } elseif (in_array($tooth['number'], ['4', '5', '12', '13', '20', '21', '28', '29'])) {
        $toothClass = 'premolar';
    }
    
    // تحديد إذا كان السن مفقوداً
    $isMissing = in_array('M', array_column($treatments, 'code'));
@endphp

<div class="tooth-container {{ $toothClass }} {{ $selected ? 'selected' : '' }} {{ $isMissing ? 'missing' : '' }}"
     data-tooth="{{ $tooth['number'] }}"
     title="السن {{ $tooth['number'] }} - {{ $primary ? 'لبني' : 'دائم' }}">
     
    @if($isMissing)
        <div class="missing-tooth-indicator">✖</div>
    @else
        <svg version="1.0" xmlns="http://www.w3.org/2000/svg"  
             class="tooth-svg"
             viewBox="0 0 66 126" preserveAspectRatio="xMidYMid meet">
            <g transform="translate(0,126) scale(0.1,-0.1)"
               fill="{{ $baseColor }}"
               stroke="{{ $strokeColor }}"
               stroke-width="{{ $strokeWidth }}">
                <path d="M436 1149 c-19 -17 -45 -46 -57 -66 -18 -29 -23 -33 -31 -19 -15 26
-33 19 -82 -34 -98 -104 -145 -257 -171 -550 -8 -85 -21 -184 -29 -220 -14
-57 -14 -71 -1 -115 21 -73 53 -100 119 -100 41 0 62 7 102 32 28 18 55 33 61
33 5 0 25 -14 43 -31 26 -25 38 -30 62 -25 109 22 169 175 119 303 -52 133
-55 456 -5 630 9 29 14 69 12 90 -3 33 -7 38 -29 41 -21 2 -28 10 -33 33 -7
39 -35 38 -80 -2z m59 -27 c-3 -14 -21 -43 -39 -64 -19 -21 -47 -67 -64 -101
-27 -56 -38 -87 -70 -207 -10 -34 -10 -33 -11 20 -1 82 24 182 65 260 54 103
138 168 119 92z m62 -64 c-2 -24 -12 -77 -22 -118 -20 -81 -27 -354 -12 -462
l7 -58 -47 30 c-119 76 -223 65 -329 -32 l-47 -43 7 95 c22 294 74 457 180
559 47 46 53 27 25 -75 -16 -54 -23 -108 -23 -169 -1 -96 10 -169 23 -160 5 2
14 41 20 85 23 156 81 290 152 355 54 49 72 47 66 -7z m-131 -602 c68 -31 120
-79 135 -123 24 -75 -6 -194 -60 -232 -48 -34 -66 -34 -104 -1 -43 37 -61 37
-116 0 -55 -37 -106 -48 -140 -29 -94 53 -81 232 26 330 87 80 168 97 259 55z" />
            </g>
            
            @foreach($treatments as $treatment)
                @switch($treatment['code'])
                    @case('C') <!-- Caries -->
                        <circle cx="33" cy="30" r="10" fill="#a52a2a" opacity="0.7" />
                    @break
                    
                    @case('F') <!-- Filling -->
                        <rect x="20" y="20" width="26" height="20" fill="#ffd700" opacity="0.7" />
                    @break
                    
                    @case('R') <!-- Root Canal -->
                        <path d="M20,60 L46,60 L46,100 L20,100 Z" fill="#8b4513" opacity="0.7" />
                    @break
                @endswitch
            @endforeach
        </svg>
    @endif
    
    <div class="tooth-number">{{ $toothSymbol }}</div>
    
    @if(count($treatments) > 0 && !$isMissing)
        <div class="tooth-treatments">
            @foreach($treatments as $treatment)
                <span class="treatment-badge" title="{{ $treatment['name'] }}">
                    {{ $treatment['code'] }}
                </span>
            @endforeach
        </div>
    @endif
</div>

@push('styles')
<style>
    .tooth-container {
        position: relative;
        margin: 0 5px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .tooth-container:hover {
        transform: scale(1.1);
    }
    
    .tooth-container.selected {
        transform: scale(1.15);
        z-index: 10;
    }
    
    .tooth-svg {
        width: 40px;
        height: 60px;
    }
    
    .tooth-number {
        text-align: center;
        font-weight: bold;
        font-size: 12px;
        margin-top: 2px;
    }
    
    .tooth-treatments {
        position: absolute;
        top: -8px;
        right: -8px;
        display: flex;
        flex-direction: column;
        gap: 2px;
    }
    
    .treatment-badge {
        background: #4a5568;
        color: white;
        border-radius: 50%;
        width: 16px;
        height: 16px;
        font-size: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .missing-tooth-indicator {
        width: 40px;
        height: 60px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: #e53e3e;
        opacity: 0.7;
    }
    
    /* أنماط خاصة بأنواع الأسنان */
    .molar .tooth-svg {
        width: 45px;
    }
    
    .premolar .tooth-svg {
        width: 38px;
    }
    
    .canine .tooth-svg {
        height: 70px;
    }
    
    .incisor .tooth-svg {
        width: 30px;
        height: 50px;
    }
</style>
@endpush