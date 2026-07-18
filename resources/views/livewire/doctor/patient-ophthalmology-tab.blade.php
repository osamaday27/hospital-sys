<div>
    <style>
        .medical-tabs-wrapper {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .medical-tabs-nav {
            display: flex;
            gap: 0.5rem;
            overflow-x: auto;
            padding: 1rem 2.5rem;
            background-color: #f2f4f7;
            border-radius: 12px;
            scroll-behavior: smooth;
            scrollbar-width: none;
        }

        .medical-tabs-nav::-webkit-scrollbar {
            display: none;
        }

        .medical-tab-link {
            display: flex;
            align-items: center;
            gap: 6px;
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
            font-weight: 600;
            border-radius: 10px;
            color: #0b3558;
            background-color: #ffffff;
            border: 1px solid #dee2e6;
            transition: all 0.25s ease-in-out;
            white-space: nowrap;
            cursor: pointer;
        }

        .medical-tab-link:hover {
            background-color: #e8f3fc;
            color: #1373c3;
        }

        .medical-tab-link.active {
            background: linear-gradient(90deg, #1373c3, #2ba6df);
            color: #ffffff;
            border-color: transparent;
        }

        .medical-tab-link i {
            font-size: 1rem;
        }

        .tab-badge {
            background: #1373c3;
            color: #fff;
            font-size: 0.7rem;
            padding: 0.1rem 0.4rem;
            border-radius: 10px;
            font-weight: 700;
        }

        .scroll-button {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            background: rgba(255, 255, 255, 0.6);
            backdrop-filter: blur(6px);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex !important;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            cursor: pointer;
            border: 1px solid rgba(19, 115, 195, 0.3);
            z-index: 10;
            transition: 0.3s ease;
        }

        .scroll-button:hover {
            background: rgba(255, 255, 255, 0.85);
            transform: translateY(-50%) scale(1.05);
        }

        .scroll-button i {
            color: #1373c3;
            font-size: 1.5rem;
            transition: transform 0.2s ease;
        }

        .scroll-button:hover i {
            transform: scale(1.2);
        }

        .scroll-left {
            left: 5px;
        }

        .scroll-right {
            right: 5px;
        }

        @media (max-width: 768px) {
            .medical-tab-link span {
                display: none;
            }

            .medical-tab-link {
                padding: 0.6rem;
                border-radius: 50%;
            }
        }
    </style>
    <div class="medical-tabs-wrapper">
         <div class="d-flex profile-wrapper">
                    @include('medical.doctor.components.program.ophthalmology.medical_file.tab_option')
                </div>
                <div class="tab-content pt-4 text-muted">
                    @include('medical.doctor.components.program.ophthalmology.medical_file.overview')
                    @include('medical.doctor.components.program.ophthalmology.medical_file.complaint')
                    @include('medical.doctor.components.program.ophthalmology.medical_file.medical_history')
                    @include('medical.doctor.components.program.ophthalmology.medical_file.glasses')
                    @include('medical.doctor.components.program.ophthalmology.medical_file.eye-examination')
                    @include('medical.doctor.components.program.ophthalmology.medical_file.general_examination')
                    @include('medical.doctor.components.program.ophthalmology.medical_file.surgery_request')
                    @include('medical.doctor.components.program.ophthalmology.medical_file.prescription')
                    @include('medical.doctor.components.program.ophthalmology.medical_file.doctor_order')
                    @include('medical.doctor.components.program.medical_sheet.view')
                    @include('medical.doctor.components.program.medical_file.medical_reports')
                    @include('medical.doctor.components.program.medical_file.notes')
                </div>
    </div>

    <script>
        function scrollTabs(direction) {
            const container = document.getElementById('medicalTabsScroll');
            const isRTL = document.documentElement.getAttribute('dir') === 'rtl';
            const scrollAmount = container.clientWidth * 0.7;

            container.scrollBy({
                left: direction === 'left' ? (isRTL ? scrollAmount : -scrollAmount) : (isRTL ? -scrollAmount :
                    scrollAmount),
                behavior: 'smooth'
            });
        }
        function scrollToActiveTab() {
            const container = document.getElementById('medicalTabsScroll');
            if (!container) return;

            setTimeout(() => {
                const activeTab = container.querySelector('.medical-tab-link.active');
                if (activeTab) {
                    const containerRect = container.getBoundingClientRect();
                    const tabRect = activeTab.getBoundingClientRect();
                    const offset = tabRect.left - containerRect.left - (container.clientWidth / 2) + (
                        tabRect.width / 2
                    );

                    container.scrollBy({
                        left: offset,
                        behavior: 'smooth'
                    });
                } else {
                    container.scrollTo({
                        left: 0,
                        behavior: 'smooth'
                    });
                }
            }, 200);
        }

        document.addEventListener('livewire:load', () => {
            scrollToActiveTab();

            Livewire.hook('message.processed', (message, component) => {
                scrollToActiveTab();
            });
        });
    </script>
</div>
