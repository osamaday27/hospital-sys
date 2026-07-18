<div class="card mb-3 border-warning">
    <div class="card-header bg-warning text-white">
        <i class="mdi mdi-microscope me-2"></i> Investigation
    </div>
    <div class="card-body">
        <form wire:submit.prevent="save">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label class="form-label">Investigation</label>
                    <input type="text" class="form-control" wire:model.lazy="investigation" placeholder="Investigation">
                    @error('investigation') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="col-md-2">
                    <label class="form-label">Eye</label>
                    <div class="btn-group w-100">
                        @foreach (['OD', 'OU', 'OS'] as $eyeOption)
                            <button type="button"
                                class="btn btn-outline-primary {{ $eye === $eyeOption ? 'active' : '' }}"
                                wire:click="$set('eye', '{{ $eyeOption }}')">
                                {{ $eyeOption }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="col-md-2">
                    <label class="form-label">For</label>
                    <input type="number" class="form-control" wire:model.lazy="forNumber" placeholder="Enter Number">
                    @error('forNumber') <span class="text-danger">{{ $message }}</span> @enderror
                </div>

                <div class="col-md-2">
                    <label class="form-label">Period</label>
                    <div class="btn-group w-100">
                        @foreach (['D', 'W', 'M', 'Y'] as $p)
                            <button type="button"
                                class="btn btn-outline-secondary {{ $period === $p ? 'active' : '' }}"
                                wire:click="$set('period', '{{ $p }}')">
                                {{ $p }}
                            </button>
                        @endforeach
                    </div>
                    @error('period') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-12">
                    <label class="form-label">Upload Image</label>
                    <input type="file" class="form-control" wire:model="file">
                    @error('file') <span class="text-danger">{{ $message }}</span> @enderror

                    @if ($file)
                        <div class="mt-2">
                            <img src="{{ $file->temporaryUrl() }}" alt="Preview" class="img-thumbnail" width="150">
                        </div>
                    @endif
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-12">
                    <label class="form-label">Remarks</label>
                    <textarea class="form-control" wire:model.lazy="remarks" rows="3" placeholder="Enter remarks"></textarea>
                    @error('remarks') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="d-flex justify-content-between">
                <button type="submit" class="btn btn-success">
                    <i class="mdi mdi-plus me-2"></i> Add Investigation
                </button>
                <button type="button" class="btn btn-danger" wire:click="resetInput">
                    <i class="mdi mdi-delete me-2"></i> Clear Form
                </button>
            </div>
        </form>

        <hr>

        <h5>History</h5>
        <input type="text" wire:model.debounce.500ms="search" class="form-control mb-2" placeholder="Search Investigation">

        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="bg-light">
                    <tr>
                        <th>Investigation</th>
                        <th>Eye</th>
                        <th>Duration</th>
                        <th>File</th>
                        <th>Remarks</th>
                        <th>Created By</th>
                        <th>Created At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($histories as $history)
                        <tr>
                            <td>{{ $history->investigation }}</td>
                            <td>{{ $history->eye }}</td>
                            <td>{{ $history->forNumber }} {{ $history->period }}</td>
                            <td>
                                @if ($history->file_path)
                                    <br>
                                    <a href="{{ asset('storage/' . $history->file_path) }}" target="_blank">View File</a>
                                @endif
                            </td>
                            
                            <td>{{ $history->remarks }}</td>
                            <td>{{ $history->user->name ?? 'Unknown' }}</td>
                            <td>{{ $history->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                <button class="btn btn-warning btn-sm" wire:click="edit({{ $history->id }})">Edit</button>
                                <button class="btn btn-danger btn-sm" wire:click="delete({{ $history->id }})">Delete</button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">No records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{ $histories->links() }}
    </div>
</div>
