<div class="card shadow-sm border-0">
    <div class="card-body">
        <!-- ✅ رسائل النجاح -->
        @if (session()->has('message'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <h5 class="card-title mb-4 text-primary">
            <i class="mdi mdi-eye-outline me-2"></i> K-Readings
        </h5>

        <div class="row">
            <!-- ✅ OD (Right Eye) -->
            <div class="col-md-6">
                <h6 class="text-primary fw-bold">OD (Right Eye)</h6>
                <div class="mb-2">
                    <label class="form-label">K1 Number</label>
                    <input type="text" class="form-control" wire:model="od_k1_number" placeholder="Enter K1 Number">
                </div>
                <div class="mb-2">
                    <label class="form-label">@ (180° scale)</label>
                    <input type="text" class="form-control" wire:model="od_k1_axis"  list="axis_list"  placeholder="Enter Axis">
                    
                </div>
                <div class="mb-2">
                    <label class="form-label">K2 Number</label>
                    <input type="text" class="form-control" wire:model="od_k2_number" placeholder="Enter K2 Number">
                </div>
                <div class="mb-2">
                    <label class="form-label">@ (180° scale)</label>
                    <input type="text" class="form-control" wire:model="od_k2_axis"  list="axis_list"  placeholder="Enter Axis">
                </div>
                <div class="mb-2">
                    <label class="form-label">Comment</label>
                    <textarea class="form-control" wire:model="od_comment" rows="2" placeholder="OD Comment"></textarea>
                </div>
            </div>

            <!-- ✅ OS (Left Eye) -->
            <div class="col-md-6">
                <h6 class="text-danger fw-bold">OS (Left Eye)</h6>
                <div class="mb-2">
                    <label class="form-label">K1 Number</label>
                    <input type="text" class="form-control" wire:model="os_k1_number" placeholder="Enter K1 Number">
                </div>
                <div class="mb-2">
                    <label class="form-label">@ (180° scale)</label>
                    <input type="text" class="form-control" wire:model="os_k1_axis"  list="axis_list"  placeholder="Enter Axis">
                </div>
                <div class="mb-2">
                    <label class="form-label">K2 Number</label>
                    <input type="text" class="form-control" wire:model="os_k2_number" placeholder="Enter K2 Number">
                </div>
                <div class="mb-2">
                    <label class="form-label">@ (180° scale)</label>
                    <input type="text" class="form-control" wire:model="os_k2_axis"  list="axis_list"  placeholder="Enter Axis">
                </div>
                <div class="mb-2">
                    <label class="form-label">Comment</label>
                    <textarea class="form-control" wire:model="os_comment" rows="2" placeholder="OS Comment"></textarea>
                </div>
            </div>
        </div>

        <!-- ✅ الأزرار -->
        <div class="d-flex justify-content-between mt-3">
            <button class="btn btn-success" wire:click="save"><i class="mdi mdi-content-save"></i> Save</button>
            <button class="btn btn-danger" wire:click="resetInput"><i class="mdi mdi-delete"></i> Clear</button>
        </div>

        <hr>

        <!-- ✅ عرض البيانات -->
        <h5 class="mt-4">K-Readings History</h5>
        <table class="table table-bordered">
            <thead class="bg-light">
                <tr>
                    <th>K1 OD</th>
                    <th>K2 OD</th>
                    <th>K1 OS</th>
                    <th>K2 OS</th>
                    <th>IPD</th>
                    <th>Created By</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($records as $record)
                    <tr>
                        <td>{{ $record->od_k1_number }} @ {{ $record->od_k1_axis }}</td>
                        <td>{{ $record->od_k2_number }} @ {{ $record->od_k2_axis }}</td>
                        <td>{{ $record->os_k1_number }} @ {{ $record->os_k1_axis }}</td>
                        <td>{{ $record->os_k2_number }} @ {{ $record->os_k2_axis }}</td>
                        <td>{{ $record->user->name ?? 'Unknown' }}</td>
                        <td>{{ $record->created_at->format('Y-m-d H:i') }}</td>
                        <td>
                            <button class="btn btn-info btn-sm" wire:click="viewDetails({{ $record->id }})">
                                <i class="mdi mdi-eye"></i> View
                            </button>
                            <button class="btn btn-warning btn-sm" wire:click="edit({{ $record->id }})">
                                <i class="mdi mdi-pencil"></i> Edit
                            </button>
                            <button class="btn btn-danger btn-sm" wire:click="confirmDelete({{ $record->id }})">
                                <i class="mdi mdi-delete"></i> Delete
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- ✅ عرض التفاصيل -->
        @if ($showDetailsModal)
            <div class="modal fade show d-block" tabindex="-1" role="dialog">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title text-primary">
                                <i class="mdi mdi-eye-outline me-2"></i> K-Readings Details
                            </h5>
                            <button type="button" class="btn btn-danger bg-gradient waves-effect waves-light"
                                wire:click="$set('showDetailsModal', false)">
                                <span>&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            <p><strong>K1 OD:</strong> {{ $selectedRecord->od_k1_number }} @
                                {{ $selectedRecord->od_k1_axis }}</p>
                            <p><strong>K2 OD:</strong> {{ $selectedRecord->od_k2_number }} @
                                {{ $selectedRecord->od_k2_axis }}</p>
                            <p><strong>K1 OS:</strong> {{ $selectedRecord->os_k1_number }} @
                                {{ $selectedRecord->os_k1_axis }}</p>
                            <p><strong>K2 OS:</strong> {{ $selectedRecord->os_k2_number }} @
                                {{ $selectedRecord->os_k2_axis }}</p>
                            <p><strong>Comments:</strong> {{ $selectedRecord->od_comment }} |
                                {{ $selectedRecord->os_comment }}</p>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-secondary"
                                wire:click="$set('showDetailsModal', false)">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
