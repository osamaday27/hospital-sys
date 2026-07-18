<div class="card shadow-sm border-0">
    <div class="card-body">
        <!-- رسائل النجاح -->
        @if (session()->has('message'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- عرض الفاليديشن -->
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <h5 class="card-title mb-4 text-primary">
            <i class="mdi mdi-eye-outline me-2"></i> Subjective Refraction
        </h5>

        <!-- اختيار نوع الفحص -->
        <div class="mb-4">
            <label class="form-label fw-bold">Choose Refraction Type:</label>
            <div class="btn-group w-100">
                <button type="button"
                    class="btn btn-outline-primary {{ $refraction_type == 'subjective' ? 'active' : '' }}"
                    wire:click="$set('refraction_type', 'subjective')">Subjective</button>
                <button type="button"
                    class="btn btn-outline-primary {{ $refraction_type == 'subjective-cyclo' ? 'active' : '' }}"
                    wire:click="$set('refraction_type', 'subjective-cyclo')">Subjective after Cyclo</button>
                <button type="button"
                    class="btn btn-outline-primary {{ $refraction_type == 'pin-hole' ? 'active' : '' }}"
                    wire:click="$set('refraction_type', 'pin-hole')">Pin Hole</button>
            </div>
        </div>

        <div class="row">
            <!-- OD (Right Eye) -->
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 text-dark">OD (Right Eye)</h6>
                    </div>
                    <div class="card-body">
                        <label class="form-label">UCVA</label>
                        <select class="form-select" wire:model="od_ucva">
                            <option value="">Select UCVA</option>
                            @foreach (['20/20', '20/40', '20/60', '2.00', '1.5', '1.2', '1.00', '0.9', '0.8', '0.7', '0.6', '0.5', '0.3', '0.2', '0.15', '0.1', '0.05', 'CF 2m', 'CF 1.5m', 'CF 1m', 'CF 75cm', 'CF 50cm', 'CF 20cm', 'CF 10cm', 'HM- GP', 'HM- BP', 'PL- GP', 'PL- BP', 'No PL', 'Can Fix and Follow', 'Cannot be assessed'] as $value)
                                <option value="{{ $value }}">{{ $value }}</option>
                            @endforeach
                        </select>
                        <label class="form-label">Sph.</label>
                        <input type="text" class="form-control" wire:model="od_sph" placeholder="Enter Sph."
                            list="sph_list" <label class="form-label">Cyl.</label>
                        <input type="text" class="form-control" wire:model="od_cyl" placeholder="Enter Cyl."
                            list="cyl_list">
                        <label class="form-label">Axis Cyl</label>
                        <input type="text" class="form-control" wire:model="od_axis" placeholder="Enter Axis"
                            list="axis_list">
                        <label class="form-label">Near Add.</label>
                        <input type="number" class="form-control" wire:model="od_near_add" list="near_add_list"
                            placeholder="Enter Near Add.">

                        <label class="form-label">Near UAVA</label>
                        <input type="text" class="form-control" wire:model="od_near_uava"
                            placeholder="Enter Near UAVA">
                        <label class="form-label">BCVA</label>
                        <select class="form-select" wire:model="od_bcva">
                            <option value="">Select BCVA</option>
                            @foreach (['20/20', '20/40', '20/60', '2.00', '1.5', '1.2', '1.00', '0.9', '0.8', '0.7', '0.6', '0.5', '0.3', '0.2', '0.15', '0.1', '0.05', 'CF 2m', 'CF 1.5m', 'CF 1m', 'CF 75cm', 'CF 50cm', 'CF 20cm', 'CF 10cm', 'HM- GP', 'HM- BP', 'PL- GP', 'PL- BP', 'No PL', 'Can Fix and Follow', 'Cannot be assessed'] as $value)
                                <option value="{{ $value }}">{{ $value }}</option>
                            @endforeach
                        </select>
                        <label class="form-label">Near BCVA</label>
                        <input type="text" class="form-control" wire:model="od_near_bcva"
                            placeholder="Enter Near BCVA">
                        <label class="form-label">comment</label>
                        <input type="text" class="form-control" wire:model="od_comment" placeholder="Enter comment">
                    </div>
                </div>
            </div>
            <!-- OU (Both Eyes) -->
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 text-dark">OU (Both Eyes)</h6>
                    </div>
                    <div class="card-body">
                        <label class="form-label">UCVA</label>
                        <select class="form-select" wire:model="ou_ucva">
                            <option value="">Select UCVA</option>
                            @foreach (['20/20', '20/40', '20/60', '2.00', '1.5', '1.2', '1.00', '0.9', '0.8', '0.7', '0.6', '0.5', '0.3', '0.2', '0.15', '0.1', '0.05', 'CF 2m', 'CF 1.5m', 'CF 1m', 'CF 75cm', 'CF 50cm', 'CF 20cm', 'CF 10cm', 'HM- GP', 'HM- BP', 'PL- GP', 'PL- BP', 'No PL', 'Can Fix and Follow', 'Cannot be assessed'] as $value)
                                <option value="{{ $value }}">{{ $value }}</option>
                            @endforeach
                        </select>
                        <label class="form-label">Near UAVA</label>
                        <input type="text" class="form-control" wire:model="ou_near_uava"
                            placeholder="Enter Near UAVA">
                        <label class="form-label">BCVA</label>
                        <select class="form-select" wire:model="ou_bcva">
                            <option value="">Select BCVA</option>
                            @foreach (['20/20', '20/40', '20/60', '2.00', '1.5', '1.2', '1.00', '0.9', '0.8', '0.7', '0.6', '0.5', '0.3', '0.2', '0.15', '0.1', '0.05', 'CF 2m', 'CF 1.5m', 'CF 1m', 'CF 75cm', 'CF 50cm', 'CF 20cm', 'CF 10cm', 'HM- GP', 'HM- BP', 'PL- GP', 'PL- BP', 'No PL', 'Can Fix and Follow', 'Cannot be assessed'] as $value)
                                <option value="{{ $value }}">{{ $value }}</option>
                            @endforeach
                        </select>
                        <label class="form-label">Near BCVA</label>
                        <input type="text" class="form-control" wire:model="ou_near_bcva"
                            placeholder="Enter Near BCVA">
                    </div>
                </div>
            </div>

            <!-- OS (Left Eye) -->
            <div class="col-md-4">
                <div class="card mb-4">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 text-dark">OS (Left Eye)</h6>
                    </div>
                    <div class="card-body">
                         <label class="form-label">UCVA</label>
                        <select class="form-select" wire:model="os_ucva">
                            <option value="">Select UCVA</option>
                            @foreach (['20/20', '20/40', '20/60', '2.00', '1.5', '1.2', '1.00', '0.9', '0.8', '0.7', '0.6', '0.5', '0.3', '0.2', '0.15', '0.1', '0.05', 'CF 2m', 'CF 1.5m', 'CF 1m', 'CF 75cm', 'CF 50cm', 'CF 20cm', 'CF 10cm', 'HM- GP', 'HM- BP', 'PL- GP', 'PL- BP', 'No PL', 'Can Fix and Follow', 'Cannot be assessed'] as $value)
                                <option value="{{ $value }}">{{ $value }}</option>
                            @endforeach
                        </select>
                        <label class="form-label">Sph.</label>
                        <input type="text" class="form-control" wire:model="os_sph" placeholder="Enter Sph."
                            list="sph_list">
                        <label class="form-label">Cyl.</label>
                        <input type="text" class="form-control" wire:model="os_cyl" placeholder="Enter Cyl."
                            list="cyl_list">
                        <label class="form-label">Axis Cyl</label>
                        <input type="text" class="form-control" wire:model="os_axis" placeholder="Enter Axis"
                            list="axis_list">
                        <label class="form-label">Near Add.</label>
                        <input type="number" class="form-control" wire:model="os_near_add" list="near_add_list"
                            placeholder="Enter Near Add.">
                       
                        <label class="form-label">Near UAVA</label>
                        <input type="text" class="form-control" wire:model="os_near_uava"
                            placeholder="Enter Near UAVA">



                        <label class="form-label">BCVA</label>
                        <select class="form-select" wire:model="os_bcva">
                            <option value="">Select BCVA</option>
                            @foreach (['20/20', '20/40', '20/60', '2.00', '1.5', '1.2', '1.00', '0.9', '0.8', '0.7', '0.6', '0.5', '0.3', '0.2', '0.15', '0.1', '0.05', 'CF 2m', 'CF 1.5m', 'CF 1m', 'CF 75cm', 'CF 50cm', 'CF 20cm', 'CF 10cm', 'HM- GP', 'HM- BP', 'PL- GP', 'PL- BP', 'No PL', 'Can Fix and Follow', 'Cannot be assessed'] as $value)
                                <option value="{{ $value }}">{{ $value }}</option>
                            @endforeach
                        </select>
                        <label class="form-label">Near BCVA</label>
                        <input type="text" class="form-control" wire:model="os_near_bcva"
                            placeholder="Enter Near BCVA">
                        <label class="form-label">comment</label>
                        <input type="text" class="form-control" wire:model="os_comment"
                            placeholder="Enter comment">
                    </div>
                </div>
            </div>
        </div>


        <!-- IPD Input -->
        <div class="row mb-12">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="mb-0 text-dark">IPD (Interpupillary Distance)</h6>
                    </div>
                    <div class="card-body">
                        <input type="number" class="form-control" wire:model="ipd" placeholder="Enter IPD">
                    </div>
                </div>
            </div>
        </div>

        <!-- Buttons -->
        <div class="d-flex justify-content-between">
            <button class="btn btn-success" wire:click="save">
                <i class="mdi mdi-content-save me-2"></i> Save
            </button>
            <button class="btn btn-danger" wire:click="resetInput">
                <i class="mdi mdi-delete me-2"></i> Clear
            </button>
        </div>

        <hr>
        <!-- Data Table -->
        <h5 class="mt-4">Refraction History</h5>
        <input type="text" wire:model="search" class="form-control mb-2" placeholder="Search">

        <table class="table table-bordered">
            <thead class="bg-light">
                <tr>
                    <th>Refraction Type</th>
                    <th>OD Sph</th>
                    <th>OS Sph</th>
                    <th>UCVA</th>
                    <th>BCVA</th>
                    <th>IPD</th>
                    <th>Created By</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($records as $record)
                    <tr>
                        <td>{{ $record->refraction_type }}</td>
                        <td>{{ $record->od_sph }}</td>
                        <td>{{ $record->os_sph }}</td>
                        <td>{{ $record->ou_ucva }}</td>
                        <td>{{ $record->ou_bcva }}</td>
                        <td>{{ $record->ipd }}</td>
                        <td>{{ $record->created_by }}</td>
                        <td>
                            <!-- عرض التفاصيل -->
                            <button class="btn btn-info btn-sm" wire:click="viewDetails({{ $record->id }})">
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

    <!-- عرض التفاصيل -->
    @if ($showDetailsModal)
        <div class="modal fade show d-block" tabindex="-1" role="dialog">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title text-primary">
                            <i class="mdi mdi-eye-outline me-2"></i> Refraction Details
                        </h5>
                        <button type="button" class="btn btn-primary bg-gradient waves-effect waves-light"
                            wire:click="$set('showDetailsModal', false)">
                            <span>&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <!-- معلومات عامة -->
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
                                <p><strong>Created At:</strong> {{ $selectedRecord->created_at->format('Y-m-d H:i') }}
                                </p>
                            </div>
                        </div>

                        <hr>

                        <!-- بيانات OD (العين اليمنى) -->
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
                                <p><strong>UCVA:</strong> {{ $selectedRecord->od_ucva }}</p>
                            </div>
                            <div class="col-md-4">
                                <p><strong>Near UAVA:</strong> {{ $selectedRecord->od_near_uava }}</p>
                            </div>
                            <div class="col-md-4">
                                <p><strong>BCVA:</strong> {{ $selectedRecord->od_bcva }}</p>
                            </div>
                            <div class="col-md-4">
                                <p><strong>Near BCVA:</strong> {{ $selectedRecord->od_near_bcva }}</p>
                            </div>
                        </div>

                        <hr>

                        <!-- بيانات OS (العين اليسرى) -->
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
                                <p><strong>UCVA:</strong> {{ $selectedRecord->os_ucva }}</p>
                            </div>
                            <div class="col-md-4">
                                <p><strong>Near UAVA:</strong> {{ $selectedRecord->os_near_uava }}</p>
                            </div>
                            <div class="col-md-4">
                                <p><strong>BCVA:</strong> {{ $selectedRecord->os_bcva }}</p>
                            </div>
                            <div class="col-md-4">
                                <p><strong>Near BCVA:</strong> {{ $selectedRecord->os_near_bcva }}</p>
                            </div>
                        </div>

                        <hr>

                        <!-- بيانات OU (العينين معًا) -->
                        <h6 class="text-info"><i class="mdi mdi-eye me-2"></i> OU (Both Eyes)</h6>
                        <div class="row">
                            <div class="col-md-4">
                                <p><strong>UCVA:</strong> {{ $selectedRecord->ou_ucva }}</p>
                            </div>
                            <div class="col-md-4">
                                <p><strong>Near UAVA:</strong> {{ $selectedRecord->ou_near_uava }}</p>
                            </div>
                            <div class="col-md-4">
                                <p><strong>BCVA:</strong> {{ $selectedRecord->ou_bcva }}</p>
                            </div>
                            <div class="col-md-4">
                                <p><strong>Near BCVA:</strong> {{ $selectedRecord->ou_near_bcva }}</p>
                            </div>
                        </div>

                        <hr>

                        <!-- التعليقات -->
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
