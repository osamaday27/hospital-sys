{{-- Nav tabs --}}
<ul class="nav nav-pills animation-nav profile-nav gap-2 gap-lg-3 flex-grow-1" id="patientTabs" role="tablist">
    <li class="nav-item" role="presentation">
        <a class="nav-link fs-14 active" id="glasses-tab" data-bs-toggle="tab" href="#glasses" role="tab"
           aria-controls="glasses" aria-selected="true">
            <i class="mdi mdi-eye-refresh"></i>
            <span class="d-none d-md-inline-block">{{ trans('doctor_trans.glasses') }}</span>
        </a>
    </li>

    <li class="nav-item" role="presentation">
        <a class="nav-link fs-14" id="notes-tab" data-bs-toggle="tab" href="#notes_tab" role="tab"
           aria-controls="notes_tab" aria-selected="false" onclick="get_notes()">
            <i class="bx bxs-message-rounded-detail"></i>
            <span class="d-none d-md-inline-block">{{ trans('doctor_trans.notes') }}</span>
        </a>
    </li>

    <li class="nav-item" role="presentation">
        <a class="nav-link fs-14" id="attachments-tab" data-bs-toggle="tab" href="#attachments_tab" role="tab"
           aria-controls="attachments_tab" aria-selected="false">
            <i class="bx bx-paperclip"></i>
            <span class="d-none d-md-inline-block">{{ trans('doctor_trans.attachments') }}</span>
        </a>
    </li>
</ul>

{{-- Actions on the right --}}
<div class="flex-shrink-0">
    <form action="{{ route('examination_test.end_session', ['id' => $reservations->id]) }}" method="POST"
          onsubmit="return confirm('{{ trans('doctor_trans.confirm_end_ophthalmology_examination_session') }}')">
        @csrf
        <button type="submit" class="btn btn-danger">
            <i class="ri-stop-circle-line align-bottom"></i> {{ trans('doctor_trans.end_ophthalmology_examination_session') }}
        </button>
    </form>

    @can('patients-edit')
        <a href="{{ route('patient.edit', ['id' => Crypt::encryptString($patient->id)]) }}" class="btn btn-success">
            <i class="ri-edit-box-line align-bottom"></i> {{ trans('doctor_trans.update_patient_data') }}
        </a>
    @endcan
</div>


{{-- حفظ آخر تبويب مفتوح + دعم الانتقال عبر الهاش --}}
<script>
    (function () {
        const TABS_KEY = 'patient_last_active_tab';

        // استعادة آخر تبويب
        document.addEventListener('DOMContentLoaded', function () {
            const last = localStorage.getItem(TABS_KEY);
            if (last && document.querySelector(`a.nav-link[href="${last}"]`)) {
                const triggerEl = document.querySelector(`a.nav-link[href="${last}"]`);
                const tab = new bootstrap.Tab(triggerEl);
                tab.show();
            } else if (location.hash && document.querySelector(`a.nav-link[href="${location.hash}"]`)) {
                const triggerEl = document.querySelector(`a.nav-link[href="${location.hash}"]`);
                new bootstrap.Tab(triggerEl).show();
            }
        });

        // حفظ التبويب عند التبديل
        const tabLinks = document.querySelectorAll('#patientTabs a[data-bs-toggle="tab"]');
        tabLinks.forEach(link => {
            link.addEventListener('shown.bs.tab', function (e) {
                const target = e.target.getAttribute('href');
                localStorage.setItem(TABS_KEY, target || '');
                if (target) history.replaceState(null, '', target);
            });
        });

        // عند تغيير الهاش (لو المستخدم كتب #attachments_tab مثلاً)
        window.addEventListener('hashchange', function () {
            const hash = location.hash;
            const triggerEl = document.querySelector(`#patientTabs a[href="${hash}"]`);
            if (triggerEl) new bootstrap.Tab(triggerEl).show();
        });
    })();
</script>
