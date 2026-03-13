@extends('admin.layouts.layouts')

@section('title', isset($lead) ? 'Edit Inquiry' : 'Add New Inquiry')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <h2 class="mb-0" style="color: var(--accent-solid);">
                <i class="{{ isset($lead) ? 'fas fa-edit' : 'fas fa-user-plus' }}"></i>
                {{ isset($lead) ? 'Edit Inquiry' : 'New Inquiry' }}
            </h2>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card">
                <div class="card-body p-4">
            <style> 
                label.form-label {
                    font-weight: 600;
                    color: #5a6268;
                    display: block;
                    margin-bottom: 4px;
                    font-size: 13px;
                }

                .form-control, .form-select {
                    padding: 6px 10px;
                    font-size: 13px;
                    border-radius: 6px;
                }

                .mb-3 {
                    margin-bottom: 1rem !important;
                }

                /* Autocomplete Styles */
                .autocomplete-container {
                    position: relative;
                }

                .autocomplete-dropdown {
                    position: absolute;
                    top: 100%;
                    left: 0;
                    right: 0;
                    background: white;
                    border: 1px solid #ced4da;
                    border-top: none;
                    max-height: 250px;
                    overflow-y: auto;
                    z-index: 1000;
                    display: none;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
                    border-radius: 0 0 8px 8px;
                    transition: all 0.2s ease;
                }

                .autocomplete-dropdown.show {
                    display: block;
                }

                .autocomplete-item {
                    padding: 10px 15px;
                    cursor: pointer;
                    border-bottom: 1px solid #f1f3f5;
                    font-size: 0.9rem;
                    color: #495057;
                    transition: background-color 0.2s;
                }

                .autocomplete-item:last-child {
                    border-bottom: none;
                }

                .autocomplete-item:hover,
                .autocomplete-item.selected {
                    background-color: #f8f9fa;
                    color: #086838;
                }

                .autocomplete-item.add-new {
                    color: #086838;
                    font-weight: 600;
                    background-color: #fdfdfd;
                    border-top: 1px solid #eee;
                }

                .autocomplete-item.add-new:hover {
                    background-color: #eef7f2;
                }

                /* Multi-Select Styles */
                .multi-select-container {
                    width: 100%;
                }

                .selected-items {
                    min-height: 40px;
                    border: 1px solid #ced4da;
                    border-radius: 0.375rem;
                    padding: 8px;
                    margin-bottom: 8px;
                    background-color: #f8f9fa;
                    display: flex;
                    flex-wrap: wrap;
                    gap: 6px;
                    align-items: center;
                }

                .selected-item {
                    background-color: #086838;
                    color: white;
                    padding: 4px 10px;
                    border-radius: 15px;
                    font-size: 13px;
                    display: inline-flex;
                    align-items: center;
                    gap: 6px;
                    animation: fadeIn 0.2s ease-in;
                }

                .selected-item .remove-item {
                    cursor: pointer;
                    background: none;
                    border: none;
                    color: white;
                    font-size: 14px;
                    padding: 0;
                    width: 16px;
                    height: 16px;
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    transition: background-color 0.2s;
                }

                .selected-item .remove-item:hover {
                    background-color: rgba(255, 255, 255, 0.2);
                }

                .selected-items:empty::before {
                    content: "No items selected";
                    color: #6c757d;
                    font-style: italic;
                    font-size: 13px;
                }

                @keyframes fadeIn {
                    from { opacity: 0; transform: scale(0.8); }
                    to { opacity: 1; transform: scale(1); }
                }
            </style>
            <form action="{{ route('store.inquiry') }}" method="POST" id="inquiryForm">
                @csrf

                <!-- Hidden fields -->
                <input type="hidden" name="existing_patient_id" id="existingPatientId" value="{{ isset($lead) ? $lead->patient_id ?? '' : '' }}">
                <input type="hidden" name="form_source" value="diet_chart">

                @if (isset($lead) && $lead->id)
                    <input type="hidden" name="lead_id" value="{{ $lead->id }}">
                @endif

                @if (!isset($lead) && !request()->has('default``````````````````````````````````````````````````````_status'))
                    <input type="hidden" name="user_status" value="Diet Chart">
                @endif

                <div class="row">
                    <!-- Left Column -->
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Select branch *</label>
                            @if(isset($lead) && $lead->id)
                                <input type="text" class="form-control" value="{{ $lead->branch ?? '' }}" readonly>
                                <input type="hidden" name="branch" value="{{ $lead->branch_id ?? '' }}">
                            @else
                                <select class="form-control" id="branch" name="branch" required>
                                    <option value="">Select branch</option>
                                    @foreach ($branches as $b)
                                        <option value="{{ $b->branch_id }}" {{ (old('branch') == $b->branch_id) ? 'selected' : '' }}>{{ $b->branch_name }}</option>
                                    @endforeach
                                </select>
                            @endif
                        </div>
 
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">First Name *</label>
                                <input type="text" class="form-control" name="patient_f_name" id="patientFName" value="{{ old('patient_f_name', $lead->patient_f_name ?? '') }}" required>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Middle Name</label>
                                <input type="text" class="form-control" name="patient_m_name" id="patientMName" value="{{ old('patient_m_name', $lead->patient_m_name ?? '') }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Last Name *</label>
                                <input type="text" class="form-control" name="patient_l_name" id="patientLName" value="{{ old('patient_l_name', $lead->patient_l_name ?? '') }}" required>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Phone Number</label>
                                <input type="text" class="form-control" name="phone_no" id="phoneInput" value="{{ old('phone_no', $lead->phone_no ?? '') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Age</label>
                                <input type="number" class="form-control" name="age" id="ageInput" value="{{ old('age', $lead->age ?? '') }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Gender</label>
                                <select class="form-select" name="gender">
                                    <option value="">Select Gender</option>
                                    <option value="Male" {{ (old('gender', $lead->gender ?? '') == 'Male') ? 'selected' : '' }}>Male</option>
                                    <option value="Female" {{ (old('gender', $lead->gender ?? '') == 'Female') ? 'selected' : '' }}>Female</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Email ID</label>
                                <input type="email" class="form-control" name="email" id="emailInput" value="{{ old('email', $lead->email ?? '') }}">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <textarea class="form-control" name="address" id="addressInput" rows="2">{{ old('address', $lead->address ?? '') }}</textarea>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Reference By</label>
                                <input type="text" class="form-control" name="reference_by" value="{{ old('reference_by', $lead->refrance ?? '') }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Reference to</label>
                                <input type="text" class="form-control" name="reference_to" value="{{ old('reference_to', $lead->reference_to ?? '') }}">
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="col-md-6">
                        <div class="row mb-3">
                            <div class="col-md-4">
                                <label class="form-label">Height (cm)</label>
                                <input type="number" step="0.1" class="form-control" name="height" id="heightInput" value="{{ old('height', $lead->height ?? '') }}" onchange="calculateMetrics()">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">Weight (kg)</label>
                                <input type="number" step="0.1" class="form-control" name="weight" id="weightInput" value="{{ old('weight', $lead->weight ?? '') }}" onchange="calculateMetrics()">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">BMI</label>
                                <input type="text" class="form-control" id="bmiInput" readonly style="background-color: #f8f9fa;">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Visit Date *</label>
                                <input type="date" class="form-control" name="inquiry_date" value="{{ old('inquiry_date', date('Y-m-d')) }}" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Time *</label>
                                <input type="time" class="form-control" name="inquiry_time" value="{{ old('inquiry_time', date('H:i')) }}" required>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Diagnosis</label>
                            <div class="multi-select-container">
                                <div class="selected-items" id="diagnosis-selected">
                                    <!-- Selected diagnoses will appear here -->
                                </div>
                                <div class="autocomplete-container">
                                    <input type="text" class="form-control" id="diagnosis" placeholder="Type to add diagnosis..." autocomplete="off">
                                    <div class="autocomplete-dropdown" id="diagnosis-dropdown"></div>
                                </div>
                                <input type="hidden" name="diagnosis" id="diagnosis-hidden" value="{{ old('diagnosis', $lead->diagnosis ?? '') }}">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label class="form-label">Attended By</label>
                                <select class="form-select" name="inquery_given_by">
                                    <option value="">Select Doctor</option>
                                    @foreach($doctors as $doctor)
                                        <option value="{{ $doctor->name }}" {{ (old('inquery_given_by', $lead->inquery_given_by ?? '') == $doctor->name) ? 'selected' : '' }}>{{ $doctor->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Client Type</label>
                                <select class="form-select" name="client_old_new">
                                    <option value="New" {{ (old('client_old_new', $lead->client_old_new ?? '') == 'New') ? 'selected' : '' }}>New</option>
                                    <option value="Old" {{ (old('client_old_new', $lead->client_old_new ?? '') == 'Old') ? 'selected' : '' }}>Old</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Process Stage <span class="text-danger">*</span></label>
                            <div class="d-flex gap-3 mt-1">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="user_status[]" value="Pending" id="status_pending" {{ in_array('Pending', old('user_status', $selectedStatuses ?? [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="status_pending">Pending</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="user_status[]" value="Diet Chart" id="status_diet" {{ in_array('Diet Chart', old('user_status', $selectedStatuses ?? [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="status_diet">Diet Chart</label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="user_status[]" value="Joined" id="status_joined" {{ in_array('Joined', old('user_status', $selectedStatuses ?? [])) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="status_joined">Joined</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <hr class="my-4">

                <div class="mb-3">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="inquiry_foc" value="Yes" id="focCheck" {{ (old('inquiry_foc', $lead->inquiry_foc ?? '') == 'Yes') ? 'checked' : '' }}>
                        <label class="form-check-label fw-bold" for="focCheck">FOC (Free of Charge Inquiry)</label>
                    </div>
                </div>

                <!-- Balanced Payment Row - Hidden when FOC is checked -->
                <div id="paymentRow" class="row align-items-end mb-3" style="{{ (old('inquiry_foc', $lead->inquiry_foc ?? '') == 'Yes') ? 'display: none;' : '' }}">
                    <div class="col-md-3">
                        <label class="form-label">Registration Charges (₹)</label>
                        <input type="number" class="form-control" name="total_payment" id="total_payment" value="{{ old('total_payment', $lead->payment ?? '200') }}" readonly>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Paid Amount (₹)</label>
                        <input type="number" step="0.01" class="form-control" name="given_payment" id="given_payment" value="{{ old('given_payment', $optMeta['given_payment'] ?? '') }}" placeholder="0.00">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Payment Method</label>
                        <select class="form-select" name="payment_method" id="payment_method">
                            <option value="Cash" {{ (old('payment_method', $optMeta['payment_method'] ?? 'Cash') == 'Cash') ? 'selected' : '' }}>Cash</option>
                            <option value="Online" {{ (old('payment_method', $optMeta['payment_method'] ?? '') == 'Online') ? 'selected' : '' }}>Online</option>
                            <option value="Cheque" {{ (old('payment_method', $optMeta['payment_method'] ?? '') == 'Cheque') ? 'selected' : '' }}>Cheque</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Due Balance (₹)</label>
                        <input type="number" class="form-control" id="due_payment" value="200" readonly style="background-color: #f8f9fa;">
                    </div>
                </div>

                <!-- Section Equation (Clinical & Medical History) -->
            

           

                <div class="d-flex flex-column flex-sm-row justify-content-end mt-4 gap-2">
                            <button type="submit" class="btn btn-primary btn-lg btn-mobile-full">
                                <i class="fas fa-save"></i> {{ isset($lead) ? 'Update' : 'Submit' }}
                            </button>
                            <a href="{{ route('diet.chart') }}" class="btn btn-secondary btn-lg btn-mobile-full">
                                <i class="fas fa-times"></i> Cancel
                            </a>
                        </div>
                    </div>
                </div>
            </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Metrics Calculation Function (BMI, IBW, etc.)
    function calculateMetrics() {
        let height = parseFloat(document.getElementById('heightInput')?.value);
        let weight = parseFloat(document.getElementById('weightInput')?.value);
        let bmiInput = document.getElementById('bmiInput');
        let ibwInput = document.getElementById('ibwInput');
        let overWeightInput = document.getElementById('overWeightInput');
        let underWeightInput = document.getElementById('underWeightInput');
        let targetWeightInput = document.getElementById('targetWeightInput');

        // BMI Calculation
        if (height && weight && height > 0 && bmiInput) {
            let heightMeter = height / 100;
            let bmi = (weight / (heightMeter * heightMeter)).toFixed(2);
            bmiInput.value = bmi;
        } else if (bmiInput) {
            bmiInput.value = '';
        }

        // IBW Calculation: Height (cm) - 100 (as per user request)
        if (height && height > 100) {
            let ibw = height - 100;
            if (ibwInput) ibwInput.value = ibw.toFixed(2);
            if (targetWeightInput) targetWeightInput.value = ibw.toFixed(2);

            if (weight) {
                if (weight > ibw) {
                    if (overWeightInput) overWeightInput.value = (weight - ibw).toFixed(2);
                    if (underWeightInput) underWeightInput.value = '0.00';
                } else if (weight < ibw) {
                    if (underWeightInput) underWeightInput.value = (ibw - weight).toFixed(2);
                    if (overWeightInput) overWeightInput.value = '0.00';
                } else {
                    if (overWeightInput) overWeightInput.value = '0.00';
                    if (underWeightInput) underWeightInput.value = '0.00';
                }
            }
        } else {
            if (ibwInput) ibwInput.value = '';
            if (overWeightInput) overWeightInput.value = '';
            if (underWeightInput) underWeightInput.value = '';
            if (targetWeightInput) targetWeightInput.value = '';
        }
    }

    // Initialize when DOM is loaded
    document.addEventListener('DOMContentLoaded', function() {
        // Metrics Calculation
        const heightInput = document.getElementById('heightInput');
        const weightInput = document.getElementById('weightInput');
        
        if (heightInput) heightInput.addEventListener('input', calculateMetrics);
        if (weightInput) weightInput.addEventListener('input', calculateMetrics);

        // FOC Checkbox and Payment Calculation Handler
        const focCheck = document.getElementById('focCheck');
        const givenPaymentInput = document.getElementById('given_payment');
        const totalPaymentInput = document.getElementById('total_payment');
        const duePaymentInput = document.getElementById('due_payment');

        function calculateDue() {
            const total = parseFloat(totalPaymentInput.value) || 0;
            const given = parseFloat(givenPaymentInput.value) || 0;
            duePaymentInput.value = (total - given).toFixed(2);
        }

        if (givenPaymentInput) {
            givenPaymentInput.addEventListener('input', calculateDue);
        }

        if (focCheck) {
            const paymentRow = document.getElementById('paymentRow');
            focCheck.addEventListener('change', function() {
                if (this.checked) {
                    if (paymentRow) paymentRow.style.display = 'none';
                    totalPaymentInput.value = '0';
                    givenPaymentInput.value = '0';
                } else {
                    if (paymentRow) paymentRow.style.display = 'flex';
                    totalPaymentInput.value = '200';
                    if (givenPaymentInput.value === '0') {
                        givenPaymentInput.value = '';
                    }
                }
                calculateDue();
            });

            // Initialize state
            if (focCheck.checked) {
                if (paymentRow) paymentRow.style.display = 'none';
                totalPaymentInput.value = '0';
                givenPaymentInput.value = '0';
            }
        }

        // Calculate Metrics on page load
        calculateMetrics();
        calculateDue();

        // Checkboxes with Single-Select Behavior
        const statusCheckboxes = document.querySelectorAll('input[name="user_status[]"]');
        
        function updateSingleSelectBehavior(clickedCheckbox) {
            // When a checkbox is checked, uncheck all others
            if (clickedCheckbox.checked) {
                statusCheckboxes.forEach(checkbox => {
                    if (checkbox !== clickedCheckbox) {
                        checkbox.checked = false;
                    }
                });
            }
        }

        statusCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                updateSingleSelectBehavior(this);
            });
        });

        // Form validation and confirmation
        const inquiryForm = document.getElementById('inquiryForm');
        if (inquiryForm) {
            inquiryForm.addEventListener('submit', function(e) {
                e.preventDefault(); // Always prevent default submission first

                const patientFName = document.getElementById('patientFName');
                const patientLName = document.getElementById('patientLName');
                const statusCheckboxes = document.querySelectorAll('input[name="user_status[]"]:checked');
                const hasStatus = statusCheckboxes.length > 0;

                // Check if patient first name is filled
                if (patientFName && !patientFName.value.trim()) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Required Field',
                        text: 'Please enter patient first name'
                    });
                    patientFName.focus();
                    return;
                }

                // Check if patient last name is filled
                if (patientLName && !patientLName.value.trim()) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Required Field',
                        text: 'Please enter patient last name'
                    });
                    patientLName.focus();
                    return;
                }

                // Check if at least one status is selected
                if (!hasStatus) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Status Required',
                        text: 'Please select at least one status for the patient'
                    });
                    return;
                }

                // If validation passes, show SweetAlert confirmation
                Swal.fire({
                    title: 'Are you sure?',
                    text: "Do you want to save this inquiry?",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, save it!',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if (result.isConfirmed) {
                        inquiryForm.submit();
                    }
                });
            });
        }

        // Additional validations
        const phoneInput = document.getElementById('phoneInput');
        if (phoneInput) {
            phoneInput.addEventListener('blur', function() {
                const phone = this.value.trim();
                if (phone && !/^[\d\s\-\+\(\)]{10,15}$/.test(phone.replace(/\s/g, ''))) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Invalid Phone',
                        text: 'Please enter a valid phone number (10-15 digits).'
                    });
                    this.focus();
                }
            });
        }

        const emailInput = document.getElementById('emailInput');
        if (emailInput) {
            emailInput.addEventListener('blur', function() {
                const email = this.value.trim();
                if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Invalid Email',
                        text: 'Please enter a valid email address.'
                    });
                    this.focus();
                }
            });
        }

        const ageInput = document.getElementById('ageInput');
        if (ageInput) {
            ageInput.addEventListener('blur', function() {
                const age = parseInt(this.value);
                if (this.value && (age < 0 || age > 150)) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Invalid Age',
                        text: 'Please enter a valid age (0-150).'
                    });
                    this.focus();
                }
            });
        }

        // Auto-format date and time for new entries
        @if(!isset($lead) || !$lead->id)
            const inquiryDateInput = document.querySelector('input[name="inquiry_date"]');
            if (inquiryDateInput && !inquiryDateInput.value) {
                const today = new Date().toISOString().split('T')[0];
                inquiryDateInput.value = today;
            }
            
            const inquiryTimeInput = document.querySelector('input[name="inquiry_time"]');
            if (inquiryTimeInput && !inquiryTimeInput.value) {
                const now = new Date();
                const timeString = now.getHours().toString().padStart(2, '0') + ':' + 
                                  now.getMinutes().toString().padStart(2, '0');
                inquiryTimeInput.value = timeString;
            }
        @endif
    });

    // Make calculateMetrics globally available
    window.calculateMetrics = calculateMetrics;

    // Autocomplete logic
    let suggestionsData = {
        complaints: [],
        diagnoses: []
    };
    
    function loadSuggestions() {
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        fetch('/get-suggestions', {
            method: 'GET',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.diagnoses && data.diagnoses.length > 0) {
                suggestionsData.diagnoses = data.diagnoses;
                initAutocomplete();
            }
        })
        .catch(error => console.error('Error loading suggestions:', error));
    }
    
    function initAutocomplete() {
        setupMultiSelect('diagnosis', suggestionsData.diagnoses);
    }
    
    function setupMultiSelect(fieldId, suggestions) {
        const input = document.getElementById(fieldId);
        const dropdown = document.getElementById(fieldId + '-dropdown');
        const selectedContainer = document.getElementById(fieldId + '-selected');
        const hiddenInput = document.getElementById(fieldId + '-hidden');
        
        let selectedItems = [];
        if (hiddenInput && hiddenInput.value) {
            selectedItems = hiddenInput.value.split(',').map(i => i.trim()).filter(i => i);
        }
        
        let selectedIndex = -1;
        
        if (!input || !dropdown || !selectedContainer || !hiddenInput) return;
        
        updateSelectedDisplay();
        
        input.addEventListener('focus', function() {
            showMultiSelectSuggestions(input, dropdown, suggestions, selectedItems);
        });
        
        input.addEventListener('input', function() {
            const value = this.value.toLowerCase();
            showMultiSelectSuggestions(input, dropdown, suggestions, selectedItems, value);
        });
        
        input.addEventListener('keydown', function(e) {
            const items = dropdown.querySelectorAll('.autocomplete-item:not(.no-results)');
            
            switch(e.key) {
                case 'ArrowDown':
                    e.preventDefault();
                    selectedIndex = Math.min(selectedIndex + 1, items.length - 1);
                    updateSelection(items, selectedIndex);
                    break;
                case 'ArrowUp':
                    e.preventDefault();
                    selectedIndex = Math.max(selectedIndex - 1, -1);
                    updateSelection(items, selectedIndex);
                    break;
                case 'Enter':
                    e.preventDefault();
                    if (selectedIndex >= 0 && items[selectedIndex]) {
                        addItem(items[selectedIndex].textContent);
                        input.value = '';
                        dropdown.classList.remove('show');
                        selectedIndex = -1;
                    } else if (input.value.trim()) {
                        addItem(input.value.trim());
                        input.value = '';
                        dropdown.classList.remove('show');
                        selectedIndex = -1;
                    }
                    break;
                case 'Escape':
                    dropdown.classList.remove('show');
                    selectedIndex = -1;
                    break;
                case 'Backspace':
                    if (input.value === '' && selectedItems.length > 0) {
                        removeItem(selectedItems[selectedItems.length - 1]);
                    }
                    break;
            }
        });
        
        document.addEventListener('click', function(e) {
            if (!input.contains(e.target) && !dropdown.contains(e.target) && !selectedContainer.contains(e.target)) {
                dropdown.classList.remove('show');
                selectedIndex = -1;
            }
        });
        
        function addItem(itemText) {
            if (!selectedItems.includes(itemText)) {
                selectedItems.push(itemText);
                updateSelectedDisplay();
                updateHiddenInput();
                
                if (!suggestions.includes(itemText)) {
                    suggestions.push(itemText);
                    suggestions.sort();
                }
            }
        }
        
        function removeItem(itemText) {
            const index = selectedItems.indexOf(itemText);
            if (index > -1) {
                selectedItems.splice(index, 1);
                updateSelectedDisplay();
                updateHiddenInput();
            }
        }
        
        function updateSelectedDisplay() {
            selectedContainer.innerHTML = '';
            if (selectedItems.length === 0) return;
            
            selectedItems.forEach(item => {
                const itemElement = document.createElement('div');
                itemElement.className = 'selected-item';
                itemElement.innerHTML = `${item} <button type="button" class="remove-item"><i class="fas fa-times"></i></button>`;
                
                const removeBtn = itemElement.querySelector('.remove-item');
                removeBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    removeItem(item);
                });
                
                selectedContainer.appendChild(itemElement);
            });
        }
        
        function updateHiddenInput() {
            hiddenInput.value = selectedItems.join(', ');
        }
        
        input.multiSelect = { addItem, removeItem };
    }
    
    function showMultiSelectSuggestions(input, dropdown, suggestions, selectedItems, filter = '') {
        dropdown.innerHTML = '';
        let selectedIndex = -1;
        
        const filteredSuggestions = suggestions.filter(s => 
            s.toLowerCase().includes(filter) && !selectedItems.includes(s)
        );
        
        if (filteredSuggestions.length === 0 && filter) {
            const addNew = document.createElement('div');
            addNew.className = 'autocomplete-item add-new';
            addNew.innerHTML = `<i class="fas fa-plus"></i> Add "${filter}" as new ${input.id}`;
            addNew.addEventListener('click', function() {
                saveNewMedicalCondition(filter, 'diagnosis', input);
            });
            dropdown.appendChild(addNew);
        } else {
            filteredSuggestions.forEach(suggestion => {
                const item = document.createElement('div');
                item.className = 'autocomplete-item';
                item.textContent = suggestion;
                item.addEventListener('click', function() {
                    if (input.multiSelect) input.multiSelect.addItem(suggestion);
                    input.value = '';
                    dropdown.classList.remove('show');
                });
                dropdown.appendChild(item);
            });
        }
        
        if (filteredSuggestions.length > 0 || filter) {
            dropdown.classList.add('show');
        } else {
            dropdown.classList.remove('show');
        }
    }
    
    function saveNewMedicalCondition(name, type, inputElement) {
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        fetch('/save-medical-condition', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ name: name, type: type })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (inputElement.multiSelect) {
                    inputElement.multiSelect.addItem(name);
                }
                const dropdownId = inputElement.id + '-dropdown';
                document.getElementById(dropdownId)?.classList.remove('show');
                inputElement.value = '';
                
                Swal.fire({
                    title: 'Saved!',
                    text: 'New diagnosis added successfully.',
                    icon: 'success',
                    timer: 2000,
                    showConfirmButton: false
                });
            }
        })
        .catch(error => console.error('Error saving:', error));
    }
    
    function updateSelection(items, selectedIndex) {
        items.forEach((item, index) => {
            if (index === selectedIndex) item.classList.add('selected');
            else item.classList.remove('selected');
        });
    }

    // Load suggestions on init
    loadSuggestions();
</script>

<style>
    /* Custom Status Badges */
    .status-badge {
        padding: 5px 12px;
        border-radius: 4px;
        font-weight: 600;
        font-size: 0.75rem;
        display: inline-block;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border: 1px solid transparent;
        margin-right: 5px;
    }

    .badge-pending {
        background-color: rgba(217, 119, 6, 0.1);
        color: #f59e0b;
        border-color: rgba(217, 119, 6, 0.2);
    }

    .badge-diet {
        background-color: rgba(8, 145, 178, 0.1);
        color: #22d3ee;
        border-color: rgba(8, 145, 178, 0.2);
    }

    .badge-joined {
        background-color: rgba(22, 163, 74, 0.1);
        color: #4ade80;
        border-color: rgba(22, 163, 74, 0.2);
    }

    .badge-active {
        background-color: rgba(124, 58, 237, 0.1);
        color: #a78bfa;
        border-color: rgba(124, 58, 237, 0.2);
    }

    /* Dark Mode Specific Badge Overrides */
    .dark .badge-pending { background-color: rgba(245, 158, 11, 0.15) !important; color: #fbbf24 !important; }
    .dark .badge-diet { background-color: rgba(34, 211, 238, 0.15) !important; color: #67e8f9 !important; }
    .dark .badge-joined { background-color: rgba(74, 222, 128, 0.15) !important; color: #86efac !important; }
    .dark .badge-active { background-color: rgba(167, 139, 250, 0.15) !important; color: #c4b5fd !important; }

    .card {
        background-color: var(--bg-card);
        border: 1px solid var(--border-subtle);
        box-shadow: var(--shadow-md);
        border-radius: 15px;
        color: var(--text-primary);
    }

    .card-body {
        padding: 1.5rem;
    }

    .card-header {
        background-color: var(--bg-hover);
        border-bottom: 1px solid var(--border-subtle);
        border-radius: 15px 15px 0 0 !important;
    }

    .btn-primary {
        background-color: #086838;
        border-color: #086838;
        color: white;
    }

    .btn-primary:hover {
        background-color: #06502b;
        border-color: #06502b;
        color: white;
    }
</style>
@endsection
