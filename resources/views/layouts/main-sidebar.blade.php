<!-- ========== App Menu ========== -->
<div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <!-- Dark Logo-->
        <a href="index.html" class="logo logo-dark">
            <span class="logo-sm">
                <img src="{{ URL::asset('assets/images/logo-sm.png') }}" alt="" height="27"
                    style="margin-top: 21px;">
            </span>
            </span>
            <span class="logo-lg">
                <img src="{{ URL::asset('assets/images/logo.png') }}" alt=""
                    style="height: 60px;width: 100% !important;margin-top: 10px;">
            </span>
        </a>
        <!-- Light Logo-->
        <a href="index.html" class="logo logo-light">
            <span class="logo-sm">
                <img src="{{ URL::asset('assets/images/logo-sm.png') }}" alt="" height="27"
                    style="margin-top: 21px;">
            </span>
            <span class="logo-lg">
                <img src="{{ URL::asset('assets/images/logo.png') }}" alt="" height="17"
                    style="height: 60px;width: 100% !important;margin-top: 10px;">
            </span>
        </a>
        <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover"
            id="vertical-hover">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>

    <div id="scrollbar">
        <div class="container-fluid">

            <div id="two-column-menu">
            </div>
            <ul class="navbar-nav" id="navbar-nav">
                <li class="nav-item" style="margin-top: 30px;">
                    <a class="nav-link menu-link font  @if (Route::currentRouteName() == 'dashbord.create' || Route::currentRouteName() == 'dashbord.index') active @endif"
                        href="{{ url('/' . ($page = 'dashbord')) }}">
                        <span class="icon_menu icon"><i class="mdi mdi-speedometer"></i></span>
                        <span data-key="t-widgets">{{ trans('main_trans.dashboard') }}</span>
                    </a>
                </li>
                <!--medical record management-->
                <li class="nav-item">
                    <a class="nav-link menu-link font" href="#sidebarmedical_record_management"
                        data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarAuth">
                        <span class="icon_menu icon"><i class="mdi mdi-sticker-text"></i></span>
                        <span data-key="t-authentication">{{ trans('main_trans.medical_record') }}</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarmedical_record_management">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a class="nav-link font" href="{{ url('/' . ($page = 'medical_record/create')) }}">
                                    {{ trans('main_trans.add_patient') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link font"
                                    href="{{ url('/' . ($page = 'medical_record')) }}">{{ trans('main_trans.patient_management') }}</a>
                            </li>
                            {{--  <li class="nav-item">
                                <a class="nav-link font" data-bs-toggle="collapse" role="button" aria-expanded="false"
                                    aria-controls="sidebarSignUp"
                                    data-key="t-signup">{{ trans('main_trans.file_requests_management') }}</a>
                            </li> --}}
                        </ul>
                    </div>
                </li>

                <!-- Outpatient Management -->
                <li class="nav-item">
                    <a class="nav-link menu-link font" href="#sidebarreception_management" data-bs-toggle="collapse"
                        role="button"
                        aria-expanded="{{ Request::is(app()->getLocale() . '/outpatient_*') ? 'true' : 'false' }}"
                        aria-controls="sidebarreception_management">
                        <span class="icon_menu icon"><i class="mdi mdi-account-clock-outline"></i></span>
                        <span data-key="t-authentication">{{ trans('main_trans.outpatient') }}</span>
                    </a>
                    <div class="collapse menu-dropdown {{ Request::is(app()->getLocale() . '/outpatient_*') ? 'show' : '' }}"
                        id="sidebarreception_management">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ url(app()->getLocale() . '/outpatient_programs') }}"
                                    class="nav-link font {{ Request::is(app()->getLocale() . '/outpatient_programs') ? 'active' : '' }}">
                                    {{ trans('main_trans.programs') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url(app()->getLocale() . '/outpatient_reports') }}"
                                    class="nav-link font {{ Request::is(app()->getLocale() . '/outpatient_reports') ? 'active' : '' }}">
                                    {{ trans('main_trans.reports') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url(app()->getLocale() . '/outpatient_settings') }}"
                                    class="nav-link font {{ Request::is(app()->getLocale() . '/outpatient_settings') ? 'active' : '' }}">
                                    {{ trans('main_trans.settings') }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <!-- Inpatient Management -->
                <li class="nav-item">
                    <a class="nav-link menu-link font" href="#sidebarinpatient_management" data-bs-toggle="collapse"
                        role="button"
                        aria-expanded="{{ Request::is(app()->getLocale() . '/inpatient_*') ? 'true' : 'false' }}"
                        aria-controls="sidebarinpatient_management">
                        <span class="icon_menu icon"><i class="mdi mdi-charity"></i></span>
                        <span data-key="t-authentication">{{ trans('main_trans.inpatient') }}</span>
                    </a>
                    <div class="collapse menu-dropdown {{ Request::is(app()->getLocale() . '/inpatient_*') ? 'show' : '' }}"
                        id="sidebarinpatient_management">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ url(app()->getLocale() . '/inpatient_programs') }}"
                                    class="nav-link font {{ Request::is(app()->getLocale() . '/inpatient_programs') ? 'active' : '' }}">
                                    {{ trans('main_trans.programs') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url(app()->getLocale() . '/inpatient_reports') }}"
                                    class="nav-link font {{ Request::is(app()->getLocale() . '/inpatient_reports') ? 'active' : '' }}">
                                    {{ trans('main_trans.reports') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url(app()->getLocale() . '/inpatient_settings') }}"
                                    class="nav-link font {{ Request::is(app()->getLocale() . '/inpatient_settings') ? 'active' : '' }}">
                                    {{ trans('main_trans.settings') }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!-- Call Center Management -->
                <li class="nav-item">
                    <a class="nav-link menu-link font" href="#sidebarcall_center_management"
                        data-bs-toggle="collapse" role="button"
                        aria-expanded="{{ Request::is(app()->getLocale() . '/call_center_*') ? 'true' : 'false' }}"
                        aria-controls="sidebarcall_center_management">
                        <span class="icon_menu icon"><i class="mdi mdi-card-account-phone"></i></span>
                        <span data-key="t-authentication">{{ trans('main_trans.call_center') }}</span>
                    </a>
                    <div class="collapse menu-dropdown {{ Request::is(app()->getLocale() . '/call_center_*') ? 'show' : '' }}"
                        id="sidebarcall_center_management">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ url(app()->getLocale() . '/call_center_all_programs') }}"
                                    class="nav-link font {{ Request::is(app()->getLocale() . '/call_center_all_programs') ? 'active' : '' }}">
                                    {{ trans('main_trans.programs') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url(app()->getLocale() . '/call_center_reports') }}"
                                    class="nav-link font {{ Request::is(app()->getLocale() . '/call_center_reports') ? 'active' : '' }}">
                                    {{ trans('main_trans.reports') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url(app()->getLocale() . '/call_center_settings') }}"
                                    class="nav-link font {{ Request::is(app()->getLocale() . '/call_center_settings') ? 'active' : '' }}">
                                    {{ trans('main_trans.settings') }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link menu-link font" href="#sidebarradiology_management" data-bs-toggle="collapse"
                        role="button"
                        aria-expanded="{{ Request::is(app()->getLocale() . '/radiology_*') ? 'true' : 'false' }}"
                        aria-controls="sidebarradiology_management">
                        <span class="icon_menu icon"><i class="mdi mdi-hospital-box"></i></span>
                        <span data-key="t-authentication">{{ trans('main_trans.radiology') }}</span>
                    </a>
                    <div class="collapse menu-dropdown {{ Request::is(app()->getLocale() . '/radiology_*') ? 'show' : '' }}"
                        id="sidebarradiology_management">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ url(app()->getLocale() . '/radiology_programs') }}"
                                    class="nav-link font {{ Request::is(app()->getLocale() . '/radiology_programs') ? 'active' : '' }}">
                                    {{ trans('main_trans.programs') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url(app()->getLocale() . '/radiology_reports') }}"
                                    class="nav-link font {{ Request::is(app()->getLocale() . '/radiology_reports') ? 'active' : '' }}">
                                    {{ trans('main_trans.reports') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url(app()->getLocale() . '/radiology_settings') }}"
                                    class="nav-link font {{ Request::is(app()->getLocale() . '/radiology_settings') ? 'active' : '' }}">
                                    {{ trans('main_trans.settings') }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!-- Surgical Management -->
                <li class="nav-item">
                    <a class="nav-link menu-link font" href="#sidebarSurgical_management" data-bs-toggle="collapse"
                        role="button"
                        aria-expanded="{{ Request::is(app()->getLocale() . 'surgical_*') ? 'true' : 'false' }}"
                        aria-controls="sidebarSurgical_management">
                        <span class="icon_menu icon"><i class="bx bx-plus-medical"></i></span>
                        <span data-key="t-authentication">{{ trans('main_trans.surgical') }}</span>
                    </a>
                    <div class="collapse menu-dropdown {{ Request::is(app()->getLocale() . '/surgical_*') ? 'show' : '' }}"
                        id="sidebarSurgical_management">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ url(app()->getLocale() . '/surgical_programs') }}"
                                    class="nav-link font {{ Request::is(app()->getLocale() . '/surgical_programs') ? 'active' : '' }}">
                                    {{ trans('main_trans.programs') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url(app()->getLocale() . '/surgical_reports') }}"
                                    class="nav-link font {{ Request::is(app()->getLocale() . '/surgical_reports') ? 'active' : '' }}">
                                    {{ trans('main_trans.reports') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url(app()->getLocale() . '/surgical_settings') }}"
                                    class="nav-link font {{ Request::is(app()->getLocale() . '/surgical_settings') ? 'active' : '' }}">
                                    {{ trans('main_trans.settings') }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <!--lab management-->
                {{-- <li class="nav-item">
                    <a class="nav-link menu-link font" href="#sidebarlab_management" data-bs-toggle="collapse"
                        role="button" aria-expanded="false" aria-controls="sidebarAuth">
                        <span class="icon_menu icon"><i class="mdi mdi-test-tube"></i></span>
                        <span data-key="t-authentication">{{ trans('main_trans.lab') }}</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarlab_management">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a class="nav-link font" data-bs-toggle="collapse" role="button"
                                    aria-expanded="false" aria-controls="sidebarSignIn"
                                    data-key="t-signin">{{ trans('main_trans.add_patients') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link font" data-bs-toggle="collapse" role="button"
                                    aria-expanded="false" aria-controls="sidebarSignUp"
                                    data-key="t-signup">{{ trans('main_trans.Patient_management') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link font" data-bs-toggle="collapse" role="button"
                                    aria-expanded="false" aria-controls="sidebarSignUp"
                                    data-key="t-signup">{{ trans('main_trans.file_requests_management') }}</a>
                            </li>
                        </ul>
                    </div>
                </li> --}}
                <li class="nav-item">
                    <a class="nav-link menu-link font
        @if (Route::currentRouteName() == 'examinations.index' ||
                Route::currentRouteName() == 'examinations.create' ||
                Route::currentRouteName() == 'examinations.show' ||
                Route::currentRouteName() == 'examinations.edit') active @endif"
                        href="{{ url('/' . ($page = 'examinations')) }}">
                        <span class="icon_menu icon"><i class="mdi mdi-clipboard-pulse-outline"></i></span>
                        <span data-key="t-examinations">{{ trans('main_trans.examinations') }}</span>
                    </a>
                </li>

                <!--doctor management-->
                <li class="nav-item">
                    <a class="nav-link menu-link font" href="#sidebardoctor_management" data-bs-toggle="collapse"
                        role="button" aria-expanded="false" aria-controls="sidebarAuth">
                        <span class="icon_menu icon"><i class="mdi mdi-stethoscope"></i></span>
                        <span data-key="t-authentication">{{ trans('main_trans.doctor') }}</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebardoctor_management">
                        <ul class="nav nav-sm flex-column">
                            <ul class="nav nav-sm flex-column">
                                <li class="nav-item">
                                    <a href="{{ url(app()->getLocale() . '/doctor_programs') }}"
                                        class="nav-link font {{ Request::is(app()->getLocale() . '/doctor_programs') ? 'active' : '' }}">
                                        {{ trans('main_trans.programs') }}
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ url(app()->getLocale() . '/doctor_settings') }}"
                                        class="nav-link font {{ Request::is(app()->getLocale() . '/doctor_settings') ? 'active' : '' }}">
                                        {{ trans('main_trans.settings') }}
                                    </a>
                                </li>
                            </ul>
                        </ul>
                    </div>
                </li>
                <!--nursing management-->
                <li class="nav-item">
                    <a class="nav-link menu-link font" href="#sidebarnursing_management" data-bs-toggle="collapse"
                        role="button" aria-expanded="false" aria-controls="sidebarAuth">
                        <span class="icon_menu icon"><i class="mdi mdi-doctor"></i></span>
                        <span data-key="t-authentication">{{ trans('main_trans.nursing') }}</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarnursing_management">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ url(app()->getLocale() . '/nursing_programs') }}"
                                    class="nav-link font {{ Request::is(app()->getLocale() . '/nursing_programs') ? 'active' : '' }}">
                                    {{ trans('main_trans.programs') }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <!--pricing contracts management-->
                <li class="nav-item">
                    <a class="nav-link menu-link font" href="#sidebarpricing_contracts_management"
                        data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarAuth">
                        <span class="icon_menu icon"><i class="ri-newspaper-fill"></i></span>
                        <span
                            data-key="t-authentication">{{ trans('main_trans.pricing_contracts_management') }}</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarpricing_contracts_management">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a class="nav-link font"
                                    href="{{ url('/' . ($page = 'pricing_management')) }}">{{ trans('main_trans.pricing_management') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link font" href="{{ url('/' . ($page = 'contract_management')) }}"
                                    class="nav-link font">{{ trans('main_trans.contracts_management') }}</a>
                            </li>
                        </ul>
                    </div>
                </li>
                <!-- Lockers Management -->
                <li class="nav-item">
                    <a class="nav-link menu-link font" href="#sidebarlockers_management" data-bs-toggle="collapse"
                        role="button"
                        aria-expanded="{{ Request::is(app()->getLocale() . '/lockers*') ? 'true' : 'false' }}"
                        aria-controls="sidebarlockers_management">
                        <span class="icon_menu icon"><i class="mdi mdi mdi-safe-square-outline"></i></span>
                        <span data-key="t-authentication">{{ trans('main_trans.safes') }}</span>
                    </a>
                    <div class="collapse menu-dropdown {{ Request::is(app()->getLocale() . '/lockers*') ? 'show' : '' }}"
                        id="sidebarlockers_management">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ url(app()->getLocale() . '/lockers_programs') }}"
                                    class="nav-link font {{ Request::is(app()->getLocale() . '/lockers_programs') ? 'active' : '' }}">
                                    {{ trans('main_trans.programs') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url(app()->getLocale() . '/lockers_reports') }}"
                                    class="nav-link font {{ Request::is(app()->getLocale() . '/lockers_reports') ? 'active' : '' }}">
                                    {{ trans('main_trans.reports') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url(app()->getLocale() . '/lockers_settings') }}"
                                    class="nav-link font {{ Request::is(app()->getLocale() . '/lockers_settings') ? 'active' : '' }}">
                                    {{ trans('main_trans.settings') }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <li class="nav-item">
                    <a class="nav-link menu-link font" href="#sidebarpatient_accounts" data-bs-toggle="collapse"
                        role="button"
                        aria-expanded="{{ Request::is(app()->getLocale() . '/patient_accounts*') ? 'true' : 'false' }}"
                        aria-controls="sidebarpatient_accounts">
                        <span class="icon_menu icon"><i class="mdi mdi-cash-register"></i></span>
                        <span data-key="t-authentication">{{ trans('main_trans.patient_accounts') }}</span>
                    </a>
                    <div class="collapse menu-dropdown {{ Request::is(app()->getLocale() . '/patient_accounts*') ? 'show' : '' }}"
                        id="sidebarpatient_accounts">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ url(app()->getLocale() . '/patient_accounts_programs') }}"
                                    class="nav-link font {{ Request::is(app()->getLocale() . '/patient_accounts_programs') ? 'active' : '' }}">
                                    {{ trans('main_trans.programs') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url(app()->getLocale() . '/patient_accounts_reports') }}"
                                    class="nav-link font {{ Request::is(app()->getLocale() . '/patient_accounts_reports') ? 'active' : '' }}">
                                    {{ trans('main_trans.reports') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url(app()->getLocale() . '/patient_accounts_settings') }}"
                                    class="nav-link font {{ Request::is(app()->getLocale() . '/patient_accounts_settings') ? 'active' : '' }}">
                                    {{ trans('main_trans.settings') }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <!-- Banks Management -->
                <li class="nav-item">
                    <a class="nav-link menu-link font" href="#sidebarbanks" data-bs-toggle="collapse" role="button"
                        aria-expanded="{{ Request::is(app()->getLocale() . '/banks*') ? 'true' : 'false' }}"
                        aria-controls="sidebarbanks">
                        <span class="icon_menu icon"><i class="mdi mdi-bank"></i></span>
                        <span data-key="t-banks">{{ trans('main_trans.banks') }}</span>
                    </a>
                    <div class="collapse menu-dropdown {{ Request::is(app()->getLocale() . '/banks*') ? 'show' : '' }}"
                        id="sidebarbanks">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ url(app()->getLocale() . '/banks_programs') }}"
                                    class="nav-link font {{ Request::is(app()->getLocale() . '/banks_programs') ? 'active' : '' }}">
                                    {{ trans('main_trans.programs') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url(app()->getLocale() . '/banks_reports') }}"
                                    class="nav-link font {{ Request::is(app()->getLocale() . '/banks_reports') ? 'active' : '' }}">
                                    {{ trans('main_trans.reports') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url(app()->getLocale() . '/banks_settings') }}"
                                    class="nav-link font {{ Request::is(app()->getLocale() . '/banks_settings') ? 'active' : '' }}">
                                    {{ trans('main_trans.settings') }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>


                <!-- GlAccounts Management -->
                <li class="nav-item">
                    <a class="nav-link menu-link font" href="#sidebarglaccounts" data-bs-toggle="collapse"
                        role="button"
                        aria-expanded="{{ Request::is(app()->getLocale() . '/glaccounts*') ? 'true' : 'false' }}"
                        aria-controls="sidebarglaccounts">
                        <span class="icon_menu icon"><i class="mdi mdi mdi-cash-multiple"></i></span>
                        <span data-key="t-authentication">{{ trans('main_trans.glaccounts') }}</span>
                    </a>
                    <div class="collapse menu-dropdown {{ Request::is(app()->getLocale() . '/glaccounts*') ? 'show' : '' }}"
                        id="sidebarglaccounts">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ url(app()->getLocale() . '/glaccounts_programs') }}"
                                    class="nav-link font {{ Request::is(app()->getLocale() . '/glaccounts_programs') ? 'active' : '' }}">
                                    {{ trans('main_trans.programs') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url(app()->getLocale() . '/glaccounts_reports') }}"
                                    class="nav-link font {{ Request::is(app()->getLocale() . '/glaccounts_reports') ? 'active' : '' }}">
                                    {{ trans('main_trans.reports') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url(app()->getLocale() . '/glaccounts_settings') }}"
                                    class="nav-link font {{ Request::is(app()->getLocale() . '/glaccounts_settings') ? 'active' : '' }}">
                                    {{ trans('main_trans.settings') }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!-- Suppliers -->
                <li class="nav-item">
                    <a class="nav-link menu-link font" href="#sidebar_suppliers_management" data-bs-toggle="collapse"
                        role="button"
                        aria-expanded="{{ Request::is(app()->getLocale() . '/suppliers_*') ? 'true' : 'false' }}"
                        aria-controls="sidebar_suppliers_management">
                        <span class="icon_menu icon"><i class="mdi mdi-truck-delivery"></i></span>
                        <span data-key="t-suppliers">{{ trans('main_trans.suppliers') }}</span>
                    </a>
                    <div class="collapse menu-dropdown {{ Request::is(app()->getLocale() . '/suppliers_*') ? 'show' : '' }}"
                        id="sidebar_suppliers_management">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ url(app()->getLocale() . '/suppliers_program') }}"
                                    class="nav-link font {{ Request::is(app()->getLocale() . '/suppliers_program') ? 'active' : '' }}">
                                    {{ trans('main_trans.programs') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url(app()->getLocale() . '/suppliers_reports') }}"
                                    class="nav-link font {{ Request::is(app()->getLocale() . '/suppliers_reports') ? 'active' : '' }}">
                                    {{ trans('main_trans.reports') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url(app()->getLocale() . '/suppliers_settings') }}"
                                    class="nav-link font {{ Request::is(app()->getLocale() . '/suppliers_settings') ? 'active' : '' }}">
                                    {{ trans('main_trans.settings') }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!-- Supply Chain Management -->
                <li class="nav-item">
                    <a class="nav-link menu-link font" href="#sidebarsupplychain_management"
                        data-bs-toggle="collapse" role="button"
                        aria-expanded="{{ Request::is(app()->getLocale() . '/supply_chain_*') ? 'true' : 'false' }}"
                        aria-controls="sidebarsupplychain_management">
                        <span class="icon_menu icon"><i class="mdi mdi-store"></i></span>
                        <span data-key="t-authentication">{{ trans('main_trans.stores') }}</span>
                    </a>
                    <div class="collapse menu-dropdown {{ Request::is(app()->getLocale() . '/supply_chain_*') ? 'show' : '' }}"
                        id="sidebarsupplychain_management">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ url(app()->getLocale() . '/supply_chain_program') }}"
                                    class="nav-link font {{ Request::is(app()->getLocale() . '/supply_chain_programs') ? 'active' : '' }}">
                                    {{ trans('main_trans.programs') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url(app()->getLocale() . '/supply_chain_reports') }}"
                                    class="nav-link font {{ Request::is(app()->getLocale() . '/supply_chain_reports') ? 'active' : '' }}">
                                    {{ trans('main_trans.reports') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url(app()->getLocale() . '/supply_chain_settings') }}"
                                    class="nav-link font {{ Request::is(app()->getLocale() . '/supply_chain_settings') ? 'active' : '' }}">
                                    {{ trans('main_trans.settings') }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <!-- Purchases Management -->
                <li class="nav-item">
                    <a class="nav-link menu-link font" href="#sidebarpurchases_management" data-bs-toggle="collapse"
                        role="button"
                        aria-expanded="{{ Request::is(app()->getLocale() . '/purchases_*') ? 'true' : 'false' }}"
                        aria-controls="sidebarpurchases_management">
                        <span class="icon_menu icon"><i class="mdi mdi-cart"></i></span>
                        <span data-key="t-purchases">{{ trans('main_trans.purchases') }}</span>
                    </a>
                    <div class="collapse menu-dropdown {{ Request::is(app()->getLocale() . '/purchases_*') ? 'show' : '' }}"
                        id="sidebarpurchases_management">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ url(app()->getLocale() . '/purchases_programs') }}"
                                    class="nav-link font {{ Request::is(app()->getLocale() . '/purchases_programs*') ? 'active' : '' }}">
                                    {{ trans('main_trans.programs') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url(app()->getLocale() . '/purchases_reports') }}"
                                    class="nav-link font {{ Request::is(app()->getLocale() . '/purchases_reports*') ? 'active' : '' }}">
                                    {{ trans('main_trans.reports') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url(app()->getLocale() . '/purchases_settings') }}"
                                    class="nav-link font {{ Request::is(app()->getLocale() . '/purchases_settings*') ? 'active' : '' }}">
                                    {{ trans('main_trans.settings') }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>
                <!-- Costing Management -->
                <li class="nav-item">
                    <a class="nav-link menu-link font" href="#sidebarcosting_management" data-bs-toggle="collapse"
                        role="button"
                        aria-expanded="{{ Request::is(app()->getLocale() . '/costing_*') ? 'true' : 'false' }}"
                        aria-controls="sidebarcosting_management">
                        <span class="icon_menu icon"><i class="mdi mdi-cash-multiple"></i></span>
                        <span data-key="t-costing">{{ trans('main_trans.costing') }}</span>
                    </a>
                    <div class="collapse menu-dropdown {{ Request::is(app()->getLocale() . '/costing_*') ? 'show' : '' }}"
                        id="sidebarcosting_management">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ url(app()->getLocale() . '/costing_programs') }}"
                                    class="nav-link font {{ Request::is(app()->getLocale() . '/costing_programs*') ? 'active' : '' }}">
                                    {{ trans('main_trans.programs') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url(app()->getLocale() . '/costing_reports') }}"
                                    class="nav-link font {{ Request::is(app()->getLocale() . '/costing_reports*') ? 'active' : '' }}">
                                    {{ trans('main_trans.reports') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url(app()->getLocale() . '/costing_settings') }}"
                                    class="nav-link font {{ Request::is(app()->getLocale() . '/costing_settings*') ? 'active' : '' }}">
                                    {{ trans('main_trans.settings') }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!-- Asset Management -->
                <li class="nav-item">
                    <a class="nav-link menu-link font" href="#sidebarassets_management" data-bs-toggle="collapse"
                        role="button"
                        aria-expanded="{{ Request::is(app()->getLocale() . '/assets_*') ? 'true' : 'false' }}"
                        aria-controls="sidebarassets_management">
                        <span class="icon_menu icon"><i class="mdi mdi-file-multiple"></i></span>
                        <span data-key="t-assets">{{ trans('main_trans.assets') }}</span>
                    </a>
                    <div class="collapse menu-dropdown {{ Request::is(app()->getLocale() . '/assets_*') ? 'show' : '' }}"
                        id="sidebarassets_management">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a href="{{ url(app()->getLocale() . '/assets_programs') }}"
                                    class="nav-link font {{ Request::is(app()->getLocale() . '/assets_programs*') ? 'active' : '' }}">
                                    {{ trans('main_trans.programs') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url(app()->getLocale() . '/assets_reports') }}"
                                    class="nav-link font {{ Request::is(app()->getLocale() . '/assets_reports*') ? 'active' : '' }}">
                                    {{ trans('main_trans.reports') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ url(app()->getLocale() . '/assets_settings') }}"
                                    class="nav-link font {{ Request::is(app()->getLocale() . '/assets_settings*') ? 'active' : '' }}">
                                    {{ trans('main_trans.settings') }}
                                </a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!-- Human Resources -->
                <li class="nav-item">
                    <a class="nav-link menu-link font" href="#sidebarhr_management" data-bs-toggle="collapse"
                        role="button" aria-expanded="false" aria-controls="sidebarAuth">
                        <span class="icon_menu icon"><i class="mdi mdi-account-group"></i></span>
                        <span data-key="t-authentication">{{ trans('hr.human_resources') }}</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarhr_management">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a class="nav-link font collapsed" href="#sidebarEmployees" data-bs-toggle="collapse"
                                    role="button" aria-expanded="false" aria-controls="sidebarEmployees">
                                    {{ trans('hr.employees_management') }}
                                    <i class="mdi mdi-chevron-right"></i>
                                </a>
                                <div class="collapse" id="sidebarEmployees">
                                    <ul class="nav nav-sm flex-column">
                                        <li class="nav-item">
                                            <a class="nav-link font"
                                                href="{{ url(app()->getLocale() . '/hr-employees-programs') }}">
                                                {{ trans('hr.programs') }}
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link font"
                                                href="{{ url(app()->getLocale() . '/hr-employees-settings') }}">
                                                {{ trans('hr.settings') }}
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>


                            <li class="nav-item">
                                <a class="nav-link font collapsed" href="#sidebarAttendance"
                                    data-bs-toggle="collapse" role="button" aria-expanded="false"
                                    aria-controls="sidebarAttendance">
                                    {{ trans('hr.attendance_management') }}
                                    <i class="mdi mdi-chevron-right"></i>
                                </a>
                                <div class="collapse" id="sidebarAttendance">
                                    <ul class="nav nav-sm flex-column">
                                        <li class="nav-item">
                                            <a class="nav-link font"
                                                href="{{ url(app()->getLocale() . '/hr-attendance-programs') }}">
                                                {{ trans('hr.programs') }}
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link font"
                                                href="{{ url(app()->getLocale() . '/hr-attendance-settings') }}">
                                                {{ trans('hr.settings') }}
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link font collapsed" href="#sidebarVacations" data-bs-toggle="collapse"
                                    role="button" aria-expanded="false" aria-controls="sidebarVacations">
                                    {{ trans('hr.vacations_management') }}
                                    <i class="mdi mdi-chevron-right"></i>
                                </a>
                                <div class="collapse" id="sidebarVacations">
                                    <ul class="nav nav-sm flex-column">
                                        <li class="nav-item">
                                            <a class="nav-link font"
                                                href="{{ url(app()->getLocale() . '/hr-vacations-programs') }}">
                                                {{ trans('hr.programs') }}
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link font"
                                                href="{{ url(app()->getLocale() . '/hr-vacations-settings') }}">
                                                {{ trans('hr.settings') }}
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link font collapsed" href="#sidebarPayroll" data-bs-toggle="collapse"
                                    role="button" aria-expanded="false" aria-controls="sidebarPayroll">
                                    {{ trans('hr.payroll_management') }}
                                    <i class="mdi mdi-chevron-right"></i>
                                </a>
                                <div class="collapse" id="sidebarPayroll">
                                    <ul class="nav nav-sm flex-column">
                                        <li class="nav-item">
                                            <a class="nav-link font"
                                                href="{{ url(app()->getLocale() . '/hr-payroll-programs') }}">
                                                {{ trans('hr.programs') }}
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link font"
                                                href="{{ url(app()->getLocale() . '/hr-payroll-settings') }}">
                                                {{ trans('hr.settings') }}
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link font collapsed" href="#sidebarAppraisal" data-bs-toggle="collapse"
                                    role="button" aria-expanded="false" aria-controls="sidebarAppraisal">
                                    {{ trans('hr.appraisal_management') }}
                                    <i class="mdi mdi-chevron-right"></i>
                                </a>
                                <div class="collapse" id="sidebarAppraisal">
                                    <ul class="nav nav-sm flex-column">
                                        <li class="nav-item">
                                            <a class="nav-link font"
                                                href="{{ url(app()->getLocale() . '/hr-appraisal-programs') }}">
                                                {{ trans('hr.programs') }}
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link font"
                                                href="{{ url(app()->getLocale() . '/hr-appraisal-settings') }}">
                                                {{ trans('hr.settings') }}
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>


                            <li class="nav-item">
                                <a class="nav-link font collapsed" href="#sidebarRecruitment"
                                    data-bs-toggle="collapse" role="button" aria-expanded="false"
                                    aria-controls="sidebarRecruitment">
                                    {{ trans('hr.recruitment_management') }}
                                    <i class="mdi mdi-chevron-right"></i>
                                </a>
                                <div class="collapse" id="sidebarRecruitment">
                                    <ul class="nav nav-sm flex-column">
                                        <li class="nav-item">
                                            <a class="nav-link font"
                                                href="{{ url(app()->getLocale() . '/hr-recruitment-programs') }}">
                                                {{ trans('hr.programs') }}
                                            </a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link font"
                                                href="{{ url(app()->getLocale() . '/hr-recruitment-settings') }}">
                                                {{ trans('hr.settings') }}
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                            
                            <!-- Reports Section -->
                            <li class="nav-item">
                                <a class="nav-link font collapsed" href="#sidebarHrReports"
                                    data-bs-toggle="collapse" role="button" aria-expanded="false"
                                    aria-controls="sidebarHrReports">
                                    {{ trans('hr.reports') }}
                                    <i class="mdi mdi-chevron-right"></i>
                                </a>
                                <div class="collapse" id="sidebarHrReports">
                                    <ul class="nav nav-sm flex-column">
                                        <li class="nav-item">
                                            <a class="nav-link font" href="{{ route('reports.employee-acknowledgment.form') }}">
                                                <i class="mdi mdi-file-document-outline"></i>
                                                إقرار الاطلاع على دليل السياسات والإجراءات
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </li>
                        </ul>
                    </div>
                </li>
                <!--user management-->
                <li class="nav-item">
                    <a class="nav-link menu-link font" href="#sidebaruser_management" data-bs-toggle="collapse"
                        role="button" aria-expanded="false" aria-controls="sidebarAuth">
                        <span class="icon_menu icon"><i class="mdi mdi-account-lock-outline"></i></span>
                        <span data-key="t-authentication">{{ trans('main_trans.users') }}</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebaruser_management">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="false"
                                    aria-controls="sidebarSignIn"
                                    data-key="t-signin">{{ trans('main_trans.user_add') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="false"
                                    aria-controls="sidebarSignUp"
                                    data-key="t-signup">{{ trans('main_trans.user_management') }}</a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!--reports-->
                <li class="nav-item">
                    <a class="nav-link menu-link font" href="#sidebarreports" data-bs-toggle="collapse"
                        role="button" aria-expanded="false" aria-controls="sidebarAuth">
                        <span class="icon_menu icon"><i class="mdi mdi-book-search-outline"></i></span>
                        <span data-key="t-authentication">{{ trans('main_trans.reports') }}</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarreports">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="false"
                                    aria-controls="sidebarSignIn"
                                    data-key="t-signin">{{ trans('main_trans.collections_expenses') }}</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="collapse" role="button" aria-expanded="false"
                                    aria-controls="sidebarSignUp"
                                    data-key="t-signup">{{ trans('main_trans.claims_management') }}</a>
                            </li>
                        </ul>
                    </div>
                </li>

                <!--settings-->
                <li class="nav-item">
                    <a class="nav-link menu-link font @if (Route::currentRouteName() == 'settings.create' || Route::currentRouteName() == 'settings.index') active @endif"
                        href="{{ url('/' . ($page = 'settings')) }}">
                        <span class="icon_menu icon"><i class="mdi mdi-cog-outline"></i></span>
                        <span data-key="t-widgets">{{ trans('main_trans.settings') }}</span>
                    </a>
                </li>

            </ul>
        </div>
        <!-- Sidebar -->
    </div>

    <div class="sidebar-background"></div>
</div>
<!-- Left Sidebar End -->
<!-- Vertical Overlay-->
<div class="vertical-overlay"></div>
