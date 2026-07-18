<div>
    @if (session()->has('message'))
        <div class="alert alert-success">{{ session('message') }}</div>
    @endif

    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a class="nav-link {{ $tab === 'form' ? 'active' : '' }}" href="#" wire:click.prevent="$set('tab', 'form')">New Entry</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $tab === 'history' ? 'active' : '' }}" href="#" wire:click.prevent="$set('tab', 'history')">Previous Exams</a>
        </li>
    </ul>

    @if ($tab === 'form')
        <form wire:submit.prevent="save">
            <input type="hidden" wire:model="patient_id">
            <input type="hidden" wire:model="id_reservation">
            <input type="hidden" wire:model="id_visit">
            <input type="hidden" wire:model="type_visit">

            <div class="mb-3">
                <label>General appearance: patient’s gait</label>
                <input class="form-control" wire:model="general_appearance">
            </div>
            <div class="mb-3">
                <label>Mobility</label>
                <input class="form-control" wire:model="mobility">
            </div>
            <div class="mb-3">
                <label>Lesions or scars</label>
                <input class="form-control" wire:model="lesions_or_scars">
            </div>
            <div class="mb-3">
                <label>Other notes</label>
                <textarea class="form-control" wire:model="other_notes"></textarea>
            </div>
            <div class="mb-3">
                <label>Head and Neck exam: Lymph node (tenderness - enlargements)</label>
                <input class="form-control" wire:model="lymph_node">
            </div>
            <div class="mb-3">
                <label>Other notes (Head and Neck)</label>
                <textarea class="form-control" wire:model="head_neck_notes"></textarea>
            </div>
            <div class="mb-3">
                <label>TMJ (clicking - tenderness - swelling)</label>
                <input class="form-control" wire:model="tmj">
            </div>
            <div class="mb-3">
                <label>TMJ other notes</label>
                <textarea class="form-control" wire:model="tmj_notes"></textarea>
            </div>
            <div class="mb-3">
                <label>Asymmetries</label>
                <input class="form-control" wire:model="asymmetries">
            </div>
            <div class="mb-3">
                <label>Lips and Labial mucosa</label>
                <textarea class="form-control" wire:model="lips_labial_mucosa"></textarea>
            </div>
            <div class="mb-3">
                <label>Buccal and Vestibular mucosa</label>
                <textarea class="form-control" wire:model="buccal_vestibular_mucosa"></textarea>
            </div>
            <div class="mb-3">
                <label>Hard and Soft palate</label>
                <textarea class="form-control" wire:model="hard_soft_palate"></textarea>
            </div>
            <div class="mb-3">
                <label>Tongue</label>
                <textarea class="form-control" wire:model="tongue"></textarea>
            </div>
            <div class="mb-3">
                <label>Floor of mouth</label>
                <textarea class="form-control" wire:model="floor_of_mouth"></textarea>
            </div>
            <div class="mb-3">
                <label>Gingiva and Alveolar mucosa</label>
                <textarea class="form-control" wire:model="gingiva_alveolar_mucosa"></textarea>
            </div>
            <div class="mb-3">
                <label>Further Examinations needed</label>
                <textarea class="form-control" wire:model="further_examinations"></textarea>
            </div>

            <button type="submit" class="btn btn-primary">Save</button>
        </form>
    @elseif ($tab === 'history')
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>General Appearance</th>
                        <th>Mobility</th>
                        <th>Lesions/Scars</th>
                        <th>Other Notes</th>
                        <th>Head/Neck</th>
                        <th>TMJ</th>
                        <th>Asymmetries</th>
                        <th>Intraoral</th>
                        <th>Further Exams</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($previousExams as $exam)
                        <tr>
                            <td>{{ $exam->created_at->format('Y-m-d H:i') }}</td>
                            <td>{{ $exam->general_appearance }}</td>
                            <td>{{ $exam->mobility }}</td>
                            <td>{{ $exam->lesions_or_scars }}</td>
                            <td>{{ $exam->other_notes }}</td>
                            <td>
                                LN: {{ $exam->lymph_node }}<br>
                                Notes: {{ $exam->head_neck_notes }}
                            </td>
                            <td>
                                {{ $exam->tmj }}<br>
                                Notes: {{ $exam->tmj_notes }}
                            </td>
                            <td>{{ $exam->asymmetries }}</td>
                            <td>
                                Lips: {{ $exam->lips_labial_mucosa }}<br>
                                Buccal: {{ $exam->buccal_vestibular_mucosa }}<br>
                                Palate: {{ $exam->hard_soft_palate }}<br>
                                Tongue: {{ $exam->tongue }}<br>
                                Floor: {{ $exam->floor_of_mouth }}<br>
                                Gingiva: {{ $exam->gingiva_alveolar_mucosa }}
                            </td>
                            <td>{{ $exam->further_examinations }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center">No previous exams found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif
</div>
