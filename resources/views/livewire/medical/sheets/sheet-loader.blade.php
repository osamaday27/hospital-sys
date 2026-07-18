<div>
    {{-- 🧾 أزرار تحميل الشيتات --}}
    <div class="mb-4 d-flex flex-wrap gap-2">
        <button wire:click="loadSheet('icl')" class="btn btn-outline-primary btn-sm">ICL</button>
        <button wire:click="loadSheet('corneal-cross-linking')" class="btn btn-outline-primary btn-sm">Corneal Cross Linking</button>
        <button wire:click="loadSheet('evisceration-enucleation')" class="btn btn-outline-primary btn-sm">Evisceration / Enucleation / Exenteration</button>
        <button wire:click="loadSheet('glaucoma-surgery')" class="btn btn-outline-primary btn-sm">Glaucoma Surgery</button>
        <button wire:click="loadSheet('keratoplasty')" class="btn btn-outline-primary btn-sm">Keratoplasty Surgery</button>
        <button wire:click="loadSheet('oct-nomenclature')" class="btn btn-outline-primary btn-sm">OCT Nomenclature</button>
        <button wire:click="loadSheet('pars-plana-vitrectomy')" class="btn btn-outline-primary btn-sm">Pars Plana Vitrectomy</button>
        <button wire:click="loadSheet('phakoemulsification')" class="btn btn-outline-primary btn-sm">Phakoemulsification & IOL</button>
        <button wire:click="loadSheet('squint-surgery')" class="btn btn-outline-primary btn-sm">Squint Surgery</button>

        {{-- ✅ الزر الجديد لعرض التقارير السابقة --}}
        <button wire:click="loadSheet('patient-reports')" class="btn btn-outline-success btn-sm">
            <i class="ri-file-list-3-line"></i> التقارير السابقة للمريض
        </button>
    </div>

    {{-- 📄 عرض الشيت أو التقارير --}}
    @if ($component)
        <div>
            @livewire(
                $component,
                [
                    'patient_id' => $patient_id,
                    'reservationId' => $reservationId,
                ],
                key($component . '-' . $patient_id . '-' . $reservationId)
            )
        </div>
    @else
        <div class="alert alert-info text-center rounded-4">
            اختر نوع الشيت أو عرض التقارير السابقة للمريض.
        </div>
    @endif
</div>
