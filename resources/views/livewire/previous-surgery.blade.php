<div class="card mb-3 border-info">
    <div class="card-header bg-info text-white">
        <i class="mdi mdi-hospital-building me-2"></i> Previous Surgery
    </div>
    <div class="card-body">
        <div class="row mb-3">
            <div class="col-md-6">
                <label class="form-label">Surgery</label>

                <input type="text" class="form-control" list="surgery_list" wire:model="surgery"
                    placeholder="Previous Surgery">

                <datalist id="surgery_list">
                    @foreach ($surgery_options as $option)
                        <option value="{{ $option }}"></option>
                    @endforeach
                </datalist>

                @error('surgery')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>

            <div class="col-md-2">
                <label class="form-label">Eye</label>
                <div class="btn-group w-100">
                    <button type="button" class="btn btn-outline-primary {{ $eye == 'OD' ? 'active' : '' }}"
                        wire:click="$set('eye', 'OD')">OD</button>
                    <button type="button" class="btn btn-outline-primary {{ $eye == 'OU' ? 'active' : '' }}"
                        wire:click="$set('eye', 'OU')">OU</button>
                    <button type="button" class="btn btn-outline-primary {{ $eye == 'OS' ? 'active' : '' }}"
                        wire:click="$set('eye', 'OS')">OS</button>
                </div>
            </div>
            <div class="col-md-2">
                <label class="form-label">For</label>
                <input type="number" class="form-control" wire:model="forNumber" placeholder="Enter Number">
            </div>
            <div class="col-md-2">
                <label class="form-label">Period</label>
                <div class="btn-group w-100">
                    <button type="button" class="btn btn-outline-secondary {{ $period == 'D' ? 'active' : '' }}"
                        wire:click="$set('period', 'D')">D</button>
                    <button type="button" class="btn btn-outline-secondary {{ $period == 'W' ? 'active' : '' }}"
                        wire:click="$set('period', 'W')">W</button>
                    <button type="button" class="btn btn-outline-secondary {{ $period == 'M' ? 'active' : '' }}"
                        wire:click="$set('period', 'M')">M</button>
                    <button type="button" class="btn btn-outline-secondary {{ $period == 'Y' ? 'active' : '' }}"
                        wire:click="$set('period', 'Y')">Y</button>
                </div>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-12">
                <label class="form-label">Remarks</label>
                <textarea class="form-control" wire:model="remarks" rows="3" placeholder="Enter remarks"></textarea>
            </div>
        </div>

        <div class="d-flex justify-content-between">
            <button type="button" class="btn btn-success" wire:click="save">
                <i class="mdi mdi-plus me-2"></i> Add Surgery
            </button>
            <button type="button" class="btn btn-danger" wire:click="clearForm">
                <i class="mdi mdi-delete me-2"></i> Clear Form
            </button>
        </div>

        <hr>

        <div id="surgery_list">
            <h5>History</h5>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th wire:click="sortBy('surgery')">Surgery</th>
                        <th wire:click="sortBy('eye')">Eye</th>
                        <th wire:click="sortBy('forNumber')">Duration</th>
                        <th>Remarks</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($histories as $history)
                        <tr>
                            <td>{{ $history->surgery }}</td>
                            <td>{{ $history->eye }}</td>
                            <td>{{ $history->forNumber }} {{ $history->period }}</td>
                            <td>{{ $history->remarks }}</td>
                            <td>
                                <button class="btn btn-warning btn-sm"
                                    wire:click="edit({{ $history->id }})">Edit</button>
                                <button class="btn btn-danger btn-sm"
                                    wire:click="delete({{ $history->id }})">Delete</button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            {{ $histories->links() }}
        </div>
    </div>
</div>
