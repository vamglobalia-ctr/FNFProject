@extends('admin.layouts.layouts')

@section('content')

    <style>
        .section-divider {
            display: flex;
            align-items: center;
            width: 100%;
            margin: 10px 0;
            font-size: 16px;
            font-weight: 600;
            color: var(--accent-solid);
            cursor: pointer;
            padding: 10px 15px;
            background: var(--bg-main);
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .section-divider:hover {
            background: var(--accent-glow);
        }

        .section-divider:after {
            content: "\f078";
            font-family: "Font Awesome 5 Free";
            font-weight: 900;
            margin-left: auto;
            transition: transform 0.3s ease;
            font-size: 14px;
        }

        .section-divider.active:after {
            transform: rotate(180deg);
        }

        .accordion-content {
            max-height: 2000px;
            overflow: hidden;
            transition: max-height 0.4s ease-out, padding 0.4s ease;
            padding: 20px 5px;
        }

        .accordion-content.collapsed {
            max-height: 0;
            padding-top: 0;
            padding-bottom: 0;
        }

        .form-container {
            background: var(--bg-card);
            padding: 30px;
            border-radius: 12px;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--border-subtle);
            margin-bottom: 2rem;
        }

        .form-group {
            margin-bottom: 20px;
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
            color: var(--text-secondary);
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
        }

        .required:after {
            content: " *";
            color: #ef4444;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 10px 15px;
            border: 1px solid var(--border-subtle);
            border-radius: 8px;
            font-size: 14px;
            background: var(--bg-main);
            color: var(--text-primary);
            transition: all 0.3s ease;
            outline: none;
        }

        input:focus,
        select:focus,
        textarea:focus {
            border-color: var(--accent-solid);
            box-shadow: 0 0 0 3px var(--accent-glow);
            background: var(--bg-main);
        }

        textarea {
            resize: vertical;
            min-height: 80px;
        }

        .btn-submit {
            background: var(--accent-solid);
            color: white;
            padding: 12px 35px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
        }

        .btn-submit:hover {
            background: var(--accent-hover);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.3);
            color: white;
        }

        .btn-cancel {
            background: var(--bg-card);
            border: 2px solid var(--border-subtle);
            padding: 12px 35px;
            border-radius: 8px;
            font-weight: 600;
            color: var(--text-secondary);
            transition: all 0.3s ease;
        }

        .btn-cancel:hover {
            background: var(--bg-main);
            border-color: var(--text-secondary);
            color: var(--text-primary);
        }

        .separate_payment {
            padding: 25px;
            border: 1px solid var(--border-subtle) !important;
            border-radius: 12px;
            background: var(--bg-main);
            margin-top: 20px;
        }

        /* Page Title Styling */
        .page-title-box h4 {
            color: var(--accent-solid);
            font-size: 24px;
            font-weight: 600;
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
            background-color: var(--accent-solid);
            border-color: var(--accent-solid);
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
            font-weight: 500;
            color: var(--text-secondary);
            font-size: 14px;
        }

        /* Custom Radio Buttons */
        .custom-radio-group {
            display: flex;
            gap: 30px;
            padding: 10px 0;
        }

        .custom-radio-item {
            position: relative;
            display: flex;
            align-items: center;
            cursor: pointer;
        }

        .custom-radio-item input {
            position: absolute;
            opacity: 0;
            cursor: pointer;
        }

        .radio-checkmark {
            height: 20px;
            width: 20px;
            background-color: var(--bg-main);
            border: 2px solid var(--border-subtle);
            border-radius: 50%;
            display: inline-block;
            position: relative;
            margin-right: 10px;
            transition: all 0.3s ease;
        }

        .custom-radio-item:hover input~.radio-checkmark {
            border-color: var(--accent-solid);
        }

        .custom-radio-item input:checked~.radio-checkmark {
            background-color: var(--bg-main);
            border-color: var(--accent-solid);
        }

        .radio-checkmark:after {
            content: "";
            position: absolute;
            display: none;
            top: 4px;
            left: 4px;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--accent-solid);
        }

        .custom-radio-item input:checked~.radio-checkmark:after {
            display: block;
        }

        .radio-label {
            font-weight: 500;
            color: var(--text-secondary);
            font-size: 15px;
        }

        /* Picture Uploads */
        .picture-upload-box {
            background: var(--bg-main);
            border: 2px dashed var(--border-subtle);
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
        }

        .picture-upload-box:hover {
            border-color: var(--accent-solid);
            background: var(--accent-glow);
        }

        .upload-icon {
            font-size: 24px;
            color: var(--text-secondary);
            margin-bottom: 10px;
        }

        .upload-text {
            font-size: 13px;
            color: var(--text-secondary);
            font-weight: 500;
            word-break: break-all;
        }

        .current-image-preview {
            max-height: 150px;
            border-radius: 8px;
            margin-bottom: 10px;
            border: 1px solid var(--border-subtle);
        }

        /* Responsive */
        @media (max-width: 768px) {
            .pro_filed {
                flex-direction: column;
                gap: 15px;
            }
        }
    </style>

    <div class="container-fluid">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-md-11 col-lg-10 m-auto">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <h4 class="mb-0">Edit Inquiry</h4>
                    <div class="d-flex gap-2">
                        <a href="{{ route('lhr.pending') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-arrow-left me-2"></i> Back to List
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="col-md-11 col-lg-10 m-auto">
                <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                    <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        @endif

        @if($errors->any())
            <div class="col-md-11 col-lg-10 m-auto">
                <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i> <strong>Please fix the following errors:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        @endif

        <div class="row">
            <div class="col-md-11 col-lg-10 m-auto">
                <div class="form-container">
                    <form id="inquiryForm" method="POST" action="{{ route('lhr.update', $inquiry->id) }}"
                        enctype="multipart/form-data">
                        @csrf

                        <div class="section-divider active" onclick="toggleSection(this)">Patient Information</div>
                        <div class="accordion-content">

                            <div class="pro_filed">
                                <div class="form">
                                    <label for="patient_name" class="required">Patient Name</label>
                                    <input type="text" id="patient_name" name="patient_name" required
                                        placeholder="Enter patient name"
                                        value="{{ old('patient_name', $inquiry->patient_name) }}">
                                </div>
                                <div class="form">
                                    <label for="inquiry_date">Inquiry Date</label>
                                    <input type="date" id="inquiry_date" name="inquiry_date"
                                        value="{{ old('inquiry_date', $inquiry->inquiry_date ? $inquiry->inquiry_date->format('Y-m-d') : '') }}">
                                </div>
                            </div>

                            <div class="pro_filed">
                                <div class="form">
                                    <label for="address">Address</label>
                                    <textarea id="address" name="address" rows="1"
                                        placeholder="Enter complete address">{{ old('address', $inquiry->address) }}</textarea>
                                </div>
                            </div>
                        </div>

                        <div class="section-divider active" onclick="toggleSection(this)">Treatment Information</div>
                        <div class="accordion-content">

                            <div class="pro_filed">
                                <div class="form">
                                    <label for="gender" class="required">Gender</label>
                                    <select id="gender" name="gender" required>
                                        <option value="">Select Gender</option>
                                        <option value="male" {{ old('gender', $inquiry->gender) == 'male' ? 'selected' : ''
                                            }}>Male</option>
                                        <option value="female" {{ old('gender', $inquiry->gender) == 'female' ? 'selected' :
        '' }}>Female</option>
                                        <option value="other" {{ old('gender', $inquiry->gender) == 'other' ? 'selected' :
        '' }}>Other</option>
                                    </select>
                                </div>
                                <div class="form" style="display: flex; gap: 10px;">
                                    <div style="flex: 1;">
                                        <label for="age" class="required">Age</label>
                                        <input type="number" id="age" name="age" required min="1" max="120"
                                            placeholder="Age" value="{{ old('age', $inquiry->age) }}">
                                    </div>
                                    <div style="width: 120px;">
                                        <label>Unit</label>
                                        <select name="year">
                                            <option value="">Select</option>
                                            <option value="Year" {{ old('year', $inquiry->year) == 'Year' ? 'selected' : ''
                                                }}>Year</option>
                                            <option value="Month" {{ old('year', $inquiry->year) == 'Month' ? 'selected' :
        '' }}>Month</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div id="area_session_section" style="display: none;">
                                <div class="pro_filed">
                                    <div class="form">
                                        <label for="area" class="required">Select Area</label>
                                        @php
                                            $rawArea = $inquiry->area;
                                            $savedAreas = [];
                                            if (is_string($rawArea) && str_starts_with($rawArea, '[')) {
                                                $savedAreas = json_decode($rawArea, true) ?: [];
                                            } elseif (is_string($rawArea) && !empty($rawArea)) {
                                                $savedAreas = [$rawArea];
                                            }

                                            $oldAreas = old('area', $savedAreas);
                                            if (!is_array($oldAreas)) {
                                                $oldAreas = [$oldAreas];
                                            }
                                        @endphp
                                        <select id="area" name="area[]" multiple required class="form-control">
                                            <option value="upper_lips" {{ in_array('upper_lips', $oldAreas) ? 'selected' : '' }}>Upper Lips</option>
                                            <option value="beard" {{ in_array('beard', $oldAreas) ? 'selected' : '' }}>Beard
                                            </option>
                                            <option value="full_face" {{ in_array('full_face', $oldAreas) ? 'selected' : '' }}>
                                                Full Face</option>
                                            <option value="half_face" {{ in_array('half_face', $oldAreas) ? 'selected' : '' }}>
                                                Half Face</option>
                                            <option value="under_arms" {{ in_array('under_arms', $oldAreas) ? 'selected' : '' }}>Under Arms</option>
                                            <option value="half_hands" {{ in_array('half_hands', $oldAreas) ? 'selected' : '' }}>Half Hands</option>
                                            <option value="full_hands" {{ in_array('full_hands', $oldAreas) ? 'selected' : '' }}>Full Hands</option>
                                            <option value="half_legs" {{ in_array('half_legs', $oldAreas) ? 'selected' : '' }}>
                                                Half Legs</option>
                                            <option value="full_legs" {{ in_array('full_legs', $oldAreas) ? 'selected' : '' }}>
                                                Full Legs</option>
                                            <option value="back" {{ in_array('back', $oldAreas) ? 'selected' : '' }}>Back
                                            </option>
                                            <option value="bikini" {{ in_array('bikini', $oldAreas) ? 'selected' : '' }}>Bikini
                                            </option>
                                            <option value="abdomen" {{ in_array('abdomen', $oldAreas) ? 'selected' : '' }}>
                                                Abdomen</option>
                                            <option value="chest" {{ in_array('chest', $oldAreas) ? 'selected' : '' }}>Chest
                                            </option>
                                        </select>
                                    </div>
                                    <div class="form">
                                        <label for="session">Session</label>
                                        <input type="text" id="session" name="session" placeholder="Session"
                                            value="{{ old('session', $inquiry->session) }}">
                                    </div>
                                </div>

                                <div class="pro_filed">
                                    <div class="form">
                                        <label for="area_code">Area Code</label>
                                        <input type="text" id="area_code" name="area_code" placeholder="Area Code"
                                            value="{{ old('area_code', $inquiry->area_code) }}">
                                    </div>
                                    <div class="form">
                                        <label for="energy">Energy</label>
                                        <input type="text" id="energy" name="energy" placeholder="Energy"
                                            value="{{ old('energy', $inquiry->energy) }}">
                                    </div>
                                </div>

                                <div class="pro_filed">
                                    <div class="form">
                                        <label for="frequency">Frequency</label>
                                        <input type="number" id="frequency" name="frequency" placeholder="Frequency"
                                            value="{{ old('frequency', $inquiry->frequency) }}">
                                    </div>
                                    <div class="form">
                                        <label for="shot">Shot</label>
                                        <input type="text" id="shot" name="shot" placeholder="Shot"
                                            value="{{ old('shot', $inquiry->shot) }}">
                                    </div>
                                </div>
                            </div>

                            <div class="pro_filed">
                                <div class="form">
                                    <label for="staff_name">Staff Name</label>
                                    <input type="text" id="staff_name" name="staff_name" placeholder="Staff Name"
                                        value="{{ old('staff_name', $inquiry->staff_name) }}">
                                </div>
                                <div class="form">
                                    <label for="status_name" class="required">Status</label>
                                    <select id="status_name" name="status_name" required>
                                        <option value="">Select Status</option>
                                        <option value="pending" {{ old('status_name', $inquiry->status_name) == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="joined" {{ old('status_name', $inquiry->status_name) == 'joined' ? 'selected' : '' }}>Joined</option>
                                    </select>
                                </div>
                            </div>

                        </div>

                        <div class="section-divider collapsed" onclick="toggleSection(this)">Medical Information</div>
                        <div class="accordion-content collapsed">

                            <div class="mb-4">
                                <label>Do you have any hormonal issues?</label>
                                <div class="custom-radio-group">
                                    <label class="custom-radio-item">
                                        <input type="radio" name="hormonal_issues" value="yes" {{ old('hormonal_issues', $inquiry->hormonal_issues) == 'yes' ? 'checked' : '' }}>
                                        <span class="radio-checkmark"></span>
                                        <span class="radio-label">Yes</span>
                                    </label>
                                    <label class="custom-radio-item">
                                        <input type="radio" name="hormonal_issues" value="no" {{ old('hormonal_issues', $inquiry->hormonal_issues) == 'no' ? 'checked' : '' }}>
                                        <span class="radio-checkmark"></span>
                                        <span class="radio-label">No</span>
                                    </label>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label>Any medication or treatment for hair loss?</label>
                                <div class="custom-radio-group">
                                    <label class="custom-radio-item">
                                        <input type="radio" name="medication" value="yes" {{ old('medication', $inquiry->medication) == 'yes' ? 'checked' : '' }}>
                                        <span class="radio-checkmark"></span>
                                        <span class="radio-label">Yes</span>
                                    </label>
                                    <label class="custom-radio-item">
                                        <input type="radio" name="medication" value="no" {{ old('medication', $inquiry->medication) == 'no' ? 'checked' : '' }}>
                                        <span class="radio-checkmark"></span>
                                        <span class="radio-label">No</span>
                                    </label>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label>Before you took hair treatment from somewhere else?</label>
                                <div class="custom-radio-group">
                                    <label class="custom-radio-item">
                                        <input type="radio" name="previous_treatment" value="yes" {{ old('previous_treatment', $inquiry->previous_treatment) == 'yes' ? 'checked' : '' }}>
                                        <span class="radio-checkmark"></span>
                                        <span class="radio-label">Yes</span>
                                    </label>
                                    <label class="custom-radio-item">
                                        <input type="radio" name="previous_treatment" value="no" {{ old('previous_treatment', $inquiry->previous_treatment) == 'no' ? 'checked' : '' }}>
                                        <span class="radio-checkmark"></span>
                                        <span class="radio-label">No</span>
                                    </label>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label>PCOD, Thyroid Issue?</label>
                                <div class="custom-radio-group">
                                    <label class="custom-radio-item">
                                        <input type="radio" name="pcod_thyroid" value="yes" {{ old('pcod_thyroid', $inquiry->pcod_thyroid) == 'yes' ? 'checked' : '' }}>
                                        <span class="radio-checkmark"></span>
                                        <span class="radio-label">Yes</span>
                                    </label>
                                    <label class="custom-radio-item">
                                        <input type="radio" name="pcod_thyroid" value="no" {{ old('pcod_thyroid', $inquiry->pcod_thyroid) == 'no' ? 'checked' : '' }}>
                                        <span class="radio-checkmark"></span>
                                        <span class="radio-label">No</span>
                                    </label>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label>Do you suffer from any skin conditions, allergies, or diseases?</label>
                                <div class="custom-radio-group">
                                    <label class="custom-radio-item">
                                        <input type="radio" name="skin_conditions" value="yes" {{ old('skin_conditions', $inquiry->skin_conditions) == 'yes' ? 'checked' : '' }}>
                                        <span class="radio-checkmark"></span>
                                        <span class="radio-label">Yes</span>
                                    </label>
                                    <label class="custom-radio-item">
                                        <input type="radio" name="skin_conditions" value="no" {{ old('skin_conditions', $inquiry->skin_conditions) == 'no' ? 'checked' : '' }}>
                                        <span class="radio-checkmark"></span>
                                        <span class="radio-label">No</span>
                                    </label>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label>Which procedure are you currently utilizing for hair removal?</label>
                                <div class="d-flex gap-4">
                                    @php
                                        $procedures = $inquiry->procedure ? json_decode($inquiry->procedure, true) : [];
                                        if (!is_array($procedures)) {
                                            $procedures = [];
                                        }
                                        $oldProcedures = old('procedure', $procedures);
                                    @endphp
                                    <label class="custom-checkbox-container">
                                        <input type="checkbox" name="procedure[]" value="waxing" {{ is_array($oldProcedures) && in_array('waxing', $oldProcedures) ? 'checked' : '' }}>
                                        <span class="checkbox-checkmark"></span>
                                        <span class="checkbox-label">Waxing</span>
                                    </label>
                                    <label class="custom-checkbox-container">
                                        <input type="checkbox" name="procedure[]" value="threading" {{ is_array($oldProcedures) && in_array('threading', $oldProcedures) ? 'checked' : '' }}>
                                        <span class="checkbox-checkmark"></span>
                                        <span class="checkbox-label">Threading</span>
                                    </label>
                                    <label class="custom-checkbox-container">
                                        <input type="checkbox" name="procedure[]" value="cream" {{ is_array($oldProcedures) && in_array('cream', $oldProcedures) ? 'checked' : '' }}>
                                        <span class="checkbox-checkmark"></span>
                                        <span class="checkbox-label">Cream</span>
                                    </label>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label>Are there any ongoing skin treatments?</label>
                                <div class="custom-radio-group">
                                    <label class="custom-radio-item">
                                        <input type="radio" name="ongoing_treatments" value="yes" {{ old('ongoing_treatments', $inquiry->ongoing_treatments) == 'yes' ? 'checked' : '' }}>
                                        <span class="radio-checkmark"></span>
                                        <span class="radio-label">Yes</span>
                                    </label>
                                    <label class="custom-radio-item">
                                        <input type="radio" name="ongoing_treatments" value="no" {{ old('ongoing_treatments', $inquiry->ongoing_treatments) == 'no' ? 'checked' : '' }}>
                                        <span class="radio-checkmark"></span>
                                        <span class="radio-label">No</span>
                                    </label>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label>Does your body have any implantations or tattoos?</label>
                                <div class="custom-radio-group">
                                    <label class="custom-radio-item">
                                        <input type="radio" name="implants_tattoos" value="yes" {{ old('implants_tattoos', $inquiry->implants_tattoos) == 'yes' ? 'checked' : '' }}>
                                        <span class="radio-checkmark"></span>
                                        <span class="radio-label">Yes</span>
                                    </label>
                                    <label class="custom-radio-item">
                                        <input type="radio" name="implants_tattoos" value="no" {{ old('implants_tattoos', $inquiry->implants_tattoos) == 'no' ? 'checked' : '' }}>
                                        <span class="radio-checkmark"></span>
                                        <span class="radio-label">No</span>
                                    </label>
                                </div>
                            </div>

                        </div>

                        <div class="section-divider collapsed" onclick="toggleSection(this)">Follow Up & Notes</div>
                        <div class="accordion-content collapsed">

                            <div class="pro_filed">
                                <div class="form">
                                    <label for="reference_by">Reference By</label>
                                    <input type="text" id="reference_by" name="reference_by" placeholder="Reference By"
                                        value="{{ old('reference_by', $inquiry->reference_by) }}">
                                </div>
                                <div class="form">
                                    <label for="next_follow_up">Next Follow Up Date</label>
                                    <input type="date" id="next_follow_up" name="next_follow_up"
                                        value="{{ old('next_follow_up', $inquiry->next_follow_up ? $inquiry->next_follow_up->format('Y-m-d') : '') }}">
                                </div>
                            </div>

                            <div class="pro_filed">
                                <div class="form">
                                    <label for="notes">Notes</label>
                                    <textarea id="notes" name="notes" rows="2"
                                        placeholder="Enter notes">{{ old('notes', $inquiry->notes) }}</textarea>
                                </div>
                            </div>

                        </div>

                        <div class="section-divider active" onclick="toggleSection(this)">Payment Information</div>
                        <div class="accordion-content">

                            <div class="mb-3">
                                <label class="custom-checkbox-container">
                                    <input type="checkbox" id="foc" name="foc" value="1" {{ old('foc', $inquiry->foc) ? 'checked' : '' }}>
                                    <span class="checkbox-checkmark"></span>
                                    <span class="checkbox-label">FOC (No payment is collected from patient)</span>
                                </label>
                            </div>

                            <div class="separate_payment">
                                <div class="pro_filed">
                                    <div class="form">
                                        <label for="total_payment">Total Payment (₹)</label>
                                        <input type="number" step="0.01" name="total_payment" id="total_payment"
                                            value="{{ old('total_payment', $inquiry->total_payment) }}" placeholder="0.00">
                                    </div>
                                    <div class="form">
                                        <label for="discount_payment">Discount (₹)</label>
                                        <input type="number" step="0.01" name="discount_payment" id="discount_payment"
                                            value="{{ old('discount_payment', $inquiry->discount_payment) }}"
                                            placeholder="0.00">
                                    </div>
                                </div>

                                <div class="pro_filed">
                                    <div class="form">
                                        <label for="given_payment">Given Payment (₹)</label>
                                        <input type="number" step="0.01" name="given_payment" id="given_payment"
                                            value="{{ old('given_payment', $inquiry->given_payment) }}" placeholder="0.00">
                                    </div>
                                    <div class="form">
                                        <label for="due_payment">Due Payment (₹)</label>
                                        <input type="number" step="0.01" name="due_payment" id="due_payment"
                                            value="{{ old('due_payment', $inquiry->due_payment) }}" readonly
                                            placeholder="0.00">
                                    </div>
                                </div>

                                <div class="pro_filed mt-3">
                                    <div class="form">
                                        <label for="payment_method">Payment Method</label>
                                        <select class="form-control" name="payment_method" id="payment_method">
                                            <option value="">Select Payment Method</option>
                                            <option value="cash_payment" {{ old('payment_method', $inquiry->payment_method) == 'cash_payment' ? 'selected' : '' }}>Cash Payment</option>
                                            <option value="google_pay" {{ old('payment_method', $inquiry->payment_method) == 'google_pay' ? 'selected' : '' }}>Google Pay</option>
                                            <option value="cheque_payment" {{ old('payment_method', $inquiry->payment_method) == 'cheque_payment' ? 'selected' : '' }}>Cheque Payment</option>
                                        </select>
                                    </div>
                                    <div class="form">
                                        <label for="payment_amount">Payment Amount (₹)</label>
                                        <input type="number" step="0.01" name="payment_amount" id="payment_amount"
                                            value="{{ old('payment_amount', $inquiry->payment_amount) }}"
                                            placeholder="0.00">
                                    </div>
                                </div>
                            </div>

                        </div>

                        <div class="section-divider collapsed" onclick="toggleSection(this)">Account & Media</div>
                        <div class="accordion-content collapsed">

                            <div class="pro_filed">
                                <div class="form">
                                    <label for="account">Account</label>
                                    <input type="text" id="account" name="account" placeholder="Account"
                                        value="{{ old('account', $inquiry->account) }}">
                                </div>
                                <div class="form">
                                    <label for="time">Time</label>
                                    <input type="time" id="time" name="time"
                                        value="{{ old('time', $inquiry->time ? \Carbon\Carbon::parse($inquiry->time)->format('H:i') : '13:00') }}">
                                </div>
                            </div>

                            <div class="pro_filed mt-4">
                                <div class="form">
                                    <label>Before Picture 1</label>
                                    <div class="picture-upload-box"
                                        onclick="document.getElementById('before_picture_1_input').click()">
                                        @if($inquiry->before_picture_1)
                                            @php
                                                $beforePicturePath = str_replace('storage/', '', $inquiry->before_picture_1);
                                                $beforePictureUrl = Storage::disk('public')->exists($beforePicturePath)
                                                    ? asset('storage/' . $beforePicturePath)
                                                    : asset('images/default-placeholder.png');
                                            @endphp
                                            <img src="{{ $beforePictureUrl }}" class="current-image-preview" alt="Before">
                                        @else
                                            <i class="fas fa-cloud-upload-alt upload-icon"></i>
                                        @endif
                                        <div class="upload-text" id="before_picture_1_name">
                                            {{ $inquiry->before_picture_1 ? basename($inquiry->before_picture_1) : 'Choose
                                            Before Picture' }}
                                        </div>
                                        <input type="file" name="before_picture_1" id="before_picture_1_input"
                                            class="d-none" accept="image/*">
                                    </div>
                                    @if($inquiry->before_picture_1)
                                        <div class="mt-2">
                                            <label class="custom-checkbox-container" style="margin-bottom: 0;">
                                                <input type="checkbox" name="remove_before_picture" value="1">
                                                <span class="checkbox-checkmark" style="height: 18px; width: 18px;"></span>
                                                <span class="checkbox-label" style="font-size: 13px;">Remove current
                                                    picture</span>
                                            </label>
                                        </div>
                                    @endif
                                </div>
                                <div class="form">
                                    <label>After Picture 1</label>
                                    <div class="picture-upload-box"
                                        onclick="document.getElementById('after_picture_1_input').click()">
                                        @if($inquiry->after_picture_1)
                                            @php
                                                $afterPicturePath = str_replace('storage/', '', $inquiry->after_picture_1);
                                                $afterPictureUrl = Storage::disk('public')->exists($afterPicturePath)
                                                    ? asset('storage/' . $afterPicturePath)
                                                    : asset('images/default-placeholder.png');
                                            @endphp
                                            <img src="{{ $afterPictureUrl }}" class="current-image-preview" alt="After">
                                        @else
                                            <i class="fas fa-cloud-upload-alt upload-icon"></i>
                                        @endif
                                        <div class="upload-text" id="after_picture_1_name">
                                            {{ $inquiry->after_picture_1 ? basename($inquiry->after_picture_1) : 'Choose
                                            After Picture' }}
                                        </div>
                                        <input type="file" name="after_picture_1" id="after_picture_1_input" class="d-none"
                                            accept="image/*">
                                    </div>
                                    @if($inquiry->after_picture_1)
                                        <div class="mt-2">
                                            <label class="custom-checkbox-container" style="margin-bottom: 0;">
                                                <input type="checkbox" name="remove_after_picture" value="1">
                                                <span class="checkbox-checkmark" style="height: 18px; width: 18px;"></span>
                                                <span class="checkbox-label" style="font-size: 13px;">Remove current
                                                    picture</span>
                                            </label>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="pro_filed mt-4">
                                <div class="form">
                                    <label>Before Picture 2</label>
                                    <div class="picture-upload-box"
                                        onclick="document.getElementById('before_picture_2_input').click()">
                                        @if($inquiry->before_picture_2)
                                            @php
                                                $beforePicturePath = str_replace('storage/', '', $inquiry->before_picture_2);
                                                $beforePictureUrl = Storage::disk('public')->exists($beforePicturePath)
                                                    ? asset('storage/' . $beforePicturePath)
                                                    : asset('images/default-placeholder.png');
                                            @endphp
                                            <img src="{{ $beforePictureUrl }}" class="current-image-preview" alt="Before">
                                        @else
                                            <i class="fas fa-cloud-upload-alt upload-icon"></i>
                                        @endif
                                        <div class="upload-text" id="before_picture_2_name">
                                            {{ $inquiry->before_picture_2 ? basename($inquiry->before_picture_2) : 'Choose
                                            Before Picture' }}
                                        </div>
                                        <input type="file" name="before_picture_2" id="before_picture_2_input"
                                            class="d-none" accept="image/*">
                                    </div>
                                </div>
                                <div class="form">
                                    <label>After Picture 2</label>
                                    <div class="picture-upload-box"
                                        onclick="document.getElementById('after_picture_2_input').click()">
                                        @if($inquiry->after_picture_2)
                                            @php
                                                $afterPicturePath = str_replace('storage/', '', $inquiry->after_picture_2);
                                                $afterPictureUrl = Storage::disk('public')->exists($afterPicturePath)
                                                    ? asset('storage/' . $afterPicturePath)
                                                    : asset('images/default-placeholder.png');
                                            @endphp
                                            <img src="{{ $afterPictureUrl }}" class="current-image-preview" alt="After">
                                        @else
                                            <i class="fas fa-cloud-upload-alt upload-icon"></i>
                                        @endif
                                        <div class="upload-text" id="after_picture_2_name">
                                            {{ $inquiry->after_picture_2 ? basename($inquiry->after_picture_2) : 'Choose
                                            After Picture' }}
                                        </div>
                                        <input type="file" name="after_picture_2" id="after_picture_2_input" class="d-none"
                                            accept="image/*">
                                    </div>
                                </div>
                            </div>

                            <div class="pro_filed mt-4">
                                <div class="form">
                                    <label>Before Picture 3</label>
                                    <div class="picture-upload-box"
                                        onclick="document.getElementById('before_picture_3_input').click()">
                                        @if($inquiry->before_picture_3)
                                            @php
                                                $beforePicturePath = str_replace('storage/', '', $inquiry->before_picture_3);
                                                $beforePictureUrl = Storage::disk('public')->exists($beforePicturePath)
                                                    ? asset('storage/' . $beforePicturePath)
                                                    : asset('images/default-placeholder.png');
                                            @endphp
                                            <img src="{{ $beforePictureUrl }}" class="current-image-preview" alt="Before">
                                        @else
                                            <i class="fas fa-cloud-upload-alt upload-icon"></i>
                                        @endif
                                        <div class="upload-text" id="before_picture_3_name">
                                            {{ $inquiry->before_picture_3 ? basename($inquiry->before_picture_3) : 'Choose
                                            Before Picture' }}
                                        </div>
                                        <input type="file" name="before_picture_3" id="before_picture_3_input"
                                            class="d-none" accept="image/*">
                                    </div>
                                </div>
                                <div class="form">
                                    <label>After Picture 3</label>
                                    <div class="picture-upload-box"
                                        onclick="document.getElementById('after_picture_3_input').click()">
                                        @if($inquiry->after_picture_3)
                                            @php
                                                $afterPicturePath = str_replace('storage/', '', $inquiry->after_picture_3);
                                                $afterPictureUrl = Storage::disk('public')->exists($afterPicturePath)
                                                    ? asset('storage/' . $afterPicturePath)
                                                    : asset('images/default-placeholder.png');
                                            @endphp
                                            <img src="{{ $afterPictureUrl }}" class="current-image-preview" alt="After">
                                        @else
                                            <i class="fas fa-cloud-upload-alt upload-icon"></i>
                                        @endif
                                        <div class="upload-text" id="after_picture_3_name">
                                            {{ $inquiry->after_picture_3 ? basename($inquiry->after_picture_3) : 'Choose
                                            After Picture' }}
                                        </div>
                                        <input type="file" name="after_picture_3" id="after_picture_3_input" class="d-none"
                                            accept="image/*">
                                    </div>
                                </div>
                            </div>

                            <div class="pro_filed mt-4">
                                <div class="form">
                                    <label>Before Picture 4</label>
                                    <div class="picture-upload-box"
                                        onclick="document.getElementById('before_picture_4_input').click()">
                                        @if($inquiry->before_picture_4)
                                            @php
                                                $beforePicturePath = str_replace('storage/', '', $inquiry->before_picture_4);
                                                $beforePictureUrl = Storage::disk('public')->exists($beforePicturePath)
                                                    ? asset('storage/' . $beforePicturePath)
                                                    : asset('images/default-placeholder.png');
                                            @endphp
                                            <img src="{{ $beforePictureUrl }}" class="current-image-preview" alt="Before">
                                        @else
                                            <i class="fas fa-cloud-upload-alt upload-icon"></i>
                                        @endif
                                        <div class="upload-text" id="before_picture_4_name">
                                            {{ $inquiry->before_picture_4 ? basename($inquiry->before_picture_4) : 'Choose
                                            Before Picture' }}
                                        </div>
                                        <input type="file" name="before_picture_4" id="before_picture_4_input"
                                            class="d-none" accept="image/*">
                                    </div>
                                </div>
                                <div class="form">
                                    <label>After Picture 4</label>
                                    <div class="picture-upload-box"
                                        onclick="document.getElementById('after_picture_4_input').click()">
                                        @if($inquiry->after_picture_4)
                                            @php
                                                $afterPicturePath = str_replace('storage/', '', $inquiry->after_picture_4);
                                                $afterPictureUrl = Storage::disk('public')->exists($afterPicturePath)
                                                    ? asset('storage/' . $afterPicturePath)
                                                    : asset('images/default-placeholder.png');
                                            @endphp
                                            <img src="{{ $afterPictureUrl }}" class="current-image-preview" alt="After">
                                        @else
                                            <i class="fas fa-cloud-upload-alt upload-icon"></i>
                                        @endif
                                        <div class="upload-text" id="after_picture_4_name">
                                            {{ $inquiry->after_picture_4 ? basename($inquiry->after_picture_4) : 'Choose
                                            After Picture' }}
                                        </div>
                                        <input type="file" name="after_picture_4" id="after_picture_4_input" class="d-none"
                                            accept="image/*">
                                    </div>
                                </div>
                            </div>

                            <div class="pro_filed mt-4">
                                <div class="form">
                                    <label>Before Picture 5</label>
                                    <div class="picture-upload-box"
                                        onclick="document.getElementById('before_picture_5_input').click()">
                                        @if($inquiry->before_picture_5)
                                            @php
                                                $beforePicturePath = str_replace('storage/', '', $inquiry->before_picture_5);
                                                $beforePictureUrl = Storage::disk('public')->exists($beforePicturePath)
                                                    ? asset('storage/' . $beforePicturePath)
                                                    : asset('images/default-placeholder.png');
                                            @endphp
                                            <img src="{{ $beforePictureUrl }}" class="current-image-preview" alt="Before">
                                        @else
                                            <i class="fas fa-cloud-upload-alt upload-icon"></i>
                                        @endif
                                        <div class="upload-text" id="before_picture_5_name">
                                            {{ $inquiry->before_picture_5 ? basename($inquiry->before_picture_5) : 'Choose
                                            Before Picture' }}
                                        </div>
                                        <input type="file" name="before_picture_5" id="before_picture_5_input"
                                            class="d-none" accept="image/*">
                                    </div>
                                </div>
                                <div class="form">
                                    <label>After Picture 5</label>
                                    <div class="picture-upload-box"
                                        onclick="document.getElementById('after_picture_5_input').click()">
                                        @if($inquiry->after_picture_5)
                                            @php
                                                $afterPicturePath = str_replace('storage/', '', $inquiry->after_picture_5);
                                                $afterPictureUrl = Storage::disk('public')->exists($afterPicturePath)
                                                    ? asset('storage/' . $afterPicturePath)
                                                    : asset('images/default-placeholder.png');
                                            @endphp
                                            <img src="{{ $afterPictureUrl }}" class="current-image-preview" alt="After">
                                        @else
                                            <i class="fas fa-cloud-upload-alt upload-icon"></i>
                                        @endif
                                        <div class="upload-text" id="after_picture_5_name">
                                            {{ $inquiry->after_picture_5 ? basename($inquiry->after_picture_5) : 'Choose
                                            After Picture' }}
                                        </div>
                                        <input type="file" name="after_picture_5" id="after_picture_5_input" class="d-none"
                                            accept="image/*">
                                    </div>
                                </div>
                            </div>

                        </div>

                        <!-- Form Actions -->
                        <div class="mt-5 text-center">
                            <button type="submit" class="btn btn-submit me-3">
                                <i class="fas fa-save me-2"></i> Update Inquiry
                            </button>
                            <a href="{{ route('lhr.pending') }}" class="btn btn-cancel">
                                <i class="fas fa-times me-2"></i> Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function toggleSection(header) {
            header.classList.toggle('active');
            header.classList.toggle('collapsed');
            const content = header.nextElementSibling;
            content.classList.toggle('collapsed');
        }

        document.addEventListener('DOMContentLoaded', function () {
            // Picture upload display
            const fileInputs = [
                { id: 'before_picture_1_input', textId: 'before_picture_1_name' },
                { id: 'after_picture_1_input', textId: 'after_picture_1_name' }
            ];

            fileInputs.forEach(file => {
                const input = document.getElementById(file.id);
                const text = document.getElementById(file.textId);
                if (input && text) {
                    input.addEventListener('change', function () {
                        if (this.files && this.files[0]) {
                            text.textContent = this.files[0].name;
                            text.style.color = 'var(--accent-solid)';
                            text.style.fontWeight = '600';
                        }
                    });
                }
            });

            // Form submission confirmation
            const inquiryForm = document.getElementById('inquiryForm');
            if (inquiryForm) {
                inquiryForm.addEventListener('submit', function (e) {
                    e.preventDefault();

                    Swal.fire({
                        title: 'Are you sure?',
                        text: "Do you want to update this inquiry?",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: 'var(--accent-solid)',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Yes, update it!',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Show loading
                            const submitBtn = this.querySelector('button[type="submit"]');
                            const originalText = submitBtn.innerHTML;
                            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Updating...';
                            submitBtn.disabled = true;

                            // Submit form
                            this.submit();
                        }
                    });
                });
            }

            // Payment Calculation
            const focCheckbox = document.getElementById('foc');
            const totalPayment = document.getElementById('total_payment');
            const discountPayment = document.getElementById('discount_payment');
            const givenPayment = document.getElementById('given_payment');
            const duePayment = document.getElementById('due_payment');
            const paymentMethod = document.getElementById('payment_method');
            const paymentAmount = document.getElementById('payment_amount');

            const paymentInputs = [totalPayment, discountPayment, givenPayment, paymentAmount];

            function calculateDue() {
                const total = parseFloat(totalPayment.value) || 0;
                const discount = parseFloat(discountPayment.value) || 0;
                const given = parseFloat(givenPayment.value) || 0;

                const due = (total - discount) - given;
                duePayment.value = Math.max(0, due).toFixed(2);

                duePayment.style.color = due > 0 ? '#ef4444' : 'var(--accent-solid)';
            }

            paymentInputs.forEach(input => {
                if (input && input !== duePayment) {
                    input.addEventListener('input', calculateDue);
                }
            });

            const separatePaymentSection = document.querySelector('.separate_payment');

            function handleFOC() {
                if (focCheckbox.checked) {
                    separatePaymentSection.style.display = 'none';
                    paymentInputs.forEach(field => { if (field) field.value = '0'; });
                    if (paymentMethod) paymentMethod.value = '';
                    duePayment.value = '0.00';
                } else {
                    separatePaymentSection.style.display = 'block';
                    paymentInputs.forEach(field => {
                        if (field && field.value === '0') {
                            // Attempt to restore original values if it's an edit, but for simplicity we'll just let them type
                        }
                    });
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

            calculateDue();

            // Auto-hide alerts
            setTimeout(() => {
                const alerts = document.querySelectorAll('.alert');
                alerts.forEach(alert => {
                    if (alert && alert.classList.contains('show')) {
                        const bsAlert = new bootstrap.Alert(alert);
                        bsAlert.close();
                    }
                });
            }, 5000);

            // Initialize Select2 for multiple selection
            if (typeof jQuery !== 'undefined' && typeof jQuery.fn.select2 !== 'undefined') {
                $('#area').select2({
                    placeholder: "Select Area",
                    allowClear: true,
                    width: '100%'
                });
            }
            // Toggle Area & Session based on Status
            const statusSelect = document.getElementById('status_name');
            const areaSessionSection = document.getElementById('area_session_section');
            if (statusSelect && areaSessionSection) {
                const toggleAreaSession = () => {
                    if (statusSelect.value === 'joined') {
                        areaSessionSection.style.display = 'block';
                    } else {
                        areaSessionSection.style.display = 'none';
                    }
                };
                statusSelect.addEventListener('change', toggleAreaSession);
                toggleAreaSession(); // Initial check
            }
        });
    </script>

    <!-- Add Select2 CSS and JS if not already loaded by layout -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <style>
        /* Custom Select2 styling to match theme */
        .select2-container--default .select2-selection--multiple {
            border: 1px solid #ced4da;
            border-radius: 4px;
            min-height: 44px;
            padding: 4px 8px;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: var(--accent-solid, #086838);
            border: 1px solid var(--accent-solid, #086838);
            color: white;
            border-radius: 4px;
            padding: 2px 6px;
            margin-top: 6px;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            color: white;
            margin-right: 5px;
            border-right: 1px solid rgba(255, 255, 255, 0.2);
            padding-right: 5px;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover {
            background: transparent;
            color: #ffdddd;
        }
    </style>

@endsection