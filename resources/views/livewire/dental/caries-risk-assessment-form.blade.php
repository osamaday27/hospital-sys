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
                wire:click.prevent="$set('tab', 'form')">New Assessment</a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ $tab === 'history' ? 'active' : '' }}" href="#"
                wire:click.prevent="$set('tab', 'history')">Previous Assessments</a>
        </li>
    </ul>

    @if ($tab === 'form')
        <form wire:submit.prevent="save">
            <!-- High Risk Section -->
            <h5>1. High Risk Factors</h5>
            @foreach (['Visible caries into dentin by radiograph', 'Caries restored in past three years', '# of new carious lesions', '# of carious lesions with existing restorations', 'DMFT', 'Heavy plaque on teeth', 'Frequent snacks of sugars/starch', 'Hyposalivatory medications', 'Radiation to head/neck', 'Systemic reasons (Sjogren\'s)', 'Inadequate salivary flow', 'Appliances present'] as $item)
                <div class="row mb-2">
                    <div class="col-md-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox"
                                wire:model="high_risk.{{ $item }}.checked" id="high_{{ $loop->index }}">
                            <label class="form-check-label" for="high_{{ $loop->index }}">{{ $item }}</label>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <input type="text" class="form-control" wire:model="high_risk.{{ $item }}.note"
                            placeholder="Notes...">
                    </div>
                </div>
            @endforeach

            <h5 class="mt-4">2. Moderate Risk Factors</h5>
            @foreach (['Exposed roots', 'Deep pits & fissures', 'Interproximal enamel lesions', 'White spot lesions/discoloration', 'Uses recreational drugs'] as $item)
                <div class="row mb-2">
                    <div class="col-md-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox"
                                wire:model="moderate_risk.{{ $item }}.checked"
                                id="moderate_{{ $loop->index }}">
                            <label class="form-check-label"
                                for="moderate_{{ $loop->index }}">{{ $item }}</label>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <input type="text" class="form-control" wire:model="moderate_risk.{{ $item }}.note"
                            placeholder="Notes...">
                    </div>
                </div>
            @endforeach

            <h5 class="mt-4">3. Protective Factors</h5>
            @foreach (['Lives/works in fluoridated community', 'Uses fluoride toothpaste daily', 'Uses fluoride mouthwash', 'Adequate salivary flow', 'Uses Xylitol gum/mints'] as $item)
                <div class="row mb-2">
                    <div class="col-md-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox"
                                wire:model="protective_factors.{{ $item }}.checked"
                                id="protective_{{ $loop->index }}">
                            <label class="form-check-label"
                                for="protective_{{ $loop->index }}">{{ $item }}</label>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <input type="text" class="form-control"
                            wire:model="protective_factors.{{ $item }}.note" placeholder="Notes...">
                    </div>
                </div>
            @endforeach

            <!-- Other Inputs -->
            <div class="row mt-3">
                <div class="col">
                    <label>Brushing Frequency</label>
                    <input type="text" class="form-control" wire:model="brushing_frequency">
                </div>
                <div class="col">
                    <label>Brushing Method</label>
                    <input type="text" class="form-control" wire:model="brushing_method">
                </div>
                <div class="col">
                    <label>Brush Type</label>
                    <input type="text" class="form-control" wire:model="brush_type">
                </div>
            </div>
            <div class="mt-3">
                <label>Interproximal Cleansing</label>
                <input type="text" class="form-control" wire:model="interproximal_cleansing">
            </div>
            <div class="mt-3">
                <label>Caries Risk Overall</label>
                <select class="form-select" wire:model="caries_risk_overall">
                    <option value="">Select Risk</option>
                    <option>High</option>
                    <option>Moderate</option>
                    <option>Low</option>
                </select>
            </div>
            <div class="mt-3 form-check">
                <input type="checkbox" class="form-check-input" wire:model="recommendations_given" id="recommendations">
                <label class="form-check-label" for="recommendations">Recommendations Given</label>
            </div>
            <div class="mt-3">
                <label>Date Given</label>
                <input type="date" class="form-control" wire:model="date_given">
            </div>
            <div class="mt-3">
                <label>Faculty Signature</label>
                <input type="text" class="form-control" wire:model="faculty_signature">
            </div>
            <div class="mt-3">
                <label>Date Reviewed</label>
                <input type="date" class="form-control" wire:model="date_reviewed">
            </div>
            <button type="submit" class="btn btn-primary mt-3">{{ $editId ? 'Update' : 'Save' }}</button>
        </form>
    @elseif ($tab === 'history')
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead class="table-dark">
                    <tr>
                        <th>Date</th>
                        <th>Overall Risk</th>
                        <th>Faculty</th>
                        <th>Status</th>
                        <th>Details</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($records as $record)
                        <tr>
                            <td>{{ $record->created_at->format('Y-m-d') }}</td>
                            <td>{{ $record->caries_risk_overall }}</td>
                            <td>{{ $record->faculty_signature }}</td>
                            <td>
                                <span
                                    class="badge {{ $record->status === 'active' ? 'bg-success' : 'bg-secondary' }}" style="border-radius: 4px;width: 100%;margin-bottom: 10px;">
                                    {{ ucfirst($record->status) }}
                                </span>
                            </td>
                            <td>
                                <button class="btn btn-sm btn-info" type="button" data-bs-toggle="collapse"
                                    data-bs-target="#details-{{ $record->id }}">
                                    View
                                </button>
                            </td>
                            <td>
                                <button wire:click="edit({{ $record->id }})"
                                    class="btn btn-sm btn-warning">Edit</button>
                                <button wire:click="delete({{ $record->id }})" class="btn btn-sm btn-danger"
                                    onclick="confirm('Are you sure?') || event.stopImmediatePropagation()">Delete</button>
                                <button wire:click="toggleStatus({{ $record->id }})"
                                    class="btn btn-sm btn-secondary">Toggle Status</button>
                            </td>
                        </tr>
                        <tr class="collapse" id="details-{{ $record->id }}">
                            <td colspan="6">
                                <strong>High Risk:</strong>
                                <ul>
                                    @foreach ($record->high_risk as $k => $v)
                                        <li>
                                            {{ $k }} -
                                            {{ is_array($v) && isset($v['checked']) && $v['checked'] ? 'Yes' : 'No' }}
                                            | Notes:
                                            {{ is_array($v) && isset($v['note']) ? $v['note'] : '' }}
                                        </li>
                                    @endforeach

                                </ul>
                                <strong>Moderate Risk:</strong>
                                <ul>
                                    @foreach ($record->moderate_risk as $k => $v)
                                         <li>
                                            {{ $k }} -
                                            {{ is_array($v) && isset($v['checked']) && $v['checked'] ? 'Yes' : 'No' }}
                                            | Notes:
                                            {{ is_array($v) && isset($v['note']) ? $v['note'] : '' }}
                                        </li>
                                    @endforeach
                                </ul>
                                <strong>Protective Factors:</strong>
                                <ul>
                                    @foreach ($record->protective_factors as $k => $v)
                                          <li>
                                            {{ $k }} -
                                            {{ is_array($v) && isset($v['checked']) && $v['checked'] ? 'Yes' : 'No' }}
                                            | Notes:
                                            {{ is_array($v) && isset($v['note']) ? $v['note'] : '' }}
                                        </li>
                                    @endforeach
                                </ul>
                                <p><strong>Brushing:</strong> {{ $record->brushing_frequency }},
                                    {{ $record->brushing_method }}, {{ $record->brush_type }}</p>
                                <p><strong>Interproximal:</strong> {{ $record->interproximal_cleansing }}</p>
                                <p><strong>Recommendations Given:</strong>
                                    {{ $record->recommendations_given ? 'Yes' : 'No' }} | Date:
                                    {{ $record->date_given }}</p>
                                <p><strong>Date Reviewed:</strong> {{ $record->date_reviewed }}</p>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">No records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif
</div>
