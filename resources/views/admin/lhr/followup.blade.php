@extends('admin.layouts.layouts')

@section('content')

    <style>
        .section-divider {
            display: flex;
            align-items: center;
            width: 100%;
            margin: 30px 0 20px;
            font-size: 16px;
            font-weight: 600;
            color: var(--accent-solid);
        }

        .section-divider:after {
            content: "";
            flex-grow: 1;
            height: 1px;
            background: var(--border-subtle);
            margin-left: 15px;
        }

        .form-container {
            background: var(--bg-card);
            padding: 30px;
            border-radius: 12px;
            box-shadow: var(--shadow-lg);
            border: 1px solid var(--border-subtle);
            margin-bottom: 2rem;
            color: var(--text-primary);
        }

        .pro_filed {
            display: flex;
            gap: 20px;
            margin-bottom: 15px;
            width: 100%;
        }

        .pro_filed .form {
            flex: 1;
            position: relative;
        }

        label {
            font-weight: 600;
            color: var(--text-primary);
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .required:after {
            content: " *";
            color: #e74c3c;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 10px 15px;
            background-color: var(--bg-main);
            color: var(--text-primary);
            border: 1px solid var(--border-subtle);
            border-radius: 8px;
            font-size: 14px;
            transition: all 0.3s ease;
            outline: none;
        }

        input:focus,
        select:focus,
        textarea:focus {
            border-color: var(--accent-solid);
            box-shadow: 0 0 0 3px rgba(8, 104, 56, 0.1);
            background-color: var(--bg-card);
        }

        textarea {
            resize: vertical;
            min-height: 80px;
        }

        .btn-submit {
            background: #086838;
            color: white;
            padding: 12px 35px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(8, 104, 56, 0.2);
        }

        .btn-submit:hover {
            background: #067945;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(8, 104, 56, 0.3);
            color: white;
        }

        .btn-cancel {
            background-color: var(--bg-hover);
            border: 2px solid var(--border-subtle);
            padding: 12px 35px;
            border-radius: 8px;
            font-weight: 600;
            color: var(--text-primary);
            transition: all 0.3s ease;
        }

        .btn-cancel:hover {
            background-color: var(--bg-card);
            border-color: var(--text-muted);
            color: var(--text-primary);
        }

        .separate_payment {
            padding: 25px;
            border: 1px solid var(--border-subtle) !important;
            border-radius: 12px;
            background: var(--bg-hover);
            margin-top: 20px;
        }

        /* Page Title Styling */
        .page-title-box h4 {
            color: var(--accent-solid);
            font-size: 24px;
            font-weight: 600;
        }

        /* Patient Info Card */
        .patient-info-card {
            background: var(--bg-main);
            border-radius: 12px;
            padding: 25px 35px;
            border: 1px solid var(--border-subtle);
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
        }

        .patient-info-label {
            font-size: 11px;
            color: var(--text-muted);
            margin-bottom: 4px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .patient-info-details h2 {
            color: var(--accent-solid);
            font-size: 24px;
            font-weight: 700;
            margin: 0 0 5px 0;
            line-height: 1.2;
        }

        .patient-id-badge {
            font-weight: 600;
            color: var(--text-muted);
            font-size: 14px;
            display: inline-block;
        }

        .patient-meta {
            text-align: right;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .patient-meta-item {
            font-size: 14px;
            color: var(--text-primary);
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: flex-end;
        }

        .patient-meta-item i {
            color: #0d6efd;
            margin-right: 10px;
            width: 16px;
            text-align: center;
            font-size: 14px;
        }

        /* Custom Checkbox */
        .custom-checkbox-container {
            display: inline-flex;
            align-items: center;
            cursor: pointer;
            user-select: none;
            margin-bottom: 20px;
            padding: 5px 0;
            transition: all 0.3s ease;
        }

        .custom-checkbox-container input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
            height: 0;
            width: 0;
        }

        .checkbox-checkmark {
            height: 22px;
            width: 22px;
            background-color: var(--bg-main);
            border: 2px solid var(--border-subtle);
            border-radius: 6px;
            margin-right: 12px;
            position: relative;
            transition: all 0.3s ease;
        }

        .custom-checkbox-container input:checked~.checkbox-checkmark {
            background-color: #0d6efd;
            border-color: #0d6efd;
        }

        .checkbox-checkmark:after {
            content: "";
            position: absolute;
            display: none;
            left: 7px;
            top: 3px;
            width: 5px;
            height: 10px;
            border: solid white;
            border-width: 0 2px 2px 0;
            transform: rotate(45deg);
        }

        .custom-checkbox-container input:checked~.checkbox-checkmark:after {
            display: block;
        }

        .checkbox-label {
            font-weight: 600;
            color: #067945;
            font-size: 14px;
        }

        /* Payment Summary */
        .payment-total {
            background: var(--bg-main);
            padding: 20px;
            border-radius: 8px;
            border: 1px solid var(--border-subtle);
            margin-top: 20px;
        }

        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-size: 14px;
            color: var(--text-muted);
        }

        .total-row.final {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px solid var(--border-subtle);
            font-weight: 700;
            font-size: 16px;
            color: var(--accent-solid);
        }

        /* Error Message */
        .error-message {
            color: #e74c3c;
            font-size: 12px;
            margin-top: 5px;
            display: none;
        }

        .form-control.error {
            border-color: #e74c3c;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .pro_filed {
                flex-direction: column;
                gap: 15px;
            }

            .patient-info-card {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .patient-meta {
                text-align: left;
            }
        }
    </style>

    <div class="container-fluid">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-md-11 col-lg-10 m-auto">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Follow Up</h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('lhr.pending') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i> Back to List
                        </a>
                    </div>
                </div>
            </div>
        </div>


        @if(session('success'))
            <div class="row mb-4">
                <div class="col-md-11 col-lg-10 m-auto">
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                </div>
            </div>
        @endif

        <div class="row">
            <div class="col-md-11 col-lg-10 m-auto">
                <div class="form-container">
                    <form id="followupForm" action="{{ route('lhr.followup.store', $inquiry->id) }}" method="POST">
                        @csrf

                        <!-- Patient Info Card -->
                        <div class="patient-info-card">
                            <div class="patient-info-details">
                                <div class="patient-info-label">Patient Name</div>
                                <h2>{{ $inquiry->patient_name }}</h2>
                                <div class="patient-id-badge">ID: {{ $inquiry->patient_id }}</div>
                            </div>
                            <div class="patient-meta">
                                <div class="patient-meta-item">
                                    <i class="fas fa-phone-alt"></i>
                                    <span>{{ $inquiry->mobile_no ?? 'N/A' }}</span>
                                </div>
                                <div class="patient-meta-item">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span>{{ $inquiry->address ?? 'N/A' }}</span>
                                </div>
                            </div>
                        </div>

                        <input type="hidden" name="patient_id" value="{{ $inquiry->id }}">
                        <input type="hidden" name="patient_name" value="{{ $inquiry->patient_name }}">

                        <div class="section-divider">Branch Information</div>

                        <div class="pro_filed">
                            <div class="form">
                                <label class="required">Branch</label>
                                <select class="form-control" disabled>
                                    @foreach($branches as $branch)
                                        <option value="{{ $branch->branch_id }}" {{ old('branch_id', $inquiry->branch_id ?? '') == $branch->branch_id ? 'selected' : '' }}>
                                            {{ $branch->branch_name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="form">
                                <label>Staff Name</label>
                                <input type="text" name="staff_name" placeholder="Enter staff name"
                                    value="{{ old('staff_name', auth()->user()->name ?? '') }}">
                            </div>
                        </div>

                        <div class="section-divider">Inquiry Details</div>

                        <div class="pro_filed">
                            <div class="form">
                                <label class="required">Inquiry Date</label>
                                <input type="date" name="inquiry_date" required
                                    value="{{ old('inquiry_date', date('Y-m-d')) }}">
                                <div class="error-message" id="inquiry_date_error"></div>
                            </div>
                            <div class="form">
                                <label class="required">Inquiry Time</label>
                                <input type="time" name="inquiry_time" required
                                    value="{{ old('inquiry_time', date('H:i')) }}">
                                <div class="error-message" id="inquiry_time_error"></div>
                            </div>
                            <div class="form">
                                <label>Month & Year</label>
                                <input type="month" name="month_year" value="{{ old('month_year', date('Y-m')) }}">
                            </div>
                        </div>

                        <div class="section-divider">Patient Information</div>

                        <div class="pro_filed">
                            <div class="form">
                                <label>Address</label>
                                <input type="text" name="address" placeholder="Enter address"
                                    value="{{ old('address', $inquiry->address ?? '') }}">
                            </div>
                            <div class="form">
                                <label class="required">Gender</label>
                                <select name="gender" required>
                                    <option value="">Select Gender</option>
                                    <option value="male" {{ (old('gender', $inquiry->gender) == 'male') ? 'selected' : '' }}>
                                        Male</option>
                                    <option value="female" {{ (old('gender', $inquiry->gender) == 'female') ? 'selected' : '' }}>Female</option>
                                    <option value="other" {{ (old('gender', $inquiry->gender) == 'other') ? 'selected' : '' }}>Other</option>
                                </select>
                                <div class="error-message" id="gender_error"></div>
                            </div>
                            <div class="form">
                                <label>Age</label>
                                <input type="number" name="age" min="0" max="120" placeholder="Age"
                                    value="{{ old('age', $inquiry->age ?? '') }}">
                            </div>
                        </div>

                        <div class="pro_filed">
                            <div class="form">
                                <label>Area Code</label>
                                <input type="text" name="afra_code" placeholder="Enter afra code"
                                    value="{{ old('afra_code') }}">
                            </div>
                            <div class="form">
                                <label>Treatment Area</label>
                                <select name="area">
                                    <option value="">Select Area</option>
                                    <option value="face" {{ old('area', $inquiry->area) == 'face' ? 'selected' : '' }}>Face
                                    </option>
                                    <option value="underarms" {{ old('area', $inquiry->area) == 'underarms' ? 'selected' : '' }}>Underarms</option>
                                    <option value="full_arms" {{ old('area', $inquiry->area) == 'full_arms' ? 'selected' : '' }}>Full Arms</option>
                                    <option value="half_arms" {{ old('area', $inquiry->area) == 'half_arms' ? 'selected' : '' }}>Half Arms</option>
                                    <option value="full_legs" {{ old('area', $inquiry->area) == 'full_legs' ? 'selected' : '' }}>Full Legs</option>
                                    <option value="half_legs" {{ old('area', $inquiry->area) == 'half_legs' ? 'selected' : '' }}>Half Legs</option>
                                    <option value="bikini" {{ old('area', $inquiry->area) == 'bikini' ? 'selected' : '' }}>
                                        Bikini</option>
                                    <option value="brazilian" {{ old('area', $inquiry->area) == 'brazilian' ? 'selected' : '' }}>Brazilian</option>
                                    <option value="chest" {{ old('area', $inquiry->area) == 'chest' ? 'selected' : '' }}>Chest
                                    </option>
                                    <option value="back" {{ old('area', $inquiry->area) == 'back' ? 'selected' : '' }}>Back
                                    </option>
                                    <option value="stomach" {{ old('area', $inquiry->area) == 'stomach' ? 'selected' : '' }}>
                                        Stomach</option>
                                    <option value="full_body" {{ old('area', $inquiry->area) == 'full_body' ? 'selected' : '' }}>Full Body</option>
                                </select>
                            </div>
                        </div>

                        <div class="pro_filed">
                            <div class="form">
                                <label>Frequency</label>
                                <select name="frequency">
                                    <option value="">Select Frequency</option>
                                    <option value="weekly" {{ old('frequency', $inquiry->frequency) == 'weekly' ? 'selected' : '' }}>Weekly</option>
                                    <option value="biweekly" {{ old('frequency', $inquiry->frequency) == 'biweekly' ? 'selected' : '' }}>Bi-weekly</option>
                                    <option value="monthly" {{ old('frequency', $inquiry->frequency) == 'monthly' ? 'selected' : '' }}>Monthly</option>
                                    <option value="quarterly" {{ old('frequency', $inquiry->frequency) == 'quarterly' ? 'selected' : '' }}>Quarterly</option>
                                    <option value="custom" {{ old('frequency', $inquiry->frequency) == 'custom' ? 'selected' : '' }}>Custom</option>
                                </select>
                            </div>
                            <div class="form">
                                <label>Shot</label>
                                <input type="text" name="shot" placeholder="Enter shot details"
                                    value="{{ old('shot', $inquiry->shot ?? '') }}">
                            </div>
                            <div class="form">
                                <label>Reference By</label>
                                <input type="text" name="reference_by" placeholder="Enter reference name"
                                    value="{{ old('reference_by', $inquiry->reference_by ?? '') }}">
                            </div>
                        </div>

                        <div class="section-divider">Follow Up Details</div>

                        <div class="pro_filed">
                            <div class="form">
                                <label>Next Follow Up Date</label>
                                <input type="date" name="next_follow_up"
                                    value="{{ old('next_follow_up', $inquiry->next_follow_up ?? '') }}">
                            </div>
                        </div>

                        <div class="pro_filed">
                            <div class="form">
                                <label>Notes</label>
                                <textarea name="notes"
                                    placeholder="Enter any notes or observations...">{{ old('notes') }}</textarea>
                            </div>
                        </div>

                        <div class="section-divider">Payment Information</div>

                        <div class="mb-3">
                            <label class="custom-checkbox-container">
                                <input type="checkbox" id="foc" name="foc" value="1" {{ old('foc') ? 'checked' : '' }}>
                                <span class="checkbox-checkmark"></span>
                                <span class="checkbox-label">FOC (No payment is collected from patient)</span>
                            </label>
                        </div>

                        <div class="separate_payment">
                            <div class="pro_filed">
                                <div class="form">
                                    <label class="required">Registration & Consultation Charges (₹)</label>
                                    <input type="number" step="0.01" name="registration_charges" id="registration_charges"
                                        value="{{ old('registration_charges', 200) }}" placeholder="0.00" required
                                        oninput="calculateDue()">
                                </div>
                                <div class="form">
                                    <label class="required">Paid Amount (₹)</label>
                                    <input type="number" step="0.01" name="paid_amount" id="paid_amount"
                                        value="{{ old('paid_amount', 200) }}" placeholder="0.00" required
                                        oninput="calculateDue()">
                                </div>
                                <div class="form">
                                    <label class="required">Payment Method</label>
                                    <select name="payment_method" id="payment_method" class="form-control" required
                                        onchange="calculateDue()">
                                        <option value="">Select Payment Method</option>
                                        <option value="online" {{ old('payment_method') == 'online' ? 'selected' : '' }}>
                                            Online</option>
                                        <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Cash
                                        </option>
                                        <option value="card" {{ old('payment_method') == 'card' ? 'selected' : '' }}>Card
                                        </option>
                                        <option value="other" {{ old('payment_method') == 'other' ? 'selected' : '' }}>Other
                                        </option>
                                    </select>
                                </div>
                                <div class="form">
                                    <label>Due Amount (₹)</label>
                                    <input type="number" step="0.01" name="due_amount" id="due_amount"
                                        value="{{ old('due_amount', 0.00) }}" readonly placeholder="0.00">
                                </div>
                            </div>



                            <div class="mt-5 text-center">
                                <button type="submit" class="btn btn-submit me-3">
                                    <i class="fas fa-save me-2"></i> Save Follow Up
                                </button>
                                <button type="button" class="btn btn-cancel" onclick="resetForm()">
                                    <i class="fas fa-redo me-2"></i> Reset Form
                                </button>
                            </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Initialize form validation
            const form = document.getElementById('followupForm');
            const focCheckbox = document.getElementById('foc');

            // Set default next follow up date if not already set
            const nextFollowDateInput = document.querySelector('input[name="next_follow_up"]');
            if (nextFollowDateInput && !nextFollowDateInput.value) {
                const nextWeek = new Date();
                nextWeek.setDate(nextWeek.getDate() + 7);
                const nextWeekDate = nextWeek.toISOString().split('T')[0];
                nextFollowDateInput.value = nextWeekDate;
            }

            const separatePaymentSection = document.querySelector('.separate_payment');
            const paymentInputs = document.querySelectorAll('input[name="registration_charges"], input[name="paid_amount"], select[name="payment_method"]');

            function handleFOC() {
                if (focCheckbox.checked) {
                    separatePaymentSection.style.display = 'none';
                    paymentInputs.forEach(input => {
                        if (input.tagName === 'SELECT') {
                            input.value = '';
                        } else {
                            input.value = '0';
                        }
                    });
                    document.getElementById('due_amount').value = '0.00';
                    updateDisplay();
                } else {
                    separatePaymentSection.style.display = 'block';
                    // Reset to default values when unchecking FOC
                    document.getElementById('registration_charges').value = '200';
                    document.getElementById('paid_amount').value = '200';
                    document.getElementById('payment_method').value = 'online';
                    calculateDue();
                }
            }

            if (focCheckbox) {
                focCheckbox.addEventListener('change', handleFOC);
                // Initial check
                if (focCheckbox.checked) {
                    handleFOC();
                }
            }

            // Calculate due payment
            window.calculateDue = function () {
                const registrationCharges = parseFloat(document.getElementById('registration_charges').value) || 0;
                const paidAmount = parseFloat(document.getElementById('paid_amount').value) || 0;

                const due = registrationCharges - paidAmount;
                document.getElementById('due_amount').value = due >= 0 ? due.toFixed(2) : '0.00';

                updateDisplay();
            };

            // Update display values
            function updateDisplay() {
                const registrationCharges = parseFloat(document.getElementById('registration_charges').value) || 0;
                const paidAmount = parseFloat(document.getElementById('paid_amount').value) || 0;
                const due = parseFloat(document.getElementById('due_amount').value) || 0;

                // Payment total display elements were removed, so we just update the due amount color coding
                const balanceDisplay = document.getElementById('due_amount');
                if (balanceDisplay) {
                    if (due > 0) {
                        balanceDisplay.style.color = '#e74c3c';
                    } else {
                        balanceDisplay.style.color = '#27ae60';
                    }
                }
            }

            calculateDue();

            // Form validation
            form.addEventListener('submit', function (e) {
                let isValid = true;
                const requiredFields = form.querySelectorAll('[required]');

                // Reset errors
                document.querySelectorAll('.error-message').forEach(el => {
                    el.style.display = 'none';
                });
                document.querySelectorAll('.form-control.error').forEach(el => {
                    el.classList.remove('error');
                });

                // Validate required fields
                requiredFields.forEach(field => {
                    if (!field.value.trim()) {
                        isValid = false;
                        field.classList.add('error');
                        const errorId = field.name + '_error';
                        const errorEl = document.getElementById(errorId);
                        if (errorEl) {
                            errorEl.textContent = 'This field is required';
                            errorEl.style.display = 'block';
                        }
                    }
                });

                if (!isValid) {
                    e.preventDefault();
                    const firstError = document.querySelector('.error');
                    if (firstError) {
                        firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }
                } else {
                    // Form is valid, show confirmation dialog
                    e.preventDefault();

                    // Check if SweetAlert is available, otherwise use native confirm
                    if (typeof Swal !== 'undefined') {
                        Swal.fire({
                            title: 'Are you sure?',
                            text: "Do you want to save this follow up?",
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#086838',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Yes, save it!'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Show loading
                                const submitBtn = form.querySelector('button[type="submit"]');
                                const originalText = submitBtn.innerHTML;
                                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Saving...';
                                submitBtn.disabled = true;

                                // Submit form
                                form.submit();
                            }
                        });
                    } else {
                        // Fallback to native confirm dialog
                        if (confirm('Are you sure? Do you want to save this follow up?')) {
                            // Show loading
                            const submitBtn = form.querySelector('button[type="submit"]');
                            const originalText = submitBtn.innerHTML;
                            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Saving...';
                            submitBtn.disabled = true;

                            // Submit form
                            form.submit();
                        }
                    }
                }
            });

            // Reset Form
            window.resetForm = function () {
                // Check if SweetAlert is available, otherwise use native confirm
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "Do you want to reset the form? All data will be lost.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Yes, reset it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            form.reset();
                            calculateDue();
                            if (focCheckbox) handleFOC();
                        }
                    });
                } else {
                    // Fallback to native confirm dialog
                    if (confirm('Are you sure? Do you want to reset the form? All data will be lost.')) {
                        form.reset();
                        calculateDue();
                        if (focCheckbox) handleFOC();
                    }
                }
            };

            // Auto-hide alerts
            setTimeout(() => {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(alert => {
                    try {
                        // Check if bootstrap is available
                        if (typeof bootstrap !== 'undefined' && bootstrap.Alert) {
                            const bsAlert = new bootstrap.Alert(alert);
                            bsAlert.close();
                        } else {
                            // Fallback: just hide the alert
                            alert.style.display = 'none';
                        }
                    } catch (e) {
                        // If there's an error, just hide the element
                        alert.style.display = 'none';
                    }
                });
            }, 5000);
        });
    </script>

@endsection