<div >
    <div class="operative-card">
        <!-- Header with Icon -->
        <div class="operative-header">
            <div class="header-content">
                <i class="mdi mdi-clipboard-text-outline mdi-48px header-icon"></i>
                <div>
                    <h1 style="color: #fff">Operative / Procedure Report</h1>
                    <p class="header-subtitle">Complete all relevant sections below</p>
                </div>
            </div>
            <div class="header-badge">
                <i class="mdi mdi-calendar-clock"></i>
                {{ now()->format('M d, Y - h:i A') }}
            </div>
        </div>

        <!-- Success Message -->
        @if(session()->has('success'))
            <div class="success-message">
                <i class="mdi mdi-check-circle-outline mdi-24px"></i>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        <!-- Form -->
        <form wire:submit.prevent="save" class="operative-form">
            <!-- Diagnosis Section -->
            <div class="form-section">
                <div class="section-header">
                    <i class="mdi mdi-diagnosis"></i>
                    <h2>Diagnosis</h2>
                </div>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="preoperative_diagnosis">
                            <i class="mdi mdi-account-arrow-left-outline"></i>
                            Preoperative Diagnosis
                        </label>
                        <div class="input-with-icon">
                            <i class="mdi mdi-text-box-search-outline"></i>
                            <input type="text" id="preoperative_diagnosis" wire:model.defer="preoperative_diagnosis" placeholder="Enter preoperative diagnosis">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="postoperative_diagnosis">
                            <i class="mdi mdi-account-arrow-right-outline"></i>
                            Postoperative Diagnosis
                        </label>
                        <div class="input-with-icon">
                            <i class="mdi mdi-text-box-check-outline"></i>
                            <input type="text" id="postoperative_diagnosis" wire:model.defer="postoperative_diagnosis" placeholder="Enter postoperative diagnosis">
                        </div>
                    </div>
                </div>
            </div>
<!-- Medical Team Section -->
<div class="form-section">
    <div class="section-header">
        <i class="mdi mdi-account-group"></i>
        <h2>Medical Team</h2>
    </div>

    <div class="form-grid">

        {{-- 🩺 Surgeon --}}
        <div class="form-group">
            <label for="surgeon_id">
                <i class="mdi mdi-doctor"></i>
                Surgeon / Doctor
            </label>
            <div class="input-with-icon">
                <i class="mdi mdi-account-search"></i>
                <input type="text"
                       id="surgeonSearch"
                       class="form-control mb-2"
                       placeholder="Search by name or code"
                       wire:model.debounce.500ms="surgeonSearch">
            </div>

            <select id="surgeon_id"
                    class="form-select"
                    wire:model="surgeon_id">
                <option value="">Select Surgeon</option>
                @foreach ($surgeons as $s)
                    <option value="{{ $s->id }}">
                        {{ $s->full_name }} ({{ $s->employee_code }})
                    </option>
                @endforeach
            </select>
        </div>

        {{-- 👨‍⚕️ 1st Assistant --}}
        <div class="form-group">
            <label for="first_assistant_id">
                <i class="mdi mdi-account-supervisor-circle"></i>
                1st Assistant
            </label>
            <div class="input-with-icon">
                <i class="mdi mdi-account-search"></i>
                <input type="text"
                       id="assistantSearch"
                       class="form-control mb-2"
                       placeholder="Search by name or code"
                       wire:model.debounce.500ms="assistantSearch">
            </div>

            <select id="first_assistant_id"
                    class="form-select"
                    wire:model="first_assistant_id">
                <option value="">Select Assistant</option>
                @foreach ($assistants as $a)
                    <option value="{{ $a->id }}">
                        {{ $a->full_name }} ({{ $a->employee_code }})
                    </option>
                @endforeach
            </select>
        </div>

        {{-- 💉 Anesthesiologist --}}
        <div class="form-group">
            <label for="anesthesiologist_id">
                <i class="mdi mdi-needle"></i>
                Anesthesiologist
            </label>
            <div class="input-with-icon">
                <i class="mdi mdi-account-search"></i>
                <input type="text"
                       id="anesthSearch"
                       class="form-control mb-2"
                       placeholder="Search by name or code"
                       wire:model.debounce.500ms="anesthSearch">
            </div>

            <select id="anesthesiologist_id"
                    class="form-select"
                    wire:model="anesthesiologist_id">
                <option value="">Select Anesthesiologist</option>
                @foreach ($anesthesiologists as $an)
                    <option value="{{ $an->id }}">
                        {{ $an->full_name }} ({{ $an->employee_code }})
                    </option>
                @endforeach
            </select>
        </div>

    </div>
</div>

            <!-- Timing Section -->
            <div class="form-section">
                <div class="section-header">
                    <i class="mdi mdi-clock-outline"></i>
                    <h2>Timing</h2>
                </div>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="time_started">
                            <i class="mdi mdi-play-circle-outline"></i>
                            Time Started
                        </label>
                        <div class="input-with-icon">
                            <i class="mdi mdi-clock-start"></i>
                            <input type="time" id="time_started" wire:model.defer="time_started">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="time_ended">
                            <i class="mdi mdi-stop-circle-outline"></i>
                            Time Ended
                        </label>
                        <div class="input-with-icon">
                            <i class="mdi mdi-clock-end"></i>
                            <input type="time" id="time_ended" wire:model.defer="time_ended">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Anesthesia Section -->
            <div class="form-section">
                <div class="section-header">
                    <i class="mdi mdi-needle"></i>
                    <h2>Anesthesia</h2>
                </div>
                
                <div class="radio-group">
                    <div class="radio-card" @click="$wire.anesthesia_type = 'Topical'">
                        <input type="radio" id="anesthesia-topical" wire:model="anesthesia_type" value="Topical">
                        <label for="anesthesia-topical">
                            <i class="mdi mdi-spray"></i>
                            Topical
                        </label>
                    </div>
                    
                    <div class="radio-card" @click="$wire.anesthesia_type = 'Local'">
                        <input type="radio" id="anesthesia-local" wire:model="anesthesia_type" value="Local">
                        <label for="anesthesia-local">
                            <i class="mdi mdi-needle"></i>
                            Local
                        </label>
                    </div>
                    
                    <div class="radio-card" @click="$wire.anesthesia_type = 'General'">
                        <input type="radio" id="anesthesia-general" wire:model="anesthesia_type" value="General">
                        <label for="anesthesia-general">
                            <i class="mdi mdi-hospital-box"></i>
                            General
                        </label>
                    </div>
                    
                    <div class="radio-card with-input" @click="$wire.anesthesia_type = 'Other'">
                        <input type="radio" id="anesthesia-other" wire:model="anesthesia_type" value="Other">
                        <label for="anesthesia-other">
                            <i class="mdi mdi-dots-horizontal-circle"></i>
                            Other
                        </label>
                        <div class="input-with-icon">
                            <i class="mdi mdi-form-textbox"></i>
                            <input type="text" wire:model.defer="anesthesia_other" placeholder="Specify anesthesia type">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Procedure Details Section -->
            <div class="form-section">
                <div class="section-header">
                    <i class="mdi mdi-scissors-cutting"></i>
                    <h2>Procedure Details</h2>
                </div>
                
                <div class="form-group">
                    <label for="procedures_done">
                        <i class="mdi mdi-clipboard-list-outline"></i>
                        Procedure(s) Done
                    </label>
                    <div class="textarea-with-icon">
                        <i class="mdi mdi-text"></i>
                        <textarea id="procedures_done" rows="3" wire:model.defer="procedures_done" placeholder="Describe procedures performed"></textarea>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="operation_details">
                        <i class="mdi mdi-file-document-edit-outline"></i>
                        Operation/Procedure Details
                    </label>
                    <div class="textarea-with-icon">
                        <i class="mdi mdi-text-long"></i>
                        <textarea id="operation_details" rows="6" wire:model.defer="operation_details" placeholder="Provide detailed description of the operation"></textarea>
                    </div>
                </div>
            </div>

            <!-- Blood Section -->
            <div class="form-section">
                <div class="section-header">
                    <i class="mdi mdi-water"></i>
                    <h2>Blood</h2>
                </div>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="blood_loss_ml">
                            <i class="mdi mdi-water-remove"></i>
                            Blood Loss (ml)
                        </label>
                        <div class="input-with-icon">
                            <i class="mdi mdi-numeric"></i>
                            <input type="number" id="blood_loss_ml" wire:model.defer="blood_loss_ml" placeholder="Enter amount">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="blood_transfused_ml">
                            <i class="mdi mdi-water-plus"></i>
                            Blood Transfused (ml)
                        </label>
                        <div class="input-with-icon">
                            <i class="mdi mdi-numeric"></i>
                            <input type="number" id="blood_transfused_ml" wire:model.defer="blood_transfused_ml" placeholder="Enter amount">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Complications Section -->
            <div class="form-section">
                <div class="section-header">
                    <i class="mdi mdi-alert-circle-outline"></i>
                    <h2>Complications</h2>
                </div>
                
                <div class="form-group">
                    <div class="checkbox-card" @click="$wire.complication_occurred = !$wire.complication_occurred">
                        <input type="checkbox" id="complication-occurred" wire:model.defer="complication_occurred">
                        <label for="complication-occurred">
                            <i class="mdi mdi-alert-octagon-outline"></i>
                            Complication occurred during operation/procedure
                        </label>
                    </div>
                    
                    @if($complication_occurred)
                    <div class="form-group">
                        <label for="complication_details">
                            <i class="mdi mdi-comment-alert-outline"></i>
                            Complication Details
                        </label>
                        <div class="textarea-with-icon">
                            <i class="mdi mdi-text"></i>
                            <textarea id="complication_details" rows="3" wire:model.defer="complication_details" placeholder="Describe the complication"></textarea>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Specimens Section -->
            <div class="form-section">
                <div class="section-header">
                    <i class="mdi mdi-test-tube"></i>
                    <h2>Specimens</h2>
                </div>
                
                <div class="form-group">
                    <div class="checkbox-card" @click="$wire.specimens_sent = !$wire.specimens_sent">
                        <input type="checkbox" id="specimens-sent" wire:model.defer="specimens_sent">
                        <label for="specimens-sent">
                            <i class="mdi mdi-send"></i>
                            Surgical specimens sent for examination
                        </label>
                    </div>
                    
                    @if($specimens_sent)
                    <div class="form-grid">
                        <div class="form-group">
                            <label for="specimen_no">
                                <i class="mdi mdi-identifier"></i>
                                Specimen No
                            </label>
                            <div class="input-with-icon">
                                <i class="mdi mdi-numeric"></i>
                                <input type="text" id="specimen_no" wire:model.defer="specimen_no" placeholder="Enter specimen number">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="specimen_origin">
                                <i class="mdi mdi-human-handsup"></i>
                                Origin of Specimen
                            </label>
                            <div class="input-with-icon">
                                <i class="mdi mdi-map-marker-radius"></i>
                                <input type="text" id="specimen_origin" wire:model.defer="specimen_origin" placeholder="Enter origin">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="specimen_size_description">
                                <i class="mdi mdi-ruler"></i>
                                Size & Description
                            </label>
                            <div class="input-with-icon">
                                <i class="mdi mdi-text-box-outline"></i>
                                <input type="text" id="specimen_size_description" wire:model.defer="specimen_size_description" placeholder="Enter size and description">
                            </div>
                        </div>
                    </div>
                    
                    <div class="checkbox-group">
                        <div class="checkbox-card" @click="$wire.specimen_sent_to_lab = !$wire.specimen_sent_to_lab">
                            <input type="checkbox" id="specimen-lab" wire:model.defer="specimen_sent_to_lab">
                            <label for="specimen-lab">
                                <i class="mdi mdi-flask-outline"></i>
                                Sent to Lab
                            </label>
                        </div>
                        
                        <div class="checkbox-card" @click="$wire.specimen_pathology = !$wire.specimen_pathology">
                            <input type="checkbox" id="specimen-pathology" wire:model.defer="specimen_pathology">
                            <label for="specimen-pathology">
                                <i class="mdi mdi-microscope"></i>
                                Pathology
                            </label>
                        </div>
                        
                        <div class="checkbox-card" @click="$wire.specimen_culture = !$wire.specimen_culture">
                            <input type="checkbox" id="specimen-culture" wire:model.defer="specimen_culture">
                            <label for="specimen-culture">
                                <i class="mdi mdi-bacteria-outline"></i>
                                Culture & Sensitivity
                            </label>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Implants Section -->
            <div class="form-section">
                <div class="section-header">
                    <i class="mdi mdi-pacemaker"></i>
                    <h2>Implantable Devices</h2>
                </div>
                
                <div class="form-group">
                    <div class="checkbox-card" @click="$wire.implantable_devices = !$wire.implantable_devices">
                        <input type="checkbox" id="implantable-devices" wire:model.defer="implantable_devices">
                        <label for="implantable-devices">
                            <i class="mdi mdi-chip"></i>
                            Implantable devices used
                        </label>
                    </div>
                    
                    @if($implantable_devices)
                    <div class="form-group">
                        <label for="implant_details">
                            <i class="mdi mdi-information-outline"></i>
                            Details & Batch Number
                        </label>
                        <div class="textarea-with-icon">
                            <i class="mdi mdi-text"></i>
                            <textarea id="implant_details" rows="3" wire:model.defer="implant_details" placeholder="Enter device details and batch numbers"></textarea>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Signature Section -->
            <div class="form-section">
                <div class="section-header">
                    <i class="mdi mdi-signature-freehand"></i>
                    <h2>Signature</h2>
                </div>
                
                <div class="form-grid">
                    <div class="form-group">
                        <label for="surgeon_signature">
                            <i class="mdi mdi-account-details"></i>
                            Surgeon/Doctor Signature
                        </label>
                        <div class="input-with-icon">
                            <i class="mdi mdi-pen"></i>
                            <input type="text" id="surgeon_signature" wire:model.defer="surgeon_signature" placeholder="Enter signature">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="report_date">
                            <i class="mdi mdi-calendar"></i>
                            Date
                        </label>
                        <div class="input-with-icon">
                            <i class="mdi mdi-calendar-blank"></i>
                            <input type="date" id="report_date" wire:model.defer="report_date">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="report_time">
                            <i class="mdi mdi-clock-outline"></i>
                            Time
                        </label>
                        <div class="input-with-icon">
                            <i class="mdi mdi-clock-time-eight-outline"></i>
                            <input type="time" id="report_time" wire:model.defer="report_time">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Actions -->
            <div class="form-actions">
                <button type="button" class="btn-secondary">
                    <i class="mdi mdi-arrow-left"></i>
                    Cancel
                </button>
                <button type="submit" class="btn-primary">
                    <i class="mdi mdi-content-save-outline"></i>
                    Save Report
                </button>
            </div>
        </form>
    </div>
</div>

<style>
/* Base Styles */
:root {
    --primary-color: #1e88e5;
    --primary-hover: #1565c0;
    --secondary-color: #757575;
    --success-color: #4caf50;
    --danger-color: #f44336;
    --light-gray: #f5f5f5;
    --border-color: #e0e0e0;
    --text-color: #333;
    --text-light: #757575;
    --shadow-sm: 0 1px 3px rgba(0,0,0,0.12);
    --shadow-md: 0 4px 6px rgba(0,0,0,0.1);
    --radius-sm: 4px;
    --radius-md: 8px;
    --radius-lg: 12px;
    --transition: all 0.3s ease;
}

* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {
    font-family: 'Roboto', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    color: var(--text-color);
    line-height: 1.6;
    background-color: #f9f9f9;
}

/* Container Styles */
.operative-container {
    max-width: 1200px;
    margin: 20px auto;
    padding: 0 20px;
}

.operative-card {
    background: white;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-md);
    overflow: hidden;
}

/* Header Styles */
.operative-header {
    background: linear-gradient(135deg, var(--primary-color), #0d47a1);
    color: white;
    padding: 25px 30px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 20px;
}

.header-content {
    display: flex;
    align-items: center;
    gap: 20px;
}

.header-icon {
    color: rgba(255,255,255,0.9);
}

.operative-header h1 {
    margin: 0;
    font-size: 28px;
    font-weight: 600;
    line-height: 1.2;
}

.header-subtitle {
    margin: 5px 0 0;
    opacity: 0.9;
    font-size: 15px;
    font-weight: 400;
}

.header-badge {
    background: rgba(255,255,255,0.15);
    padding: 8px 15px;
    border-radius: 20px;
    font-size: 14px;
    display: flex;
    align-items: center;
    gap: 8px;
}

/* Success Message */
.success-message {
    background: #e8f5e9;
    color: var(--success-color);
    padding: 15px 20px;
    margin: 0 30px;
    border-radius: var(--radius-sm);
    border-left: 4px solid var(--success-color);
    font-size: 15px;
    display: flex;
    align-items: center;
    gap: 10px;
    margin-top: 20px;
}

/* Form Styles */
.operative-form {
    padding: 30px;
}

.form-section {
    margin-bottom: 40px;
}

.section-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 25px;
    padding-bottom: 12px;
    border-bottom: 1px solid var(--border-color);
}

.section-header h2 {
    font-size: 20px;
    font-weight: 600;
    color: var(--primary-color);
    margin: 0;
}

.section-header i {
    font-size: 24px;
    color: var(--primary-color);
}

/* Form Grid Layout */
.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
}

/* Form Group Styles */
.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 8px;
    font-size: 14px;
    font-weight: 500;
    color: var(--text-color);
}

.form-group label i {
    font-size: 18px;
    color: var(--primary-color);
}

/* Input Styles */
.input-with-icon {
    position: relative;
    display: flex;
    align-items: center;
}

.input-with-icon i {
    position: absolute;
    left: 12px;
    color: var(--secondary-color);
    font-size: 20px;
}

.input-with-icon input,
.input-with-icon select {
    width: 100%;
    padding: 12px 15px 12px 40px;
    border: 1px solid var(--border-color);
    border-radius: var(--radius-sm);
    font-size: 14px;
    transition: var(--transition);
}

.input-with-icon input:focus,
.input-with-icon select:focus {
    border-color: var(--primary-color);
    outline: none;
    box-shadow: 0 0 0 3px rgba(30, 136, 229, 0.2);
}

/* Textarea Styles */
.textarea-with-icon {
    position: relative;
}

.textarea-with-icon i {
    position: absolute;
    top: 15px;
    left: 12px;
    color: var(--secondary-color);
    font-size: 20px;
}

.textarea-with-icon textarea {
    width: 100%;
    padding: 15px 15px 15px 40px;
    border: 1px solid var(--border-color);
    border-radius: var(--radius-sm);
    font-size: 14px;
    min-height: 120px;
    resize: vertical;
    transition: var(--transition);
}

.textarea-with-icon textarea:focus {
    border-color: var(--primary-color);
    outline: none;
    box-shadow: 0 0 0 3px rgba(30, 136, 229, 0.2);
}

/* Radio and Checkbox Card Styles */
.radio-group,
.checkbox-group {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 15px;
}

.radio-card,
.checkbox-card {
    background: var(--light-gray);
    border: 1px solid var(--border-color);
    border-radius: var(--radius-sm);
    padding: 15px;
    cursor: pointer;
    transition: var(--transition);
    display: flex;
    align-items: center;
    gap: 10px;
}

.radio-card:hover,
.checkbox-card:hover {
    border-color: var(--primary-color);
    background: rgba(30, 136, 229, 0.05);
}

.radio-card input[type="radio"],
.checkbox-card input[type="checkbox"] {
    margin-right: 8px;
    cursor: pointer;
}

.radio-card label,
.checkbox-card label {
    margin: 0;
    font-weight: 500;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 8px;
}

.radio-card label i,
.checkbox-card label i {
    font-size: 20px;
}

.radio-card.with-input {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 10px;
}

.radio-card.with-input .input-with-icon {
    width: 100%;
    margin-top: 10px;
}

/* Button Styles */
.form-actions {
    display: flex;
    justify-content: flex-end;
    gap: 15px;
    margin-top: 40px;
    padding-top: 20px;
    border-top: 1px solid var(--border-color);
}

.btn-primary,
.btn-secondary {
    padding: 12px 24px;
    border-radius: var(--radius-sm);
    font-size: 15px;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 8px;
    cursor: pointer;
    transition: var(--transition);
    border: none;
}

.btn-primary {
    background: var(--primary-color);
    color: white;
}

.btn-primary:hover {
    background: var(--primary-hover);
    box-shadow: var(--shadow-sm);
}

.btn-secondary {
    background: white;
    color: var(--secondary-color);
    border: 1px solid var(--border-color);
}

.btn-secondary:hover {
    background: var(--light-gray);
    box-shadow: var(--shadow-sm);
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .operative-header {
        flex-direction: column;
        align-items: flex-start;
        padding: 20px;
    }
    
    .header-content {
        flex-direction: column;
        align-items: flex-start;
        gap: 10px;
    }
    
    .operative-header h1 {
        font-size: 24px;
    }
    
    .operative-form {
        padding: 20px;
    }
    
    .form-grid {
        grid-template-columns: 1fr;
    }
    
    .radio-group,
    .checkbox-group {
        grid-template-columns: 1fr;
    }
    
    .form-actions {
        justify-content: center;
        flex-wrap: wrap;
    }
}

@media (max-width: 480px) {
    .operative-container {
        padding: 0 10px;
    }
    
    .operative-card {
        border-radius: 0;
    }
    
    .form-actions {
        flex-direction: column;
    }
    
    .btn-primary,
    .btn-secondary {
        width: 100%;
        justify-content: center;
    }
}

</style>