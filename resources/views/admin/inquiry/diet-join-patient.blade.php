@extends('admin.layouts.layouts')

@section('title', 'Diet Chart - ' . ($patient->patient_name ?? 'Patient'))

@section('content')
    <div class="container">
        <div class="row mb-4">
            <div class="col-md-12">
                <h2 class="mb-0" style="color: var(--accent-solid);">
                    <i class="fas fa-chart-line"></i> Diet Chart - {{ $patient->patient_name ?? 'Patient' }}
                </h2>
            </div>
        </div>

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

            /* Diet History Section Styles */
            .diet-history-container {
                padding: 10px 0;
            }
            .diet-history-header {
                text-align: center;
                color: #28a745;
                font-weight: 800;
                font-size: 1.5rem;
                margin: 30px 0 20px;
                display: flex;
                align-items: center;
                justify-content: center;
                gap: 15px;
                text-transform: uppercase;
                letter-spacing: 1px;
            }
            .diet-history-header i {
                font-size: 1.2rem;
            }
            .diet-line {
                display: flex;
                align-items: center;
                gap: 10px;
                margin-bottom: 15px;
                flex-wrap: wrap;
                width: 100%;
            }
            .diet-line label {
                margin-bottom: 0 !important;
                white-space: nowrap;
                color: #28a745 !important;
                font-weight: 700 !important;
                font-size: 14px;
            }
            .form-control-line {
                border: none !important;
                border-bottom: 1px solid #28a745 !important;
                border-radius: 0 !important;
                padding: 2px 5px !important;
                flex-grow: 1;
                background: transparent !important;
                font-size: 14px;
                min-width: 50px;
                color: #333;
            }
            .form-control-line:focus {
                outline: none !important;
                border-bottom: 2px solid #28a745 !important;
                box-shadow: none !important;
            }
            .diet-checkbox-group {
                display: flex;
                gap: 12px;
                align-items: center;
            }
            .diet-checkbox-group .form-check-label {
                font-weight: 600 !important;
                color: #28a745 !important;
                font-size: 13px;
                margin-bottom: 0 !important;
            }
            .diet-checkbox-group .form-check-input:checked {
                background-color: #28a745;
                border-color: #28a745;
            }

            /* Upper Section Metric Styles */
            .diet-metrics-row {
                display: flex;
                justify-content: space-between;
                gap: 20px;
                margin-bottom: 25px;
                padding-bottom: 15px;
            }
            .diet-metric-box {
                flex: 1;
                text-align: center;
            }
            .diet-metric-box label {
                display: block;
                color: #28a745 !important;
                font-weight: 800 !important;
                font-size: 15px;
                margin-bottom: 8px !important;
                text-transform: capitalize;
            }
            .diet-metric-input {
                border: none !important;
                border-bottom: 2px solid #28a745 !important;
                width: 100%;
                text-align: center;
                background: transparent !important;
                font-weight: 700;
                font-size: 16px;
                padding: 4px 0;
            }
            .diet-metric-input:focus {
                outline: none !important;
                border-bottom: 3px solid #2e7d32 !important;
            }

            /* ── Mobile Responsive ── */
            @media (max-width: 768px) {
                .diet-metrics-row {
                    flex-wrap: wrap;
                    gap: 12px;
                }
                .diet-metric-box {
                    flex: 0 0 calc(50% - 8px);
                    min-width: 0;
                }
                .diet-metric-box:last-child {
                    flex: 0 0 100%;
                }
                .diet-line {
                    flex-direction: column !important;
                    align-items: flex-start !important;
                    gap: 8px !important;
                }
                .diet-line .form-control-line {
                    width: 100% !important;
                }
                .diet-checkbox-group {
                    flex-wrap: wrap;
                }
            }

            .lipid-profile-header {
                display: flex;
                align-items: center;
                gap: 10px;
                margin: 25px 0 15px;
                width: 100%;
            }
            .lipid-profile-header label {
                margin: 0 !important;
                color: #28a745 !important;
                font-weight: 800 !important;
                font-size: 16px;
                white-space: nowrap;
            }
            .lipid-header-line {
                flex-grow: 1;
                height: 1.5px;
                background-color: #28a745;
            }

            /* Adjust card for cleaner look */
            .card {
                border: 1px solid #e0e0e0;
                border-radius: 12px;
                box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            }
        </style>
        <form action="{{ route('save.diet.chart') }}" method="POST" id="dietChartForm" enctype="multipart/form-data">
            @csrf


            <input type="hidden" name="patient_id" value="{{ $patient->patient_id ?? '' }}">
            <input type="hidden" name="patient_name" value="{{ $patient->patient_name ?? '' }}">
            <input type="hidden" name="branch_id" value="{{ $patient->branch_id ?? '' }}">
            <input type="hidden" name="branch" value="{{ $patient->branch ?? '' }}">
            <input type="hidden" name="latest_opt_id" value="{{ $latestOpt->id ?? '' }}">

            <div class="card">
                <div class="card-body">
                    <!-- Patient ID, Name, Branch Row (Hidden or Compact) -->
                    <div class="row mb-4 opacity-75">
                        <div class="col-md-3">
                            <small class="text-muted">ID: {{ $patient->patient_id ?? 'N/A' }}</small>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">Name: {{ $patient->patient_f_name ?? 'N/A' }}</small>
                        </div>
                        <div class="col-md-3">
                            <small class="text-muted">Branch: {{ $patient->branch ?? 'N/A' }}</small>
                        </div>
                        <div class="col-md-3 text-end">
                            <div class="diet-line" style="justify-content: flex-end; margin-bottom: 0;">
                                <label>Date:</label>
                                <input type="date" name="pod_bd_date" class="form-control-line" style="max-width: 150px;" value="{{ date('Y-m-d') }}">
                            </div>
                        </div>
                    </div>

                    <!-- Top Metrics Section: Height, Weight, BMI, BMR, Calories req. -->
                    <div class="diet-metrics-row">
                        <div class="diet-metric-box">
                            <label>Height (cm)</label>
                            <input type="number" step="0.01" name="pod_data" id="heightInput" class="diet-metric-input" value="{{ $latestMeta['pod_data'] ?? $initialMeasurements['height'] ?? '' }}" placeholder="{{ $initialMeasurements['height'] ?? 'Enter height' }}">
                            @if(isset($initialMeasurements['height']))
                                <small class="text-muted d-block mt-1" style="font-size: 11px;">Initial: {{ $initialMeasurements['height'] }} cm ({{ $initialMeasurements['entry_date'] ?? 'First entry' }})</small>
                            @endif
                        </div>
                        <div class="diet-metric-box">
                            <label>Weight (kg)</label>
                            <input type="number" step="0.01" name="pod_bdy_weight" id="weightInput" class="diet-metric-input" value="{{ $latestMeta['pod_bdy_weight'] ?? $initialMeasurements['weight'] ?? '' }}" placeholder="{{ $initialMeasurements['weight'] ?? 'Enter weight' }}">
                            @if(isset($initialMeasurements['weight']))
                                <small class="text-muted d-block mt-1" style="font-size: 11px;">Initial: {{ $initialMeasurements['weight'] }} kg ({{ $initialMeasurements['entry_date'] ?? 'First entry' }})</small>
                            @endif  
                        </div>
                        <div class="diet-metric-box">
                            <label>BMI</label>
                            <input type="text" name="pod_bmr" id="bmiInput" class="diet-metric-input" value="{{ $latestMeta['pod_bmr'] ?? $initialMeasurements['bmi'] ?? '' }}" readonly placeholder="{{ $initialMeasurements['bmi'] ?? 'Auto-calculated' }}">
                            @if(isset($initialMeasurements['bmi']))
                                <small class="text-muted d-block mt-1" style="font-size: 11px;">Initial: {{ $initialMeasurements['bmi'] }} ({{ $initialMeasurements['entry_date'] ?? 'First entry' }})</small>
                            @endif
                        </div>
                        <div class="diet-metric-box">
                            <label>BMR</label>      
                            <input type="text" name="pod_bmr_value" class="diet-metric-input" value="{{ $latestMeta['pod_bmr_value'] ?? '' }}">
                        </div>
                        <div class="diet-metric-box">
                            <label>Calories req.</label>
                            <input type="text" name="pod_calories" class="diet-metric-input" value="{{ $latestMeta['pod_calories'] ?? '' }}">
                        </div>
                    </div>

                    <!-- Clinical Parameters (Linear Layout) -->
                    <div class="diet-history-container">
                        <!-- Row 1: Over Weight, Ideal Weight, Under Weight, Target Weight (Auto-calculated) -->
                        <div class="diet-line">
                            <label>Over Weight :</label>
                            <input type="text" name="over_weight" id="overWeightInput" class="form-control-line" value="{{ $latestMeta['over_weight'] ?? '' }}" placeholder="Auto" readonly>
                            
                            <label>Ideal Body Weight :</label>
                            <input type="text" name="lead_body_weight" id="idealWeightInput" class="form-control-line" value="{{ $latestMeta['lead_body_weight'] ?? '' }}" placeholder="Auto" readonly>
                            
                            <label>Under Weight :</label>
                            <input type="text" name="under_weight" id="underWeightInput" class="form-control-line" value="{{ $latestMeta['under_weight'] ?? '' }}" placeholder="Auto" readonly>
                            
                            <label>Target Weight :</label>
                            <input type="text" name="target_weight" id="targetWeightInput" class="form-control-line" value="{{ $latestMeta['target_weight'] ?? '' }}" placeholder="Auto" readonly>
                        </div>

                        <!-- Row 3: Package, Birth Date, Validity Date, Bg-Rh -->
                        <div class="diet-line">
                          
                            
                            <label>Birth Date :</label>
                            <input type="date" name="birth_date" class="form-control-line" value="{{ $latestMeta['birth_date'] ?? '' }}">
                            
                            <label>Validity Date :</label>
                            <input type="date" name="validity_date" class="form-control-line" value="{{ $latestMeta['validity_date'] ?? '' }}">
                            
                            <label>Bg-Rh :</label>
                            <select name="bg_rh" class="form-control-line">
                                <option value="" {{ ($latestMeta['bg_rh'] ?? '') == '' ? 'selected' : '' }}>Select Blood Group</option>
                                <option value="A+" {{ ($latestMeta['bg_rh'] ?? '') == 'A+' ? 'selected' : '' }}>A+</option>
                                <option value="A-" {{ ($latestMeta['bg_rh'] ?? '') == 'A-' ? 'selected' : '' }}>A-</option>
                                <option value="B+" {{ ($latestMeta['bg_rh'] ?? '') == 'B+' ? 'selected' : '' }}>B+</option>
                                <option value="B-" {{ ($latestMeta['bg_rh'] ?? '') == 'B-' ? 'selected' : '' }}>B-</option>
                                <option value="AB+" {{ ($latestMeta['bg_rh'] ?? '') == 'AB+' ? 'selected' : '' }}>AB+</option>
                                <option value="AB-" {{ ($latestMeta['bg_rh'] ?? '') == 'AB-' ? 'selected' : '' }}>AB-</option>
                                <option value="O+" {{ ($latestMeta['bg_rh'] ?? '') == 'O+' ? 'selected' : '' }}>O+</option>
                                <option value="O-" {{ ($latestMeta['bg_rh'] ?? '') == 'O-' ? 'selected' : '' }}>O-</option>
                            </select>
                        </div>

                        <!-- Row 4: PA/H, F/H, LMP -->
                        <div class="diet-line">
                            <label>PA/H:</label>
                            <input type="text" name="pa_h" class="form-control-line" value="{{ $latestMeta['pa_h'] ?? '' }}">
                            
                            <label>F/H:</label>
                            <input type="text" name="pod_fh" class="form-control-line" value="{{ $latestMeta['pod_fh'] ?? '' }}" placeholder="DM or family history">

                            <label>Body LMP :</label>
                            <input type="date" name="pod_bdy_lmp" class="form-control-line" value="{{ $latestMeta['pod_bdy_lmp'] ?? '' }}">
                        </div>

                        <!-- Row 6: Any Medication -->
                        <div class="diet-line">
                            <label>Any Medication:</label>
                            <textarea name="pod_medication" class="form-control-line" rows="3">{{ $latestMeta['pod_medication'] ?? '' }}</textarea>
                        </div>

                        <!-- Laboratory Investigation Section -->
                        <div class="mt-4">
                            <h4 class="border-bottom pb-2 mb-3">
                                <i class="fas fa-flask"></i> Laboratory Investigation
                            </h4>
                            
                            <div class="row mb-3">
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">S. Insulin</label>
                                    <input type="text" id="s_insulin" name="s_insulin" class="form-control"
                                        placeholder="Enter S. Insulin value" value="{{ $latestMeta['s_insulin'] ?? '' }}">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">SGPT</label>
                                    <input type="text" id="sgpt" name="sgpt" class="form-control"
                                        placeholder="Enter SGPT value" value="{{ $latestMeta['sgpt'] ?? '' }}">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">S. Creatinine</label>
                                    <input type="text" id="s_creatinine" name="s_creatinine" class="form-control"
                                        placeholder="Enter S. Creatinine value" value="{{ $latestMeta['s_creatinine'] ?? '' }}">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">S. Uric Acid</label>
                                    <input type="text" id="s_uric_acid" name="s_uric_acid" class="form-control"
                                        placeholder="Enter S. Uric Acid value" value="{{ $latestMeta['s_uric_acid'] ?? '' }}">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">RA Test</label>
                                    <input type="text" id="ra_test" name="ra_test" class="form-control"
                                        placeholder="Enter RA Test value" value="{{ $latestMeta['ra_test'] ?? '' }}">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">USG Abdomen</label>
                                    <textarea id="usg_abdomen" name="usg_abdomen" class="form-control" rows="3"
                                        placeholder="Enter USG Abdomen details">{{ $latestMeta['usg_abdomen'] ?? '' }}</textarea>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Chest X-ray</label>
                                    <textarea id="chest_xray" name="chest_xray" class="form-control" rows="3"
                                        placeholder="Enter Chest X-ray details">{{ $latestMeta['chest_xray'] ?? '' }}</textarea>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">MRI-CT Scan</label>
                                    <textarea id="mri_ct_scan" name="mri_ct_scan" class="form-control" rows="3"
                                        placeholder="Enter MRI-CT Scan details">{{ $latestMeta['mri_ct_scan'] ?? '' }}</textarea>
                                </div>
                            </div>

                            <!-- Lipid Profile Section Inside Laboratory Investigation -->
                            <div class="lipid-profile-header mt-3">
                                <label>Lipid Profile :</label>
                                <div class="lipid-header-line"></div>
                            </div>

                            <div class="row mb-3 mt-2">
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">S.Cholesterol</label>
                                    <input type="text" name="s_cholesterol" class="form-control" value="{{ $latestMeta['s_cholesterol'] ?? '' }}" placeholder="Enter S.Cholesterol">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">S.Triglycerides</label>
                                    <input type="text" name="s_triglycerides" class="form-control" value="{{ $latestMeta['s_triglycerides'] ?? '' }}" placeholder="Enter S.Triglycerides">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">HDL</label>
                                    <input type="text" name="hdl" class="form-control" value="{{ $latestMeta['hdl'] ?? '' }}" placeholder="Enter HDL">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">LDL</label>
                                    <input type="text" name="ldl" class="form-control" value="{{ $latestMeta['ldl'] ?? '' }}" placeholder="Enter LDL">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">VLDL</label>
                                    <input type="text" name="vldl" class="form-control" value="{{ $latestMeta['vldl'] ?? '' }}" placeholder="Enter VLDL">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Non-HDL C</label>
                                    <input type="text" name="non_hdl_c" class="form-control" value="{{ $latestMeta['non_hdl_c'] ?? '' }}" placeholder="Enter Non-HDL C">
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Chol/HDL</label>
                                    <input type="text" name="chol_hdl_ratio" class="form-control" value="{{ $latestMeta['chol_hdl_ratio'] ?? '' }}" placeholder="Enter Chol/HDL ratio">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Assigned Programs Section -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <h4 class="border-bottom pb-2 mb-3">Assigned Programs</h4>

                            <!-- Main Program Row -->
                            <div class="program-rows-container">
                                @php
                                    $savedPrograms = [];
                                    if (isset($latestMeta['programs_array'])) {
                                        $savedPrograms = json_decode($latestMeta['programs_array'], true) ?: [];
                                    }
                                @endphp

                                @if(empty($savedPrograms))
                                    <!-- First Row (Default if none exist) -->
                                    <div class="program-row mb-3 permanent-row">
                                        <div class="row">
                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Select Program</label>
                                                <select class="form-select program-select" name="selected_program[]">
                                                    <option value="">Select Program</option>
                                                    @foreach($available_programs as $prog)
                                                        <option value="{{ $prog->program_name }}" {{ ($latestMeta['selected_program'] ?? '') == $prog->program_name ? 'selected' : '' }}>
                                                            {{ $prog->program_name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>

                                            <div class="col-md-4 mb-3">
                                                <label class="form-label">Session</label>
                                                <input type="text" class="form-control session-input" name="session[]"
                                                    value="{{ $latestMeta['session'] ?? '' }}"
                                                    placeholder="Enter session details">
                                            </div>

                                            <div class="col-md-4 mb-3">
                                                <div class="d-flex align-items-end">
                                                    <div class="flex-grow-1">
                                                        <label class="form-label">Months</label>
                                                        <input type="number" class="form-control months-input"
                                                            name="months[]" value="{{ $latestMeta['months'] ?? '' }}"
                                                            placeholder="Enter number of months"
                                                            min="1">
                                                    </div>
                                                    <div class="ms-2 d-flex" style="padding-bottom: 8px;">
                                                        <button type="button" class="btn btn-success btn-sm add-program-btn"
                                                            onclick="addProgramRow()">
                                                            <i class="fas fa-plus"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    @foreach($savedPrograms as $index => $prog_data)
                                        <div class="program-row mb-3 {{ $index == 0 ? 'permanent-row' : 'additional-program-row' }}">
                                            <div class="row">
                                                <div class="col-md-4 mb-3">
                                                    <label class="form-label">Select Program</label>
                                                    <select class="form-select program-select" name="selected_program[]">
                                                        <option value="">Select Program</option>
                                                        @foreach($available_programs as $prog)
                                                            <option value="{{ $prog->program_name }}" {{ ($prog_data['program'] ?? '') == $prog->program_name ? 'selected' : '' }}>
                                                                {{ $prog->program_name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <div class="col-md-4 mb-3">
                                                    <label class="form-label">Session</label>
                                                    <input type="text" class="form-control session-input" name="session[]"
                                                        value="{{ $prog_data['session'] ?? '' }}"
                                                        placeholder="Enter session details">
                                                </div>

                                                <div class="col-md-4 mb-3">
                                                    <div class="d-flex align-items-end">
                                                        <div class="flex-grow-1">
                                                            <label class="form-label">Months</label>
                                                            <input type="number" class="form-control months-input"
                                                                name="months[]" value="{{ $prog_data['months'] ?? '' }}"
                                                                placeholder="Enter number of months"
                                                                min="1">
                                                        </div>
                                                        <div class="ms-2 d-flex" style="padding-bottom: 8px;">
                                                            <button type="button" class="btn btn-success btn-sm add-program-btn me-1"
                                                                onclick="addProgramRow()">
                                                                <i class="fas fa-plus"></i>
                                                            </button>
                                                            @if($index > 0)
                                                                <button type="button" class="btn btn-danger btn-sm remove-program-btn"
                                                                    onclick="removeProgramRow(this)">
                                                                    <i class="fas fa-minus"></i>
                                                                </button>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @endif
                                <!-- Additional rows will be inserted here -->
                            </div>

                            <!-- Other payment fields -->
                            <!-- <div class="row">
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Position</label>
                                    <input type="text" class="form-control" name="position"
                                        placeholder="Enter position">
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Total Payment</label>
                                    <input type="number" step="0.01" class="form-control" name="total_payment"
                                        placeholder="Enter total payment">
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Discount Payment</label>
                                    <input type="number" step="0.01" class="form-control" name="discount_payment"
                                        placeholder="Enter discount payment">
                                </div>

                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Given Payment</label>
                                    <input type="number" step="0.01" class="form-control" name="given_payment"
                                        placeholder="Enter given payment">
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Due Payment</label>
                                    <input type="number" step="0.01" class="form-control" name="due_payment"
                                        placeholder="Calculate automatically or enter">
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Payment Method</label>
                                    <select class="form-select" name="payment_method">
                                        <option value="">Select Payment Method</option>
                                        <option value="cash">Cash</option>
                                        <option value="card">Card</option>
                                        <option value="upi">UPI</option>
                                        <option value="bank_transfer">Bank Transfer</option>
                                        <option value="cheque">Cheque</option>
                                    </select>
                                </div>

                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Due Date</label>
                                    <input type="date" class="form-control" name="due_date">
                                </div>
                            </div> -->
                        </div>
                    </div>

                    <!-- Patient Images Section -->
                                    


                        <div class="diet-history-container">
                            <div class="diet-history-header">
                                <i class="fas fa-leaf"></i>
                                Diet History
                                <i class="fas fa-leaf"></i>
                            </div>

                            <!-- Row 1: Waking time, Sleeping time, Food choices -->
                            <div class="diet-line">
                                <label>Waking time:</label>
                                <input type="text" name="waking_time" class="form-control-line" value="{{ $latestMeta['waking_time'] ?? '' }}">
                                
                                <label>Sleeping time:</label>
                                <input type="text" name="sleeping_time" class="form-control-line" value="{{ $latestMeta['sleeping_time'] ?? '' }}">

                                <div class="diet-checkbox-group ms-2">
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="food_choices" id="veg" value="vegetarian" {{ ($latestMeta['food_choices'] ?? '') == 'vegetarian' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="veg">Veg.</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="food_choices" id="nonveg" value="non-vegetarian" {{ ($latestMeta['food_choices'] ?? '') == 'non-vegetarian' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="nonveg">Non Veg.</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="food_choices" id="veg_egg" value="veg_egg" {{ ($latestMeta['food_choices'] ?? '') == 'veg_egg' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="veg_egg">Veg. + Egg</label>
                                    </div>
                                    <div class="form-check form-check-inline">
                                        <input class="form-check-input" type="radio" name="food_choices" id="jain" value="jain" {{ ($latestMeta['food_choices'] ?? '') == 'jain' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="jain">Jain</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Row 2: Occupation, Time -->
                            <div class="diet-line">
                                <label>Occupation :</label>
                                <input type="text" name="occupation" class="form-control-line" value="{{ $latestMeta['occupation'] ?? '' }}">
                                
                                <label>Time :</label>
                                <input type="text" name="time" class="form-control-line" value="{{ $latestMeta['time'] ?? '' }}">
                            </div>

                            <!-- Row 3: Early Morning -->
                            <div class="diet-line">
                                <label>Early Morning:</label>
                                <input type="text" name="early_morning" class="form-control-line" value="{{ $latestMeta['early_morning'] ?? '' }}">
                                
                                <label>Early Morning Meal:</label>                                                                                                                                              
                                <input type="text" name="early_morning_meal" class="form-control-line" value="{{ $latestMeta['early_morning_meal'] ?? '' }}">
                            </div>

                            <!-- Row 4: Breakfast -->
                            <div class="diet-line">
                                <label>Breakfast:</label>
                                <input type="text" name="breakfast" class="form-control-line" value="{{ $latestMeta['breakfast'] ?? '' }}">
                            </div>

                            <!-- Row 5: Brunch -->
                            <div class="diet-line">
                                <label>Brunch:</label>
                                <input type="text" name="brunch" class="form-control-line" value="{{ $latestMeta['brunch'] ?? '' }}">
                            </div>

                            <!-- Row 6: Lunch -->
                            <div class="diet-line">
                                <label>Lunch:</label>
                                <input type="text" name="lunch" class="form-control-line" value="{{ $latestMeta['lunch'] ?? '' }}">
                            </div>

                            <!-- Row 7: Snacks -->
                            <div class="diet-line">
                                <label>Snacks :</label>
                                <input type="text" name="snacks" class="form-control-line" value="{{ $latestMeta['snacks'] ?? '' }}">
                            </div>

                            <!-- Row 8: Dinner -->
                            <div class="diet-line">
                                <label>Dinner:</label>
                                <input type="text" name="dinner" class="form-control-line" value="{{ $latestMeta['dinner'] ?? '' }}">
                            </div>

                            <!-- Row 9: Bed Time -->
                            <div class="diet-line">
                                <label>Bed Time :</label>
                                <input type="text" name="bed_time" class="form-control-line" value="{{ $latestMeta['bed_time'] ?? '' }}">
                            </div>

                            <!-- Row 10: Milk, Oil, Salt, Water Intake -->
                            <div class="diet-line">
                                <label>Milk:</label>
                                <input type="text" name="milk" class="form-control-line" value="{{ $latestMeta['milk'] ?? '' }}">
                                
                                <label>Oil:</label>
                                <input type="text" name="oil" class="form-control-line" value="{{ $latestMeta['oil'] ?? '' }}">
                                
                                <label>Salt:</label>
                                <input type="text" name="salt" class="form-control-line" value="{{ $latestMeta['salt'] ?? '' }}">
                                
                                <label>Water Intake:</label>
                                <input type="text" name="water_intake" class="form-control-line" value="{{ $latestMeta['water_intake'] ?? '' }}">
                            </div>

                            <!-- Row 11: Physical Activity & Walking Time -->
                            <div class="diet-line">
                                <label>Physical Activity:</label>
                                <select name="physical_activity" class="form-control-line">
                                    <option value="" {{ ($latestMeta['physical_activity'] ?? '') == '' ? 'selected' : '' }}>Select Activity</option>
                                    <option value="Sedentary (Very Low Activity)" {{ ($latestMeta['physical_activity'] ?? '') == 'Sedentary (Very Low Activity)' ? 'selected' : '' }}>Sedentary (Very Low Activity)</option>
                                    <option value="Lightly Active" {{ ($latestMeta['physical_activity'] ?? '') == 'Lightly Active' ? 'selected' : '' }}>Lightly Active</option>
                                    <option value="Moderately Active" {{ ($latestMeta['physical_activity'] ?? '') == 'Moderately Active' ? 'selected' : '' }}>Moderately Active</option>
                                    <option value="Very Active" {{ ($latestMeta['physical_activity'] ?? '') == 'Very Active' ? 'selected' : '' }}>Very Active</option>
                                    <option value="Extra Active" {{ ($latestMeta['physical_activity'] ?? '') == 'Extra Active' ? 'selected' : '' }}>Extra Active</option>
                                </select>
                                
                                <label>Walking Time:</label>
                                <input type="text" name="walking_time" class="form-control-line" value="{{ $latestMeta['walking_time'] ?? '' }}">
                            </div>

                            <!-- Row 12: Fasting Day -->
                            <div class="diet-line">
                                <label>Fasting Day :</label>
                                <input type="text" name="fasting_day" class="form-control-line" value="{{ $latestMeta['fasting_day'] ?? '' }}">
                            </div>

                            <!-- Row 13: Fast Food & Alcohol -->
                            <div class="diet-line">
                                <label>Fast Food/Hotel Food:</label>
                                <input type="text" name="fast_food" class="form-control-line" value="{{ $latestMeta['fast_food'] ?? '' }}">
                                
                                <label>Alcohol:</label>
                                <input type="text" name="alcohol" class="form-control-line" value="{{ $latestMeta['alcohol'] ?? '' }}">
                            </div>

                            <!-- Row 14: Habit & Food Allergy -->
                            <div class="diet-line">
                                <label>Habit:</label>
                                <input type="text" name="habit" class="form-control-line" value="{{ $latestMeta['habit'] ?? '' }}" placeholder="e.g. Tea in morning, smoking...">
                                
                                <label>Food Allergy:</label>
                                <input type="text" name="food_allergy" class="form-control-line" value="{{ $latestMeta['food_allergy'] ?? '' }}" placeholder="e.g. Milk allergy, gluten...">
                            </div>

                            <!-- Row 15: Anything Else -->
                            <div class="diet-line mb-4">
                                <label>Anything Else:</label>
                                <input type="text" name="anything_else" class="form-control-line" value="{{ $latestMeta['anything_else'] ?? '' }}">
                            </div>
                        </div>

                        <!-- Measurement History Table (as per screenshot) -->
                        <div class="mt-4">
                            <h4 class="mb-3 text-success font-weight-bold" style="border-bottom: 2px solid #28a745; display: inline-block;"><i class="fas fa-ruler-combined"></i> Measurement History</h4>
                            <div class="table-responsive">
                                <table class="table table-bordered table-sm text-center">
                                    <thead class="bg-success text-white">
                                        <tr>
                                            <th>Date</th>
                                            <th>Waist (cm)</th>
                                            <th>Hips (cm)</th>
                                            <th>Thighs (cm)</th>
                                            <th>Arms (cm)</th>
                                            <th>Waist / Hips</th>
                                            <th>Wt (kg)</th>
                                            <th>BMI</th>
                                        </tr>
                                    </thead>
                                    <tbody id="measurementHistoryBody">
                                        @forelse($measurements ?? [] as $m)
                                        <tr>
                                            <td>{{ $m->assessment_date ? $m->assessment_date->format('d/m/Y') : '-' }}</td>
                                            <td>{{ $m->waist_middle ?? '-' }}</td>
                                            <td>{{ $m->hips ?? '-' }}</td>
                                            <td>{{ $m->thighs ?? '-' }}</td>
                                            <td>{{ $m->arms ?? '-' }}</td>
                                            <td>{{ $m->waist_hips_ratio ?? '-' }}</td>
                                            <td class="fw-bold">{{ $m->weight ?? '-' }}</td>
                                            <td>{{ $m->bmi ?? '-' }}</td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="8" class="text-muted italic">No measurement history available.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                    </div> <!-- Closing mt-4 -->

                <!-- Date-wise Diet History Timeline (Simplified Accordion) -->
                <div class="my-5 py-3">
                    <div class="accordion shadow-sm" id="dietHistoryAccordion">
                        <div class="accordion-item border-success border-opacity-25">
                            <h2 class="accordion-header" id="headingTimeline">
                                <button class="accordion-button collapsed bg-success bg-opacity-10 text-success fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTimeline" aria-expanded="false" aria-controls="collapseTimeline">
                                    <i class="fas fa-calendar-alt me-2"></i> Date-wise Diet History Timeline ({{ $dietHistory->count() }} Records)
                                </button>
                            </h2>
                            <div id="collapseTimeline" class="accordion-collapse collapse" aria-labelledby="headingTimeline" data-bs-parent="#dietHistoryAccordion">
                                <div class="accordion-body p-0">
                                    @if(!empty($dietHistory) && $dietHistory->count() > 0)
                                    <div class="list-group list-group-flush">
                                        @foreach($dietHistory as $history)
                                            @php
                                                $meta = [];
                                                foreach($history->meta as $m) {
                                                    $meta[$m->meta_key] = $m->meta_value;
                                                }
                                            @endphp
                                            <div class="list-group-item diet-history-entry timeline-entry bg-white border-start border-4 border-success border-opacity-25 py-3 px-4">
                                                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                                    <div class="d-flex align-items-center">
                                                        <div class="bg-success text-white rounded px-2 py-1 me-2 shadow-sm" style="font-size: 0.8rem; font-weight: 700;">
                                                            {{ $history->created_at->format('d M, Y') }}
                                                        </div>
                                                        <span class="text-muted small fw-bold">
                                                            <i class="far fa-clock me-1 text-success"></i>{{ $history->created_at->format('h:i A') }}
                                                        </span>
                                                    </div>
                                                    
                                                    <div class="d-flex align-items-center gap-2">
                                                        <button type="button" class="btn btn-sm btn-outline-warning rounded-pill px-3 py-1 fw-bold" 
                                                                onclick='openEditHistoryModal({{ $history->id }}, @json($meta))'>
                                                            <i class="fas fa-edit me-1"></i> Edit
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-light text-primary rounded-pill px-3 py-1 fw-bold" 
                                                                data-bs-toggle="collapse" data-bs-target="#fullData-{{ $history->id }}">
                                                            <i class="fas fa-eye me-1"></i> Details
                                                        </button>
                                                        <button type="button" class="btn btn-sm btn-link text-danger p-0 ms-2" 
                                                                onclick="deleteHistory({{ $history->id }}, this)">
                                                            <i class="fas fa-trash-alt"></i>
                                                        </button>
                                                    </div>
                                                </div>

                                                <div class="collapse mt-3" id="fullData-{{ $history->id }}">
                                                    <div class="bg-white p-3 rounded border border-success border-opacity-25" style="font-size: 0.85rem;">
                                                        <div class="row g-3">
                                                            <div class="col-md-3">
                                                                <p class="mb-1 text-muted small fw-bold text-uppercase">Breakfast</p>
                                                                <p class="mb-0 text-dark font-weight-bold">{{ $meta['breakfast'] ?? '---' }}</p>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <p class="mb-1 text-muted small fw-bold text-uppercase">Lunch</p>
                                                                <p class="mb-0 text-dark font-weight-bold">{{ $meta['lunch'] ?? '---' }}</p>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <p class="mb-1 text-muted small fw-bold text-uppercase">Dinner</p>
                                                                <p class="mb-0 text-dark font-weight-bold">{{ $meta['dinner'] ?? '---' }}</p>
                                                            </div>
                                                            <div class="col-md-3">
                                                                <p class="mb-1 text-muted small fw-bold text-uppercase">Water / Activity</p>
                                                                <p class="mb-0 text-dark">{{ $meta['water_intake'] ?? '-' }} {{ $meta['water_unit'] ?? '' }} | {{ $meta['activity'] ?? '---' }}</p>
                                                            </div>
                                                            
                                                            <div class="col-md-4">
                                                                <ul class="list-unstyled mb-0 border-top pt-2">
                                                                    <li><span class="text-muted">Occupation:</span> {{ $meta['occupation'] ?? 'N/A' }}</li>
                                                                    <li><span class="text-muted">Waking Time:</span> {{ $meta['waking_time'] ?? 'N/A' }}</li>
                                                                    <li><span class="text-muted">Sleeping Time:</span> {{ $meta['sleeping_time'] ?? 'N/A' }}</li>
                                                                </ul>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <ul class="list-unstyled mb-0 border-top pt-2">
                                                                    <li><span class="text-muted">Brunch:</span> {{ $meta['brunch'] ?? 'N/A' }}</li>
                                                                    <li><span class="text-muted">Snacks:</span> {{ $meta['snacks'] ?? 'N/A' }}</li>
                                                                    <li><span class="text-muted">Food Choice:</span> {{ ucfirst($meta['food_choices'] ?? 'N/A') }}</li>
                                                                </ul>
                                                            </div>
                                                            <div class="col-md-4">
                                                                <ul class="list-unstyled mb-0 border-top pt-2">
                                                                    <li><span class="text-muted">Milk:</span> {{ $meta['milk'] ?? 'N/A' }}</li>
                                                                    <li><span class="text-muted">Allergy:</span> {{ $meta['food_allergy'] ?? 'N/A' }}</li>
                                                                    <li><span class="text-muted">Habit:</span> {{ $meta['habit'] ?? 'N/A' }}</li>
                                                                </ul>
                                                            </div>
                                                            <!-- Weight Row -->
                                                         

                                                            <!-- Investigation Row -->
                                                          
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    @else
                                    <div class="text-center py-5 text-muted">
                                        <i class="fas fa-info-circle me-1"></i> No previous diet history found.
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>



                     <div class="row mb-4">
                        <div class="col-md-12">
                            <h4 class="border-bottom pb-2 mb-3">Patient Images</h4>

                            {{-- <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Before Profile Photo</label>
                                    <input type="file" class="form-control" name="before_profile_photo"
                                        accept="image/*">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">After Profile Photo</label>
                                    <input type="file" class="form-control" name="after_profile_photo"
                                        accept="image/*">
                                </div>
                            </div> --}}

                            <!-- Additional Images (for meta) -->
                            <div class="row">
                                <div class="col-md-6">
                                    <!-- <h5>Additional Before Pictures</h5> -->
                                    @for ($i = 1; $i <= 5; $i++)
                                        <div class="mb-3">
                                            <label class="form-label small">Before Picture {{ $i }}</label>
                                            <input type="file" class="form-control form-control-sm"
                                                name="before_picture_{{ $i }}" accept="image/*">
                                            @if(!empty($latestMeta['before_picture_'.$i]))
                                                <input type="hidden" name="existing_before_picture_{{ $i }}" value="{{ $latestMeta['before_picture_'.$i] }}">
                                                <div class="mt-2 preview-image-container">
                                                    <a href="{{ asset('before/' . $latestMeta['before_picture_'.$i]) }}" target="_blank">
                                                        <img src="{{ asset('before/' . $latestMeta['before_picture_'.$i]) }}" 
                                                             alt="Before Picture {{ $i }}" 
                                                             class="img-thumbnail" style="max-height: 100px;">
                                                    </a>
                                                    <div class="small text-muted">Stored Image</div>
                                                </div>
                                            @endif
                                        </div>
                                    @endfor
                                </div>

                                <div class="col-md-6">
                                    <!-- <h5>Additional After Pictures</h5> -->
                                    @for ($i = 1; $i <= 5; $i++)
                                        <div class="mb-3">
                                            <label class="form-label small">After Picture {{ $i }}</label>
                                            <input type="file" class="form-control form-control-sm"
                                                name="after_picture_{{ $i }}" accept="image/*">
                                            @if(!empty($latestMeta['after_picture_'.$i]))
                                                <input type="hidden" name="existing_after_picture_{{ $i }}" value="{{ $latestMeta['after_picture_'.$i] }}">
                                                <div class="mt-2 preview-image-container">
                                                    <a href="{{ asset('after/' . $latestMeta['after_picture_'.$i]) }}" target="_blank">
                                                        <img src="{{ asset('after/' . $latestMeta['after_picture_'.$i]) }}" 
                                                             alt="After Picture {{ $i }}" 
                                                             class="img-thumbnail" style="max-height: 100px;">
                                                    </a>
                                                    <div class="small text-muted">Stored Image</div>
                                                </div>
                                            @endif
                                        </div>
                                    @endfor
                                </div>
                            </div>
                        </div>
                    </div>
                        <div class="row mt-4">
                                    <div class="col-md-12 text-center">
                                        <button type="submit" class="btn btn-primary btn-lg">
                                            <i class="fas fa-save"></i> Save Diet Chart
                                        </button>
                                        <a href="{{ route('joined.inquiry') }}" class="btn btn-secondary btn-lg">
                                            <i class="fas fa-times"></i> Cancel
                                        </a>
                                    </div>
                        </div>
                </div> <!-- Closing card-body -->
            </div> <!-- Closing card -->
        </form>
    </div> <!-- Closing container -->

    <!-- Diet History Modal -->
    <div class="modal fade" id="dietHistoryModal" tabindex="-1" aria-labelledby="dietHistoryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title fw-bold" id="dietHistoryModalLabel">
                        <i class="fas fa-history me-2"></i> Diet History Archives - {{ $patient->patient_name ?? 'Patient' }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4">Date & Time</th>
                                    <th>Breakfast</th>
                                    <th>Lunch</th>
                                    <th>Dinner</th>
                                    <th class="text-center">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($dietHistory as $history)
                                    @php
                                        $metaArray = [];
                                        foreach($history->meta as $m) {
                                            $metaArray[$m->meta_key] = $m->meta_value;
                                        }
                                    @endphp
                                    <tr>
                                        <td class="ps-4">
                                            <div class="fw-bold text-dark">{{ $history->created_at->format('d M, Y') }}</div>
                                            <small class="text-muted">{{ $history->created_at->format('h:i A') }}</small>
                                        </td>
                                        <td><div class="text-truncate" style="max-width: 200px;">{{ $metaArray['breakfast'] ?? '---' }}</div></td>
                                        <td><div class="text-truncate" style="max-width: 200px;">{{ $metaArray['lunch'] ?? '---' }}</div></td>
                                        <td><div class="text-truncate" style="max-width: 200px;">{{ $metaArray['dinner'] ?? '---' }}</div></td>
                                        <td class="text-center pe-4">
                                            <button class="btn btn-warning btn-sm rounded-pill px-3 shadow-sm" 
                                                    onclick='openEditHistoryModal({{ $history->id }}, @json($metaArray))' 
                                                    data-bs-dismiss="modal">
                                                <i class="fas fa-edit me-1"></i> Edit History
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <i class="fas fa-info-circle me-1"></i> No diet history archives found for this patient.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="modal-footer bg-light border-0">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {

            // ─── AUTO-CALCULATE BMI ───────────────────────────────────────────────
            function calculateBMI() {
                const hEl = document.getElementById('heightInput');
                const wEl = document.getElementById('weightInput');
                const bEl = document.getElementById('bmiInput');

                if (!hEl || !wEl || !bEl) return;

                const height = parseFloat(hEl.value);
                const weight = parseFloat(wEl.value);

                if (height && weight && height > 0) {
                    const heightMeter = height / 100;
                    const bmi = (weight / (heightMeter * heightMeter)).toFixed(1);
                    bEl.value = bmi;
                } else {
                    bEl.value = '';
                }
            }

            // ─── AUTO-CALCULATE BODY WEIGHT METRICS ──────────────────────────────
            function calculateBodyWeightMetrics() {
                const heightEl = document.getElementById('heightInput');
                const weightEl = document.getElementById('weightInput');
                
                const height = parseFloat(heightEl?.value);
                const weight = parseFloat(weightEl?.value);

                const overEl   = document.getElementById('overWeightInput');
                const idealEl  = document.getElementById('idealWeightInput');
                const underEl  = document.getElementById('underWeightInput');
                const targetEl = document.getElementById('targetWeightInput');

                if (!overEl || !idealEl || !underEl || !targetEl) return;

                if (!height || height <= 0) {
                    overEl.value   = '';
                    idealEl.value  = '';
                    underEl.value  = '';
                    targetEl.value = '';
                    return;
                }

                // Formulas based on User Example:
                // IBW IS = Height - 100
                const ibw = height - 100;

                idealEl.value = ibw.toFixed(2) + " kg";
                targetEl.value = ibw.toFixed(2) + " kg";

                if (weight > 0) {
                    // Under or Over Weight= Current Weight - IBW
                    const diff = weight - ibw;

                    if (diff > 0) {
                        overEl.value = diff.toFixed(2) + " kg";
                        underEl.value = "0 kg";
                        overEl.style.color = "#dc3545";
                        underEl.style.color = "#28a745";
                    } else if (diff < 0) {
                        underEl.value = Math.abs(diff).toFixed(2) + " kg";
                        overEl.value = "0 kg";
                        underEl.style.color = "#dc3545";
                        overEl.style.color = "#28a745";
                    } else {
                        overEl.value = "0 kg";
                        underEl.value = "0 kg";
                        overEl.style.color = "#28a745";
                        underEl.style.color = "#28a745";
                    }
                } else {
                    overEl.value = '';
                    underEl.value = '';
                }
            }

            // ─── EVENT LISTENERS ─────────────────────────────────────────────────
            const heightInput = document.getElementById('heightInput');
            const weightInput = document.getElementById('weightInput');

            if (heightInput) {
                heightInput.addEventListener('input', function() {
                    calculateBMI();
                    calculateBodyWeightMetrics();
                });
            }

            if (weightInput) {
                weightInput.addEventListener('input', function() {
                    calculateBMI();
                    calculateBodyWeightMetrics();
                });
            }

            // Run on page load if values already present
            calculateBodyWeightMetrics();

            // Auto-calculate due payment
            function calculateDuePayment() {
                const totalEl = document.querySelector('[name="total_payment"]');
                const discountEl = document.querySelector('[name="discount_payment"]');
                const givenEl = document.querySelector('[name="given_payment"]');
                const dueEl = document.querySelector('[name="due_payment"]');

                if (!totalEl || !dueEl) return;

                const total = parseFloat(totalEl.value) || 0;
                const discount = parseFloat(discountEl?.value) || 0;
                const given = parseFloat(givenEl?.value) || 0;

                const due = total - discount - given;
                dueEl.value = due > 0 ? due.toFixed(2) : '0.00';
            }

            // Add event listeners for payment fields
            const paymentFields = ['total_payment', 'discount_payment', 'given_payment'];
            paymentFields.forEach(fieldName => {
                const field = document.querySelector(`[name="${fieldName}"]`);
                if (field) {
                    field.addEventListener('input', calculateDuePayment);
                }
            });

            // Initial calculations on page load
            calculateBMI();
            calculateDuePayment();

            // Add preview for file uploads
            document.querySelectorAll('input[type="file"]').forEach(input => {
                input.addEventListener('change', function() {
                    if (this.files && this.files[0]) {
                        const reader = new FileReader();
                        const container = this.closest('.mb-3') || this.closest('.mb-2');
                        let preview = container.querySelector('.preview-image-container');
                        
                        if (!preview) {
                            preview = document.createElement('div');
                            preview.className = 'mt-2 preview-image-container';
                            container.appendChild(preview);
                        }
                        
                        reader.onload = function(e) {
                            preview.innerHTML = `
                                <div class="d-inline-block border p-1 rounded bg-light">
                                    <img src="${e.target.result}" class="img-thumbnail" style="max-height: 100px;">
                                    <div class="small text-primary mt-1 fw-bold"><i class="fas fa-check-circle"></i> New Selection</div>
                                </div>
                            `;
                        }
                        reader.readAsDataURL(this.files[0]);
                    }
                });
            });
            
            // Form submission confirmation
            const dietForm = document.getElementById('dietChartForm');
            if (dietForm) {
                dietForm.addEventListener('submit', function(e) {
                    e.preventDefault();

                    Swal.fire({
                        title: 'Are you sure?',
                        text: "Do you want to save this diet chart?",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, save it!',
                        cancelButtonText: 'Cancel'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            dietForm.submit();
                        }
                    });
                });
            }
        });

        let rowCounter = 0;

        function addProgramRow() {
            rowCounter++;
            const container = document.querySelector('.program-rows-container');

            const newRow = document.createElement('div');
            newRow.className = 'program-row mb-3 additional-program-row';

            newRow.innerHTML = `
        <div class="row">
            <div class="col-md-4 mb-3">
                <label class="form-label">Select Program</label>
                <select class="form-select program-select" name="selected_program[]">
                    <option value="">Select Program</option>
                    @foreach($available_programs as $prog)
                        <option value="{{ $prog->program_name }}">{{ $prog->program_name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label">Session</label>
                <input type="text" class="form-control session-input" name="session[]"
                       placeholder="Enter session details">
            </div>

            <div class="col-md-4 mb-3">
                <div class="d-flex align-items-end">
                    <div class="flex-grow-1">
                        <label class="form-label">Months</label>
                        <input type="number" class="form-control months-input" name="months[]"
                               placeholder="Enter number of months" min="1">
                    </div>
                    <div class="ms-2 d-flex" style="padding-bottom: 8px;">
                        <button type="button" class="btn btn-success btn-sm add-program-btn me-1"
                                onclick="addProgramRow()">
                            <i class="fas fa-plus"></i>
                        </button>
                        <button type="button" class="btn btn-danger btn-sm remove-program-btn"
                                onclick="removeProgramRow(this)">
                            <i class="fas fa-minus"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;

            container.appendChild(newRow);
        }

        function removeProgramRow(button) {
            const rowToRemove = button.closest('.program-row');
            if (rowToRemove && !rowToRemove.classList.contains('permanent-row')) {
                rowToRemove.remove();
            }
        }

        function loadDietHistory(meta) {
            if(!meta) return;
            
            // Loop through the meta and fill the form fields
            for (let key in meta) {
                let value = meta[key];
                
                // Special handling for radio buttons
                if (key === 'food_choices') {
                    let radio = document.querySelector(`input[name="${key}"][value="${value}"]`);
                    if (radio) radio.checked = true;
                    continue;
                }

                let element = document.getElementsByName(key)[0];
                if (element && value !== null && value !== undefined) {
                    // Skip file inputs to avoid InvalidStateError
                    if (element.type === 'file') {
                        continue;
                    }
                    element.value = value;
                }
            }
            
            // Trigger BMI and Payment calculations as they might depend on filled fields
            if (typeof calculateBMI === 'function') calculateBMI();
            if (typeof calculateDuePayment === 'function') calculateDuePayment();

            Swal.fire({
                icon: 'success',
                title: 'History Loaded',
                text: 'Previous diet history has been loaded into the form.',
                timer: 2000,
                showConfirmButton: false
            });

            // Scroll to form nicely
            document.querySelector('.diet-history-section').scrollIntoView({ behavior: 'smooth', block: 'start' });
        }

        async function deleteHistory(id, button) {
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to delete this diet history entry?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then(async (result) => {
                if (result.isConfirmed) {
                    try {
                        const response = await fetch(`/admin/delete-diet-history/${id}`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                'Content-Type': 'application/json'
                            }
                        });
                        const data = await response.json();
                        
                        if(data.success) {
                            Swal.fire(
                                'Deleted!',
                                'Diet history entry removed successfully.',
                                'success'
                            );
                            // Remove the timeline item with animation
                            const item = button.closest('.timeline-entry');
                            item.style.transition = 'all 0.5s ease';
                            item.style.opacity = '0';
                            item.style.transform = 'translateX(-50px)';
                            setTimeout(() => item.remove(), 500);
                        } else {
                            Swal.fire('Error', data.message, 'error');
                        }
                    } catch (e) {
                        Swal.fire('Error', 'Connection failed', 'error');
                    }
                }
            });
        }

        function openEditHistoryModal(id, meta) {
            document.getElementById('modal_history_id').value = id;
            const form = document.getElementById('editHistoryMetaForm');
            form.reset();
            
            // Fill fields from meta object
            for (let key in meta) {
                let value = meta[key];
                
                // Handle radio buttons
                if (key === 'food_choices') {
                    let radio = form.querySelector(`input[name="food_choices"][value="${value}"]`);
                    if (radio) radio.checked = true;
                } else {
                    let element = form.elements[key];
                    if (element) {
                        element.value = value;
                    }
                }
            }
            
            // Show modal
            const modalElement = document.getElementById('editHistoryMetaModal');
            const bsModal = new bootstrap.Modal(modalElement);
            bsModal.show();
        }

        async function submitEditHistory() {
            const form = document.getElementById('editHistoryMetaForm');
            const formData = new FormData(form);
            const saveBtn = document.getElementById('saveHistoryMetaBtn');
            
            saveBtn.disabled = true;
            saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Updating...';
            
            try {
                const response = await fetch("{{ route('update.diet.history.meta') }}", {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: data.message,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    throw new Error(data.message || 'Update failed');
                }
            } catch (error) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: error.message
                });
            } finally {
                saveBtn.disabled = false;
                saveBtn.innerHTML = '<i class="fas fa-save me-1"></i> Update History';
            }
        }
    </script>

    <!-- Edit Historical Data Modal -->
    <div class="modal fade" id="editHistoryMetaModal" tabindex="-1" aria-labelledby="editHistoryMetaModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header" style="background-color: #28a745; color: white;">
                    <h5 class="modal-title fw-bold" id="editHistoryMetaModalLabel">
                        <i class="fas fa-edit me-2"></i> Edit Historical Diet Record
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-4 bg-light">
                    <form id="editHistoryMetaForm">
                        @csrf
                        <input type="hidden" name="history_id" id="modal_history_id">
                        
                        <div class="row g-4">
                            <!-- Left Column: Meal Timings & Diet -->
                            <div class="col-lg-6">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-header bg-white py-3">
                                        <h6 class="mb-0 fw-bold" style="color: #28a745;"><i class="fas fa-utensils me-2"></i> Meal Schedule</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold">Waking Time</label>
                                                <input type="text" name="waking_time" class="form-control form-control-sm" placeholder="e.g. 6:00 AM">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold">Sleeping Time</label>
                                                <input type="text" name="sleeping_time" class="form-control form-control-sm" placeholder="e.g. 10:00 PM">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold">Early Morning</label>
                                                <input type="text" name="early_morning" class="form-control form-control-sm">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold">Early Morning Meal</label>
                                                <input type="text" name="early_morning_meal" class="form-control form-control-sm">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold">Breakfast</label>
                                                <input type="text" name="breakfast" class="form-control form-control-sm">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold">Brunch</label>
                                                <input type="text" name="brunch" class="form-control form-control-sm">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold">Lunch</label>
                                                <input type="text" name="lunch" class="form-control form-control-sm">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold">Snacks</label>
                                                <input type="text" name="snacks" class="form-control form-control-sm">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold">Dinner</label>
                                                <input type="text" name="dinner" class="form-control form-control-sm">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold">Bed Time</label>
                                                <input type="text" name="bed_time" class="form-control form-control-sm">
                                            </div>
                                            <div class="col-md-12">
                                                <label class="form-label small fw-bold">Food Choices</label>
                                                <div class="d-flex gap-2 flex-wrap">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="food_choices" value="vegetarian" id="edit_veg">
                                                        <label class="form-check-label small" for="edit_veg">Veg.</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="food_choices" value="non-vegetarian" id="edit_nonveg">
                                                        <label class="form-check-label small" for="edit_nonveg">Non Veg.</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="food_choices" value="veg_egg" id="edit_vegegg">
                                                        <label class="form-check-label small" for="edit_vegegg">Veg.+Egg</label>
                                                    </div>
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="radio" name="food_choices" value="jain" id="edit_jain">
                                                        <label class="form-check-label small" for="edit_jain">Jain</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column: Lifestyle & Other Details -->
                            <div class="col-lg-6">
                                <div class="card border-0 shadow-sm h-100">
                                    <div class="card-header bg-white py-3">
                                        <h6 class="mb-0 fw-bold" style="color: #28a745;"><i class="fas fa-walking me-2"></i> Lifestyle & Habits</h6>
                                    </div>
                                    <div class="card-body">
                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold">Occupation</label>
                                                <input type="text" name="occupation" class="form-control form-control-sm">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold">Time (Work/Study)</label>
                                                <input type="text" name="time" class="form-control form-control-sm">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label small fw-bold">Milk</label>
                                                <input type="text" name="milk" class="form-control form-control-sm">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label small fw-bold">Oil</label>
                                                <input type="text" name="oil" class="form-control form-control-sm">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label small fw-bold">Salt</label>
                                                <input type="text" name="salt" class="form-control form-control-sm">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold">Water Intake</label>
                                                <input type="text" name="water_intake" class="form-control form-control-sm">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold">Physical Activity</label>
                                                <input type="text" name="physical_activity" class="form-control form-control-sm">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold">Fasting Day</label>
                                                <input type="text" name="fasting_day" class="form-control form-control-sm">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold">Fast Food / Hotel Food</label>
                                                <input type="text" name="fast_food" class="form-control form-control-sm">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold">Walking Time</label>
                                                <input type="text" name="walking_time" class="form-control form-control-sm">
                                            </div>
                                            <div class="col-md-6">
                                                <label class="form-label small fw-bold">Habit</label>
                                                <input type="text" name="habit" class="form-control form-control-sm">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label small fw-bold">Food Allergy</label>
                                                <input type="text" name="food_allergy" class="form-control form-control-sm">
                                            </div>
                                            <div class="col-md-4">
                                                <label class="form-label small fw-bold">Alcohol</label>
                                                <input type="text" name="alcohol" class="form-control form-control-sm">
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label small fw-bold">Anything Else</label>
                                                <textarea name="anything_else" class="form-control form-control-sm" rows="2"></textarea>
                                            </div>
                                            <div class="col-12">
                                                <label class="form-label small fw-bold">Medication</label>
                                                <textarea name="pod_medication" class="form-control form-control-sm" rows="2"></textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary px-4" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn text-white px-4 fw-bold" id="saveHistoryMetaBtn" onclick="submitEditHistory()" style="background-color: #28a745;">
                        <i class="fas fa-save me-1"></i> Update History
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Button styling for program rows */
        .add-program-btn,
        .remove-program-btn {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
        }

        .add-program-btn {
            background-color: #28a745;
            border-color: #28a745;
        }

        .add-program-btn:hover {
            background-color: #218838;
            border-color: #1e7e34;
        }

        .remove-program-btn {
            background-color: #dc3545;
            border-color: #dc3545;
        }

        .remove-program-btn:hover {
            background-color: #c82333;
            border-color: #bd2130;
        }

        /* Styling for program rows */
        .program-row {
            position: relative;
        }

        .additional-program-row {
            margin-top: 10px;
            padding-top: 10px;
            border-top: 1px dashed var(--border-subtle);
            animation: fadeIn 0.3s ease-in;
        }

        .permanent-row {
            padding-bottom: 10px;
            border-bottom: 2px solid var(--border-subtle);
            margin-bottom: 15px;
        }

        /* Animation for smooth appearance */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Ensure proper spacing for buttons */
        .ms-2 {
            margin-left: 0.5rem !important;
        }

        /* Make sure the container has proper spacing */
        .program-rows-container {
            padding: 10px 0;
        }

        .form-label {
            font-weight: 600;
            color: #5a6268;
            margin-bottom: 4px;
            display: block;
            font-size: 13px;
        }

        .form-control,
        .form-select {
            background-color: var(--bg-main);
            color: var(--text-primary);
            border-radius: 6px;
            border: 1px solid var(--border-subtle);
            padding: 6px 10px;
            font-size: 13px;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, .25);
        }

        textarea.form-control {
            min-height: 80px;
            resize: vertical;
        }

        .card {
            background-color: var(--bg-card);
            border: 1px solid var(--border-subtle);
            border-radius: 0.25rem;
        }

        .card-body {
            padding: 1.5rem;
            color: var(--text-primary);
        }

        .btn-lg {
            padding: 0.5rem 2rem;
            font-size: 1rem;
            margin: 0 10px;
        }

        .border-bottom {
            border-bottom: 2px solid var(--border-subtle) !important;
        }

        h4 {
            color: var(--accent-solid);
            font-weight: 600;
        }

        .table th {
            background-color: rgba(0, 102, 55, 0.1);
            color: var(--text-primary);
            font-weight: 600;
            text-align: center;
        }

        input[readonly] {
            background-color: var(--bg-hover);
            color: var(--text-muted);
        }

        .diet-history-section {
            background: var(--bg-card);
            border-radius: 8px;
            padding: 25px;
            margin: 20px 0;
            box-shadow: var(--shadow-md);
            border: 1px solid var(--border-subtle);
        }

        .diet-history-section h3 {
            color: var(--text-primary);
            margin-bottom: 25px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--accent-solid);
        }

        .diet-form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }

        @media (max-width: 768px) {
            .diet-form-grid {
                grid-template-columns: 1fr;
            }
        }

        .form-column {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .form-field {
            display: flex;
            flex-direction: column;
        }

        .form-field label {
            font-weight: 600;
            margin-bottom: 4px;
            color: #5a6268;
            font-size: 13px;
        }

        .form-input {
            padding: 6px 10px;
            background-color: var(--bg-main);
            color: var(--text-primary);
            border: 1px solid var(--border-subtle);
            border-radius: 6px;
            font-size: 13px;
            transition: border 0.3s;
        }

        .form-input:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
        }

        .form-textarea {
            padding: 6px 10px;
            background-color: var(--bg-main);
            color: var(--text-primary);
            border: 1px solid var(--border-subtle);
            border-radius: 6px;
            font-size: 13px;
            min-height: 80px;
            resize: vertical;
            transition: border 0.3s;
        }

        .form-textarea:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
        }

        .form-select {
            padding: 6px 10px;
            background-color: var(--bg-main);
            color: var(--text-primary);
            border: 1px solid var(--border-subtle);
            border-radius: 6px;
            font-size: 13px;
            cursor: pointer;
            transition: border 0.3s;
        }

        .form-select:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2);
        }

        .meal-times {
            margin-top: 20px;
        }

        .meal-times h4 {
            color: var(--text-primary);
            margin-bottom: 15px;
            font-size: 16px;
        }

        .meal-item {
            margin-bottom: 15px;
        }

        .meal-item:last-child {
            margin-bottom: 0;
        }

        .meal-item label {
            font-weight: 600;
            display: block;
            margin-bottom: 4px;
            color: #5a6268;
            font-size: 13px;
        }

        .small-input {
            width: 100px;
            margin-bottom: 8px;
        }

        .form-submit {
            margin-top: 20px;
            text-align: right;
        }

        .submit-btn {
            background: #3498db;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 6px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: background 0.3s;
        }

        .submit-btn:hover {
            background: #2980b9;
        }
    </style>
@endsection