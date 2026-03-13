@extends('admin.layouts.layouts')

@section('title', 'Edit SVC Inquiry')

@section('content')
<style>
    .form-container {
        background: white;
        padding: 30px;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        border: 1px solid #dee2e6;
        max-width: 1200px;
        margin: 0 auto;
    }
    .form-group {
        margin-bottom: 20px;
    }
    .form-row {
        display: flex;
        gap: 20px;
        margin-bottom: 15px;
    }
    .form-col {
        flex: 1;
    }
    .form-col-2 {
        flex: 2;
    }
    .form-col-3 {
        flex: 3;
    }
    label {
        font-weight: bold;
        color: #2c3e50;
        display: block;
        margin-bottom: 5px;
        font-size: 14px;
    }
    input, select, textarea {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #bdc3c7;
        border-radius: 4px;
        font-size: 14px;
        font-family: Arial, sans-serif;
    }
    textarea {
        resize: vertical;
        min-height: 80px;
    }

    .autocomplete-container {
        position: relative;
        width: 100%;
    }

    .autocomplete-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #ddd;
        border-top: none;
        border-radius: 0 0 4px 4px;
        max-height: 200px;
        overflow-y: auto;
        z-index: 1000;
        display: none;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    .autocomplete-dropdown.show {
        display: block;
    }
    .form-actions {
        display: flex;
        gap: 15px;
        justify-content: flex-end;
        margin-top: 30px;
        padding-top: 20px;
        border-top: 1px solid #dee2e6;
    }
    .btn {
        padding: 10px 25px;
        border: none;   
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
        font-weight: bold;
        text-decoration: none;
        display: inline-block;
        text-align: center;
    }
    .btn-primary {
        background: rgb(8, 104, 56);
        color: white;
    }
    .btn-primary:hover {
        background: #067945;
    }
    .btn-secondary {
        background: #6c757d;
        color: white;
    }
    .btn-secondary:hover {
        background: #5a6268;
    }
    .btn-sm {
        padding: 6px 12px;
        font-size: 12px;
    }
    .btn-success {
        background: #28a745;
        color: white;
    }
    .btn-success:hover {
        background: #218838;
    }
    .btn-danger {
        background: #dc3545;
        color: white;
        border-radius: 4px;
    }
    .btn-danger:hover {
        background: #c82333;
    }
    .required::after {
        content: " *";
        color: #e74c3c;
    }

    /* Enhanced styling for "How many days?" fields */
    .days-field {
        position: relative;
    }

    .days-field input {
        border: 2px solid #e9ecef !important;
        border-radius: 8px !important;
        padding: 10px 12px !important;
        font-weight: 500 !important;
        transition: all 0.3s ease !important;
        background: #f8f9fa !important;
        display: block !important;
        width: 100% !important;
    }

    .days-field input:focus {
        border-color: #067945;
        box-shadow: 0 0 0 3px rgba(6, 121, 69, 0.1);
        background: white;
        outline: none;
    }

    .days-field input::placeholder {
        color: #6c757d;
        font-style: italic;
    }

    /* Consistent form styling */
    .pro_filed .form-col {
        display: flex;
        flex-direction: column;
        gap: 8px;
    }

    .pro_filed .form-col label {
        font-weight: 600;
        color: #495057;
        font-size: 14px;
        margin-bottom: 4px;
    }
   .section-divider {
            display: flex;
            align-items: center;
            width: 100%;
        }

        .section-divider .title {
            white-space: nowrap;
            font-size: 16px;
            font-weight: 500;
            color: #666;
            margin-right: 10px;
        }

        .section-divider .line {
            flex-grow: 1;
            height: 1px;
            background: #dcdcdc;
        }

        .section-divider .icon-box {
            /* width: 26px;
                        height: 26px;
                        border-radius: 5px; */
            display: flex;
            align-items: center;
            justify-content: center;
            /* margin-left: 10px; */
        }

        .section-divider .icon-box i {
            color: #067945;
            font-size: 23px;
        }

    .hidden-field {
        display: none;
    }
    .form-section {
        margin: 20px 0;
        padding: 15px;
        border: 1px solid #ccc;
        border-radius: 8px;
        background: #fdfdfd;
    }

    .form-section h3 {
        margin-bottom: 10px;
        font-size: 18px;
        color: #444;
    }

     .medicine-row {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 15px;
            /* padding: 10px;
                            border: 1px dashed #ddd;
                            border-radius: 4px;
                            background: white; */
        }

    .medicine-actions {
        display: flex;
        gap: 10px;
        align-items: center;
        margin-top: 10px;
    }

    .form-col {
        flex: 1;
        min-width: 200px;
        display: flex;
        flex-direction: column;
    }

    .form-col.full-width {
        flex: 1 1 100%;
    }

    /* New styles for inline buttons */
    .field-with-button {
        display: flex;
        align-items: flex-end;
        gap: 8px;
    }

    .field-with-button .form-col {
        flex: 1;
        margin-bottom: 0;
    }

    .inline-btn {
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-size: 18px;
        font-weight: bold;
        cursor: pointer;
        border: none;
        margin-bottom: 5px;
    }

    .inline-btn.add {
        background-color: #28a745;
        color: white;
    }

    .inline-btn.remove {
        background-color: #dc3545;
        color: white;
    }

    .inline-btn:hover {
        opacity: 0.9;
    }
          .fnf-title {
            font-size: 20px;
        }

        .pro_filed .form {
            position: relative;
            width: 100%;
            margin-right: 30px !important;
        }

        input {
            outline: none
        }

        textarea {
            outline: none
        }

        label {
            color: #5a6268
        }

        select {
            outline: none
        }
           .select-with-button {
            display: flex;
            align-items: center;
            gap:10px;
        }

        .select-with-button select {
            flex: 1;
        }

        /* Styles for input with button */
        .input-with-button {
            display: flex;
            align-items: center;
              gap:10px;
        }

        .input-with-button input {
            flex: 1;
        }
         .btn-circle {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: #28a745;
            color: white;
            border: none;
            font-size: 20px;
            font-weight: bold;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
        }

        .btn-circle.small {
            width: 30px;
            height: 30px;
            font-size: 16px;
            margin-left: 8px;
        }

        .btn-circle.danger {
            background-color: #dc3545;
        }

        .btn-circle.danger:hover {
            background-color: #c82333;
            transform: scale(1.05);
        }

        .btn-circle:hover {
            background-color: #218838;
            transform: scale(1.05);
        }
          input {
            outline: none
        }

        textarea {
            outline: none
        }

        label {
            color: #5a6268
        }

        select {
            outline: none
        }

        .separate_payment {
            padding: 20px 20px 20px;
            border: 1px solid #8ec038 !important;
            border-radius: 5px;
            background: #f6f6f6;
            margin-top: 20px
        }
        #foc.form-check-input {
            accent-color: green !important;   /* modern browsers */
            -webkit-appearance: checkbox;      /* try to keep native look on WebKit */
        }

            /* extra fallback for browsers that ignore accent-color */
            #foc.form-check-input:checked {
            background-color: green !important;

        }
    }

    /* Treatment Sections Styling */

    .treatment-table .form-control-sm,
    .treatment-table .form-select-sm {
        border: 1px solid #ced4da;
        border-radius: 4px;
        padding: 0.4rem 0.6rem;
        font-size: 0.875rem;
    }

    .treatment-table .form-control-sm:focus,
    .treatment-table .form-select-sm:focus {
        border-color: #0d6efd;
        box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    }

    .treatment-table .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }

    .section-divider {
        display: flex;
        align-items: center;
        margin-bottom: 1rem;
    }

    .section-divider .title {
        font-weight: 700;
        color: #2c3e50;
        font-size: 1rem;
        padding-right: 15px;
        text-transform: uppercase;
    }

    .section-divider .line {
        flex-grow: 1;
        height: 1px;
        background: #dee2e6;
    }

    .section-divider .icon-box {
        margin-left: 15px;
        cursor: pointer;
        width: 30px;
        height: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        background: #f8f9fa;
        transition: all 0.2s;
    }

    .section-divider .icon-box:hover {
        background: #e9ecef;
    }

    .section-divider .icon-box i {
        color: #6c757d;
        font-size: 1.2rem;
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
        border: 1px solid #ddd;
        border-top: none;
        max-height: 200px;
        overflow-y: auto;
        z-index: 1000;
        display: none;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .autocomplete-dropdown.show {
        display: block;
    }

    .autocomplete-item {
        padding: 10px 12px;
        cursor: pointer;
        border-bottom: 1px solid #eee;
        font-size: 14px;
    }

    .autocomplete-item:hover,
    .autocomplete-item.selected {
        background-color: #f8f9fa;
    }

    .autocomplete-item.no-results {
        color: #999;
        font-style: italic;
    }

    .autocomplete-item.add-new {
        color: #197040;
        font-weight: bold;
        background-color: #f8fff8;
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
        background-color: #067945;
        color: white;
        padding: 4px 8px;
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

    /* Treatment Sections Styling */
    .treatment-section {
        background: #fff;
        padding: 5px;
        border-radius: 8px;
    }

    .treatment-table {
        margin-bottom: 0;
        width: 100%;
    }

    .treatment-table thead th {
        background-color: #f8f9fa;
        color: #495057;
        font-weight: 600;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        border-bottom: 2px solid #dee2e6;
        padding: 12px 8px;
    }

    .treatment-table tbody td {
        padding: 8px;
        vertical-align: middle;
    }

    .treatment-table .form-control-sm,
    .treatment-table .form-select-sm {
        font-size: 0.875rem;
        padding: 0.4rem 0.6rem;
        height: auto;
        border: 1px solid #ced4da;
        border-radius: 4px;
    }
</style>

<!-- DEBUG: Edit SVC Inquiry Page - Fields Updated -->
<div class="col-md-12 col-lg-10 m-auto p-0">
    <div class="card rounded shadow mb-5">
        <div class="card-header">
            <h3 class="bold font-up fnf-title text-success">Edit Patient Inquiry</h3>
        </div>
        <div class="row">
            <div class="col-md-12 m-auto">
                <div class="bg-light rounded-5">
                    <section class="w-100 p-4 pb-4">
                        <div class="">
                            <form action="{{ route('update.svc.inquiry', $patient->id) }}" method="POST" id="inquiryForm">
                                @csrf
                                @method('PUT')
                                
                                <datalist id="dose-options">
                                    <option value="1 – 0 – 0">
                                    <option value="0 – 0 – 1">
                                    <option value="1 – 0 – 1">
                                    <option value="1 – 1 – 1">
                                    <option value="1 – 1 – 0">
                                    <option value="0 – 1 – 0">
                                    <option value="0 – 1 – 1">
                                    <option value="1/2 – 0 – 1/2">
                                    <option value="1/2 – 1/2 – 1/2">
                                    <option value="2 – 0 – 0">
                                    <option value="0 – 0 – 2">
                                    <option value="2 – 0 – 2">
                                    <option value="1 – 1 – 1 – 1">
                                </datalist>

                                <input type="hidden" id="patient_id" name="patient_id" value="{{ $patient->patient_id }}">
                                <input type="hidden" id="branch_id" name="branch_id" value="{{ $patient->branch_id }}">
                                <input type="hidden" name="branch" value="SVC">

                                <div class="section-divider">Personal Information</div>
                                <div class="pt-4">
                                    <div class="pro_filed d-sm-block d-md-flex">
                                        <div class="form">
                                            <div class="form-col">
                                                <label for="patient_name" class="required">Patient Name</label>
                                                <input type="text" id="patient_name" name="patient_name"
                                                    placeholder="Enter patient name" value="{{ $patient->patient_name }}" required>
                                            </div>
                                        </div>
                                        <div class="form">
                                            <div class="form-col">
                                                <label for="address" class="required">Address</label>
                                                <textarea id="address" name="address" placeholder="Enter complete address" required>{{ $patient->address }}</textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="pro_filed d-sm-block d-md-flex pt-3">
                                        <div class="form">
                                            <div class="form-col">
                                                <label for="inquiry_date" class="required">Inquiry Date</label>
                                                <input type="date" id="inquiry_date" name="inquiry_date"
                                                    value="{{ $patient->inquiry_date ? \Carbon\Carbon::parse($patient->inquiry_date)->format('Y-m-d') : '' }}" required>
                                            </div>
                                        </div>
                                        <div class="form">
                                            <div class="form-col">
                                                <label for="inquiry_time">Inquiry Time</label>
                                                <input type="time" id="inquiry_time" name="inquiry_time"
                                                    value="{{ $patient->getMeta('inquiry_time') }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="pro_filed d-sm-block d-md-flex pt-3">
                                        <div class="form">
                                            <div class="form-col">
                                                <label for="gender" class="required">Gender</label>
                                                <select id="gender" name="gender" required>
                                                    <option value="">Select Gender</option>
                                                    <option value="male" {{ ($meta['gender'] ?? '') == 'male' ? 'selected' : '' }}>Male</option>
                                                    <option value="female" {{ ($meta['gender'] ?? '') == 'female' ? 'selected' : '' }}>Female</option>
                                                    <option value="other" {{ ($meta['gender'] ?? '') == 'other' ? 'selected' : '' }}>Other</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form">
                                            <div class="form-col">
                                                <label for="weight">Weight (kg)</label>
                                                <input type="number" id="weight" name="weight"
                                                    placeholder="Enter weight" value="{{ $patient->getMeta('weight') ?? ''}}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="pro_filed d-sm-block d-md-flex pt-3">
                                        <div class="form">
                                            <div class="form-col">
                                                <label for="phone">Phone Number</label>
                                                <input type="number" id="phone" name="phone"
                                                    placeholder="Enter phone number" value="{{ $patient->getMeta('phone') }}">
                                            </div>
                                        </div>
                                        <div class="form">
                                            <div class="form-col">
                                                <label for="age" class="required">Age</label>
                                                <input type="number" id="age" name="age"
                                                    placeholder="Enter age" value="{{ $patient->age }}" required>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="pro_filed d-sm-block d-md-flex pt-3">
                                        <div class="form">
                                            <div class="form-col">
                                                <label for="pt_status">PT.Status</label>
                                                <select id="pt_status" name="pt_status">
                                                    <option value="">Select Status</option>
                                                    <option value="IPD" {{ $patient->getMeta('pt_status') == 'IPD' ? 'selected' : '' }}>IPD</option>
                                                    <option value="OPD" {{ $patient->getMeta('pt_status') == 'OPD' ? 'selected' : '' }}>OPD</option>
                                                    <option value="Home Visit" {{ $patient->getMeta('pt_status') == 'Home Visit' ? 'selected' : '' }}>Home Visit</option>
                                                </select>
                                            </div>
                                        </div>
                                        <div class="form">
                                            <div class="form-col">
                                                <label for="temperature">Temperature (°C)</label>
                                                <input type="text" step="0.1" id="temperature" name="temperature"
                                                    placeholder="e.g., 98.6" value="{{ $patient->getMeta('temperature') }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="pro_filed d-sm-block d-md-flex pt-3">
                                        <div class="form">
                                            <div class="form-col">
                                                <label for="diagnosis" class="required">Diagnosis</label>
                                                <div class="multi-select-container">
                                                    <div class="selected-items" id="diagnosis-selected">
                                                        <!-- Selected diagnoses will appear here -->
                                                    </div>
                                                    <div class="autocomplete-container">
                                                        <input type="text" id="diagnosis" 
                                                            placeholder="Type to add diagnoses..." class="form-control" autocomplete="off">
                                                        <div class="autocomplete-dropdown" id="diagnosis-dropdown"></div>
                                                    </div>
                                                    <input type="hidden" name="diagnosis" id="diagnosis-hidden" value="{{ old('diagnosis', $patient->diagnosis) }}">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form">
                                            <div class="form-col">
                                                <label for="complain">Complaint</label>
                                                <div class="multi-select-container">
                                                    <div class="selected-items" id="complain-selected">
                                                        <!-- Selected complaints will appear here -->
                                                    </div>
                                                    <div class="autocomplete-container">
                                                        <input type="text" id="complain" 
                                                            placeholder="Type to add complaints..." class="form-control" autocomplete="off">
                                                        <div class="autocomplete-dropdown" id="complain-dropdown"></div>
                                                    </div>
                                                    <input type="hidden" name="complain" id="complain-hidden" value="{{ old('complain', $patient->getMeta('complain')) }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="pro_filed d-sm-block d-md-flex pt-3">
                                        <div class="form">
                                            <div class="form-col">
                                                <label for="pulse">Pulse</label>
                                                <input type="text" id="pulse" name="pulse"
                                                    placeholder="Enter pulse rate" value="{{ $patient->getMeta('pulse') ?? '' }}">
                                            </div>
                                        </div>
                                        <div class="form">
                                            <div class="form-col">
                                                <label for="blood_pressure">Blood Pressure</label>
                                                <input type="text" id="blood_pressure" name="blood_pressure"
                                                    placeholder="e.g., 120/80" value="{{ $patient->getMeta('blood_pressure') }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="pro_filed d-sm-block d-md-flex pt-3">
                                        <div class="form">
                                            <div class="form-col">
                                                <label for="spo2">SpO2 (%)</label>
                                                <input type="number" id="spo2" name="spo2"
                                                    placeholder="Enter SpO2" value="{{ $patient->getMeta('spo2') }}">
                                            </div>
                                        </div>
                                        <div class="form">
                                            <div class="form-col">
                                                <label for="rbs">RBS</label>
                                                <input type="text" id="rbs" name="rbs"
                                                    placeholder="Enter RBS" value="{{ $patient->getMeta('rbs') }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="section-divider">Medical Information</div>

                                    <div class="pro_filed d-sm-block d-md-flex pt-3">
                                        <div class="form">
                                            <div class="form-col">
                                                <label for="investigation">Investigation</label>
                                                <textarea id="investigation" name="investigation" placeholder="Enter investigation details">{{ $patient->getMeta('investigation') }}</textarea>
                                            </div>
                                        </div>
                                        <div class="form">
                                            <div class="form-col">
                                                <label for="past_history">Past History</label>
                                                <textarea id="past_history" name="past_history" placeholder="Enter past medical history">{{ $patient->getMeta('past_history') }}</textarea>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="pro_filed d-sm-block d-md-flex pt-3">
                                        <div class="form">
                                            <div class="form-col">
                                                <label for="family_history">Family History</label>
                                                <textarea id="family_history" name="family_history" placeholder="Enter family medical history">{{ $patient->getMeta('family_history') }}</textarea>
                                            </div>
                                        </div>
                                        <div class="form">
                                            <div class="form-col">
                                                <label for="doctor_id">Assign Doctor</label>
                                                <select name="doctor_id" id="doctor_id" class="form-control">
                                                    <option value="">Select Doctor</option>
                                                    @foreach($doctors as $doctor)
                                                        <option value="{{ $doctor->id }}" 
                                                            {{ (isset($meta['doctor_id']) && $meta['doctor_id'] == $doctor->id) ? 'selected' : '' }}>
                                                            {{ $doctor->name }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="section-divider mt-4">
                                        <div class="title">Laboratory Investigation</div>
                                        <div class="line"></div>
                                        <div class="icon-box" onclick="toggleSection(this, 'lab-investigation-section')">
                                            <i class="bi bi-dash-lg" id="lab-toggle-icon"></i>
                                        </div>
                                    </div>

                                    <div class="lab-investigation-section">
                                        <div class="pro_filed d-sm-block d-md-flex pt-3">
                                            <div class="form">
                                                <div class="form-col">
                                                    <label for="hb">HB</label>
                                                    <input type="text" id="hb" name="hb" placeholder="HB" value="{{ $patient->getMeta('hb') }}">
                                                </div>
                                            </div>
                                            <div class="form">
                                                <div class="form-col">
                                                    <label for="tc">TC</label>
                                                    <input type="text" id="tc" name="tc" placeholder="TC" value="{{ $patient->getMeta('tc') }}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="pro_filed d-sm-block d-md-flex pt-3">
                                            <div class="form">
                                                <div class="form-col">
                                                    <label for="pc">PC</label>
                                                    <input type="text" id="pc" name="pc" placeholder="PC" value="{{ $patient->getMeta('pc') }}">
                                                </div>
                                            </div>
                                            <div class="form">
                                                <div class="form-col">
                                                    <label for="MP">MP</label>
                                                    <input type="text" id="MP" name="MP" placeholder="MP" value="{{ $patient->getMeta('MP') }}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="pro_filed d-sm-block d-md-flex pt-3">
                                            <div class="form">
                                                <div class="form-col">
                                                    <label for="HB1AC">HB1AC</label>
                                                    <input type="text" id="HB1AC" name="HB1AC" placeholder="HB1AC" value="{{ $patient->getMeta('HB1AC') }}">
                                                </div>
                                            </div>
                                            <div class="form">
                                                <div class="form-col">
                                                    <label for="fbs">FBS</label>
                                                    <input type="text" id="fbs" name="fbs" placeholder="FBS" value="{{ $patient->getMeta('fbs') }}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="pro_filed d-sm-block d-md-flex pt-3">
                                            <div class="form">
                                                <div class="form-col">
                                                    <label for="pp2bs">PP2BS</label>
                                                    <input type="text" id="pp2bs" name="pp2bs" placeholder="PP2BS" value="{{ $patient->getMeta('pp2bs') }}">
                                                </div>
                                            </div>
                                            <div class="form">
                                                <div class="form-col">
                                                    <label for="S_widal">S.widal</label>
                                                    <input type="text" id="S_widal" name="S_widal" placeholder="S.widal" value="{{ $patient->getMeta('S_widal') }}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="pro_filed d-sm-block d-md-flex pt-3">
                                            <div class="form">
                                                <div class="form-col">
                                                    <label for="USG">USG</label>
                                                    <input type="text" id="USG" name="USG" placeholder="USG" value="{{ $patient->getMeta('USG') }}">
                                                </div>
                                            </div>
                                            <div class="form">
                                                <div class="form-col">
                                                    <label for="X_ray">X-ray</label>
                                                    <input type="text" id="X_ray" name="X_ray" placeholder="X-ray" value="{{ $patient->getMeta('X_ray') }}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="pro_filed d-sm-block d-md-flex pt-3">
                                            <div class="form">
                                                <div class="form-col">
                                                    <label for="SGPT">SGPT</label>
                                                    <input type="text" id="SGPT" name="SGPT" placeholder="SGPT" value="{{ $patient->getMeta('SGPT') }}">
                                                </div>
                                            </div>
                                            <div class="form">
                                                <div class="form-col">
                                                    <label for="s_creatinine">S. Creatinine</label>
                                                    <input type="text" id="s_creatinine" name="s_creatinine" placeholder="S. Creatinine" value="{{ $patient->getMeta('s_creatinine') }}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="pro_filed d-sm-block d-md-flex pt-3">
                                            <div class="form">
                                                <div class="form-col">
                                                    <label for="NS1Ag">NS1Ag</label>
                                                    <input type="text" id="NS1Ag" name="NS1Ag" placeholder="NS1Ag" value="{{ $patient->getMeta('NS1Ag') }}">
                                                </div>
                                            </div>
                                            <div class="form">
                                                <div class="form-col">
                                                    <label for="DengueIGM">Dengue IGM</label>
                                                    <input type="text" id="DengueIGM" name="DengueIGM" placeholder="Dengue IGM" value="{{ $patient->getMeta('DengueIGM') }}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="pro_filed d-sm-block d-md-flex pt-3">
                                            <div class="form">
                                                <div class="form-col">
                                                    <label for="s_cholesterol">S. Cholesterol</label>
                                                    <input type="text" id="s_cholesterol" name="s_cholesterol" placeholder="S.Cholesterol" value="{{ $patient->getMeta('s_cholesterol') }}">
                                                </div>
                                            </div>
                                            <div class="form">
                                                <div class="form-col">
                                                    <label for="STriglyceride">S.Triglyceride</label>
                                                    <input type="text" id="STriglyceride" name="STriglyceride" placeholder="S.Triglyceride" value="{{ $patient->getMeta('STriglyceride') }}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="pro_filed d-sm-block d-md-flex pt-3">
                                            <div class="form">
                                                <div class="form-col">
                                                    <label for="HDL">HDL</label>
                                                    <input type="text" id="HDL" name="HDL" placeholder="HDL" value="{{ $patient->getMeta('HDL') }}">
                                                </div>
                                            </div>
                                            <div class="form">
                                                <div class="form-col">
                                                    <label for="LDL">LDL</label>
                                                    <input type="text" id="LDL" name="LDL" placeholder="LDL" value="{{ $patient->getMeta('LDL') }}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="pro_filed d-sm-block d-md-flex pt-3">
                                            <div class="form">
                                                <div class="form-col">
                                                    <label for="VLDL">VLDL</label>
                                                    <input type="text" id="VLDL" name="VLDL" placeholder="VLDL" value="{{ $patient->getMeta('VLDL') }}">
                                                </div>
                                            </div>
                                            <div class="form">
                                                <div class="form-col">
                                                    <label for="SB12">S.B12</label>
                                                    <input type="text" id="SB12" name="SB12" placeholder="S.B12" value="{{ $patient->getMeta('SB12') }}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="pro_filed d-sm-block d-md-flex pt-3">
                                            <div class="form">
                                                <div class="form-col">
                                                    <label for="SD3">S.D3</label>
                                                    <input type="text" id="SD3" name="SD3" placeholder="S.D3" value="{{ $patient->getMeta('SD3') }}">
                                                </div>
                                            </div>
                                            <div class="form">
                                                <div class="form-col">
                                                    <label for="Urine">Urine</label>
                                                    <input type="text" id="Urine" name="Urine" placeholder="Urine" value="{{ $patient->getMeta('Urine') }}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="pro_filed d-sm-block d-md-flex pt-3">
                                            <div class="form">
                                                <div class="form-col">
                                                    <label for="CRP">CRP</label>
                                                    <input type="text" id="CRP" name="CRP" placeholder="CRP" value="{{ $patient->getMeta('CRP') }}">
                                                </div>
                                            </div>
                                            <div class="form">
                                                <div class="form-col">
                                                    <label for="St3">S.t3</label>
                                                    <input type="text" id="St3" name="St3" placeholder="S.t3" value="{{ $patient->getMeta('St3') }}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="pro_filed d-sm-block d-md-flex pt-3">
                                            <div class="form">
                                                <div class="form-col">
                                                    <label for="St4">S.T4</label>
                                                    <input type="text" id="St4" name="St4" placeholder="S.t4" value="{{ $patient->getMeta('St4') }}">
                                                </div>
                                            </div>
                                            <div class="form">
                                                <div class="form-col">
                                                    <label for="STSH">S.TSH</label>
                                                    <input type="text" id="STSH" name="STSH" placeholder="S.TSH" value="{{ $patient->getMeta('STSH') }}">
                                                </div>
                                            </div>
                                        </div>

                                        <div class="pro_filed d-sm-block d-md-flex pt-3">
                                            <div class="form">
                                                <div class="form-col">
                                                    <label for="ESR">ESR</label>
                                                    <input type="text" id="ESR" name="ESR" placeholder="ESR" value="{{ $patient->getMeta('ESR') }}">
                                                </div>
                                            </div>
                                            <div class="form">
                                                <div class="form-col">
                                                    <label for="specific_test">Any specific Test</label>
                                                    <input type="text" id="specific_test" name="specific_test" placeholder="Any specific Test" value="{{ $patient->getMeta('specific_test') }}">
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- INSIDE TREATMENT -->
                                    <div class="treatment-section mb-4">
                                        <div class="section-divider">
                                            <div class="title">Inside Treatment</div>
                                            <div class="line"></div>
                                            <div class="icon-box" onclick="toggleInsideSection(this)">
                                                <i class="bi bi-dash-lg" id="inside-toggle-icon"></i>
                                            </div>
                                        </div>
                                        
                                        <div id="inside-section" class="mt-3">
                                            <table class="table table-borderless treatment-table">
                                                <thead>
                                                    <tr class="text-muted small">
                                                        <th style="width: 30%">Medicine</th>
                                                        <th style="width: 20%">Dose</th>
                                                        <th style="width: 15%">Days</th>
                                                        <th style="width: 20%">Timing</th>
                                                        <th style="width: 15%"></th>
                                                    </tr>
                                                </thead>
                                                <tbody id="inside-treatment-body">
                                                    @php
                                                        $insideRows = $treatments['inside'] ?? [];
                                                    @endphp
                                                    @if(count($insideRows) > 0)
                                                        @foreach($insideRows as $index => $row)
                                                            <tr>
                                                                <td><input type="text" name="inside_medicine[]" class="form-control form-control-sm" value="{{ $row['medicine'] ?? '' }}" placeholder="Medicine name"></td>
                                                                 <td>
                                                                     <div class="autocomplete-container">
                                                                         <input type="text" name="inside_dose[]" class="form-control form-control-sm dose-input" value="{{ $row['dose'] ?? '' }}" placeholder="Select or type dose" autocomplete="off">
                                                                         <div class="autocomplete-dropdown"></div>
                                                                     </div>
                                                                 </td>
                                                                <td><input type="text" name="inside_days[]" class="form-control form-control-sm" value="{{ $row['days'] ?? '' }}" placeholder="Days"></td>
                                                                <td>
                                                                    <select name="inside_timing[]" class="form-select form-select-sm">
                                                                        <option value="">Select</option>
                                                                        <option {{ (isset($row['timing']) && $row['timing'] == 'Before Food') ? 'selected' : '' }}>Before Food</option>
                                                                        <option {{ (isset($row['timing']) && $row['timing'] == 'After Food') ? 'selected' : '' }}>After Food</option>
                                                                        <option {{ (isset($row['timing']) && $row['timing'] == 'With Food') ? 'selected' : '' }}>With Food</option>
                                                                    </select>
                                                                </td>
                                                                <td>
                                                                    <div class="d-flex gap-1">
                                                                        @if($loop->first)
                                                                            <button type="button" class="btn btn-success btn-sm" onclick="addInsideRow()">
                                                                                <i class="bi bi-plus"></i> Add
                                                                            </button>
                                                                        @else
                                                                            <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove()">
                                                                                <i class="bi bi-trash"></i>
                                                                            </button>
                                                                        @endif
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @else
                                                        <tr>
                                                            <td><input type="text" name="inside_medicine[]" class="form-control form-control-sm" placeholder="Medicine name" autocomplete="off"></td>
                                                             <td>
                                                                 <div class="autocomplete-container">
                                                                     <input type="text" name="inside_dose[]" class="form-control form-control-sm dose-input" placeholder="Select or type dose" autocomplete="off">
                                                                     <div class="autocomplete-dropdown"></div>
                                                                 </div>
                                                             </td>
                                                            <td><input type="text" name="inside_days[]" class="form-control form-control-sm" placeholder="Days"></td>
                                                            <td>
                                                                <select name="inside_timing[]" class="form-select form-select-sm">
                                                                    <option value="">Select</option>
                                                                    <option>Before Food</option>
                                                                    <option>After Food</option>
                                                                    <option>With Food</option>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <button type="button" class="btn btn-success btn-sm" onclick="addInsideRow()">
                                                                    <i class="bi bi-plus"></i> Add
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <!-- PRESCRIPTION -->
                                    <div class="treatment-section mb-4">
                                        <div class="section-divider">
                                            <div class="title">Prescription</div>
                                            <div class="line"></div>
                                            <div class="icon-box" onclick="togglePrescriptionSection(this)">
                                                <i class="bi bi-dash-lg" id="prescription-toggle-icon"></i>
                                            </div>
                                        </div>
                                        
                                        <div id="prescription-section" class="mt-3">
                                            <table class="table table-borderless treatment-table">
                                                <thead>
                                                    <tr class="text-muted small">
                                                        <th style="width: 30%">Medicine</th>
                                                        <th style="width: 20%">Dose</th>
                                                        <th style="width: 15%">Days</th>
                                                        <th style="width: 20%">Timing</th>
                                                        <th style="width: 15%"></th>
                                                    </tr>
                                                </thead>
                                                <tbody id="prescription-treatment-body">
                                                    @php
                                                        $prescriptionRows = $treatments['prescription'] ?? [];
                                                    @endphp
                                                    @if(count($prescriptionRows) > 0)
                                                        @foreach($prescriptionRows as $index => $row)
                                                            <tr>
                                                                <td><input type="text" name="prescription_medicine[]" class="form-control form-control-sm" value="{{ $row['medicine'] ?? '' }}" placeholder="Medicine name"></td>
                                                                 <td>
                                                                     <div class="autocomplete-container">
                                                                         <input type="text" name="prescription_dose[]" class="form-control form-control-sm dose-input" value="{{ $row['dose'] ?? '' }}" placeholder="Select or type dose" autocomplete="off">
                                                                         <div class="autocomplete-dropdown"></div>
                                                                     </div>
                                                                 </td>
                                                                <td><input type="text" name="prescription_days[]" class="form-control form-control-sm" value="{{ $row['days'] ?? '' }}" placeholder="Days"></td>
                                                                <td>
                                                                    <select name="prescription_timing[]" class="form-select form-select-sm">
                                                                        <option value="">Select</option>
                                                                        <option {{ (isset($row['timing']) && $row['timing'] == 'Before Food') ? 'selected' : '' }}>Before Food</option>
                                                                        <option {{ (isset($row['timing']) && $row['timing'] == 'After Food') ? 'selected' : '' }}>After Food</option>
                                                                        <option {{ (isset($row['timing']) && $row['timing'] == 'With Food') ? 'selected' : '' }}>With Food</option>
                                                                    </select>
                                                                </td>
                                                                <td>
                                                                    <div class="d-flex gap-1">
                                                                        @if($loop->first)
                                                                            <button type="button" class="btn btn-success btn-sm" onclick="addPrescriptionRow()">
                                                                                <i class="bi bi-plus"></i> Add
                                                                            </button>
                                                                        @else
                                                                            <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove()">
                                                                                <i class="bi bi-trash"></i>
                                                                            </button>
                                                                        @endif
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @else
                                                        <tr>
                                                            <td><input type="text" name="prescription_medicine[]" class="form-control form-control-sm" placeholder="Medicine name" autocomplete="off"></td>
                                                             <td>
                                                                 <div class="autocomplete-container">
                                                                     <input type="text" name="prescription_dose[]" class="form-control form-control-sm dose-input" placeholder="Select or type dose" autocomplete="off">
                                                                     <div class="autocomplete-dropdown"></div>
                                                                 </div>
                                                             </td>
                                                            <td><input type="text" name="prescription_days[]" class="form-control form-control-sm" placeholder="Days"></td>
                                                            <td>
                                                                <select name="prescription_timing[]" class="form-select form-select-sm">
                                                                    <option value="">Select</option>
                                                                    <option>Before Food</option>
                                                                    <option>After Food</option>
                                                                    <option>With Food</option>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <button type="button" class="btn btn-success btn-sm" onclick="addPrescriptionRow()">
                                                                    <i class="bi bi-plus"></i> Add
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <!-- Homeopathic and Indoor Treatment Section -->
                                    <!-- HOMEOPATHIC TREATMENT -->
                                    <div class="treatment-section mb-4">
                                        <div class="section-divider">
                                            <div class="title">Homeopathic Treatment</div>
                                            <div class="line"></div>
                                            <div class="icon-box" onclick="toggleHomeoSection(this)">
                                                <i class="bi bi-dash-lg" id="homeo-toggle-icon"></i>
                                            </div>
                                        </div>
                                        
                                        <div id="homeo-section" class="mt-3">
                                            <table class="table table-borderless treatment-table">
                                                <thead>
                                                    <tr class="text-muted small">
                                                        <th style="width: 30%">Medicine</th>
                                                        <!-- <th style="width: 20%">Dose</th>
                                                        <th style="width: 15%">Days</th> -->
                                                        <th style="width: 20%">Timing</th>
                                                        <th style="width: 15%"></th>
                                                    </tr>
                                                </thead>
                                                <tbody id="homeo-treatment-body">
                                                    @php
                                                        $homeoRows = $treatments['homeo'] ?? [];
                                                    @endphp
                                                    @if(count($homeoRows) > 0)
                                                        @foreach($homeoRows as $index => $row)
                                                            <tr>
                                                                <td><input type="text" name="homeo_medicine[]" class="form-control form-control-sm" value="{{ $row['medicine'] ?? '' }}" placeholder="Medicine name"></td>
                                                                <!-- <td>
                                                                    <div class="autocomplete-container">
                                                                        <input type="text" name="homeo_dose[]" class="form-control form-control-sm dose-input" value="{{ $row['dose'] ?? '' }}" placeholder="Select or type dose" autocomplete="off">
                                                                        <div class="autocomplete-dropdown"></div>
                                                                    </div>
                                                                </td> -->
                                                                <!-- <td><input type="text" name="homeo_days[]" class="form-control form-control-sm" value="{{ $row['days'] ?? '' }}" placeholder="Days"></td> -->
                                                                <td>
                                                                    <select name="homeo_timing[]" class="form-select form-select-sm">
                                                                        <option value="">Select</option>
                                                                        <option {{ (isset($row['timing']) && $row['timing'] == 'Before Food') ? 'selected' : '' }}>Before Food</option>
                                                                        <option {{ (isset($row['timing']) && $row['timing'] == 'After Food') ? 'selected' : '' }}>After Food</option>
                                                                        <option {{ (isset($row['timing']) && $row['timing'] == 'With Food') ? 'selected' : '' }}>With Food</option>
                                                                    </select>
                                                                </td>
                                                                <td>
                                                                    <div class="d-flex gap-1">
                                                                        @if($loop->first)
                                                                            <button type="button" class="btn btn-success btn-sm" onclick="addHomeoRow()">
                                                                                <i class="bi bi-plus"></i> Add
                                                                            </button>
                                                                        @else
                                                                            <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove()">
                                                                                <i class="bi bi-trash"></i>
                                                                            </button>
                                                                        @endif
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @else
                                                        <tr>
                                                            <td><input type="text" name="homeo_medicine[]" class="form-control form-control-sm" placeholder="Medicine name" autocomplete="off"></td>
                                                            <td>
                                                                <div class="autocomplete-container">
                                                                    <input type="text" name="homeo_dose[]" class="form-control form-control-sm dose-input" placeholder="Select or type dose" autocomplete="off">
                                                                    <div class="autocomplete-dropdown"></div>
                                                                </div>
                                                            </td>
                                                            <td><input type="text" name="homeo_days[]" class="form-control form-control-sm" placeholder="Days"></td>
                                                            <td>
                                                                <select name="homeo_timing[]" class="form-select form-select-sm">
                                                                    <option value="">Select</option>
                                                                    <option>Before Food</option>
                                                                    <option>After Food</option>
                                                                    <option>With Food</option>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                <button type="button" class="btn btn-success btn-sm" onclick="addHomeoRow()">
                                                                    <i class="bi bi-plus"></i> Add
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                             
                                    <div class="treatment-section mb-4">
                                        <div class="section-divider">
                                            <div class="title">Indoor Treatment</div>
                                            <div class="line"></div>
                                            <div class="icon-box" onclick="toggleIndoorSection(this)">
                                                <i class="bi bi-dash-lg" id="indoor-toggle-icon"></i>
                                            </div>
                                        </div>
                                        
                                        <div id="indoor-section" class="mt-3">
                                            <table class="table table-borderless treatment-table">
                                                <thead>
                                                    <tr class="text-muted small">
                                                        <th style="width: 18%">Medicine</th>
                                                        <!-- <th style="width: 15%">Dose</th>
                                                        <th style="width: 10%">Days</th> -->
                                                        <th style="width: 12%">Date</th>
                                                        <th style="width: 10%">Time</th>
                                                        <th style="width: 20%">Note</th>
                                                        <th style="width: 15%"></th>
                                                    </tr>
                                                </thead>
                                                <tbody id="indoor-treatment-body">
                                                    @php
                                                        $indoorRows = $treatments['indoor'] ?? [];
                                                    @endphp
                                                    @if(count($indoorRows) > 0)
                                                        @foreach($indoorRows as $index => $row)
                                                            <tr>
                                                                <td><input type="text" name="indoor_medicine[]" class="form-control form-control-sm" value="{{ $row['medicine'] ?? '' }}" placeholder="Medicine name"></td>
                                                                <!-- <td>
                                                                    <div class="autocomplete-container">
                                                                        <input type="text" name="indoor_dose[]" class="form-control form-control-sm dose-input" value="{{ $row['dose'] ?? '' }}" placeholder="Select or type dose" autocomplete="off">
                                                                        <div class="autocomplete-dropdown"></div>
                                                                    </div>
                                                                </td> -->
                                                                <!-- <td><input type="text" name="indoor_days[]" class="form-control form-control-sm" value="{{ $row['days'] ?? '' }}" placeholder="Days"></td> -->
                                                                <td><input type="date" name="indoor_date[]" class="form-control form-control-sm" value="{{ $row['date'] ?? '' }}"></td>
                                                                <td><input type="time" name="indoor_time[]" class="form-control form-control-sm" value="{{ $row['time'] ?? '' }}"></td>
                                                                <td><input type="text" name="indoor_note[]" class="form-control form-control-sm" value="{{ $row['note'] ?? '' }}" placeholder="Note"></td>
                                                                <td>
                                                                    <div class="d-flex gap-1">
                                                                        @if($loop->first)
                                                                            <button type="button" class="btn btn-success btn-sm" onclick="addMedicineRow()">
                                                                                <i class="bi bi-plus"></i> Add
                                                                            </button>
                                                                        @else
                                                                            <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove()">
                                                                                <i class="bi bi-trash"></i>
                                                                            </button>
                                                                        @endif
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @else
                                                        <tr>
                                                            <td><input type="text" name="indoor_medicine[]" class="form-control form-control-sm" placeholder="Medicine name" autocomplete="off"></td>
                                                             <td>
                                                                 <div class="autocomplete-container">
                                                                     <input type="text" name="indoor_dose[]" class="form-control form-control-sm dose-input" placeholder="Select or type dose" autocomplete="off">
                                                                     <div class="autocomplete-dropdown"></div>
                                                                 </div>
                                                             </td>
                                                            <td><input type="text" name="indoor_days[]" class="form-control form-control-sm" placeholder="Days"></td>
                                                            <td><input type="date" name="indoor_date[]" class="form-control form-control-sm"></td>
                                                            <td><input type="time" name="indoor_time[]" class="form-control form-control-sm"></td>
                                                            <td><input type="text" name="indoor_note[]" class="form-control form-control-sm" placeholder="Note"></td>
                                                            <td>
                                                                <button type="button" class="btn btn-success btn-sm" onclick="addMedicineRow()">
                                                                    <i class="bi bi-plus"></i> Add
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                    <!-- OTHER TREATMENT -->
                                    <div class="treatment-section mb-4">
                                        <div class="section-divider">
                                            <div class="title">Other Treatment</div>
                                            <div class="line"></div>
                                            <div class="icon-box" onclick="toggleOtherSection(this)">
                                                <i class="bi bi-dash-lg" id="other-toggle-icon"></i>
                                            </div>
                                        </div>
                                        
                                        <div id="other-section" class="mt-3">
                                            <table class="table table-borderless treatment-table">
                                                <thead>
                                                    <tr class="text-muted small">
                                                        <th style="width: 40%">Medicine</th>
                                                        <th style="width: 45%">Note</th>
                                                        <th style="width: 15%"></th>
                                                    </tr>
                                                </thead>
                                                <tbody id="other-treatment-body">
                                                    @php
                                                        $otherRows = $treatments['other'] ?? [];
                                                    @endphp
                                                    @if(count($otherRows) > 0)
                                                        @foreach($otherRows as $index => $row)
                                                            <tr>
                                                                <td><input type="text" name="other_medicine[]" class="form-control form-control-sm" value="{{ $row['medicine'] ?? '' }}" placeholder="Medicine name"></td>
                                                                <td><input type="text" name="other_note[]" class="form-control form-control-sm" value="{{ $row['note'] ?? '' }}" placeholder="Note"></td>
                                                                <td>
                                                                    <div class="d-flex gap-1">
                                                                        @if($loop->first)
                                                                            <button type="button" class="btn btn-success btn-sm" onclick="addOtherRow()">
                                                                                <i class="bi bi-plus"></i> Add
                                                                            </button>
                                                                        @else
                                                                            <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove()">
                                                                                <i class="bi bi-trash"></i>
                                                                            </button>
                                                                        @endif
                                                                    </div>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    @else
                                                        <tr>
                                                            <td><input type="text" name="other_medicine[]" class="form-control form-control-sm" placeholder="Medicine name" autocomplete="off"></td>
                                                            <td><input type="text" name="other_note[]" class="form-control form-control-sm" placeholder="Note"></td>
                                                            <td>
                                                                <button type="button" class="btn btn-success btn-sm" onclick="addOtherRow()">
                                                                    <i class="bi bi-plus"></i> Add
                                                                </button>
                                                            </td>
                                                        </tr>
                                                    @endif
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>

                                 <div class="pro_filed d-sm-block d-md-flex pt-3">
                                        <div class="form">
                                            <div class="form-col">
                                                <label for="reference_by">Reference by</label>
                                                <input type="text" id="reference_by" name="reference_by"
                                                    placeholder="Reference by" value="{{ $patient->getMeta('reference_by') }}">
                                            </div>
                                        </div>
                                        <div class="form">
                                            <div class="form-col">
                                                <label for="referto">Refer to</label>
                                                <input type="text" id="referto" name="referto"
                                                    placeholder="Refer to" value="{{ $patient->getMeta('referto') }}">
                                            </div>
                                        </div>
                                    </div>

                                    <div class="pro_filed d-sm-block d-md-flex pt-3">
                                        <div class="form">
                                            <div class="form-col">
                                                <label for="next_follow_date">Next follow up date</label>
                                                <input type="date" id="next_follow_date" name="next_follow_date"
                                                    value="{{ $patient->next_follow_date }}">
                                            </div>
                                        </div>
                                        <div class="form">
                                            <div class="form-col">
                                                <label for="notes">Notes</label>
                                                <input type="text" id="notes" name="notes"
                                                    placeholder="Notes" value="{{ $patient->getMeta('notes') }}">
                                            </div>
                                        </div>
                                    </div>

                                    <!-- FOC Checkbox -->
                                    <div class="d-flex align-items-center bg-light p-3 mb-3">
                                        <input type="checkbox" name="foc" id="foc" class="form-check-input me-3"
                                            {{ $patient->getMeta('foc') ? 'checked' : '' }}>

                                        <label for="foc" class="mb-0 fw-semibold">
                                            FOC (Free of Charge Inquiry)
                                        </label>
                                    </div>

                                    <div class="section-divider mt-4">
                                        <div class="title">Payment Information</div>
                                        <div class="line"></div>
                                    </div>
                                    <div id="payment_section" class="pt-4">
                                        <div class="pro_filed d-sm-block d-md-flex">
                                            <div class="form">
                                                <div class="form-col">
                                                    <label for="charge_id">Registration Charges</label>
                                                    <select id="charge_id" name="charge_id" class="form-control">
                                                        <option value="">Select Charge</option>
                                                        @foreach($charges as $charge)
                                                            <option value="{{ $charge->id }}" 
                                                                    data-price="{{ $charge->charges_price }}"
                                                                    {{ ($patient->getMeta('charge_id') == $charge->id) ? 'selected' : '' }}>
                                                                {{ $charge->charges_name }} - ₹{{ $charge->charges_price }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <input type="hidden" id="total_payment" name="total_payment" value="{{ $patient->getMeta('total_payment') ?? '200' }}">
                                                </div>
                                            </div>
                                            <div class="form">
                                                <div class="form-col">
                                                    <label for="given_payment">Paid Amount</label>
                                                    <input type="number" id="given_payment" name="given_payment" placeholder="Enter amount paid" step="0.01" value="{{ $patient->getMeta('given_payment') }}">
                                                </div>
                                            </div>
                                            <div class="form">
                                                <div class="form-col">
                                                    <label for="payment_method">Payment Method</label>
                                                    @php $method = $patient->getMeta('payment_method'); @endphp
                                                    <select id="payment_method" name="payment_method">
                                                        <option value="Cash" {{ ($method == 'Cash') ? 'selected' : '' }}>Cash</option>
                                                        <option value="Online" {{ ($method == 'Online') ? 'selected' : '' }}>Online</option>
                                                        <option value="Cheque" {{ ($method == 'Cheque') ? 'selected' : '' }}>Cheque</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form">
                                                <div class="form-col">
                                                    <label for="due_payment">Due Amount</label>
                                                    <input type="number" id="due_payment" name="due_payment" value="{{ $patient->getMeta('due_payment') ?? '200' }}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                </div>

                                <div class="form-actions">
                                    <a href="{{ route('svc-patient') }}" class="btn btn-secondary">Cancel</a>
                                    <button type="submit" class="btn btn-primary" id="submitBtn">Update Inquiry</button>
                                </div>
                            </form>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </div>
</div>

<script>


document.addEventListener('DOMContentLoaded', function() {
    const focCheckbox = document.getElementById('foc');
    const paymentSection = document.getElementById('payment_section');
    const chargeSelect = document.getElementById('charge_id');
    const totalPaymentInput = document.getElementById('total_payment');
    const givenPaymentInput = document.getElementById('given_payment');
    const duePaymentInput = document.getElementById('due_payment');
    const paymentMethod = document.getElementById('payment_method');

    // Function to toggle payment section
    function togglePaymentSection() {
        if (focCheckbox.checked) {
            paymentSection.style.display = 'none';

            // Clear payment fields when FOC is checked
            if (totalPaymentInput) totalPaymentInput.value = '';
            if (givenPaymentInput) givenPaymentInput.value = '';
            if (duePaymentInput) duePaymentInput.value = '';
            if (paymentMethod) paymentMethod.value = '';
            if (chargeSelect) chargeSelect.value = '';
        } else {
            paymentSection.style.display = 'block';
        }
    }

    // Charge selection functionality
    function handleChargeSelection() {
        if (chargeSelect) {
            chargeSelect.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const price = selectedOption.getAttribute('data-price');
                
                if (price) {
                    totalPaymentInput.value = price;
                    givenPaymentInput.value = price;
                    duePaymentInput.value = '0.00';
                    givenPaymentInput.readOnly = false;
                } else {
                    totalPaymentInput.value = '';
                    givenPaymentInput.value = '';
                    duePaymentInput.value = '';
                    givenPaymentInput.readOnly = false;
                }
            });
        }
    }

    // Only run toggle if FOC checkbox exists
    if (focCheckbox && paymentSection) {
        // Initial state
        togglePaymentSection();

        // Add event listener
        focCheckbox.addEventListener('change', togglePaymentSection);
    }

    // Initialize charge selection
    handleChargeSelection();

    // Auto-calculate due payment
    const totalPayment = document.getElementById('total_payment');
    const discountPayment = document.getElementById('discount_payment');
    const givenPayment = document.getElementById('given_payment');
    const duePayment = document.getElementById('due_payment');

    function calculateDuePayment() {
        const total = parseFloat(totalPayment.value) || 0;
        const discount = parseFloat(discountPayment.value) || 0;
        const given = parseFloat(givenPayment.value) || 0;

        const due = total - discount - given;
        duePayment.value = due > 0 ? due.toFixed(2) : '0.00';
    }

    // Add event listeners for auto-calculation
    totalPayment.addEventListener('input', calculateDuePayment);
    discountPayment.addEventListener('input', calculateDuePayment);
    givenPayment.addEventListener('input', calculateDuePayment);
});
function addMedicineRow(containerId) {
    const container = document.getElementById(containerId);
    const firstRow = container.querySelector('.medicine-row');
    const clone = firstRow.cloneNode(true);

    // Clear all input values
    clone.querySelectorAll('input[type="text"]').forEach(input => {
        input.value = '';
    });

    // Clear all select values
    clone.querySelectorAll('select').forEach(select => {
        select.selectedIndex = 0;
    });

    // Change the button to a remove button
    const button = clone.querySelector('.inline-btn');
    if (button) {
        button.textContent = '-';
        button.className = 'inline-btn remove';
        button.onclick = function() {
            this.closest('.medicine-row').remove();
        };
    }

    container.appendChild(clone);
}

function addHomeoRow(containerId) {
    addMedicineRow(containerId);
}

function addOtherRow(containerId) {
    addMedicineRow(containerId);
}

// Prevent double form submission with SweetAlert confirmation
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('inquiryForm');
    const submitBtn = document.getElementById('submitBtn');

    if (form && submitBtn) {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to update this SVC inquiry?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#007bff',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, update it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Show loading
                    const originalText = submitBtn.innerHTML;
                    submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Updating...';
                    submitBtn.disabled = true;
                    
                    // Submit form
                    form.submit();
                }
            });
        });
    }
});
function toggleSection(iconBox, sectionId) {
    const section = document.querySelector(`.${sectionId}`);
    const icon = iconBox.querySelector('i');

    if (section.style.display === 'none') {
        section.style.display = 'block';
        icon.classList.remove('bi-plus-lg');
        icon.classList.add('bi-dash-lg');
    } else {
        section.style.display = 'none';
        icon.classList.remove('bi-dash-lg');
        icon.classList.add('bi-plus-lg');
    }
}

// Initialize sections to be collapsed by default
document.addEventListener('DOMContentLoaded', function() {
    const sections = [
        'lab-investigation-section',
        'inside-treatment-section',
        'prescription-section',
        'homeo-treatment-section',
        'indoor-treatment-section',
        'other-treatment-section'
    ];

    sections.forEach(section => {
        const element = document.querySelector(`.${section}`);
        if (element) {
            element.style.display = 'none';
        }
    });
});
// Treatment Rows JavaScript
function addInsideRow() {
    let tbody = document.getElementById("inside-treatment-body");
    let newRow = document.createElement("tr");
    newRow.innerHTML = `
        <td><input type="text" name="inside_medicine[]" class="form-control form-control-sm" placeholder="Medicine name" autocomplete="off"></td>
        <td>
            <div class="autocomplete-container">
                <input type="text" name="inside_dose[]" class="form-control form-control-sm dose-input" placeholder="Select or type dose" autocomplete="off">
                <div class="autocomplete-dropdown"></div>
            </div>
        </td>
        <td><input type="text" name="inside_days[]" class="form-control form-control-sm" placeholder="Days"></td>
        <td>
            <select name="inside_timing[]" class="form-select form-select-sm">
                <option value="">Select</option>
                <option>Before Food</option>
                <option>After Food</option>
                <option>With Food</option>
            </select>
        </td>
        <td>
            <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove()">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    `;
    tbody.appendChild(newRow);
    setupDoseAutocomplete(newRow.querySelector('.dose-input'), newRow.querySelector('.autocomplete-dropdown'));
}

function addPrescriptionRow() {
    let tbody = document.getElementById("prescription-treatment-body");
    let newRow = document.createElement("tr");
    newRow.innerHTML = `
        <td><input type="text" name="prescription_medicine[]" class="form-control form-control-sm" placeholder="Medicine name" autocomplete="off"></td>
        <td>
            <div class="autocomplete-container">
                <input type="text" name="prescription_dose[]" class="form-control form-control-sm dose-input" placeholder="Select or type dose" autocomplete="off">
                <div class="autocomplete-dropdown"></div>
            </div>
        </td>
        <td><input type="text" name="prescription_days[]" class="form-control form-control-sm" placeholder="Days"></td>
        <td>
            <select name="prescription_timing[]" class="form-select form-select-sm">
                <option value="">Select</option>
                <option>Before Food</option>
                <option>After Food</option>
                <option>With Food</option>
            </select>
        </td>
        <td>
            <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove()">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    `;
    tbody.appendChild(newRow);
    setupDoseAutocomplete(newRow.querySelector('.dose-input'), newRow.querySelector('.autocomplete-dropdown'));
}

function addHomeoRow() {
    let tbody = document.getElementById("homeo-treatment-body");
    let newRow = document.createElement("tr");
    newRow.innerHTML = `
        <td><input type="text" name="homeo_medicine[]" class="form-control form-control-sm" placeholder="Medicine name" autocomplete="off"></td>
        <td>
            <select name="homeo_timing[]" class="form-select form-select-sm">
                <option value="">Select</option>
                <option>Before Food</option>
                <option>After Food</option>
                <option>With Food</option>
            </select>
        </td>
        <td>
            <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove()">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    `;
    tbody.appendChild(newRow);
    setupDoseAutocomplete(newRow.querySelector('.dose-input'), newRow.querySelector('.autocomplete-dropdown'));
}

function addMedicineRow() {
    let tbody = document.getElementById("indoor-treatment-body");
    let newRow = document.createElement("tr");
    newRow.innerHTML = `
        <td><input type="text" name="indoor_medicine[]" class="form-control form-control-sm" placeholder="Medicine name" autocomplete="off"></td>
        <td><input type="date" name="indoor_date[]" class="form-control form-control-sm"></td>
        <td><input type="time" name="indoor_time[]" class="form-control form-control-sm"></td>
        <td><input type="text" name="indoor_note[]" class="form-control form-control-sm" placeholder="Note"></td>
        <td>
            <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove()">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    `;
    tbody.appendChild(newRow);
    setupDoseAutocomplete(newRow.querySelector('.dose-input'), newRow.querySelector('.autocomplete-dropdown'));
}

function addOtherRow() {
    let tbody = document.getElementById("other-treatment-body");
    let newRow = document.createElement("tr");
    newRow.innerHTML = `
        <td><input type="text" name="other_medicine[]" class="form-control form-control-sm" placeholder="Medicine name" autocomplete="off"></td>
        <td><input type="text" name="other_note[]" class="form-control form-control-sm" placeholder="Note"></td>
        <td>
            <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove()">
                <i class="bi bi-trash"></i>
            </button>
        </td>
    `;
    tbody.appendChild(newRow);
}

function toggleInsideSection(iconBox) {
    let section = document.getElementById("inside-section");
    let icon = iconBox.querySelector("i");
    if (section.style.display === "none") {
        section.style.display = "block";
        icon.classList.replace("bi-plus-lg", "bi-dash-lg");
    } else {
        section.style.display = "none";
        icon.classList.replace("bi-dash-lg", "bi-plus-lg");
    }
}

function togglePrescriptionSection(iconBox) {
    let section = document.getElementById("prescription-section");
    let icon = iconBox.querySelector("i");
    if (section.style.display === "none") {
        section.style.display = "block";
        icon.classList.replace("bi-plus-lg", "bi-dash-lg");
    } else {
        section.style.display = "none";
        icon.classList.replace("bi-dash-lg", "bi-plus-lg");
    }
}

function toggleHomeoSection(iconBox) {
    let section = document.getElementById("homeo-section");
    let icon = iconBox.querySelector("i");
    if (section.style.display === "none") {
        section.style.display = "block";
        icon.classList.replace("bi-plus-lg", "bi-dash-lg");
    } else {
        section.style.display = "none";
        icon.classList.replace("bi-dash-lg", "bi-plus-lg");
    }
}

function toggleIndoorSection(iconBox) {
    let section = document.getElementById("indoor-section");
    let icon = iconBox.querySelector("i");
    if (section.style.display === "none") {
        section.style.display = "block";
        icon.classList.replace("bi-plus-lg", "bi-dash-lg");
    } else {
        section.style.display = "none";
        icon.classList.replace("bi-dash-lg", "bi-plus-lg");
    }
}

function toggleOtherSection(iconBox) {
    let section = document.getElementById("other-section");
    let icon = iconBox.querySelector("i");
    if (section.style.display === "none") {
        section.style.display = "block";
        icon.classList.replace("bi-plus-lg", "bi-dash-lg");
    } else {
        section.style.display = "none";
        icon.classList.replace("bi-dash-lg", "bi-plus-lg");
    }
}

        // Medical Conditions Autocomplete Functionality
        const doseSuggestions = [
            "1 – 0 – 0", "0 – 0 – 1", "1 – 0 – 1", "1 – 1 – 1", 
            "1 – 1 – 0", "0 – 1 – 0", "0 – 1 – 1", "1/2 – 0 – 1/2", 
            "1/2 – 1/2 – 1/2", "2 – 0 – 0", "0 – 0 – 2", "2 – 0 – 2", 
            "1 – 1 – 1 – 1"
        ];

        let suggestionsData = {
            complaints: [],
            diagnoses: []
        };

        document.addEventListener('DOMContentLoaded', function() {
            // Initialize with sample data immediately
            suggestionsData.complaints = [
                'Fever', 'Cough', 'Headache', 'Abdominal Pain', 
                'Chest Pain', 'Nausea', 'Vomiting', 'Weakness'
            ];
            suggestionsData.diagnoses = [
                'Viral Fever', 'Hypertension', 'Diabetes Mellitus',
                'Acute Gastroenteritis', 'Respiratory Infection'
            ];
            
            // Initialize autocomplete with sample data
            initAutocomplete();
            
            // Then load real data from API
            loadSuggestions();
        });

        function loadSuggestions() {
            console.log('Loading suggestions...');
            
            // Initialize existing dose fields
            document.querySelectorAll('.dose-input').forEach(input => {
                const dropdown = input.nextElementSibling;
                if (dropdown && dropdown.classList.contains('autocomplete-dropdown')) {
                    setupDoseAutocomplete(input, dropdown);
                }
            });

            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
            
            fetch('/get-suggestions', {
                method: 'GET',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            })
                .then(response => {
                    console.log('API response status:', response.status);
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Suggestions data received:', data);
                    
                    if (data.complaints && data.complaints.length > 0) {
                        suggestionsData.complaints = data.complaints;
                        console.log('Updated with real complaint data');
                    }
                    
                    if (data.diagnoses && data.diagnoses.length > 0) {
                        suggestionsData.diagnoses = data.diagnoses;
                        console.log('Updated with real diagnosis data');
                    }
                    
                    console.log('Final suggestions data:', suggestionsData);
                    
                    initAutocomplete();
                })
                .catch(error => {
                    console.error('Error loading suggestions:', error);
                    console.log('Continuing with sample data');
                });
        }

        function initAutocomplete() {
        console.log('Initializing multi-select autocomplete...');
        
        // Complaint field
        setupMultiSelect('complain', suggestionsData.complaints);
        
        // Diagnosis field
        setupMultiSelect('diagnosis', suggestionsData.diagnoses);
        
        // Initialize with existing values
        initializeExistingValues();
    }
    
    function initializeExistingValues() {
        // Initialize complaints
        const complainHidden = document.getElementById('complain-hidden');
        const complainInput = document.getElementById('complain');
        if (complainHidden && complainHidden.value && complainInput && complainInput.multiSelect) {
            const existingComplaints = complainHidden.value.split(',').map(item => item.trim()).filter(item => item);
            existingComplaints.forEach(complaint => {
                if (complaint) complainInput.multiSelect.addItem(complaint);
            });
        }
        
        // Initialize diagnoses
        const diagnosisHidden = document.getElementById('diagnosis-hidden');
        const diagnosisInput = document.getElementById('diagnosis');
        if (diagnosisHidden && diagnosisHidden.value && diagnosisInput && diagnosisInput.multiSelect) {
            const existingDiagnoses = diagnosisHidden.value.split(',').map(item => item.trim()).filter(item => item);
            existingDiagnoses.forEach(diagnosis => {
                if (diagnosis) diagnosisInput.multiSelect.addItem(diagnosis);
            });
        }
    }
    
    function setupMultiSelect(fieldId, suggestions) {
        const input = document.getElementById(fieldId);
        const dropdown = document.getElementById(fieldId + '-dropdown');
        const selectedContainer = document.getElementById(fieldId + '-selected');
        const hiddenInput = document.getElementById(fieldId + '-hidden');
        
        let selectedItems = [];
        let selectedIndex = -1;
        
        console.log(`${fieldId} elements found:`, {
            input: !!input,
            dropdown: !!dropdown,
            selectedContainer: !!selectedContainer,
            hiddenInput: !!hiddenInput,
            suggestions: suggestions.length
        });
        
        if (!input || !dropdown || !selectedContainer || !hiddenInput) {
            console.error(`Missing elements for ${fieldId} multi-select`);
            return;
        }
        
        // Show suggestions when input gets focus
        input.addEventListener('focus', function() {
            showMultiSelectSuggestions(input, dropdown, suggestions, selectedItems);
        });
        
        // Filter suggestions on input
        input.addEventListener('input', function() {
            const value = this.value.toLowerCase();
            showMultiSelectSuggestions(input, dropdown, suggestions, selectedItems, value);
        });
        
        // Keyboard navigation
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
                        // Add new item if typed
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
                        // Remove last item when backspace is pressed on empty input
                        removeItem(selectedItems[selectedItems.length - 1]);
                    }
                    break;
            }
        });
        
        // Hide dropdown when clicking outside
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
                
                // Add to suggestions if new
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
            
            if (selectedItems.length === 0) {
                return;
            }
            
            selectedItems.forEach(item => {
                const itemElement = document.createElement('div');
                itemElement.className = 'selected-item';
                itemElement.innerHTML = `
                    ${item}
                    <button type="button" class="remove-item" data-item="${item}">
                        <i class="fas fa-times"></i>
                    </button>
                `;
                
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
        
        // Store functions in the input element for external access
        input.multiSelect = {
            addItem: addItem,
            removeItem: removeItem,
            selectedItems: () => selectedItems
        };
    }
    
    function showMultiSelectSuggestions(input, dropdown, suggestions, selectedItems, filter = '') {
        dropdown.innerHTML = '';
        selectedIndex = -1;
        
        if (!suggestions || suggestions.length === 0) {
            dropdown.classList.remove('show');
            return;
        }
        
        const filteredSuggestions = suggestions.filter(suggestion => 
            suggestion.toLowerCase().includes(filter) && !selectedItems.includes(suggestion)
        );
        
        if (filteredSuggestions.length === 0 && filter) {
            // Show "Add new" option when no matches found
            const addNew = document.createElement('div');
            addNew.className = 'autocomplete-item add-new';
            addNew.innerHTML = `<i class="fas fa-plus"></i> Add "${filter}" as new ${input.id}`;
            addNew.style.color = '#197040';
            addNew.style.fontWeight = 'bold';
            
            addNew.addEventListener('click', function() {
                if (input.multiSelect) {
                    input.multiSelect.addItem(filter);
                }
                input.value = '';
                dropdown.classList.remove('show');
            });
            
            dropdown.appendChild(addNew);
        } else {
            filteredSuggestions.forEach(suggestion => {
                const item = document.createElement('div');
                item.className = 'autocomplete-item';
                item.textContent = suggestion;
                
                item.addEventListener('click', function() {
                    if (input.multiSelect) {
                        input.multiSelect.addItem(suggestion);
                    }
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
    
    function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show position-fixed`;
            notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
            notification.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            `;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.parentNode.removeChild(notification);
                }
            }, 3000);
        }

        function updateSelection(items, selectedIndex) {
            items.forEach((item, index) => {
                if (index === selectedIndex) {
                    item.classList.add('selected');
                } else {
                    item.classList.remove('selected');
                }
            });
        }

        function setupDoseAutocomplete(input, dropdown) {
            let selectedIndex = -1;
            
            input.addEventListener('focus', function() {
                showDoseSuggestions(input, dropdown, '');
            });
            
            input.addEventListener('input', function() {
                const value = this.value.toLowerCase();
                showDoseSuggestions(input, dropdown, value);
            });
            
            input.addEventListener('keydown', function(e) {
                const items = dropdown.querySelectorAll('.autocomplete-item');
                
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
                            input.value = items[selectedIndex].textContent;
                            dropdown.classList.remove('show');
                            selectedIndex = -1;
                        }
                        break;
                        
                    case 'Escape':
                        dropdown.classList.remove('show');
                        selectedIndex = -1;
                        break;
                }
            });
            
            document.addEventListener('click', function(e) {
                if (!input.contains(e.target) && !dropdown.contains(e.target)) {
                    dropdown.classList.remove('show');
                    selectedIndex = -1;
                }
            });
        }

        function showDoseSuggestions(input, dropdown, filter) {
            dropdown.innerHTML = '';
            
            const filtered = doseSuggestions.filter(s => s.toLowerCase().includes(filter));
            
            if (filtered.length > 0) {
                filtered.forEach(suggestion => {
                    const item = document.createElement('div');
                    item.className = 'autocomplete-item';
                    item.textContent = suggestion;
                    item.addEventListener('click', function() {
                        input.value = suggestion;
                        dropdown.classList.remove('show');
                    });
                    dropdown.appendChild(item);
                });
                dropdown.classList.add('show');
            } else {
                dropdown.classList.remove('show');
            }
        }
        
        // Payment calculation script
        const givenPaymentInput = document.getElementById('given_payment');
        const totalPaymentInput = document.getElementById('total_payment');
        const duePaymentInput = document.getElementById('due_payment');

        if (givenPaymentInput && totalPaymentInput && duePaymentInput) {
            givenPaymentInput.addEventListener('input', function() {
                const total = parseFloat(totalPaymentInput.value) || 0;
                const given = parseFloat(this.value) || 0;
                duePaymentInput.value = (total - given).toFixed(2);
            });
        }
</script>

@endsection
