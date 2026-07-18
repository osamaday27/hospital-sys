<style>
/* Phone Input Styles */
.phone-input, .whatsapp-input {
    position: relative;
}

.input-icon {
    position: absolute;
    left: 10px;
    top: 50%;
    transform: translateY(-50%);
    color: #6c757d;
    z-index: 3;
}

.whatsapp-icon {
    color: #25D366;
}

.phone-input .form-control, 
.whatsapp-input .form-control {
    padding-left: 40px;
}

.form-floating label {
    padding-left: 40px;
}

.form-floating>.form-control:focus~label, 
.form-floating>.form-control:not(:placeholder-shown)~label, 
.form-floating>.form-select~label {
    transform: scale(0.85) translateY(-0.5rem) translateX(0.15rem);
    padding-left: 40px;
}

@media (max-width: 768px) {
    .input-icon {
        left: 15px;
    }
    
    .form-floating label {
        padding-left: 35px;
    }
}

/* Force invalid-feedback to show when .d-block is present */
.invalid-feedback.d-block {
    display: block;
}

/* Patient Data Form Styles */
.patient-data-form {
    background-color: #fff;
    border-radius: 10px;
    padding: 20px;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.05);
}

.form-section {
    margin-bottom: 25px;
    padding-bottom: 15px;
    border-bottom: 1px solid #eee;
}

.form-section:last-child {
    border-bottom: none;
    margin-bottom: 0;
    padding-bottom: 0;
}

.section-title {
    font-size: 18px;
    color: #2c3e50;
    margin-bottom: 20px;
    display: flex;
    align-items: center;
}

.section-title i {
    color: #3498db;
}

.form-label {
    font-weight: 500;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
}

.form-label.required-field::after {
    content: " *";
    color: #e74c3c;
}

.form-label i {
    margin-right: 8px;
    color: #7f8c8d;
}

.select2-nationality .select2-selection {
    height: 42px;
    display: flex;
    align-items: center;
}

/* Gender Selection Styles */
.gender-selection {
    display: flex;
    gap: 15px;
    padding: 5px;
}

.gender-option {
    flex: 1;
}

.gender-input {
    display: none;
}

.gender-label {
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    padding: 12px 10px;
    border-radius: 8px;
    background-color: #f8f9fa;
    cursor: pointer;
    transition: all 0.3s ease;
    border: 2px solid #e0e0e0;
    text-align: center;
    height: 100%;
}

.gender-input:checked + .gender-label {
    border-color: #3498db;
    background-color: #e3f2fd;
    box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
}

.gender-icon {
    font-size: 24px;
    margin-bottom: 8px;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
}

.male-icon {
    color: #2980b9;
    background-color: #d4e6f1;
}

.female-icon {
    color: #e91e63;
    background-color: #f8d7e6;
}

.gender-text {
    font-weight: 500;
    color: #333;
}

.invalid-feedback {
    display: none;
    color: #e74c3c;
    font-size: 0.875em;
    margin-top: 5px;
}

.was-validated .gender-input:invalid ~ .invalid-feedback {
    display: block;
}

.was-validated .gender-input:invalid + .gender-label {
    border-color: #e74c3c;
}

@media (max-width: 768px) {
    .form-section {
        padding: 15px;
    }
    
    .gender-selection {
        flex-direction: column;
        gap: 10px;
    }
}

/* Patient Form Card Styles */
.patient-form-card {
    border-radius: 15px;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    border: none;
    overflow: hidden;
}

.profile-section {
    background-color: #f8f9fa;
    border-radius: 10px;
    height: 100%;
}

.profile-image-container {
    position: relative;
    margin: 0 auto;
    width: fit-content;
}

.user-profile-image {
    border: 3px solid #e9ecef;
    transition: all 0.3s ease;
}

.user-profile-image:hover {
    transform: scale(1.05);
    border-color: #0d6efd;
}

.profile-photo-edit {
    position: absolute;
    bottom: 10px;
    right: 10px;
}

.cursor-pointer {
    cursor: pointer;
}

.section-title {
    border-radius: 5px;
    font-size: 0.9rem;
    color: #495057;
}

.select2-insurance {
    width: 100% !important;
}

.insurance-field {
    background: white;
    padding: 15px;
    border-radius: 8px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
}

.full-name-section .input-group-text {
    min-width: 45px;
    justify-content: center;
}

/* Required Field Styles */
.required-field input:required,
.required-field select:required {
    border-left: 3px solid #dc3545 !important;
    background-color: rgba(220, 53, 69, 0.05) !important;
}

/* Valid Required Fields */
.required-field input:required:valid, 
.required-field select:required:valid {
    border-left: 3px solid #28a745 !important;
    background-color: rgba(40, 167, 69, 0.05) !important;
}

/* Focus Styles */
.required-field input:required:focus,
.required-field select:required:focus {
    box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25) !important;
}

.required-field input:required:valid:focus, 
.required-field select:required:valid:focus {
    box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.25) !important;
}

/* Required Label Styles */
.required-label::after {
    content: " *";
    color: #dc3545;
    font-size: 1em;
}

/* Hide Helper Text for Valid Input */
.required-field input:valid + .required-hint,
.required-field select:valid + .required-hint {
    display: none;
}

/* Validation States */
.is-invalid {
    border-left: 3px solid #dc3545 !important;
    background-color: rgba(220, 53, 69, 0.05) !important;
}

.is-valid {
    border-left: 3px solid #28a745 !important;
    background-color: rgba(40, 167, 69, 0.05) !important;
}

.is-invalid:focus {
    box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25) !important;
    border-color: #dc3545 !important;
}

.is-valid:focus {
    box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.25) !important;
    border-color: #28a745 !important;
}

@media (max-width: 768px) {
    .profile-section {
        border-right: none;
        border-bottom: 1px solid #dee2e6;
        margin-bottom: 20px;
        padding-bottom: 20px;
    }
}
   /* Custom Styles */
   .progress-card {
        background-color: #f8f9fa;
        border-radius: 10px;
    }
    
    .custom-accordion .accordion-button {
        border-radius: 8px !important;
        transition: all 0.3s ease;
    }
    
    .custom-accordion .accordion-button:not(.collapsed) {
        background-color: rgba(13, 110, 253, 0.1);
        color: #0d6efd;
        box-shadow: none;
    }
    
    .custom-accordion .accordion-item {
        border: none;
        border-radius: 10px;
        overflow: hidden;
        margin-bottom: 15px;
    }
    
    .animated-progress {
        height: 10px;
        border-radius: 5px;
        overflow: hidden;
    }
    
    .bg-gradient {
        background: linear-gradient(90deg, #3b5df9 0%, #9543f9 100%);
    }
    
    /* Form Validation Styles */
    .is-invalid {
        border-color: #dc3545 !important;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right calc(0.375em + 0.1875rem) center;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    }
    
    .is-valid {
        border-color: #198754 !important;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%23198754' d='M2.3 6.73L.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right calc(0.375em + 0.1875rem) center;
        background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    }
    
    .invalid-feedback {
        display: none;
        width: 100%;
        margin-top: 0.25rem;
        font-size: 0.875em;
        color: #dc3545;
    }
    
    .is-invalid ~ .invalid-feedback {
        display: block;
    }
    
    .required-field label:after {
        content: " *";
        color: #dc3545;
    }
    
    /* Responsive Adjustments */
    @media (max-width: 768px) {
        .card-body {
            padding: 1.25rem;
        }
        
        .accordion-body {
            padding: 1rem;
        }
    }
    </style>