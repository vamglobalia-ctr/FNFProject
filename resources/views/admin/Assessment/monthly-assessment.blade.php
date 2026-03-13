@extends('admin.layouts.layouts')

@section('title', 'Monthly Assessment')

@section('content')

<style>
    .section-divider {
        display: flex;
        align-items: center;
        width: 100%;
        margin: 30px 0 20px;
        font-size: 16px;
        font-weight: 600;
        color: #067945;
    }

    .section-divider:after {
        content: "";
        flex-grow: 1;
        height: 1px;
        background: #dcdcdc;
        margin-left: 15px;
    }

    .form-container {
        border-radius: 12px;
        margin-bottom: 2rem;
    }

    .pro_filed {
        display: flex;
        gap: 12px;
        margin-bottom: 8px;
        width: 100%;
    }

    .pro_filed .form {
        flex: 1;
        min-width: 0;
        position: relative;
    }

    .pro_filed .form input[type="number"] {
        max-width: 120px;
    }

    label {
        font-weight: 600;
        color: #5a6268;
        display: block;
        margin-bottom: 2px;
        font-size: 13px;
        white-space: nowrap;
    }

    .required:after {
        content: " *";
        color: #e74c3c;
    }

    input,
    select,
    textarea {
        width: 100%;
        max-width: 100%;
        padding: 5px 12px;
        border: 1px solid #e2e8f0;
        border-radius: 5px;
        font-size: 13px;
        background: #ffffff;
        transition: all 0.2s ease;
        outline: none;
        text-overflow: ellipsis;
        white-space: nowrap;
        overflow: hidden;
    }

    input:focus,
    select:focus,
    textarea:focus {
        border-color: #197040;
        background: #ffffff;
    }

    .btn-submit {
        background: #086838;
        color: white;
        padding: 12px 35px;
        border: none;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-submit:hover {
        background: #067945;
        color: white;
    }

    .btn-cancel {
        background: white;
        border: 1px solid #dee2e6;
        padding: 12px 35px;
        border-radius: 8px;
        font-weight: 600;
        color: #6c757d;
        transition: all 0.3s ease;
    }

    .btn-cancel:hover {
        background: #f8f9fa;
        color: #495057;
    }

    .page-title-box h4 {
        color: #197040;
        font-size: 24px;
        font-weight: 600;
    }

    /* Custom Alert */
    .custom-alert {
        position: fixed;
        top: 30px;
        right: 30px;
        min-width: 300px;
        z-index: 9999;
        display: none;
    }

    .loading-spinner {
        display: inline-block;
        width: 20px;
        height: 20px;
        border: 3px solid rgba(255,255,255,0.3);
        border-radius: 50%;
        border-top-color: #fff;
        animation: spin 1s ease-in-out infinite;
        margin-right: 10px;
        vertical-align: middle;
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    @media (max-width: 768px) {
        .pro_filed {
            flex-direction: column;
            gap: 10px;
        }
        .pro_filed .form input[type="number"] {
            max-width: 100%;
        }
    }
</style>

<div class="container px-0">
    <div class="card shadow-sm mt-3">
        <div class="card-header bg-light px-3 py-3">
            <h5 class="mb-0 fw-bold" style="color: #197040">Monthly Assessment</h5>
        </div>
        <div class="card-body bg-white">
            <!-- Alert Message -->
            <div id="alertContainer" class="mb-3" style="display: none;">
                <div id="alertBox" class="alert alert-dismissible fade show" role="alert">
                    <span id="alertMessage"></span>
                    <button type="button" class="btn-close" onclick="this.parentElement.parentElement.style.display='none'"></button>
                </div>
            </div>

            <form id="monthlyAssessmentForm">
                @csrf
                    
                    <div class="section-divider">Basic Information</div>
                    
                    <div class="pro_filed basic-info-row">
                        <div class="form">
                            <label class="required">Assessment Date</label>
                            <input type="date" id="assessmentDate" name="assessment_date" value="{{ date('Y-m-d') }}" required>
                        </div>
                        <div class="form">
                            <label class="required">Branch Name</label>
                            <select name="branch_id" id="branchSelect" required>
                                <option value="">Select Branch</option>
                                @foreach($branches as $branch)
                                    @if($branch->delete_status == '0' || $branch->delete_status == '')
                                        <option value="{{ $branch->branch_id }}">{{ $branch->branch_name }}</option>
                                    @endif
                                @endforeach
                            </select>
                        </div>
                        <div class="form">
                            <label class="required">Patient Name</label>
                            <select name="patient_id" id="patientSelect" required disabled>
                                <option value="">Choose Branch First</option>
                            </select>
                            <div class="text-muted small mt-1" id="patientCount"></div>
                        </div>
                    </div>

                    <!-- Hidden fields for saving -->
                    <input type="hidden" name="patient_name" id="patient_name_hidden">
                    <input type="hidden" name="branch_name" id="branch_name_hidden">
                    <input type="hidden" name="patient_code" id="patient_code_hidden">

                    <div class="section-divider">Measurement Report</div>

                    <div class="pro_filed">
                        <div class="form">
                            <label>Waist Upper (cm)</label> 
                            <input type="number" step="0.1" name="waist_upper" id="measure_waist_upper" placeholder="0.0">
                        </div>
                        <div class="form">
                            <label>Waist Middle (cm)</label>
                            <input type="number" step="0.1" name="waist_middle" id="measure_waist_middle" placeholder="0.0">
                        </div>
                        <div class="form">
                            <label>Waist Lower (cm)</label>
                            <input type="number" step="0.1" name="waist_lower" id="measure_waist_lower" placeholder="0.0">
                        </div>
                    </div>

                    <div class="pro_filed">
                        <div class="form">
                            <label>Hips (cm)</label>
                            <input type="number" step="0.1" name="hips" id="measure_hips" placeholder="0.0">
                        </div>
                        <div class="form">
                            <label>Thighs (cm)</label>
                            <input type="number" step="0.1" name="thighs" id="measure_thighs" placeholder="0.0">
                        </div>
                        <div class="form">
                            <label>Arms (cm)</label>
                            <input type="number" step="0.1" name="arms" id="measure_arms" placeholder="0.0">
                        </div>
                    </div>

                    <div class="pro_filed">
                        <div class="form">
                            <label>Waist/Hips Ratio</label>
                            <input type="number" step="0.01" name="waist_hips" id="measure_waist_hips" placeholder="0.00" readonly style="background: #f1f5f9;">
                        </div>
                        <div class="form">
                            <label>Height (cm/m)</label>
                            <input type="number" step="0.01" name="height" id="measure_height" placeholder="e.g. 175 or 1.75">
                        </div>
                        <div class="form">
                            <label>Weight (kg)</label>
                            <input type="number" step="0.1" name="weight" id="measure_weight" placeholder="0.0">
                        </div>
                    </div>

                    <div class="pro_filed">
                        <div class="form">
                            <label>BMI (kg/m²)</label>
                            <input type="number" step="0.1" name="bmi" id="measure_bmi" placeholder="0.0" readonly style="background: #f1f5f9;">
                        </div>
                        <div class="form"></div>
                        <div class="form"></div>
                    </div>

                    <div class="section-divider">Body Fat Mass</div>

                    <div class="pro_filed">
                        <div class="form">
                            <label>WBF (%)</label>
                            <input type="number" step="0.1" name="bca_vbf" id="bca_vbf" placeholder="0.0">
                        </div>
                        <div class="form">
                            <label>Arms (%)</label>
                            <input type="number" step="0.1" name="bca_arms" id="bca_arms" placeholder="0.0">
                        </div>
                        <div class="form">
                            <label>Trunk (%)</label>
                            <input type="number" step="0.1" name="bca_trunk" id="bca_trunk" placeholder="0.0">
                        </div>
                        <div class="form">
                            <label>Legs (%)</label>
                            <input type="number" step="0.1" name="bca_legs" id="bca_legs" placeholder="0.0">
                        </div>
                    </div>

                    <div class="pro_filed">
                        <div class="form">
                            <label>WBF (kg)</label>
                            <input type="number" step="0.1" name="muscle_vbf" id="muscle_vbf" placeholder="0.0">
                        </div>
                        <div class="form">
                            <label>Arms (kg)</label>
                            <input type="number" step="0.1" name="muscle_arms" id="muscle_arms" placeholder="0.0">
                        </div>
                        <div class="form">
                            <label>Trunk (kg)</label>
                            <input type="number" step="0.1" name="muscle_trunk" id="muscle_trunk" placeholder="0.0">
                        </div>
                        <div class="form">
                            <label>Legs (kg)</label>
                            <input type="number" step="0.1" name="muscle_legs" id="muscle_legs" placeholder="0.0">
                        </div>
                    </div>
                    <div class="section-divider">Skeletal Muscle Mass</div>

                    <div class="pro_filed">
                        <div class="form">
                            <label>S.F. (%)</label>
                            <input type="number" step="0.1" name="bca_sf" id="bca_sf" placeholder="0.0">
                        </div>
                        <div class="form">
                            <label>V.F. (%)</label>
                            <input type="number" step="0.1" name="bca_vf" id="bca_vf" placeholder="0.0">
                        </div>
                        <div class="form"></div> {{-- Empty space for balance --}}
                        <div class="form"></div> {{-- Empty space for balance --}}
                    </div>
                    </div>

                    <div class="mt-5 text-center">
                        <button type="button" class="btn btn-submit me-2" id="submitAssessmentBtn">Submit Report</button>
                        <button type="reset" class="btn btn-cancel">Reset</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const branchSelect = document.getElementById('branchSelect');
    const patientSelect = document.getElementById('patientSelect');
    const patientNameHidden = document.getElementById('patient_name_hidden');
    const branchNameHidden = document.getElementById('branch_name_hidden');
    const patientCodeHidden = document.getElementById('patient_code_hidden');
    const patientCount = document.getElementById('patientCount');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    function showAlert(type, message) {
        const container = document.getElementById('alertContainer');
        const box = document.getElementById('alertBox');
        const msg = document.getElementById('alertMessage');
        
        container.style.display = 'block';
        box.className = `alert alert-dismissible fade show alert-${type === 'success' ? 'success' : 'danger'}`;
        msg.textContent = message;
        
        window.scrollTo({ top: 0, behavior: 'smooth' });
        setTimeout(() => { container.style.display = 'none'; }, 5000);
    }

    branchSelect.addEventListener('change', function() {
        const branchId = this.value;
        const selectedBranch = this.options[this.selectedIndex];
        branchNameHidden.value = selectedBranch.text.trim();
        
        patientSelect.innerHTML = '<option value="">Searching...</option>';
        patientSelect.disabled = true;
        patientCount.textContent = '';
        
        if (!branchId) {
            patientSelect.innerHTML = '<option value="">Select Branch First</option>';
            return;
        }
        
        fetch('/monthly-assessment/get-patients', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
            body: JSON.stringify({ branch_id: branchId })
        })
        .then(res => res.json())
        .then(data => {
            patientSelect.innerHTML = '<option value="">Choose Patient</option>';
            if (data.success && data.patients.length > 0) {
                const isMobile = window.innerWidth <= 768;
                data.patients.forEach(patient => {
                    const option = document.createElement('option');
                    option.value = patient.id;
                    
                    let displayName = patient.patient_name;
                    if (isMobile && displayName.length > 18) {
                        displayName = displayName.substring(0, 18) + '..';
                    }
                    
                    let idText = patient.patient_id ? `(ID: ${patient.patient_id})` : '';
                    if (isMobile && idText.length > 15) {
                        idText = idText.substring(0, 15) + '..';
                    }
                    
                    option.textContent = displayName + ' ' + idText;
                    option.dataset.patientName = patient.patient_name;
                    option.dataset.patientCode = patient.patient_id;
                    patientSelect.appendChild(option);
                });
                patientSelect.disabled = false;
                patientCount.textContent = `${data.patients.length} patients found`;
            } else {
                patientSelect.innerHTML = '<option value="">No patients found</option>';
            }
        });
    });

    patientSelect.addEventListener('change', function() {
        const option = this.options[this.selectedIndex];
        if (this.value) {
            patientNameHidden.value = option.dataset.patientName;
            patientCodeHidden.value = option.dataset.patientCode;
        }
    });

    // Auto Calculations
    const waist = document.getElementById('measure_waist_middle');
    const hips = document.getElementById('measure_hips');
    const whr = document.getElementById('measure_waist_hips');
    const weight = document.getElementById('measure_weight');
    const height = document.getElementById('measure_height');
    const bmi = document.getElementById('measure_bmi');

    const runMath = () => {
        const wVal = parseFloat(waist.value) || 0;
        const hVal = parseFloat(hips.value) || 0;
        const weVal = parseFloat(weight.value) || 0;
        const heVal = parseFloat(height?.value) || 0;
        
        if (wVal > 0 && hVal > 0) whr.value = (wVal / hVal).toFixed(2);
        
        if (weVal > 0 && heVal > 0) {
            let heightM = heVal > 3 ? heVal / 100 : heVal;
            bmi.value = (weVal / (heightM * heightM)).toFixed(1);
        } else {
            bmi.value = '';
        }
    };

    [waist, hips, weight, height].forEach(el => {
        if (el) el.addEventListener('input', runMath);
    });

    async function submitAssessment(status) {
        if (!branchSelect.value || !patientSelect.value) {
            showAlert('error', 'Please select branch and patient.');
            return;
        }

        const btn = status === 'draft' ? document.getElementById('saveDraftBtn') : document.getElementById('submitAssessmentBtn');
        const origText = btn.textContent;
        btn.innerHTML = '<span class="loading-spinner"></span> Saving...';
        btn.disabled = true;

        const formData = {
            branch_id: branchSelect.value,
            patient_id: patientSelect.value,
            patient_name: patientNameHidden.value,
            patient_code: patientCodeHidden.value,
            branch_name: branchNameHidden.value,
            assessment_date: document.getElementById('assessmentDate').value,
            status: status,
            measurements: {}
        };

        const numerics = [
            'waist_upper', 'waist_middle', 'waist_lower', 'hips', 'thighs', 'arms', 'waist_hips', 'weight', 'bmi',
            'bca_vbf', 'bca_arms', 'bca_trunk', 'bca_legs', 'bca_sf', 'bca_vf',
            'muscle_vbf', 'muscle_arms', 'muscle_trunk', 'muscle_legs'
        ];

        numerics.forEach(key => {
            const el = document.getElementsByName(key)[0] || document.getElementById('measure_' + key);
            if (el) formData.measurements[key] = el.value !== '' ? parseFloat(el.value) : null;
        });

        try {
            const response = await fetch('/monthly-assessment/store', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
                body: JSON.stringify(formData)
            });
            const data = await response.json();
            if (data.success) {
                showAlert('success', data.message);
                if (status === 'submitted') setTimeout(() => { location.reload(); }, 2000);
            } else {
                showAlert('error', data.message || 'Submission failed');
            }
        } catch (e) {
            showAlert('error', 'Connection error');
        } finally {
            btn.textContent = origText;
            btn.disabled = false;
        }
    }

    document.getElementById('submitAssessmentBtn').addEventListener('click', () => {
        // Basic validation before confirmation
        if (!branchSelect.value || !patientSelect.value) {
            showAlert('error', 'Please select branch and patient.');
            return;
        }

        Swal.fire({
            title: 'Are you sure?',
            text: "Do you want to submit this monthly report?",
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, submit it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                submitAssessment('submitted');
            }
        });
    });

    document.getElementById('monthlyAssessmentForm').addEventListener('submit', e => e.preventDefault());
});
</script>

@endsection
