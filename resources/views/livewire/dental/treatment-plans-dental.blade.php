<div>
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a class="nav-link {{ $tab === 'form' ? 'active' : '' }}" href="#" wire:click.prevent="$set('tab', 'form')">New Entry</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $tab === 'history' ? 'active' : '' }}" href="#" wire:click.prevent="$set('tab', 'history')">Previous Plans</a>
        </li>
    </ul>

    @if ($tab === 'form')
        <form wire:submit.prevent="save">
            <div class="row mb-3">
                <div class="col">
                    <label>Tooth</label>
                    <input class="form-control @error('tooth') is-invalid @enderror" wire:model="tooth">
                    @error('tooth') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="col">
                    <label>Seq.</label>
                    <input type="number" class="form-control @error('seq') is-invalid @enderror" wire:model="seq">
                    @error('seq') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>

            <div class="mb-3">
                <label>Clinical/Radiographic Findings</label>
                <textarea class="form-control @error('clinical_radiographic_findings') is-invalid @enderror" wire:model="clinical_radiographic_findings"></textarea>
                @error('clinical_radiographic_findings') <span class="text-danger">{{ $message }}</span> @enderror
            </div>

            <div class="row mb-3">
                <div class="col">
                    <label>Department</label>
                    <input class="form-control @error('department') is-invalid @enderror" wire:model="department">
                    @error('department') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="col">
                    <label>Treatment Options</label>
                    <input class="form-control @error('treatment_options') is-invalid @enderror" wire:model="treatment_options">
                    @error('treatment_options') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
                <div class="col">
                    <label>Faculty Signature</label>
                    <input class="form-control @error('faculty_signature') is-invalid @enderror" wire:model="faculty_signature">
                    @error('faculty_signature') <span class="text-danger">{{ $message }}</span> @enderror
                </div>
            </div>

            <button type="submit" class="btn btn-primary">{{ $editId ? 'Update' : 'Save' }}</button>
        </form>
    @elseif ($tab === 'history')
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Date</th>
                        <th>Tooth</th>
                        <th>Seq.</th>
                        <th>Findings</th>
                        <th>Department</th>
                        <th>Options</th>
                        <th>Signature</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($previousPlans as $plan)
                        <tr>
                            <td>{{ $plan->created_at->format('Y-m-d') }}</td>
                            <td>{{ $plan->tooth }}</td>
                            <td>{{ $plan->seq }}</td>
                            <td>{{ $plan->clinical_radiographic_findings }}</td>
                            <td>{{ $plan->department }}</td>
                            <td>{{ $plan->treatment_options }}</td>
                            <td>{{ $plan->faculty_signature }}</td>
                            <td>
                                <span class="badge {{ $plan->status === 'active' ? 'bg-success' : 'bg-secondary' }}" style="border-radius: 4px;width: 100%;margin-bottom: 10px;">
                                    {{ ucfirst($plan->status) }}
                                </span>
                            </td>
                            <td>
                                <button wire:click="edit({{ $plan->id }})" class="btn btn-sm btn-warning">Edit</button>
                                <button wire:click="delete({{ $plan->id }})" class="btn btn-sm btn-danger" onclick="confirm('Are you sure?') || event.stopImmediatePropagation()">Delete</button>
                                <button wire:click="toggleStatus({{ $plan->id }})" class="btn btn-sm btn-info">
                                    Toggle Status
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">No previous plans found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif
</div>
