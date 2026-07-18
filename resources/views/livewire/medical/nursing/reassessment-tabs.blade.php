<div>
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link {{ $currentTab==='infectious' ? 'active' : '' }}"
               wire:click.prevent="setTab('infectious')">فحص الأمراض المعدية</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $currentTab==='motor_functional' ? 'active' : '' }}"
               wire:click.prevent="setTab('motor_functional')">الفحص الحركي/الوظيفي</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $currentTab==='pain' ? 'active' : '' }}"
               wire:click.prevent="setTab('pain')">فحص الألم</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $currentTab==='vital_signs' ? 'active' : '' }}"
               wire:click.prevent="setTab('vital_signs')">العلامات الحيوية</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $currentTab==='fall_risk' ? 'active' : '' }}"
               wire:click.prevent="setTab('fall_risk')">مخاطر السقوط</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $currentTab==='summary' ? 'active' : '' }}"
               wire:click.prevent="setTab('summary')">ملخص التقييم</a>
        </li>
    </ul>

    <div class="tab-content p-3 border border-top-0">
        @if($currentTab==='infectious')
            @livewire('medical.nursing.infectious', compact('patientId','visitId','reservationId'))
        @elseif($currentTab==='motor_functional')
            @livewire('medical.nursing.motor-functional', compact('patientId','visitId','reservationId'))
        @elseif($currentTab==='pain')
            @livewire('medical.nursing.pain', compact('patientId','visitId','reservationId'))
        @elseif($currentTab==='vital_signs')
            @livewire('medical.nursing.vital-signs', compact('patientId','visitId','reservationId'))
        @elseif($currentTab==='fall_risk')
            @livewire('medical.nursing.fall-risk', compact('patientId','visitId','reservationId'))
        @elseif($currentTab==='summary')
        @livewire('medical.nursing.reassessment-summary', compact('patientId','visitId','reservationId'))

        @endif

        <div class="mt-3 text-end">
            <button wire:click.prevent="previousTab()" class="btn btn-secondary">السابق</button>
            <button wire:click.prevent="nextTab()"     class="btn btn-primary"  >التالي</button>
        </div>
    </div>
</div>
