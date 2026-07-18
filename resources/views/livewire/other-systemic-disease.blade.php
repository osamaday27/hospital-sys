<div class="card mb-4 border-warning">
    <div class="card-header bg-warning text-white">
        <i class="mdi mdi-medical-bag me-2"></i> Other Systemic Disease
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <label class="form-label">Disease:</label>
                <input type="text" class="form-control" wire:model="disease" placeholder="Enter disease">
            </div>
            <div class="col-md-3">
                <label class="form-label">Duration:</label>
                <input type="number" min="0" class="form-control" wire:model="duration" placeholder="Enter duration">
            </div>
            <div class="col-md-3">
                <label class="form-label">Duration Unit:</label>
                <select class="form-control" wire:model="duration_unit">
                    <option value="New">New</option>
                    <option value="M">Months</option>
                    <option value="Y">Years</option>
                </select>
            </div>
        </div>

        <button class="btn btn-success mt-3" wire:click="save">Save</button>
        <hr>

        <h5>History</h5>
        <input type="text" wire:model="search" class="form-control mb-2" placeholder="Search by Disease">

        <table class="table">
            <thead>
                <tr>
                    <th wire:click="sortBy('disease')">Disease</th>
                    <th wire:click="sortBy('duration')">Duration</th>
                    <th wire:click="sortBy('remark')">Remark</th>
                    <th wire:click="sortBy('created_at')">Created At</th>
                </tr>
            </thead>
            <tbody>
                @foreach($disease_records as $record)
                    <tr>
                        <td>{{ $record->disease }}</td>
                        <td>{{ $record->duration }} {{ $record->duration_unit }}</td>
                        <td>{{ $record->remark }}</td>
                        <td>{{ $record->created_at->format('Y-m-d H:i') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{ $disease_records->links() }}
    </div>
</div>
