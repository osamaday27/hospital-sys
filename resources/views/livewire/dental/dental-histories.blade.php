<div>
    @if (session()->has('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif

    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a class="nav-link {{ $historyTab === 'form' ? 'active' : '' }}" href="#"
                wire:click.prevent="$set('historyTab', 'form')">New Entry</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $historyTab === 'history' ? 'active' : '' }}" href="#"
                wire:click.prevent="$set('historyTab', 'history')">Previous History</a>
        </li>
    </ul>

    @if ($historyTab === 'form')
        <form wire:submit.prevent="save">
            <input type="hidden" wire:model="patient_id">
            <input type="hidden" wire:model="id_reservation">
            <input type="hidden" wire:model="id_visit">
            <input type="hidden" wire:model="type_visit">

            <div class="mb-3">
                <label class="form-label fw-bold">Dental Care:</label><br>
                @foreach (['Regular dental care', 'Irregular', 'More than twice a year', 'Emergencies', 'First visit'] as $care)
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" wire:model="dental_care"
                            value="{{ $care }}">
                        <label class="form-check-label">{{ $care }}</label>
                    </div>
                @endforeach
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Past Treatment:</label><br>
                @foreach (['Wisdom tooth extractions', 'Orthodontics', 'Gum disease', 'Treatment for jaw trauma/fracture', 'Adverse reaction to dental treatment', 'Dental fears or anxiety'] as $item)
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" wire:model="past_treatment"
                            value="{{ $item }}">
                        <label class="form-check-label">{{ $item }}</label>
                    </div>
                @endforeach
            </div>

            <div class="mb-3">
                <label>Jaw trauma/fracture notes:</label>
                <textarea class="form-control" wire:model="jaw_trauma_notes"></textarea>
            </div>

            <div class="mb-3">
                <label>Adverse reaction notes:</label>
                <textarea class="form-control" wire:model="adverse_reaction_notes"></textarea>
            </div>

            <div class="mb-3">
                <label>Dental fears/anxiety:</label>
                <textarea class="form-control" wire:model="dental_fears_notes"></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Oral Health State:</label><br>
                @foreach (['Excellent', 'Very Good', 'Good', 'Fair', 'Poor'] as $state)
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" wire:model="oral_health_state"
                            value="{{ $state }}">
                        <label class="form-check-label">{{ $state }}</label>
                    </div>
                @endforeach
            </div>

            <button type="submit" class="btn btn-primary">Save</button>
        </form>
    @elseif ($historyTab === 'history')
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Dental Care</th>
                        <th>Past Treatment</th>
                        <th>Jaw Trauma Notes</th>
                        <th>Adverse Reaction Notes</th>
                        <th>Dental Fears Notes</th>
                        <th>Oral Health State</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($previousHistories as $history)
                        <tr>
                            <td>{{ $history->created_at->format('Y-m-d H:i') }}</td>
                            <td>{{ $history->dental_care }}</td>
                            <td>
                                @php
                                    $treatments = is_array($history->past_treatment)
                                        ? $history->past_treatment
                                        : json_decode($history->past_treatment, true);
                                @endphp

                                @if (is_array($treatments))
                                    @foreach ($treatments as $item)
                                        <span class="badge text-bg-secondary" style="border-radius: 4px;width: 100%;margin-bottom: 10px;">{{ $item }}</span>
                                    @endforeach
                                @else
                                    <span class="text-muted">No data</span>
                                @endif
                            </td>

                            <td>{{ $history->jaw_trauma_notes }}</td>
                            <td>{{ $history->adverse_reaction_notes }}</td>
                            <td>{{ $history->dental_fears_notes }}</td>
                            <td>{{ $history->oral_health_state }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center">No previous history found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif
</div>
