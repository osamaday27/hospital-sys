<div>
    {{-- ✅ Flash Messages --}}
    @foreach (['success' => 'success', 'error' => 'danger', 'info' => 'info'] as $key => $type)
        @if (session()->has($key))
            <div class="alert alert-{{ $type }} alert-dismissible fade show position-fixed top-0 end-0 m-3 shadow-lg no-print" style="z-index:1055;">
                {{ session($key) }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif
    @endforeach

    <div class="py-4">
        {{-- 🔹 Header --}}
        <div class="text-center bg-info text-white rounded-4 p-4 mb-4 shadow-sm">
            <h2 class="fw-bold mb-2"><i class="bi bi-eye"></i> OCT Nomenclature Report</h2>
            <small>EyeWorld Hospital - Giza, Egypt</small>
        </div>

        {{-- 🧍 Patient Info --}}
        <div class="card mb-4 border-0 shadow-sm">
            <div class="card-header bg-light fw-bold"><i class="bi bi-person-badge me-2"></i> Patient Information</div>
            <div class="card-body row g-3">
                <div class="col-md-4"><label class="form-label">Name</label><div class="form-control-plaintext">{{ $patient->full_name ?? 'N/A' }}</div></div>
                <div class="col-md-4"><label class="form-label">Age</label><div class="form-control-plaintext">{{ $age }} years</div></div>
                <div class="col-md-4"><label class="form-label">Gender</label><div class="form-control-plaintext">{{ ucfirst($patient->gender ?? 'N/A') }}</div></div>
            </div>
        </div>

        {{-- 🩵 Tabs --}}
        <ul class="nav nav-tabs mb-3">
            <li class="nav-item"><button class="nav-link @if($activeTab==='anterior') active @endif" wire:click="setActiveTab('anterior')" type="button">Anterior Segment</button></li>
            <li class="nav-item"><button class="nav-link @if($activeTab==='macula') active @endif" wire:click="setActiveTab('macula')" type="button">Macula</button></li>
            <li class="nav-item"><button class="nav-link @if($activeTab==='retina') active @endif" wire:click="setActiveTab('retina')" type="button">Retinal Layers</button></li>
            <li class="nav-item"><button class="nav-link @if($activeTab==='conclusion') active @endif" wire:click="setActiveTab('conclusion')" type="button">Conclusion</button></li>
        </ul>

        <div class="tab-content">
            {{-- 🩵 1️⃣ Anterior Segment --}}
            <div class="tab-pane fade @if($activeTab==='anterior') show active @endif" id="anterior">
                {{-- 1️⃣ Epithelium --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light fw-bold">1️⃣ Epithelium</div>
                    <div class="card-body row g-3">
                        <div class="col-md-3">
                            <label>Integrity</label>
                            <select wire:model="epithelium_integrity" class="form-select">
                                <option value="">Select...</option>
                                <option>Intact</option>
                                <option>Not Intact</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Thickness</label>
                            <select wire:model="epithelium_thickness" class="form-select">
                                <option value="">Select...</option>
                                <option>Thin (40 µ)</option>
                                <option>45 µ</option>
                                <option>50 µ</option>
                                <option>Thick (55 µ)</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Regularity</label>
                            <select wire:model="epithelium_regular" class="form-select">
                                <option value="">Select...</option>
                                <option>Regular</option>
                                <option>Irregular</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Transparency</label>
                            <select wire:model="epithelium_opacity" class="form-select">
                                <option value="">Select...</option>
                                <option>Clear</option>
                                <option>Show Opacity (measurement)</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- 2️⃣ Stroma --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light fw-bold">2️⃣ Stroma</div>
                    <div class="card-body row g-3">
                        <div class="col-md-3">
                            <label>Thickness Status</label>
                            <select wire:model="stroma_thickness_status" class="form-select">
                                <option value="">Select...</option>
                                <option>Normal</option>
                                <option>Decreased</option>
                                <option>Increased</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Opacity Location</label>
                            <select wire:model="stroma_opacity_location" class="form-select">
                                <option value="">Select...</option>
                                <option>Central</option>
                                <option>Paracentral</option>
                                <option>Peripheral</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Opacity Site</label>
                            <select wire:model="stroma_opacity_site" class="form-select">
                                <option value="">Select...</option>
                                <option>Anterior stromal</option>
                                <option>Midstromal</option>
                                <option>Posterior stromal</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Opacity Thickness (µ)</label>
                            <input type="text" wire:model="stroma_opacity_value" class="form-control" placeholder="e.g. 450 µ">
                        </div>
                    </div>
                </div>

                {{-- 3️⃣ Central Corneal Thickness --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light fw-bold">3️⃣ Central Corneal Thickness</div>
                    <div class="card-body">
                        <select wire:model="cct_status" class="form-select">
                            <option value="">Select...</option>
                            <option>Normal</option>
                            <option>Decreased</option>
                            <option>Increased</option>
                        </select>
                    </div>
                </div>

                {{-- 4️⃣ Anterior Chamber Angle --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light fw-bold">4️⃣ Anterior Chamber Angle Measurement</div>
                    <div class="card-body row g-3">
                        <div class="col-md-3"><label>Temporal (µm)</label><input type="text" wire:model="angle_temporal" class="form-control" placeholder="0.44 µm"></div>
                        <div class="col-md-3"><label>Nasal (µm)</label><input type="text" wire:model="angle_nasal" class="form-control" placeholder="0.42 µm"></div>
                        <div class="col-md-3"><label>Degree</label><select wire:model="angle_degree" class="form-select"><option>33º</option><option>40º</option></select></div>
                        <div class="col-md-3"><label>Classification (Shaffer)</label><select wire:model="angle_classification" class="form-select"><option>Open</option><option>Closed</option></select></div>
                    </div>
                </div>
            </div>

            {{-- 🩵 2️⃣ Macula --}}
            <div class="tab-pane fade @if($activeTab==='macula') show active @endif" id="macula">
                {{-- 1️⃣ PVD --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light fw-bold">1️⃣ PVD / VMT</div>
                    <div class="card-body row g-3">
                        <div class="col-md-3"><label>PVD</label><select wire:model="pvd" class="form-select"><option value="">Select...</option><option>Yes</option><option>No</option></select></div>
                        <div class="col-md-3"><label>VMT</label><select wire:model="vmt" class="form-select"><option value="">Select...</option><option>With</option><option>Without</option></select></div>
                        <div class="col-md-3"><label>Pattern</label><select wire:model="vmt_pattern" class="form-select"><option value="">Select...</option><option>Butterfly</option><option>Table top</option></select></div>
                    </div>
                </div>

                {{-- 2️⃣ ERM --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light fw-bold">2️⃣ ERM (Epiretinal Membrane)</div>
                    <div class="card-body row g-3">
                        <div class="col-md-3"><label>Type</label><select wire:model="erm" class="form-select"><option value="">Select...</option><option>Faint</option><option>Interrupted</option></select></div>
                        <div class="col-md-3"><label>Wrinkling</label><select wire:model="wrinkling" class="form-select"><option value="">Select...</option><option>Present</option><option>Absent</option></select></div>
                        <div class="col-md-3"><label>Tangential Traction</label><select wire:model="traction" class="form-select"><option value="">Select...</option><option>Present</option><option>Absent</option></select></div>
                        <div class="col-md-3"><label>Detached</label><select wire:model="detachment" class="form-select"><option value="">Select...</option><option>Yes</option><option>No</option></select></div>
                        <div class="col-md-3"><label>Edema A-P Traction</label><select wire:model="edema_ap" class="form-select"><option value="">Select...</option><option>Present</option><option>Absent</option></select></div>
                    </div>
                </div>

                {{-- 3️⃣ Ocular Contour --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light fw-bold">3️⃣ Ocular Contour</div>
                    <div class="card-body">
                        <select wire:model="ocular_contour" class="form-select">
                            <option value="">Select...</option>
                            <option>Normal</option>
                            <option>Altered</option>
                            <option>Convex</option>
                        </select>
                    </div>
                </div>

                {{-- 4️⃣ Foveal Pit --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light fw-bold">4️⃣ Foveal Pit</div>
                    <div class="card-body">
                        <select wire:model="foveal_pit" class="form-select">
                            <option value="">Select...</option>
                            <option>Normal preserved pit</option>
                            <option>Deep</option>
                            <option>Lost or altered</option>
                            <option>Shallow or wide</option>
                            <option>Thin</option>
                        </select>
                    </div>
                </div>
            </div>
            {{-- 🩵 3️⃣ Retinal Layers --}}
            <div class="tab-pane fade @if($activeTab==='retina') show active @endif" id="retina">

                {{-- 1️⃣ Reflectivity --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light fw-bold">1️⃣ Reflectivity</div>
                    <div class="card-body row g-3">
                        <div class="col-md-3">
                            <label>General Reflectivity</label>
                            <select wire:model="reflectivity" class="form-select">
                                <option value="">Select...</option>
                                <option>Normal reflectivity</option>
                                <option>Hyporeflective areas</option>
                                <option>Hyperreflective</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label>Hyporeflective Type</label>
                            <select wire:model="hyporeflective_type" class="form-select">
                                <option value="">Select...</option>
                                <option>Cystoid</option>
                                <option>Diffuse</option>
                            </select>
                        </div>

                        <div class="col-md-3">
                            <label>Hyperreflective Type</label>
                            <select wire:model="hyperreflective_type" class="form-select">
                                <option value="">Select...</option>
                                <option>Hemorrhage</option>
                                <option>Hard exudates</option>
                                <option>Cotton wool spots</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- 2️⃣ Intra Retinal Edema --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light fw-bold">2️⃣ Intra Retinal Edema</div>
                    <div class="card-body row g-3">
                        <div class="col-md-4">
                            <label>Location</label>
                            <select wire:model="edema_location" class="form-select">
                                <option value="">Select...</option>
                                <option>Inner layers</option>
                                <option>Outer layers</option>
                                <option>Both</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label>Shape</label>
                            <select wire:model="edema_shape" class="form-select">
                                <option value="">Select...</option>
                                <option>Cystic spaces (spongy)</option>
                                <option>Cystoid edema</option>
                                <option>Diffuse edema</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- 3️⃣ External Limiting Membrane --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light fw-bold">3️⃣ External Limiting Membrane</div>
                    <div class="card-body">
                        <select wire:model="elm" class="form-select">
                            <option value="">Select...</option>
                            <option>Intact</option>
                            <option>Not Intact</option>
                        </select>
                    </div>
                </div>

                {{-- 4️⃣ Ellipsoid Zone --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light fw-bold">4️⃣ Ellipsoid Zone</div>
                    <div class="card-body">
                        <select wire:model="ellipsoid_zone" class="form-select">
                            <option value="">Select...</option>
                            <option>Intact</option>
                            <option>Disrupted</option>
                        </select>
                    </div>
                </div>

                {{-- 5️⃣ Neuro Sensory Retina --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light fw-bold">5️⃣ Neuro Sensory Retina</div>
                    <div class="card-body">
                        <select wire:model="neurosensory_retina" class="form-select">
                            <option value="">Select...</option>
                            <option>Intact</option>
                            <option>Detached</option>
                        </select>
                    </div>
                </div>

                {{-- 6️⃣ RPE Line --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light fw-bold">6️⃣ RPE Line</div>
                    <div class="card-body">
                        <select wire:model="rpe_line" class="form-select">
                            <option value="">Select...</option>
                            <option>Intact</option>
                            <option>Irregular with undulation</option>
                            <option>Inward rolling</option>
                        </select>
                    </div>
                </div>

                {{-- 7️⃣ RPE/Bruch’s Membrane --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light fw-bold">7️⃣ RPE / Bruch’s Membrane</div>
                    <div class="card-body row g-3">
                        <div class="col-md-3">
                            <label>RPE/Bruch’s</label>
                            <select wire:model="rpe_bruch" class="form-select">
                                <option value="">Select...</option>
                                <option>Intact</option>
                                <option>Irregular</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Drusen</label>
                            <select wire:model="drusen" class="form-select">
                                <option value="">Select...</option>
                                <option>Hyperreflective drusen deposition</option>
                                <option>Absent</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label>Chorio-capillaries</label>
                            <select wire:model="chorio" class="form-select">
                                <option value="">Select...</option>
                                <option>Intact</option>
                                <option>Irregular</option>
                            </select>
                        </div>
                    </div>
                </div>

                {{-- 8️⃣ Central Foveal Thickness --}}
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light fw-bold">8️⃣ Central Foveal Thickness</div>
                    <div class="card-body">
                        <select wire:model="central_foveal_thickness" class="form-select">
                            <option value="">Select...</option>
                            <option>Normal</option>
                            <option>Decreased</option>
                            <option>Increased</option>
                        </select>
                    </div>
                </div>
            </div>

            {{-- 🩵 4️⃣ Conclusion --}}
            <div class="tab-pane fade @if($activeTab==='conclusion') show active @endif" id="conclusion">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-header bg-light fw-bold">Conclusion & Diagnosis</div>
                    <div class="card-body row g-3">
                        <div class="col-md-6">
                            <label>Diagnosis</label>
                            <textarea wire:model="diagnosis" class="form-control" rows="3" placeholder="Enter diagnosis..."></textarea>
                        </div>
                        <div class="col-md-6">
                            <label>Interpretation</label>
                            <textarea wire:model="interpretation" class="form-control" rows="3" placeholder="Enter interpretation..."></textarea>
                        </div>
                    </div>
                </div>

                {{-- ✅ Report Preview --}}
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-info text-white fw-bold">Generated Narrative</div>
                    <div class="card-body" style="white-space: pre-wrap;">
                        <p><strong>{{ $narratives['intro'] }}</strong></p>
                        <p>{{ $narratives['details'] }}</p>
                    </div>
                </div>

                {{-- 💾 Save Buttons --}}
                <div class="mt-4 d-flex justify-content-end gap-2 no-print">
                    <button wire:click="saveReport" class="btn btn-success px-4"><i class="bi bi-save me-2"></i>Save</button>
                </div>
            </div>
        </div>
    </div>
</div>
