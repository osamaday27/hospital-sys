<div class="card shadow-sm border-0">
    <div class="card-body">
        <!-- ✅ اختيار نوع الفحص -->
        <div class="mb-4">
            <label class="form-label fw-bold">Choose Refraction Type:</label>
            <div class="btn-group w-100">
                <button class="btn btn-outline-primary {{ $refraction_type == 'autoref' ? 'active' : '' }}"
                    wire:click="$set('refraction_type', 'autoref')">Autoref</button>
                <button class="btn btn-outline-primary {{ $refraction_type == 'autoref-cyclo' ? 'active' : '' }}"
                    wire:click="$set('refraction_type', 'autoref-cyclo')">Autoref after Cyclo</button>
                <button class="btn btn-outline-primary {{ $refraction_type == 'k-readings' ? 'active' : '' }}"
                    wire:click="$set('refraction_type', 'k-readings')">K-Readings</button>
            </div>
        </div>

        <div class="row">
            <!-- ✅ OD (Right Eye) -->
            <div class="col-md-6">
                <h6 class="text-primary">OD (Right Eye)</h6>
                <div class="row g-3">
                    {{-- OD Sph --}}
                    <div class="col-md-3">
                        <label class="form-label">Sph. (Scale)*</label>
                        <input type="text" class="form-control" list="sph_list" wire:model="od_sph"
                            placeholder="Enter Sph.">
                        <datalist id="sph_list">
                            @foreach ($sph_options as $option)
                                <option value="{{ $option }}"></option>
                            @endforeach
                        </datalist>
                    </div>

                    {{-- OD Cyl --}}
                    <div class="col-md-3">
                        <label class="form-label">Cyl (Scale)*</label>
                        <input type="text" class="form-control" list="cyl_list" wire:model="od_cyl"
                            placeholder="Enter Cyl.">
                        <datalist id="cyl_list">
                            @foreach ($cyl_options as $option)
                                <option value="{{ $option }}"></option>
                            @endforeach
                        </datalist>
                    </div>

                    {{-- OD Axis --}}
                    <div class="col-md-3">
                        <label class="form-label">Axis Cyl (180 scale)*</label>
                        <input type="text" class="form-control" list="axis_list" wire:model="od_axis"
                            placeholder="Enter Axis">
                        <datalist id="axis_list">
                            @foreach ($axis_options as $option)
                                <option value="{{ $option }}"></option>
                            @endforeach
                        </datalist>
                    </div>

                    {{-- OD Near Add --}}
                    <div class="col-md-3">
                        <label class="form-label">Near Add. (Scale)#</label>
                        <input type="text" class="form-control" list="near_add_list" wire:model="od_near_add"
                            placeholder="Enter Near Add.">
                        <datalist id="near_add_list">
                            @foreach ($near_add_options as $option)
                                <option value="{{ $option }}"></option>
                            @endforeach
                        </datalist>
                    </div>
                    {{-- OD BCVA --}}
                    <div class="col-md-12">
                        <label class="form-label">BCVA</label>
                        <select class="form-select" wire:model="od_bcva">
                            <option value="">Select BCVA</option>
                            @foreach ($bcva_options as $value)
                                <option value="{{ $value }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>
                    <label class="form-label">Comment</label>
                    <textarea class="form-control" wire:model="od_comment" rows="2"></textarea>
                </div>

            </div>

            <!-- ✅ OS (Left Eye) -->
            <div class="col-md-6">

                <h6 class="text-danger">OS (Left Eye)</h6>
                <div class="row g-3">
                    {{-- OS Sph --}}
                    <div class="col-md-3">
                        <label class="form-label">Sph. (Scale)*</label>
                        <input type="text" class="form-control" list="sph_list" wire:model="os_sph"
                            placeholder="Enter Sph.">
                    </div>

                    {{-- OS Cyl --}}
                    <div class="col-md-3">
                        <label class="form-label">Cyl (Scale)*</label>
                        <input type="text" class="form-control" list="cyl_list" wire:model="os_cyl"
                            placeholder="Enter Cyl.">
                    </div>

                    {{-- OS Axis --}}
                    <div class="col-md-3">
                        <label class="form-label">Axis Cyl (180 scale)*</label>
                        <input type="text" class="form-control" list="axis_list" wire:model="os_axis"
                            placeholder="Enter Axis">
                    </div>

                    {{-- OS Near Add --}}
                    <div class="col-md-3">
                        <label class="form-label">Near Add. (Scale)#</label>
                        <input type="text" class="form-control" list="near_add_list" wire:model="os_near_add"
                            placeholder="Enter Near Add.">
                    </div>

                    {{-- OS BCVA --}}
                    <div class="col-md-12">
                        <label class="form-label">BCVA</label>
                        <select class="form-select" wire:model="os_bcva">
                            <option value="">Select BCVA</option>
                            @foreach ($bcva_options as $value)
                                <option value="{{ $value }}">{{ $value }}</option>
                            @endforeach
                        </select>
                    </div>

                    <label class="form-label">Comment</label>
                    <textarea class="form-control" wire:model="os_comment" rows="2"></textarea>
                </div>
            </div>
        </div>
        <!-- ✅ الأزرار -->
        <div class="d-flex justify-content-between mt-3">
            <button class="btn btn-success" wire:click="save"><i class="mdi mdi-content-save"></i> Save</button>
            <button class="btn btn-danger" wire:click="resetInput"><i class="mdi mdi-delete"></i> Clear</button>
        </div>

        <hr>

        <!-- ✅ جدول عرض البيانات السابقة -->
        <h5 class="mt-4">Refraction History</h5>
        <input type="text" wire:model="search" class="form-control mb-2" placeholder="Search">

        <table class="table table-bordered">
            <thead class="bg-light">
                <tr>
                    <th>Refraction Type</th>
                    <th>OD Sph</th>
                    <th>OS Sph</th>
                    <th>BCVA</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($records as $record)
                    <tr>
                        <td>{{ $record->refraction_type }}</td>
                        <td>{{ $record->od_sph }}</td>
                        <td>{{ $record->os_sph }}</td>
                        <td>{{ $record->od_bcva }} / {{ $record->os_bcva }}</td>
                        <td>{{ $record->created_at->format('Y-m-d H:i') }}</td>
                        <td>
                            <!-- عرض التفاصيل -->
                            <button class="btn btn-info btn-sm" wire:click="viewDetails('{{ $record->id }}')">
                                <i class="mdi mdi-eye"></i> View
                            </button>

                            <!-- تعديل -->
                            <button class="btn btn-warning btn-sm" wire:click="edit({{ $record->id }})">
                                <i class="mdi mdi-pencil"></i> Edit
                            </button>

                            <!-- حذف -->
                            <button class="btn btn-danger btn-sm" wire:click="confirmDelete({{ $record->id }})">
                                <i class="mdi mdi-delete"></i> Delete
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- ✅ عرض التفاصيل -->
    @if ($showDetailsModal && $selectedRecord)
        <div class="modal fade show d-block" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-primary">
                            <i class="mdi mdi-eye-outline me-2"></i> Refraction Details
                        </h5>
                        <button type="button" class="btn btn-danger bg-gradient waves-effect waves-light"
                            wire:click="$set('showDetailsModal', false)">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <!-- ✅ معلومات عامة -->
                            <div class="col-md-6">
                                <p><strong>Patient ID:</strong> {{ $selectedRecord->id_patient }}</p>
                                <p><strong>Visit ID:</strong> {{ $selectedRecord->id_visit }}</p>
                                <p><strong>Reservation ID:</strong> {{ $selectedRecord->id_reservation }}</p>
                                <p><strong>Visit Type:</strong> {{ $selectedRecord->type_visit }}</p>
                                <p><strong>Refraction Type:</strong> {{ $selectedRecord->refraction_type }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>IPD:</strong> {{ $selectedRecord->ipd }}</p>
                                <p><strong>Created By:</strong> {{ $selectedRecord->user->name ?? 'Unknown' }}</p>
                                <p><strong>Created At:</strong>
                                    {{ $selectedRecord->created_at->format('Y-m-d H:i') }}
                                </p>
                            </div>
                        </div>

                        <hr>

                        <!-- ✅ بيانات OD (العين اليمنى) -->
                        <h6 class="text-primary"><i class="mdi mdi-eye me-2"></i> OD (Right Eye)</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <p><strong>Sph:</strong> {{ $selectedRecord->od_sph }}</p>
                            </div>
                            <div class="col-md-4">
                                <p><strong>Cyl:</strong> {{ $selectedRecord->od_cyl }}</p>
                            </div>
                            <div class="col-md-4">
                                <p><strong>Axis:</strong> {{ $selectedRecord->od_axis }}</p>
                            </div>
                            <div class="col-md-4">
                                <p><strong>Near Add:</strong> {{ $selectedRecord->od_near_add }}</p>
                            </div>
                            <div class="col-md-4">
                                <p><strong>BCVA:</strong> {{ $selectedRecord->od_bcva }}</p>
                            </div>
                        </div>

                        <hr>

                        <!-- ✅ بيانات OS (العين اليسرى) -->
                        <h6 class="text-success"><i class="mdi mdi-eye me-2"></i> OS (Left Eye)</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <p><strong>Sph:</strong> {{ $selectedRecord->os_sph }}</p>
                            </div>
                            <div class="col-md-4">
                                <p><strong>Cyl:</strong> {{ $selectedRecord->os_cyl }}</p>
                            </div>
                            <div class="col-md-4">
                                <p><strong>Axis:</strong> {{ $selectedRecord->os_axis }}</p>
                            </div>
                            <div class="col-md-4">
                                <p><strong>Near Add:</strong> {{ $selectedRecord->os_near_add }}</p>
                            </div>
                            <div class="col-md-4">
                                <p><strong>BCVA:</strong> {{ $selectedRecord->os_bcva }}</p>
                            </div>
                        </div>



                        <hr>

                        <!-- ✅ التعليقات -->
                        <h6 class="text-warning"><i class="mdi mdi-comment-text-outline me-2"></i> Comments</h6>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>OD Comment:</strong> {{ $selectedRecord->od_comment }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>OS Comment:</strong> {{ $selectedRecord->os_comment }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" wire:click="$set('showDetailsModal', false)">Close</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- تأكيد الحذف -->
    @if ($showDeleteModal)
        <div class="modal fade show d-block" tabindex="-1" role="dialog">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-danger">Confirm Deletion</h5>
                        <button type="button" class="btn btn-danger bg-gradient waves-effect waves-light"
                            wire:click="$set('showDeleteModal', false)">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p>Are you sure you want to delete this record?</p>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" wire:click="$set('showDeleteModal', false)">Cancel</button>
                        <button class="btn btn-danger" wire:click="deleteRecord">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
