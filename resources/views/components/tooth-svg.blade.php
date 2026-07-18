@props(['tooth', 'class' => ''])

@php
    // نظام تصنيف الأسنان مع ملفات SVG المقابلة
    $svgs = [
        // الأسنان الدائمة العلوية اليمنى (الربع 1)
        1 => '1.svg',
        2 => '2.svg',
        3 => '3.svg',
        4 => '4.svg',
        5 => '5.svg',
        6 => '6.svg',
        7 => '7.svg',
        8 => '8.svg',
        
        // الأسنان الدائمة العلوية اليسرى (الربع 2)
        9 => '9.svg',
        10 => '10.svg',
        11 => '11.svg',
        12 => '12.svg',
        13 => '13.svg',
        14 => '14.svg',
        15 => '15.svg',
        16 => '16.svg',
        
        // الأسنان الدائمة السفلية اليسرى (الربع 3)
        17 => '17.svg',
        18 => '18.svg',
        19 => '19.svg',
        20 => '20.svg',
        21 => '21.svg',
        22 => '22.svg',
        23 => '23.svg',
        24 => '24.svg',
        
        // الأسنان الدائمة السفلية اليمنى (الربع 4)
        25 => '25.svg',
        26 => '26.svg',
        27 => '27.svg',
        28 => '28.svg',
        29 => '29.svg',
        30 => '30.svg',
        31 => '31.svg',
        32 => '32.svg',
        
        // الأسنان اللبنية
        'A' => 'a.svg',
        'B' => 'b.svg',
        'C' => 'c.svg',
        'D' => 'd.svg',
        'E' => 'e.svg',
        'F' => 'f.svg',
        'G' => 'g.svg',
        'H' => 'h.svg',
        'I' => 'i.svg',
        'J' => 'j.svg',
        'K' => 'k.svg',
        'L' => 'l.svg',
        'M' => 'm.svg',
        'N' => 'n.svg',
        'O' => 'o.svg',
        'P' => 'p.svg',
        'Q' => 'q.svg',
        'R' => 'r.svg',
        'S' => 's.svg',
        'T' => 't.svg',
    ];

    $svgPath = $svgs[$tooth] ?? 'default-tooth.svg';
    $svgFullPath = public_path("svg/teeth/$svgPath");
@endphp

<div class="tooth-svg-container {{ $class }}" data-tooth="{{ $tooth }}">
    @if(file_exists($svgFullPath))
        {!! file_get_contents($svgFullPath) !!}
    @else
        <!-- SVG افتراضي مع تصميم مبسط -->
        <svg viewBox="0 0 100 120" xmlns="http://www.w3.org/2000/svg">
            <path d="M30,20 Q50,10 70,20 Q85,40 80,70 Q70,90 50,95 Q30,90 20,70 Q15,40 30,20 Z" 
                  fill="currentColor" stroke="#333" stroke-width="2"/>
            <path d="M40,35 Q50,30 60,35 Q65,50 60,60 Q50,65 40,60 Q35,50 40,35 Z" 
                  fill="#333" opacity="0.2"/>
        </svg>
    @endif
</div>

<style>


.tooth-svg-container svg {
    width: 80px !important;
    height: 100px !important; 

}



</style>