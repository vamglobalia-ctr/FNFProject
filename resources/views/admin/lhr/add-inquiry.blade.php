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
            color: #0d9488;
            cursor: pointer;
            padding: 10px 15px;
            background: #f0fdfa;
            border-radius: 8px;
            transition: all 0.2s ease;
        }

        .section-divider:hover {
            background: #ccfbf1;
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
            background: white;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border: 1px solid #dee2e6;
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
            color: #5a6268;
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
            border: 1px solid #bdc3c7;
            border-radius: 8px;
            font-size: 14px;
            background: #f8fafc;
            transition: all 0.3s ease;
            outline: none;
        }

        input:focus,
        select:focus,
        textarea:focus {
            border-color: #067945;
            box-shadow: 0 0 0 3px rgba(6, 121, 69, 0.1);
            background: white;
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
            background: white;
            border: 2px solid #dee2e6;
            padding: 12px 35px;
            border-radius: 8px;
            font-weight: 600;
            color: #6c757d;
            transition: all 0.3s ease;
        }

        .btn-cancel:hover {
            background: #f8f9fa;
            border-color: #adb5bd;
            color: #495057;
        }

        .separate_payment {
            padding: 25px;
            border: 1px solid #e2f2ea !important;
            border-radius: 12px;
            background: #f9fdfc;
            margin-top: 20px;
        }

        .branch-info-card {
            background: #f9fdfc;
            border-radius: 12px;
            padding: 25px 40px;
            border: 1px solid #e6f4ed;
            margin-bottom: 30px;
        }

        .branch-info-label {
            font-size: 11px;
            color: #8c98a5;
            margin-bottom: 6px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .branch-info-value {
            font-weight: 700;
            color: #006637;
            font-size: 17px;
        }

        /* Page Title Styling */
        .page-title-box h4 {
            color: #006637;
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
            background-color: #fff;
            border: 2px solid #bdc3c7;
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
            font-weight: 500;
            color: #5a6268;
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
            background-color: #fff;
            border: 2px solid #bdc3c7;
            border-radius: 50%;
            display: inline-block;
            position: relative;
            margin-right: 10px;
            transition: all 0.3s ease;
        }

        .custom-radio-item:hover input~.radio-checkmark {
            border-color: #0d6efd;
        }

        .custom-radio-item input:checked~.radio-checkmark {
            background-color: #fff;
            border-color: #0d6efd;
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
            background: #0d6efd;
        }

        .custom-radio-item input:checked~.radio-checkmark:after {
            display: block;
        }

        .radio-label {
            font-weight: 500;
            color: #5a6268;
            font-size: 15px;
        }

        /* Picture Uploads */
        .picture-upload-box {
            background: #f8fafc;
            border: 2px dashed #e2e8f0;
            padding: 20px;
            border-radius: 12px;
            text-align: center;
            transition: all 0.3s ease;
            cursor: pointer;
        }

        .picture-upload-box:hover {
            border-color: #067945;
            background: #f0faf4;
        }

        .upload-icon {
            font-size: 24px;
            color: #94a3b8;
            margin-bottom: 10px;
        }

        .upload-text {
            font-size: 13px;
            color: #64748b;
            font-weight: 500;
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
                    <h4 class="mb-0">Add New Inquiry</h4>
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
            <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
                <i class="fas fa-exclamation-triangle me-2"></i> <strong>Please fix the following errors:</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="row">
            <div class="col-md-11 col-lg-10 m-auto">
                <div class="form-container">
                    <form id="inquiryForm" method="POST" action="{{ route('lhr.add.inquiry.store') }}"
                        enctype="multipart/form-data">
                        @csrf

                        <!-- Branch Information -->
                        <div class="branch-info-card">
                            <div class="pro_filed">
                                @if ($branchId)
                                    <div class="form">
                                        <div class="branch-info-label">Branch Name</div>
                                        <div class="branch-info-value">{{ $branchName }}</div>
                                        <input type="hidden" name="branch" value="{{ $branchName }}">
                                    </div>
                                    <div class="form">
                                        <div class="branch-info-label">Branch ID</div>
                                        <div class="branch-info-value">{{ $branchId }}</div>
                                        <input type="hidden" name="branch_id" value="{{ $branchId }}">
                                    </div>
                                @else
                                    <div class="form">
                                        <label for="branchName" class="required">Select Branch</label>
                                        <select id="branchName" name="branch_id" required>
                                            <option value="">Select Branch</option>
                                            @foreach($branches as $b)
                                                <option value="{{ $b->branch_id }}">{{ $b->branch_name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="form">
                                        <label for="branchId" class="required">Branch ID</label>
                                        <select id="branchId" name="branch" required>
                                            <option value="">Branch ID</option>
                                        </select>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="section-divider active" onclick="toggleSection(this)">Patient Information</div>
                        <div class="accordion-content">
                            <div class="pro_filed">
                                <div class="form">
                                    <label for="patient_name" class="required">Patient Name</label>
                                    <input type="text" id="patient_name" name="patient_name" required
                                        placeholder="Enter patient name" value="{{ old('patient_name') }}">
                                </div>
                                <div class="form">
                                    <label for="inquiry_date">Inquiry Date</label>
                                    <input type="date" id="inquiry_date" name="inquiry_date"
                                        value="{{ old('inquiry_date', date('Y-m-d')) }}">
                                </div>
                            </div>

                            <div class="pro_filed">
                                <div class="form">
                                    <label for="address">Address</label>
                                    <textarea id="address" name="address" rows="1"
                                        placeholder="Enter complete address">{{ old('address') }}</textarea>
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
                                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female
                                        </option>
                                        <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                                    </select>
                                </div>
                                <div class="form">
                                    <label for="age" class="required">Age</label>
                                    <input type="number" id="age" name="age" required min="1" max="120" placeholder="Age"
                                        value="{{ old('age') }}">
                                </div>
                            </div>

                            <div class="pro_filed">
                                <div class="form">
                                    <label for="staff_name">Staff Name</label>
                                    <input type="text" id="staff_name" name="staff_name" placeholder="Staff Name"
                                        value="{{ old('staff_name') }}">
                                </div>
                                <div class="form">
                                    <label for="status_name" class="required">Status</label>
                                    <select id="status_name" name="status_name" required>
                                        <option value="">Select Status</option>
                                        <option value="pending" {{ old('status_name') == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="joined" {{ old('status_name') == 'joined' ? 'selected' : '' }}>Joined</option>
                                    </select>
                                </div>
                            </div>

                            <div id="area_session_section" style="display: none;">
                                <div id="treatment_rows_container">
                                    <div class="treatment-row border-bottom pb-4 mb-4 position-relative">
                                        <div class="pro_filed">
                                            <div class="form">
                                                <label for="area" class="required">Select Program</label>
                                                <select name="area[0][]" multiple class="form-control select2-area">
                                                    @foreach($programs as $program)
                                                        <option value="{{ $program->program_name }}" data-short-name="{{ $program->program_short_name }}">{{ $program->program_name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            <div class="form">
                                                <label for="session">Session</label>
                                                <input type="number" name="session[]" placeholder="Enter session details"
                                                    value="{{ old('session.0') }}">
                                            </div>
                                        </div>

                                        <div class="pro_filed">
                                            <div class="form">
                                                <label for="area_code">Area Code</label>
                                                <input type="text" name="area_code[]" placeholder="Area Code"
                                                    value="{{ old('area_code.0') }}" class="area-code-input">
                                            </div>
                                            <div class="form">
                                                <label for="energy">Energy</label>
                                                <input type="text" name="energy[]" placeholder="Energy"
                                                    value="{{ old('energy.0') }}">
                                            </div>
                                        </div>

                                        <div class="pro_filed">
                                            <div class="form">
                                                <label for="frequency">Frequency</label>
                                                <input type="number" name="frequency[]" placeholder="Frequency"
                                                    value="{{ old('frequency.0') }}">
                                            </div>
                                            <div class="form">
                                                <label for="shot">Shot</label>
                                                <input type="text" name="shot[]" placeholder="Shot"
                                                    value="{{ old('shot.0') }}">
                                            </div>
                                        </div>
                                        
                                        <button type="button" class="btn btn-danger btn-sm remove-row-btn position-absolute" style="top: -10px; right: 0; display: none; border-radius: 50%; width: 30px; height: 30px;">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="text-end mb-4">
                                    <button type="button" id="add_treatment_row" class="btn btn-success btn-sm">
                                        <i class="fas fa-plus me-1"></i> Add More Treatment
                                    </button>
                                </div>
                            </div>



                        </div>

                        <div class="section-divider collapsed" onclick="toggleSection(this)">Medical Information</div>
                        <div class="accordion-content collapsed">
                            <div class="mb-4">
                                <label>Do you have any hormonal issues?</label>
                                <div class="custom-radio-group">
                                    <label class="custom-radio-item">
                                        <input type="radio" name="hormonal_issues" value="yes" {{ old('hormonal_issues') == 'yes' ? 'checked' : '' }}>
                                        <span class="radio-checkmark"></span>
                                        <span class="radio-label">Yes</span>
                                    </label>
                                    <label class="custom-radio-item">
                                        <input type="radio" name="hormonal_issues" value="no" {{ old('hormonal_issues') == 'no' || !old('hormonal_issues') ? 'checked' : '' }}>
                                        <span class="radio-checkmark"></span>
                                        <span class="radio-label">No</span>
                                    </label>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label>Any medication or treatment for hair loss?</label>
                                <div class="custom-radio-group">
                                    <label class="custom-radio-item">
                                        <input type="radio" name="medication" value="yes" {{ old('medication') == 'yes' ? 'checked' : '' }}>
                                        <span class="radio-checkmark"></span>
                                        <span class="radio-label">Yes</span>
                                    </label>
                                    <label class="custom-radio-item">
                                        <input type="radio" name="medication" value="no" {{ old('medication') == 'no' || !old('medication') ? 'checked' : '' }}>
                                        <span class="radio-checkmark"></span>
                                        <span class="radio-label">No</span>
                                    </label>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label>Before you took hair treatment from somewhere else?</label>
                                <div class="custom-radio-group">
                                    <label class="custom-radio-item">
                                        <input type="radio" name="previous_treatment" value="yes" {{ old('previous_treatment') == 'yes' ? 'checked' : '' }}>
                                        <span class="radio-checkmark"></span>
                                        <span class="radio-label">Yes</span>
                                    </label>
                                    <label class="custom-radio-item">
                                        <input type="radio" name="previous_treatment" value="no" {{ old('previous_treatment') == 'no' || !old('previous_treatment') ? 'checked' : '' }}>
                                        <span class="radio-checkmark"></span>
                                        <span class="radio-label">No</span>
                                    </label>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label>PCOD, Thyroid Issue?</label>
                                <div class="custom-radio-group">
                                    <label class="custom-radio-item">
                                        <input type="radio" name="pcod_thyroid" value="yes" {{ old('pcod_thyroid') == 'yes' ? 'checked' : '' }}>
                                        <span class="radio-checkmark"></span>
                                        <span class="radio-label">Yes</span>
                                    </label>
                                    <label class="custom-radio-item">
                                        <input type="radio" name="pcod_thyroid" value="no" {{ old('pcod_thyroid') == 'no' || !old('pcod_thyroid') ? 'checked' : '' }}>
                                        <span class="radio-checkmark"></span>
                                        <span class="radio-label">No</span>
                                    </label>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label>Do you suffer from any skin conditions, allergies, or diseases?</label>
                                <div class="custom-radio-group">
                                    <label class="custom-radio-item">
                                        <input type="radio" name="skin_conditions" value="yes" {{ old('skin_conditions') == 'yes' ? 'checked' : '' }}>
                                        <span class="radio-checkmark"></span>
                                        <span class="radio-label">Yes</span>
                                    </label>
                                    <label class="custom-radio-item">
                                        <input type="radio" name="skin_conditions" value="no" {{ old('skin_conditions') == 'no' || !old('skin_conditions') ? 'checked' : '' }}>
                                        <span class="radio-checkmark"></span>
                                        <span class="radio-label">No</span>
                                    </label>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label>Which procedure are you currently utilizing for hair removal?</label>
                                <div class="d-flex gap-4">
                                    <label class="custom-checkbox-container">
                                        <input type="checkbox" name="procedure[]" value="waxing" {{ is_array(old('procedure')) && in_array('waxing', old('procedure')) ? 'checked' : '' }}>
                                        <span class="checkbox-checkmark"></span>
                                        <span class="checkbox-label">Waxing</span>
                                    </label>
                                    <label class="custom-checkbox-container">
                                        <input type="checkbox" name="procedure[]" value="threading" {{ is_array(old('procedure')) && in_array('threading', old('procedure')) ? 'checked' : '' }}>
                                        <span class="checkbox-checkmark"></span>
                                        <span class="checkbox-label">Threading</span>
                                    </label>
                                    <label class="custom-checkbox-container">
                                        <input type="checkbox" name="procedure[]" value="cream" {{ is_array(old('procedure')) && in_array('cream', old('procedure')) ? 'checked' : '' }}>
                                        <span class="checkbox-checkmark"></span>
                                        <span class="checkbox-label">Cream</span>
                                    </label>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label>Are there any ongoing skin treatments?</label>
                                <div class="custom-radio-group">
                                    <label class="custom-radio-item">
                                        <input type="radio" name="ongoing_treatments" value="yes" {{ old('ongoing_treatments') == 'yes' ? 'checked' : '' }}>
                                        <span class="radio-checkmark"></span>
                                        <span class="radio-label">Yes</span>
                                    </label>
                                    <label class="custom-radio-item">
                                        <input type="radio" name="ongoing_treatments" value="no" {{ old('ongoing_treatments') == 'no' || !old('ongoing_treatments') ? 'checked' : '' }}>
                                        <span class="radio-checkmark"></span>
                                        <span class="radio-label">No</span>
                                    </label>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label>Does your body have any implantations or tattoos?</label>
                                <div class="custom-radio-group">
                                    <label class="custom-radio-item">
                                        <input type="radio" name="implants_tattoos" value="yes" {{ old('implants_tattoos') == 'yes' ? 'checked' : '' }}>
                                        <span class="radio-checkmark"></span>
                                        <span class="radio-label">Yes</span>
                                    </label>
                                    <label class="custom-radio-item">
                                        <input type="radio" name="implants_tattoos" value="no" {{ old('implants_tattoos') == 'no' || !old('implants_tattoos') ? 'checked' : '' }}>
                                        <span class="radio-checkmark"></span>
                                        <span class="radio-label">No</span>
                                    </label>
                                </div>
                            </div>
                            </div>
                        </div>

                        <div class="section-divider active" onclick="toggleSection(this)">Health Metrics</div>
                        <div class="accordion-content">
                            <div class="pro_filed">
                                <div class="form">
                                    <label for="diet">Diet</label>
                                    <input type="text" id="diet" name="diet" placeholder="Diet"
                                        value="{{ old('diet') }}">
                                </div>
                                <div class="form">
                                    <label for="exercise">Exercise</label>
                                    <input type="text" id="exercise" name="exercise" placeholder="Exercise"
                                        value="{{ old('exercise') }}">
                                </div>
                            </div>
                            <div class="pro_filed">
                                <div class="form">
                                    <label for="sleep">Sleep</label>
                                    <input type="text" id="sleep" name="sleep" placeholder="Sleep"
                                        value="{{ old('sleep') }}">
                                </div>
                                <div class="form">
                                    <label for="water">Water</label>
                                    <input type="text" id="water" name="water" placeholder="Water"
                                        value="{{ old('water') }}">
                                </div>
                            </div>
                        </div>

                        <div class="section-divider collapsed" onclick="toggleSection(this)">Follow Up & Notes</div>
                        <div class="accordion-content collapsed">
                            <div class="pro_filed">
                                <div class="form">
                                    <label for="reference_by">Reference By</label>
                                    <input type="text" id="reference_by" name="reference_by" placeholder="Reference By"
                                        value="{{ old('reference_by') }}">
                                </div>
                            </div>

                            <div class="pro_filed">
                                <div class="form">
                                    <label for="notes">Notes</label>
                                    <textarea id="notes" name="notes" rows="2"
                                        placeholder="Enter notes">{{ old('notes') }}</textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Form Actions -->
                        <div class="mt-5 text-center">
                            <button type="submit" class="btn btn-submit me-3">
                                <i class="fas fa-save me-2"></i> Submit Inquiry
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
            const branches = @json($branches ?? []);
            const branchNameSelect = document.getElementById('branchName');
            const branchIdSelect = document.getElementById('branchId');

            if (branchNameSelect && branchIdSelect) {
                branchNameSelect.addEventListener('change', function () {
                    const selectedId = this.value;
                    branchIdSelect.innerHTML = '<option value="">Branch ID</option>';
                    if (selectedId) {
                        const branch = branches.find(b => b.branch_id === selectedId);
                        if (branch) {
                            const option = document.createElement('option');
                            option.value = branch.branch_id;
                            option.text = branch.branch_id;
                            branchIdSelect.appendChild(option);
                            branchIdSelect.value = branch.branch_id;
                        }
                    }
                });
            }

            // Form submission confirmation
            const inquiryForm = document.getElementById('inquiryForm');
            if (inquiryForm) {
                inquiryForm.addEventListener('submit', function (e) {
                    e.preventDefault();

                    Swal.fire({
                        title: 'Are you sure?',
                        text: "Do you want to submit this inquiry?",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#086838',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Yes, submit it!',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Show loading
                            const submitBtn = inquiryForm.querySelector('button[type="submit"]');
                            if (submitBtn) {
                                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Submitting...';
                                submitBtn.disabled = true;
                            }

                            // Submit form
                            inquiryForm.submit();
                        }
                    });
                });
            }

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
            function initSelect2(element) {
                $(element).select2({
                    placeholder: "Select Program",
                    allowClear: true,
                    width: '100%'
                });
            }

            $('.select2-area').each(function() {
                initSelect2(this);
            });

            // Auto-fill Area Code based on Program selection
            $(document).on('change', '.select2-area', function() {
                const selectedOptions = $(this).find('option:selected');
                const row = $(this).closest('.treatment-row');
                const areaCodeInput = row.find('.area-code-input');
                
                let shortNames = [];
                selectedOptions.each(function() {
                    const shortName = $(this).data('short-name');
                    if (shortName) {
                        shortNames.push(shortName);
                    }
                });
                
                areaCodeInput.val(shortNames.join(', '));
            });

            // Add Treatment Row
            let rowCount = 1;
            const container = document.getElementById('treatment_rows_container');
            const addBtn = document.getElementById('add_treatment_row');

            addBtn.addEventListener('click', function() {
                const firstRow = container.querySelector('.treatment-row');
                
                // If Select2 is initialized, we should destroy it before cloning to avoid cloning Select2 markup
                const existingSelect = $(firstRow).find('.select2-area');
                if (existingSelect.data('select2')) {
                    existingSelect.select2('destroy');
                }

                const newRow = firstRow.cloneNode(true);
                
                // Re-initialize Select2 on the first row
                initSelect2(existingSelect);
                
                // Reset inputs and update names
                newRow.querySelectorAll('input').forEach(input => {
                    input.value = '';
                    input.setAttribute('id', ''); // Clear IDs to avoid duplicates
                });
                
                const selectElement = newRow.querySelector('select');
                $(selectElement).attr('name', `area[${rowCount}][]`);
                $(selectElement).val(null).trigger('change');
                
                // Show remove button
                newRow.querySelector('.remove-row-btn').style.display = 'block';
                
                container.appendChild(newRow);
                initSelect2(selectElement);
                rowCount++;
            });

            // Remove Treatment Row
            container.addEventListener('click', function(e) {
                if (e.target.closest('.remove-row-btn')) {
                    e.target.closest('.treatment-row').remove();
                }
            });

            // Toggle Area & Session based on Status
            const statusSelect = document.getElementById('status_name');
            const areaSessionSection = document.getElementById('area_session_section');
            if (statusSelect && areaSessionSection) {
                const toggleAreaSession = () => {
                    const isJoined = statusSelect.value === 'joined';
                    if (isJoined) {
                        areaSessionSection.style.display = 'block';
                    } else {
                        areaSessionSection.style.display = 'none';
                    }

                    // Dynamically set/remove required attribute and labels
                    const areaSelects = areaSessionSection.querySelectorAll('.select2-area');
                    const sessionInputs = areaSessionSection.querySelectorAll('input[name="session[]"]');
                    const sessionLabels = areaSessionSection.querySelectorAll('label[for="session"]');

                    areaSelects.forEach(sel => {
                        sel.required = isJoined;
                    });
                    sessionInputs.forEach(input => {
                        input.required = isJoined;
                    });
                    sessionLabels.forEach(label => {
                        if (isJoined) label.classList.add('required');
                        else label.classList.remove('required');
                    });
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
            background-color: #086838;
            border: 1px solid #086838;
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