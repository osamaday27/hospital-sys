<div class="card shadow-sm border-0">
    <div class="card-body">
        <!-- ✅ عنوان القسم -->
        <h5 class="card-title mb-4 text-primary">
            <i class="mdi mdi-eye-outline me-2"></i> Pin Hole
        </h5>

        <div class="row">
            <!-- ✅ OD (Right Eye) -->
            <div class="col-md-6">
                <h6 class="text-primary">OD (Right Eye)</h6>
                <label class="form-label">Scale</label>
                <input type="text" class="form-control mb-2" wire:model="od_scale" placeholder="Enter Scale">
                <label class="form-label">Comment</label>
                <textarea class="form-control mb-2" wire:model="od_comment" rows="2" placeholder="OD Comment"></textarea>
            </div>

            <!-- ✅ OS (Left Eye) -->
            <div class="col-md-6">
                <h6 class="text-danger">OS (Left Eye)</h6>
                <label class="form-label">Scale</label>
                <input type="text" class="form-control mb-2" wire:model="os_scale" placeholder="Enter Scale">
                <label class="form-label">Comment</label>
                <textarea class="form-control mb-2" wire:model="os_comment" rows="2" placeholder="OS Comment"></textarea>
            </div>
        </div>

        <!-- ✅ الأزرار -->
        <div class="d-flex justify-content-between mt-3">
            <button class="btn btn-success" wire:click="save"><i class="mdi mdi-content-save"></i> Save</button>
            <button class="btn btn-danger" wire:click="resetInput"><i class="mdi mdi-delete"></i> Clear</button>
        </div>

        <hr>

        <!-- ✅ جدول عرض البيانات -->
        <h5 class="mt-4">Pin Hole History</h5>
        <input type="text" wire:model="search" class="form-control mb-2" placeholder="Search by Scale or Comment">

        <table class="table table-bordered table-hover">
            <thead class="bg-light">
                <tr>
                    <th>OD Scale</th>
                    <th>OS Scale</th>
                    <th>OD Comment</th>
                    <th>OS Comment</th>
                    <th>Created By</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($records as $record)
                    <tr>
                        <td>{{ $record->od_scale }}</td>
                        <td>{{ $record->os_scale }}</td>
                        <td>{{ $record->od_comment }}</td>
                        <td>{{ $record->os_comment }}</td>
                        <td>{{ $record->user->name ?? 'Unknown' }}</td>
                        <td>
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
                            <button class="btn btn-secondary"
                                wire:click="$set('showDeleteModal', false)">Cancel</button>
                            <button class="btn btn-danger" wire:click="deleteRecord">Delete</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

    </div>
</div>
