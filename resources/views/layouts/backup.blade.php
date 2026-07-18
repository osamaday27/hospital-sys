<!-- Human Resources -->
<li class="nav-item">
    <a class="nav-link menu-link font" href="#sidebarhr_management" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarAuth">
        <span class="icon_menu icon"><i class="mdi mdi-account-group"></i></span>
        <span data-key="t-authentication">{{ trans('hr.human_resources') }}</span>
    </a>
    <div class="collapse menu-dropdown" id="sidebarhr_management">
        <ul class="nav nav-sm flex-column">
            <li class="nav-item">
                <a class="nav-link font" href="{{ route('employees.index') }}">
                    {{ trans('hr.employees_management') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link font" href="{{ route('hr_attendance.index') }}">
                    {{ trans('hr.att_management') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link font" href="{{ route('hr_vacations.index') }}">
                    {{ trans('hr.vacations_management') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link font" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarSignUp" data-key="t-signup">
                    {{ trans('hr.payroll_management') }}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link font" href="{{ route('hr.settings') }}">
                    {{ trans('hr.general_settings') }}
                </a>
            </li>
        </ul>
    </div>
</li>