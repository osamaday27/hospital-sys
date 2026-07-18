<div class="card mb-4 border-danger">
    <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
        <div>
            <i class="mdi mdi-allergy me-2"></i> Allergy
        </div>
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="showTrashedSwitch" wire:model="showTrashed">
            <label class="form-check-label" for="showTrashedSwitch">Show Deleted</label>
        </div>
    </div>

    <div class="card-body">
        @if (session()->has('message'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-md-6">
                <label class="form-label">Drug Allergy:</label>
                <input type="text" class="form-control" wire:model.defer="drug_allergy" placeholder="Enter drug allergy">
            </div>
            <div class="col-md-6">
                <label class="form-label">Food, Other Allergy:</label>
                <input type="text" class="form-control" wire:model.defer="food_allergy" placeholder="Enter food or other allergy">
            </div>
        </div>

        <button type="button" class="btn btn-success mt-3" wire:click="save">Save</button>
        @if($editingId)
            <button type="button" class="btn btn-secondary mt-3 ms-2" wire:click="resetInput">Cancel Edit</button>
        @endif

        <hr>

        <h5>History</h5>
        <input type="text" wire:model="search" class="form-control mb-2" placeholder="Search by Allergy">

        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th role="button" wire:click="sortBy('drug_allergy')">Drug Allergy</th>
                        <th role="button" wire:click="sortBy('food_allergy')">Food, Other Allergy</th>
                        <th role="button" wire:click="sortBy('created_at')">Created At</th>
                        <th style="width:220px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($allergy_records as $record)
                        <tr @if($record->deleted_at) class="table-danger" @endif>
                            <td>{{ $record->drug_allergy }}</td>
                            <td>{{ $record->food_allergy }}</td>
                            <td>{{ optional($record->created_at)->format('Y-m-d H:i') }}</td>
                            <td>
                                @if(!$record->deleted_at)
                                    <button type="button" class="btn btn-sm btn-primary"
                                            wire:click="edit({{ $record->id }})">
                                        <i class="ri-edit-line"></i> Edit
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                            wire:click="deleteRecord({{ $record->id }})">
                                        <i class="ri-delete-bin-7-line"></i> Delete
                                    </button>
                                @else
                                    <button type="button" class="btn btn-sm btn-warning"
                                            wire:click="restoreRecord({{ $record->id }})">
                                        <i class="ri-history-line"></i> Restore
                                    </button>
                                   {{-- <button type="button" class="btn btn-sm btn-danger"
                                            wire:click="forceDeleteRecord({{ $record->id }})">
                                        <i class="ri-delete-bin-fill"></i> Delete Forever
                                    </button>--}}
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">No records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $allergy_records->links() }}
    </div>
</div>
