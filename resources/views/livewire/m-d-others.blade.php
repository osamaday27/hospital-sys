<div class="card mb-4 border-secondary">
    <div class="card-header bg-secondary text-white">
        <i class="mdi mdi-plus-circle me-2"></i> Others
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <label class="form-label">Others:</label>
                <input type="text" class="form-control" wire:model="others" placeholder="Enter other details">
            </div>
            <div class="col-md-12 mt-2">
                <label class="form-label">Remarks:</label>
                <textarea class="form-control" wire:model="remarks" rows="3" placeholder="Enter remarks"></textarea>
            </div>
        </div>

        <button class="btn btn-success mt-3" wire:click="save">Save</button>
        <hr>

        <h5>History</h5>
        <input type="text" wire:model="search" class="form-control mb-2" placeholder="Search by Others">

        <table class="table">
            <thead>
                <tr>
                    <th wire:click="sortBy('others')">Others</th>
                    <th wire:click="sortBy('remarks')">Remarks</th>
                    <th wire:click="sortBy('created_at')">Created At</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($others_records as $record)
                    <tr>
                        <td>{{ $record->others }}</td>
                        <td>{{ $record->remarks }}</td>
                        <td>{{ $record->created_at->format('Y-m-d H:i') }}</td>
                        <td>
                            <button class="btn btn-warning btn-sm" wire:click="edit({{ $record->id }})">
                                <i class="mdi mdi-pencil"></i>
                            </button>
                            <button class="btn btn-danger btn-sm" wire:click="delete({{ $record->id }})">
                                <i class="mdi mdi-trash-can"></i>
                            </button>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $others_records->links() }}
    </div>
</div>
