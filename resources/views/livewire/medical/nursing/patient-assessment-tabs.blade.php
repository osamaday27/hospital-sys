<div>
    <ul class="nav nav-nursing_trans">
        <li class="nav-item">
            <a class="nav-link {{ $currentTab==='social_economic' ? 'active' : '' }} {{ $this->canAccessTab('social_economic') ? '' : 'disabled' }}"
               wire:click.prevent="setTab('social_economic')">{{ __('nursing_trans.social_economic') }}</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $currentTab==='special_needs' ? 'active' : '' }} {{ $this->canAccessTab('special_needs') ? '' : 'disabled' }}"
               wire:click.prevent="setTab('special_needs')">{{ __('nursing_trans.special_needs') }}</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $currentTab==='infectious' ? 'active' : '' }} {{ $this->canAccessTab('infectious') ? '' : 'disabled' }}"
               wire:click.prevent="setTab('infectious')">{{ __('nursing_trans.infectious') }}</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $currentTab==='motor_functional' ? 'active' : '' }} {{ $this->canAccessTab('motor_functional') ? '' : 'disabled' }}"
               wire:click.prevent="setTab('motor_functional')">{{ __('nursing_trans.motor_functional') }}</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $currentTab==='pain' ? 'active' : '' }} {{ $this->canAccessTab('pain') ? '' : 'disabled' }}"
               wire:click.prevent="setTab('pain')">{{ __('nursing_trans.pain') }}</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $currentTab==='rights_duties' ? 'active' : '' }} {{ $this->canAccessTab('rights_duties') ? '' : 'disabled' }}"
               wire:click.prevent="setTab('rights_duties')">{{ __('nursing_trans.rights_duties') }}</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $currentTab==='allergies' ? 'active' : '' }} {{ $this->canAccessTab('allergies') ? '' : 'disabled' }}"
               wire:click.prevent="setTab('allergies')">{{ __('nursing_trans.allergies') }}</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $currentTab==='vital_signs' ? 'active' : '' }} {{ $this->canAccessTab('vital_signs') ? '' : 'disabled' }}"
               wire:click.prevent="setTab('vital_signs')">{{ __('nursing_trans.vital_signs') }}</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $currentTab==='nutrition_evaluation' ? 'active' : '' }} {{ $this->canAccessTab('nutrition_evaluation') ? '' : 'disabled' }}"
               wire:click.prevent="setTab('nutrition_evaluation')">{{ __('nursing_trans.nutrition_evaluation') }}</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $currentTab==='fall_risk' ? 'active' : '' }} {{ $this->canAccessTab('fall_risk') ? '' : 'disabled' }}"
               wire:click.prevent="setTab('fall_risk')">{{ __('nursing_trans.fall_risk') }}</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $currentTab==='discharge_planning' ? 'active' : '' }} {{ $this->canAccessTab('discharge_planning') ? '' : 'disabled' }}"
               wire:click.prevent="setTab('discharge_planning')">{{ __('nursing_trans.discharge_planning') }}</a>
        </li>
    </ul>

    <div class="tab-content mt-4">
        @foreach(array_keys($completed) as $tab)
            @if($currentTab === $tab)
                <div class="tab-pane active">
                    @switch($tab)
                        @case('social_economic')
                            @livewire('medical.nursing.social-economic', compact('patientId','visitId','reservationId'))
                            @break

                        @case('special_needs')
                            @livewire('medical.nursing.special-needs', compact('patientId','visitId','reservationId'))
                            @break

                        @case('infectious')
                            @livewire('medical.nursing.infectious', compact('patientId','visitId','reservationId'))
                            @break

                        @case('motor_functional')
                            @livewire('medical.nursing.motor-functional', compact('patientId','visitId','reservationId'))
                            @break

                        @case('pain')
                            @livewire('medical.nursing.pain', compact('patientId','visitId','reservationId'))
                            @break

                        @case('rights_duties')
                            @livewire('medical.nursing.rights-duties', compact('patientId','visitId','reservationId'))
                            @break

                        @case('allergies')
                            @livewire('medical.nursing.allergies', compact('patientId','visitId','reservationId'))
                            @break

                        @case('vital_signs')
                            @livewire('medical.nursing.vital-signs', compact('patientId','visitId','reservationId'))
                            @break

                        @case('nutrition_evaluation')
                            @livewire('medical.nursing.nutrition-evaluation', compact('patientId','visitId','reservationId'))
                            @break

                        @case('fall_risk')
                            @livewire('medical.nursing.fall-risk', compact('patientId','visitId','reservationId'))
                            @break

                        @case('discharge_planning')
                            @livewire('medical.nursing.discharge-planning', compact('patientId','visitId','reservationId'))
                            @break

                        @default
                            <p>هذا القسم لم يُعد بعد.</p>
                    @endswitch

                    <button wire:click.prevent="nextTab()" class="btn btn-primary mt-3">
                        {{ __('nursing_trans.next') }}
                    </button>
                </div>
            @endif
        @endforeach
    </div>
</div>
