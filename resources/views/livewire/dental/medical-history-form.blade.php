<div>
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a class="nav-link {{ $tab === 'form' ? 'active' : '' }}" href="#"
                wire:click.prevent="$set('tab', 'form')">New Entry</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $tab === 'history' ? 'active' : '' }}" href="#"
                wire:click.prevent="$set('tab', 'history')">History</a>
        </li>
    </ul>

    @if ($tab === 'form')
        <form wire:submit.prevent="save">
            <h5>Medical Conditions</h5>
            @foreach ($medical_conditions as $key => $value)
                @if ($key !== 'other_infectious_disease')
                    <div class="row mb-2">
                        <div class="col-md-4">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input"
                                    wire:model="medical_conditions.{{ $key }}.checked"
                                    id="{{ $key }}">
                                <label class="form-check-label"
                                    for="{{ $key }}">{{ ucwords(str_replace('_', ' ', $key)) }}</label>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <input type="text" class="form-control"
                                wire:model="medical_conditions.{{ $key }}.note" placeholder="Notes...">
                        </div>
                    </div>
                @endif
            @endforeach

            <h6>Other Infectious Diseases</h6>
            @foreach ($medical_conditions['other_infectious_disease'] as $index => $item)
                <div class="input-group mb-2">
                    <input type="text" class="form-control"
                        wire:model="medical_conditions.other_infectious_disease.{{ $index }}">
                    <button type="button" class="btn btn-danger"
                        wire:click="removeOtherInfectious({{ $index }})">Remove</button>
                </div>
            @endforeach
            <button type="button" class="btn btn-secondary mb-3" wire:click="addOtherInfectious">Add More</button>

            <div class="mb-2">
                <label>Are you undergoing any medical treatment?</label>
                <input type="checkbox" class="form-check-input ms-2" wire:model="medical_treatment">
                <input type="text" class="form-control mt-1" wire:model="medical_treatment_note"
                    placeholder="Notes...">
            </div>
            <div class="mb-2">
                <label>Had any serious operation or hospitalization?</label>
                <input type="checkbox" class="form-check-input ms-2" wire:model="serious_operation">
                <input type="text" class="form-control mt-1" wire:model="serious_operation_note"
                    placeholder="Notes...">
            </div>
            <div class="mb-2">
                <label>Are you taking any medication?</label>
                <input type="checkbox" class="form-check-input ms-2" wire:model="taking_medication">
                <input type="text" class="form-control mt-1" wire:model="taking_medication_note"
                    placeholder="Notes...">
            </div>

            <div class="mb-2">
                <label>Are you a smoker?</label>
                <input type="checkbox" class="form-check-input ms-2" wire:model="smoker">
                <input type="text" class="form-control mt-1" wire:model="smoke_count" placeholder="How many/day">
            </div>

            <h6>For Females Only</h6>
            <div class="row">
                <div class="col-md-4">
                    <input type="text" class="form-control" wire:model="female.pregnant"
                        placeholder="Pregnant (Yes / No / Not sure)">
                </div>
                <div class="col-md-4">
                    <input type="text" class="form-control" wire:model="female.weeks" placeholder="Weeks">
                </div>
                <div class="col-md-4">
                    <input type="text" class="form-control" wire:model="female.nursing"
                        placeholder="Nursing (Yes / No)">
                </div>
            </div>

            <h5 class="mt-4">Allergic or Unusual Reaction</h5>
            @foreach (['penicillins', 'aspirin', 'local_anesthesia', 'latex'] as $item)
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" wire:model="allergies.{{ $item }}"
                        id="{{ $item }}">
                    <label class="form-check-label"
                        for="{{ $item }}">{{ ucwords(str_replace('_', ' ', $item)) }}</label>
                </div>
            @endforeach

            <h6>Other Drugs</h6>
            @foreach ($allergies['other_drugs'] as $index => $drug)
                <div class="input-group mb-2">
                    <input type="text" class="form-control" wire:model="allergies.other_drugs.{{ $index }}">
                    <button type="button" class="btn btn-danger"
                        wire:click="removeOtherDrug({{ $index }})">Remove</button>
                </div>
            @endforeach
            <button type="button" class="btn btn-secondary mb-3" wire:click="addOtherDrug">Add More</button>

            <h6>Other Substances</h6>
            @foreach ($allergies['other_substances'] as $index => $substance)
                <div class="input-group mb-2">
                    <input type="text" class="form-control"
                        wire:model="allergies.other_substances.{{ $index }}">
                    <button type="button" class="btn btn-danger"
                        wire:click="removeOtherSubstance({{ $index }})">Remove</button>
                </div>
            @endforeach
            <button type="button" class="btn btn-secondary mb-3" wire:click="addOtherSubstance">Add More</button>

            <button type="submit" class="btn btn-primary mt-3">{{ $editId ? 'Update' : 'Save' }}</button>
        </form>
    @elseif ($tab === 'history')
        <div class="table-responsive">
            <table class="table table-striped">
                <thead class="table-dark">
                    <tr>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Details</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($records as $record)
                        <tr>
                            <td>{{ $record->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                <span class="badge {{ $record->status === 'active' ? 'bg-success' : 'bg-secondary' }}"
                                    style="border-radius: 4px;width: 100%;margin-bottom: 10px;">
                                    {{ ucfirst($record->status) }}
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-info btn-sm" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#details-{{ $record->id }}">
                                    View
                                </button>
                            </td>
                            <td>
                                <button wire:click="edit({{ $record->id }})"
                                    class="btn btn-warning btn-sm">Edit</button>
                                <button wire:click="delete({{ $record->id }})" class="btn btn-danger btn-sm"
                                    onclick="confirm('Are you sure?') || event.stopImmediatePropagation()">Delete</button>
                                <button wire:click="toggleStatus({{ $record->id }})"
                                    class="btn btn-secondary btn-sm">Toggle Status</button>
                            </td>
                        </tr>
                        <tr class="collapse" id="details-{{ $record->id }}">
                            <td colspan="4">
                                <div class="p-2">
                                    <strong>Medical Conditions:</strong>
                                    <ul>
                                        @foreach ($record->medical_conditions as $k => $v)
                                            @if ($k !== 'other_infectious_disease')
                                                <li>{{ ucwords(str_replace('_', ' ', $k)) }}:
                                                    {{ $v['checked'] ? 'Yes' : 'No' }} | Notes: {{ $v['note'] }}
                                                </li>
                                            @else
                                                <li>Other Infectious Diseases:
                                                    <ul>
                                                        @foreach ($v as $disease)
                                                            <li>{{ $disease }}</li>
                                                        @endforeach
                                                    </ul>
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>

                                    <strong>Medications:</strong>
                                    <ul>
                                        @foreach ($record->medications as $med)
                                            <li>{{ $med }}</li>
                                        @endforeach
                                    </ul>

                                    <strong>Medical Treatment:</strong> {{ $record->medical_treatment ? 'Yes' : 'No' }}
                                    | Notes: {{ $record->medical_treatment_note }}<br>
                                    <strong>Serious Operation:</strong> {{ $record->serious_operation ? 'Yes' : 'No' }}
                                    | Notes: {{ $record->serious_operation_note }}<br>
                                    <strong>Taking Medication:</strong> {{ $record->taking_medication ? 'Yes' : 'No' }}
                                    | Notes: {{ $record->taking_medication_note }}<br>
                                    <strong>Smoker:</strong> {{ $record->smoker ? 'Yes' : 'No' }} | How Many/Day:
                                    {{ $record->smoke_count }}

                                    <h6 class="mt-2">For Females</h6>
                                    Pregnant: {{ $record->female['pregnant'] }} | Weeks:
                                    {{ $record->female['weeks'] }} | Nursing: {{ $record->female['nursing'] }}

                                    <h6 class="mt-2">Allergies</h6>
                                    <ul>
                                        @foreach (['penicillins', 'aspirin', 'local_anesthesia', 'latex'] as $allergy)
                                            <li>{{ ucwords(str_replace('_', ' ', $allergy)) }}:
                                                {{ $record->allergies[$allergy] ? 'Yes' : 'No' }}</li>
                                        @endforeach
                                    </ul>

                                    <strong>Other Drugs:</strong>
                                    <ul>
                                        @foreach ($record->allergies['other_drugs'] as $drug)
                                            <li>{{ $drug }}</li>
                                        @endforeach
                                    </ul>

                                    <strong>Other Substances:</strong>
                                    <ul>
                                        @foreach ($record->allergies['other_substances'] as $substance)
                                            <li>{{ $substance }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="4" class="text-center">No records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif
</div>
