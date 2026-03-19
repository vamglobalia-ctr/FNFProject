@extends('admin.layouts.layouts')

@section('title', 'Add SVC Inquiry')

@section('content')
    <style>
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
            display: flex;
            padding-left: 10px;
            align-items: center;
            justify-content: center;
        }

        .section-divider .icon-box i {
            color: #067945;
            font-size: 23px;
        }

        .form-container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
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

        .pro_filed {
            display: flex;
            gap: 20px;
            margin-bottom: 15px;
            width: 100%;
            align-items: flex-start;
        }

        .pro_filed .form {
            flex: 1;
            position: relative;
            min-width: 0;
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

        label {
            font-weight: bold;
            color: #5a6268;
            display: block;
            margin-bottom: 5px;
            font-size: 14px;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #bdc3c7;
            border-radius: 4px;
            font-size: 14px;
            font-family: Arial, sans-serif;
            outline: none;
        }

        textarea {
            resize: vertical;
            min-height: 80px;
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
        }

        .btn-danger:hover {
            background: #c82333;
        }

        .required::after {
            content: " *";
            color: #e74c3c;
        }

        .hidden-field {
            display: none;
        }

        .fnf-title {
            font-size: 20px;
            color: #086838;
            font-weight: bold;
        }

        .separate_payment {
            padding: 20px 20px 20px;
            border: 1px solid #8ec038 !important;
            border-radius: 5px;
            background: #f6f6f6;
            margin-top: 20px
        }

        #foc.form-check-input {
            accent-color: green !important;
            -webkit-appearance: checkbox;
        }

        #foc.form-check-input:checked {
            background-color: green !important;
        }

        /* Dynamic Fields Styles */
        .dynamic-field-group {
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .dynamic-field-input {
            flex: 1;
        }

        .btn-add, .btn-remove {
            padding: 8px 12px;
            border: none;
            border-radius: 60%;
            cursor: pointer;
            font-size: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 25px;
            height: 26px;
            color: white;
        }

        .btn-add { background: #28a745; }
        .btn-add:hover { background: #3c703e; }
        .btn-remove { background: #bd1f2f; }
        .btn-remove:hover { background: #c82333; }

        .dynamic-fields-container {
            margin-bottom: 15px;
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

        .autocomplete-item:hover, .autocomplete-item.selected {
            background-color: #f8f9fa;
            color: #086838;
        }

        /* Multi-Select Styles */
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
        }

        .selected-item .remove-item {
            cursor: pointer;
            background: none;
            border: none;
            color: white;
            font-size: 14px;
        }

        /* Treatment Table Styles */
        .treatment-section {
            margin-bottom: 1.5rem;
        }

        .treatment-table {
            width: 100%;
            margin-top: 0.5rem;
        }

        .treatment-table thead th {
            font-weight: 600;
            font-size: 0.85rem;
            padding: 0.5rem;
            border-bottom: 2px solid #dee2e6;
            color: #6c757d;
            text-transform: uppercase;
        }

        .treatment-table tbody td {
            padding: 0.5rem;
            vertical-align: middle;
        }
    </style>


    {{-- <div class="date-filter-section">
    <form method="GET" action="{{ route('add.follow.up', $patient->patient_id) }}" class="date-filter-form">
        <label for="date">Select Follow-Up Date:</label>
        <select name="date" id="date" onchange="this.form.submit()">
            <option value="">-- Select Date --</option>
            @foreach ($followupDates as $date)
            <option value="{{ $date }}" {{ $selectedDate==$date ? 'selected' : '' }}>
                {{ \Carbon\Carbon::parse($date)->format('M d, Y') }}
                @if (isset($dateCounts) && $dateCounts[$date] > 1)
                ({{ $dateCounts[$date] }} visits)
                @endif
            </option>
            @endforeach
        </select>

        @if ($selectedDate)
        <div class="date-info">
            Showing data for: <strong>{{ \Carbon\Carbon::parse($selectedDate)->format('M d, Y') }}</strong>
            @if (isset($dateCounts) && $dateCounts[$selectedDate] > 1)
            ({{ $dateCounts[$selectedDate] }} visits on this date)
            @endif
        </div>
        <button type="button" class="btn btn-info btn-sm" onclick="openHistoryModal('{{ $selectedDate }}')">
            📋 View History
        </button>
        @endif
    </form>
</div> --}}
    <div class="col-md-12 col-lg-10 m-auto p-0">

        <div class="card rounded shadow mb-5">
            <div class="card-header">
                <h3 class="bold font-up fnf-title text-success">Add FollowUps</h3>
            </div>
            <div class="row">
                <div class="col-md-12 m-auto">
                    <div class="bg-light rounded-5">
                        <section class="w-100 p-4 pb-4">
                            <div class="date-filter-section mb-4">
                                <form method="GET" action="{{ route('add.follow.up', $patient->patient_id) }}" 
                                      class="date-filter-form d-flex gap-3 align-items-end justify-content-between">
                                    <div style="width: 300px">
                                        <label for="date">Select Date:</label>
                                        <select name="date" id="date" onchange="loadTimesForDate(this.value)" class="form-select">
                                            <option value="">Select Date</option>
                                            @foreach ($followupDates as $date => $followups)
                                                <option value="{{ $date }}" {{ $selectedDate == $date ? 'selected' : '' }}>
                                                    {{ \Carbon\Carbon::parse($date)->format('M d, Y') }}
                                                    ({{ count($followups) }} visits)
                                                </option>
                                            @endforeach
                                        </select>
                                    </div>
                                    
                                    <div style="width: 200px" id="time-selector-container">
                                        <label for="time">Select Time:</label>
                                        <select name="time" id="time" class="form-select" onchange="this.form.submit()">
                                            <option value="">All Times</option>
                                            @if(isset($followupDates[$selectedDate]))
                                                @foreach($followupDates[$selectedDate] as $followup)
                                                    @php
                                                        $timeMeta = $followup->metas->firstWhere('meta_key', 'followups_time');
                                                        $timeValue = $timeMeta ? $timeMeta->meta_value : '00:00:00';
                                                    @endphp
                                                    <option value="{{ $timeValue }}" 
                                                            {{ $selectedTime == $timeValue ? 'selected' : '' }}>
                                                        {{ $timeMeta ? \Carbon\Carbon::parse($timeMeta->meta_value)->format('h:i A') : 'N/A' }}
                                                    </option>
                                                @endforeach
                                            @endif
                                        </select>
                                    </div>
                                    
                                    <div>
                                        <button type="button" class="btn btn-success btn-lg text-white" 
                                                onclick="openHistoryModal('{{ $selectedDate }}', '{{ $selectedTime }}')">
                                            📋 View History
                                        </button>
                                    </div>
                                </form>
                                
                                @if ($selectedDate)
                                    <div class="date-info mt-2">
                                        <strong>Showing:</strong> 
                                        {{ \Carbon\Carbon::parse($selectedDate)->format('M d, Y') }}
                                        @if($selectedTime)
                                            at {{ \Carbon\Carbon::parse($selectedTime)->format('h:i A') }}
                                        @endif
                                        @if(isset($followupDates[$selectedDate]))
                                            (Total {{ count($followupDates[$selectedDate]) }} visits on this date)
                                        @endif
                                    </div>
                                @endif
                            </div>
                            <div class="">
                                <form action="{{ route('store.follow.up', $patient->patient_id) }}" method="POST">
                                    @csrf
                                    <input type="hidden" name="inquiry_id" value="{{ $patient->id }}">

                                    <input type="text" name="patient_id" value="{{ $patient->patient_id }}"
                                        class="hidden-field">
                                    <input type="hidden" id="branch_id" name="branch_id" value="SVC-0005">
                                    <div class="section-divider">Personal Information</div>
                                    <div class="pt-4">
                                        <div class="pro_filed d-sm-block d-md-flex ">
                                            <div class="form">
                                                <div class="form-col">
                                                    <div class="form-col">
                                                        <label for="patient_name" class="required">Patient Name</label>
                                                        <input type="text" name="patient_name"
                                                            value="{{ old('patient_name', $patient->patient_name) }}" readonly>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form">
                                                <div class="form-col">
                                                    <label for="address" class="required">Address</label>
                                                    <input name="address"
                                                        value="{{ old('address', $patient->address) }}" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="pro_filed d-sm-block d-md-flex pt-3">
                                            <div class="form">
                                                <div class="form-col">
                                                    <label for="follow_date">Date</label>
                                                    <input type="date" id="follow_date" name="followup_date"
                                                        placeholder="Date" value="{{ $selectedDate }}">
                                                </div>
                                            </div>

                                            {{-- <script>
                                                document.addEventListener('DOMContentLoaded', function() {
                                                    // Get today's date in YYYY-MM-DD format
                                                    const today = new Date().toISOString().split('T')[0];

                                                    // Set min attribute to today's date
                                                    document.getElementById('follow_date').setAttribute('min', today);

                                                    console.log('Past dates disabled. Minimum selectable date:', today);
                                                });
                                            </script> --}}


                                    <div class="form">
                                        <div class="form-col">
                                            <label for="followups_time">FollowUp Time</label>
                                            <input type="time" id="followups_time" name="followups_time" 
                                                value="{{ old('followups_time', $selectedTime ?: \Carbon\Carbon::now()->format('H:i')) }}">
                                        </div>
                                    </div>
                                                                            </div>
                                        <div class="pro_filed d-sm-block d-md-flex pt-3">
                                            <div class="form">
                                                <div class="form-col">
                                                    <label for="gender" class="required">Gender</label>
                                                    <select name="gender">
                                                        <option value="male"
                                                            {{ $patient->gender == 'male' ? 'selected' : '' }}>Male
                                                        </option>
                                                        <option value="female"
                                                            {{ $patient->gender == 'female' ? 'selected' : '' }}>
                                                            Female
                                                        </option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form">
                                                <div class="form-col">
                                                    <label for="doctor_id">Assigned Doctor</label>
                                                    <select name="doctor_id" id="doctor_id">
                                                        <option value="">Select Doctor</option>
                                                        @foreach($doctors as $doctor)
                                                            <option value="{{ $doctor->id }}" 
                                                                {{ (old('doctor_id', $followup->doctor_id ?? '') == $doctor->id) ? 'selected' : '' }}>
                                                                {{ $doctor->name }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form">
                                                <div class="form-col">
                                                    <label for="weight">Weight (kg)</label>
                                                    <div class="dynamic-fields-container" id="weight-container">
                                                        @php
                                                            $allWeights = $followupMetaValues['weight'] ?? [];
                                                        @endphp

                                                        @forelse ($allWeights as $index => $weight)
                                                            <div class="">
                                                                <input type="number" name="weight[]"
                                                                    class="dynamic-field-input" value="{{ $weight }}"
                                                                    placeholder="Enter weight">
                                                            </div>
                                                        @empty
                                                            <div class="">
                                                                <input type="number" name="weight[]"
                                                                    class="dynamic-field-input" value=""
                                                                    placeholder="Enter weight">
                                                            </div>
                                                        @endforelse
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="pro_filed d-sm-block d-md-flex pt-3">
                                            <div class="form">
                                                <div class="form-col">
                                                    <label for="phone">Phone Number</label>
                                                    <input type="number" id="phone" name="phone"
                                                        value="{{ $patient->getMeta('phone') }}">
                                                </div>
                                            </div>
                                            <div class="form">
                                                <div class="form-col">
                                                    <label for="age" class="required">Age</label>
                                                    <input type="number" name="age"
                                                        value="{{ old('age', $patient->age) }}">
                                                </div>
                                            </div>
                                        </div>
                                        @php
                                            $ptStatusValues = $followupMetaValues['pt_status'] ?? [];
                                            $temperatureValues = $followupMetaValues['temperature'] ?? [];
                                            $pulseValues = $followupMetaValues['pulse'] ?? [];
                                            $bloodPressureValues = $followupMetaValues['blood_pressure'] ?? [];
                                            $spo2Values = $followupMetaValues['spo2'] ?? [];
                                            $rbsValues = $followupMetaValues['rbs'] ?? [];
                                            $diagnosisValues = $followupMetaValues['diagnosis'] ?? [];
                                        @endphp
                                        <div class="pro_filed d-sm-block d-md-flex pt-3">

                                            <div class="form">
                                                <div class="form-col">
                                                    <label for="pt_status">PT.Status</label>
                                                    <div class="dynamic-fields-container" id="pt_status-container">
                                                        @forelse ($ptStatusValues as $index => $val)
                                                            <div class="">
                                                                <select name="pt_status[]" class="dynamic-field-input">
                                                                    <option value="IPD"
                                                                        {{ $val == 'IPD' ? 'selected' : '' }}>IPD
                                                                    </option>
                                                                    <option value="OPD"
                                                                        {{ $val == 'OPD' ? 'selected' : '' }}>OPD
                                                                    </option>
                                                                    <option value="Home Visit"
                                                                        {{ $val == 'Home Visit' ? 'selected' : '' }}>
                                                                        Home
                                                                        Visit
                                                                    </option>
                                                                </select>
                                                            </div>
                                                        @empty
                                                            <div class="">
                                                                <select name="pt_status[]" class="dynamic-field-input">
                                                                    <option value="">Select Status</option>
                                                                    <option value="IPD">IPD</option>
                                                                    <option value="OPD">OPD</option>
                                                                    <option value="Home Visit">Home Visit</option>
                                                                </select>
                                                            </div>
                                                        @endforelse
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form">
                                                <div class="form-col">
                                                    <label for="temperature">Temperature (°C)</label>
                                                    <div class="dynamic-fields-container" id="temperature-container">
                                                        @forelse ($temperatureValues as $index => $val)
                                                            <div class="dynamic-field-group">
                                                                <input type="text" name="temperature[]"
                                                                    class="dynamic-field-input"
                                                                    value="{{ $val === 'null' ? '' : $val }}"
                                                                    placeholder="Enter temperature">
                                                            </div>
                                                        @empty
                                                            <div class="dynamic-field-group">
                                                                <input type="number" name="temperature[]"
                                                                    class="dynamic-field-input" value=""
                                                                    placeholder="Enter temperature">
                                                            </div>
                                                        @endforelse
                                                    </div>
                                                </div>

                                            </div>                 

                                        </div>
                                        <div class="pro_filed d-sm-block d-md-flex pt-3">
                                            <div class="form">
                                                <div class="form-col">
                                                    <label for="pulse">Pulse</label>
                                                    <div class="dynamic-fields-container" id="pulse-container">
                                                        @forelse ($pulseValues as $index => $val)
                                                            <div class="">
                                                                <input type="text" name="pulse[]"
                                                                    class="dynamic-field-input"
                                                                    value="{{ $val === 'null' ? '' : $val }}"
                                                                    placeholder="Enter pulse rate">
                                                            </div>
                                                        @empty
                                                            <div class="">
                                                                <input type="text" name="pulse[]"
                                                                    class="dynamic-field-input" value=""
                                                                    placeholder="Enter pulse rate">
                                                            </div>
                                                        @endforelse
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form">
                                                <div class="form-col">
                                                    <label for="blood_pressure">Blood Pressure</label>
                                                    <div class="dynamic-fields-container" id="blood-pressure-container">
                                                        @forelse ($bloodPressureValues as $index => $val)
                                                            <div class="">
                                                                <input type="text" name="blood_pressure[]"
                                                                    class="dynamic-field-input"
                                                                    value="{{ $val === 'null' ? '' : $val }}"
                                                                    placeholder="e.g., 120/80">
                                                            </div>
                                                        @empty
                                                            <div class="">
                                                                <input type="text" name="blood_pressure[]"
                                                                    class="dynamic-field-input" value=""
                                                                    placeholder="e.g., 120/80">
                                                            </div>
                                                        @endforelse
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="pro_filed d-sm-block d-md-flex pt-3">
                                            <div class="form">
                                                <div class="form-col">
                                                    <label for="spo2">SpO2 (%)</label>
                                                    <div class="dynamic-fields-container" id="spo2-container">
                                                        @forelse ($spo2Values as $index => $val)
                                                            <div class="">
                                                                <input type="number" name="spo2[]"
                                                                    class="dynamic-field-input"
                                                                    value="{{ $val === 'null' ? '' : $val }}"
                                                                    placeholder="Enter SpO2">
                                                            </div>
                                                        @empty
                                                            <div class="">
                                                                <input type="number" name="spo2[]"
                                                                    class="dynamic-field-input" value=""
                                                                    placeholder="Enter SpO2">
                                                            </div>
                                                        @endforelse
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="form">
                                                <div class="form-col">
                                                    <label for="rbs">RBS</label>
                                                    <div class="dynamic-fields-container" id="rbs-container">
                                                        @forelse ($rbsValues as $index => $val)
                                                            <div class="">
                                                                <input type="text" name="rbs[]"
                                                                    class="dynamic-field-input"
                                                                    value="{{ $val === 'null' ? '' : $val }}"
                                                                    placeholder="Enter RBS">
                                                            </div>
                                                        @empty
                                                            <div class="">
                                                                <input type="text" name="rbs[]"
                                                                    class="dynamic-field-input" value=""
                                                                    placeholder="Enter RBS">
                                                            </div>
                                                        @endforelse
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="section-divider mt-4">Medical Information</div>
                                        <div class="pro_filed d-sm-block d-md-flex pt-4">
                                            <div class="form">
                                                <div class="form-col-2">
                                                    <label for="diagnosis">Diagnosis</label>
                                                    <div class="multi-select-container">
                                                        <div class="selected-items" id="diagnosis-selected">
                                                            <!-- Selected diagnoses will appear here -->
                                                        </div>
                                                        <div class="autocomplete-container">
                                                            <input type="text" id="diagnosis" 
                                                                placeholder="Type to add diagnoses..."  class="form-control" autocomplete="off">
                                                            <div class="autocomplete-dropdown" id="diagnosis-dropdown"></div>
                                                        </div>
                                                        <input type="hidden" name="diagnosis" id="diagnosis-hidden" value="{{ implode(', ', array_filter($diagnosisValues, fn($v) => !empty($v) && $v !== 'null')) }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="pro_filed d-sm-block d-md-flex pt-3">
                                            <div class="form">
                                                <div class="form-col">
                                                    <label for="investigation">Investigation</label>
                                                    <textarea id="investigation" name="investigation"> {{ $patient->getMeta('investigation') }}</textarea>
                                                </div>
                                            </div>
                                            <div class="form">
                                                <div class="form-col">
                                                    <label for="past_history">Past History</label>
                                                    <textarea id="past_history" name="past_history"> {{ $patient->getMeta('past_history') }}</textarea>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="pro_filed d-sm-block d-md-flex pt-3">
                                            <div class="form">
                                                <div class="form-col-2">
                                                    <label for="family_history">Family History</label>
                                                    <textarea id="family_history" name="family_history"> {{ $patient->getMeta('family_history') }}</textarea>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="section-divider mt-4">
                                            <div class="title" style="color: #28a745;">Lipid Profile</div>
                                            <div class="line"></div>
                                            <div class="icon-box" onclick="toggleLipidSection(this)">
                                                <i class="bi bi-plus-lg" id="lipid-toggle-icon"></i>
                                            </div>
                                        </div>

                                        <div class="lipid-section" id="lipid-section-container" style="display: none;">
                                            @php
                                                $cholesterolValues = $followupMetaValues['s_cholesterol'] ?? [];
                                                $triglycerideValues = $followupMetaValues['s_triglyceride'] ?? [];
                                                $hdlValues = $followupMetaValues['hdl'] ?? [];
                                                $ldlValues = $followupMetaValues['ldl'] ?? [];
                                                $vldlValues = $followupMetaValues['vldl'] ?? [];
                                                $nonHdlCValues = $followupMetaValues['non_hdl_c'] ?? [];
                                                $cholHdlRatioValues = $followupMetaValues['chol_hdl_ratio'] ?? [];
                                            @endphp
                                            <!-- Row 1: S. Cholesterol, S. Triglyceride -->
                                            <div class="pro_filed d-sm-block d-md-flex pt-3">
                                                <div class="form">
                                                    <div class="form-col">
                                                        <label for="s_cholesterol">S. Cholesterol</label>
                                                        <div class="dynamic-fields-container"
                                                            id="s-cholesterol-container">
                                                            @forelse ($cholesterolValues as $index => $val)
                                                                <div class="dynamic-field-group">
                                                                    <input type="text" name="s_cholesterol[]"
                                                                        class="dynamic-field-input"
                                                                        value="{{ $val === 'null' ? '' : $val }}"
                                                                        placeholder="S. Cholesterol">
                                                                </div>
                                                            @empty
                                                                <div class="dynamic-field-group">
                                                                    <input type="text" name="s_cholesterol[]"
                                                                        class="dynamic-field-input" value=""
                                                                        placeholder="S. Cholesterol">
                                                                </div>
                                                            @endforelse
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form">
                                                    <div class="form-col">
                                                        <label for="s_triglyceride">S. Triglyceride</label>
                                                        <div class="dynamic-fields-container"
                                                            id="s-triglyceride-container">
                                                            @forelse ($triglycerideValues as $index => $val)
                                                                <div class="dynamic-field-group">
                                                                    <input type="text" name="s_triglyceride[]"
                                                                        class="dynamic-field-input"
                                                                        value="{{ $val === 'null' ? '' : $val }}"
                                                                        placeholder="S. Triglyceride">
                                                                </div>
                                                            @empty
                                                                <div class="dynamic-field-group">
                                                                    <input type="text" name="s_triglyceride[]"
                                                                        class="dynamic-field-input" value=""
                                                                        placeholder="S. Triglyceride">
                                                                </div>
                                                            @endforelse
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Row 2: HDL, LDL -->
                                            <div class="pro_filed d-sm-block d-md-flex pt-3">
                                                <div class="form">
                                                    <div class="form-col">
                                                        <label for="hdl">HDL</label>
                                                        <div class="dynamic-fields-container" id="hdl-container">
                                                            @forelse ($hdlValues as $index => $val)
                                                                <div class="dynamic-field-group">
                                                                    <input type="text" name="hdl[]"
                                                                        class="dynamic-field-input"
                                                                        value="{{ $val === 'null' ? '' : $val }}"
                                                                        placeholder="HDL">
                                                                </div>
                                                            @empty
                                                                <div class="dynamic-field-group">
                                                                    <input type="text" name="hdl[]"
                                                                        class="dynamic-field-input" value=""
                                                                        placeholder="HDL">
                                                                </div>
                                                            @endforelse
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form">
                                                    <div class="form-col">
                                                        <label for="ldl">LDL</label>
                                                        <div class="dynamic-fields-container" id="ldl-container">
                                                            @forelse ($ldlValues as $index => $val)
                                                                <div class="dynamic-field-group">
                                                                    <input type="text" name="ldl[]"
                                                                        class="dynamic-field-input"
                                                                        value="{{ $val === 'null' ? '' : $val }}"
                                                                        placeholder="LDL">
                                                                </div>
                                                            @empty
                                                                <div class="dynamic-field-group">
                                                                    <input type="text" name="ldl[]"
                                                                        class="dynamic-field-input" value=""
                                                                        placeholder="LDL">
                                                                </div>
                                                            @endforelse
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Row 3: VLDL, Non-HDL C -->
                                            <div class="pro_filed d-sm-block d-md-flex pt-3">
                                                <div class="form">
                                                    <div class="form-col">
                                                        <label for="vldl">VLDL</label>
                                                        <div class="dynamic-fields-container" id="vldl-container">
                                                            @forelse ($vldlValues as $index => $val)
                                                                <div class="dynamic-field-group">
                                                                    <input type="text" name="vldl[]"
                                                                        class="dynamic-field-input"
                                                                        value="{{ $val === 'null' ? '' : $val }}"
                                                                        placeholder="VLDL">
                                                                </div>
                                                            @empty
                                                                <div class="dynamic-field-group">
                                                                    <input type="text" name="vldl[]"
                                                                        class="dynamic-field-input" value=""
                                                                        placeholder="VLDL">
                                                                </div>
                                                            @endforelse
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form">
                                                    <div class="form-col">
                                                        <label for="non_hdl_c">Non-HDL C</label>
                                                        <div class="dynamic-fields-container" id="non-hdl-c-container">
                                                            @forelse ($nonHdlCValues as $index => $val)
                                                                <div class="dynamic-field-group">
                                                                    <input type="text" name="non_hdl_c[]"
                                                                        class="dynamic-field-input"
                                                                        value="{{ $val === 'null' ? '' : $val }}"
                                                                        placeholder="Non-HDL C">
                                                                </div>
                                                            @empty
                                                                <div class="dynamic-field-group">
                                                                    <input type="text" name="non_hdl_c[]"
                                                                        class="dynamic-field-input" value=""
                                                                        placeholder="Non-HDL C">
                                                                </div>
                                                            @endforelse
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Row 4: Chol/HDL ratio -->
                                            <div class="pro_filed d-sm-block d-md-flex pt-3">
                                                <div class="form">
                                                    <div class="form-col-2">
                                                        <label for="chol_hdl_ratio">Chol/HDL ratio</label>
                                                        <div class="dynamic-fields-container" id="chol-hdl-ratio-container">
                                                            @forelse ($cholHdlRatioValues as $index => $val)
                                                                <div class="dynamic-field-group">
                                                                    <input type="text" name="chol_hdl_ratio[]"
                                                                        class="dynamic-field-input"
                                                                        value="{{ $val === 'null' ? '' : $val }}"
                                                                        placeholder="Chol/HDL ratio">
                                                                </div>
                                                            @empty
                                                                <div class="dynamic-field-group">
                                                                    <input type="text" name="chol_hdl_ratio[]"
                                                                        class="dynamic-field-input" value=""
                                                                        placeholder="Chol/HDL ratio">
                                                                </div>
                                                            @endforelse
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="section-divider mt-4">
                                            <div class="title">Laboratory Tests</div>
                                            <div class="line"></div>
                                            <div class="icon-box" onclick="toggleInsideSection1(this)">
                                                <i class="bi bi-dash-lg" id="inside-toggle-icon"></i>
                                            </div>
                                        </div>

                                        <div class="inside-section1">
                                            @php
                                                $hbValues = $followupMetaValues['hb'] ?? [];
                                                $tcValues = $followupMetaValues['tc'] ?? [];
                                                $pcValues = $followupMetaValues['pc'] ?? [];
                                                $mpValues = $followupMetaValues['mp'] ?? [];
                                                $hb1acValues = $followupMetaValues['hb1ac'] ?? [];
                                                $fbsValues = $followupMetaValues['fbs'] ?? [];
                                                $pp2bsValues = $followupMetaValues['pp2bs'] ?? [];
                                                $sWidalValues = $followupMetaValues['s_widal'] ?? [];
                                                $usgValues = $followupMetaValues['usg'] ?? [];
                                                $xrayValues = $followupMetaValues['x_ray'] ?? [];
                                                $sgptValues = $followupMetaValues['sgpt'] ?? [];
                                                $sCreatinineValues = $followupMetaValues['s_creatinine'] ?? [];
                                                $ns1agValues = $followupMetaValues['ns1ag'] ?? [];
                                                $dengueIgmValues = $followupMetaValues['dengue_igm'] ?? [];
                                                $sb12Values = $followupMetaValues['s_b12'] ?? [];
                                                $sd3Values = $followupMetaValues['s_d3'] ?? [];
                                                $urineValues = $followupMetaValues['urine'] ?? [];
                                                $crpValues = $followupMetaValues['crp'] ?? [];
                                                $st3Values = $followupMetaValues['s_t3'] ?? [];
                                                $st4Values = $followupMetaValues['s_t4'] ?? [];
                                                $stshValues = $followupMetaValues['s_tsh'] ?? [];
                                                $esrValues = $followupMetaValues['esr'] ?? [];
                                                $specificTestValues = $followupMetaValues['specific_test'] ?? [];
                                            @endphp

                                            <!-- Row 1: HB, TC -->
                                            <div class="pro_filed d-sm-block d-md-flex pt-3">
                                                <div class="form">
                                                    <div class="form-col">
                                                        <label for="hb">HB</label>
                                                        <div class="dynamic-fields-container" id="hb-container">
                                                            @forelse ($hbValues as $index => $val)
                                                                <div class="dynamic-field-group">
                                                                    <input type="text" name="hb[]"
                                                                        class="dynamic-field-input"
                                                                        value="{{ $val === 'null' ? '' : $val }}"
                                                                        placeholder="HB">
                                                                </div>
                                                            @empty
                                                                <div class="dynamic-field-group">
                                                                    <input type="text" name="hb[]"
                                                                        class="dynamic-field-input" value=""
                                                                        placeholder="HB">
                                                                </div>
                                                            @endforelse
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form">
                                                    <div class="form-col">
                                                        <label for="tc">TC</label>
                                                        <div class="dynamic-fields-container" id="tc-container">
                                                            @forelse ($tcValues as $index => $val)
                                                                <div class="dynamic-field-group">
                                                                    <input type="text" name="tc[]"
                                                                        class="dynamic-field-input"
                                                                        value="{{ $val === 'null' ? '' : $val }}"
                                                                        placeholder="TC">
                                                                </div>
                                                            @empty
                                                                <div class="dynamic-field-group">
                                                                    <input type="text" name="tc[]"
                                                                        class="dynamic-field-input" value=""
                                                                        placeholder="TC">
                                                                </div>
                                                            @endforelse
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Row 2: PC, MP -->
                                            <div class="pro_filed d-sm-block d-md-flex pt-3">
                                                <div class="form">
                                                    <div class="form-col">
                                                        <label for="pc">PC</label>
                                                        <div class="dynamic-fields-container" id="pc-container">
                                                            @forelse ($pcValues as $index => $val)
                                                                <div class="dynamic-field-group">
                                                                    <input type="text" name="pc[]"
                                                                        class="dynamic-field-input"
                                                                        value="{{ $val === 'null' ? '' : $val }}"
                                                                        placeholder="PC">
                                                                </div>
                                                            @empty
                                                                <div class="dynamic-field-group">
                                                                    <input type="text" name="pc[]"
                                                                        class="dynamic-field-input" value=""
                                                                        placeholder="PC">
                                                                </div>
                                                            @endforelse
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form">
                                                    <div class="form-col">
                                                        <label for="mp">MP</label>
                                                        <div class="dynamic-fields-container" id="mp-container">
                                                            @forelse ($mpValues as $index => $val)
                                                                <div class="dynamic-field-group">
                                                                    <input type="text" name="mp[]"
                                                                        class="dynamic-field-input"
                                                                        value="{{ $val === 'null' ? '' : $val }}"
                                                                        placeholder="MP">
                                                                </div>
                                                            @empty
                                                                <div class="dynamic-field-group">
                                                                    <input type="text" name="mp[]"
                                                                        class="dynamic-field-input" value=""
                                                                        placeholder="MP">
                                                                </div>
                                                            @endforelse
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Row 3: HB1AC, FBS -->
                                            <div class="pro_filed d-sm-block d-md-flex pt-3">
                                                <div class="form">
                                                    <div class="form-col">
                                                        <label for="hb1ac">HB1AC</label>
                                                        <div class="dynamic-fields-container" id="hb1ac-container">
                                                            @forelse ($hb1acValues as $index => $val)
                                                                <div class="dynamic-field-group">
                                                                    <input type="text" name="hb1ac[]"
                                                                        class="dynamic-field-input"
                                                                        value="{{ $val === 'null' ? '' : $val }}"
                                                                        placeholder="HB1AC">
                                                                </div>
                                                            @empty
                                                                <div class="dynamic-field-group">
                                                                    <input type="text" name="hb1ac[]"
                                                                        class="dynamic-field-input" value=""
                                                                        placeholder="HB1AC">
                                                                </div>
                                                            @endforelse
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form">
                                                    <div class="form-col">
                                                        <label for="fbs">FBS</label>
                                                        <div class="dynamic-fields-container" id="fbs-container">
                                                            @forelse ($fbsValues as $index => $val)
                                                                <div class="dynamic-field-group">
                                                                    <input type="text" name="fbs[]"
                                                                        class="dynamic-field-input"
                                                                        value="{{ $val === 'null' ? '' : $val }}"
                                                                        placeholder="FBS">
                                                                </div>
                                                            @empty
                                                                <div class="dynamic-field-group">
                                                                    <input type="text" name="fbs[]"
                                                                        class="dynamic-field-input" value=""
                                                                        placeholder="FBS">
                                                                </div>
                                                            @endforelse
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Row 4: PP2BS, S.widal -->
                                            <div class="pro_filed d-sm-block d-md-flex pt-3">
                                                <div class="form">
                                                    <div class="form-col">
                                                        <label for="pp2bs">PP2BS</label>
                                                        <div class="dynamic-fields-container" id="pp2bs-container">
                                                            @forelse ($pp2bsValues as $index => $val)
                                                                <div class="dynamic-field-group">
                                                                    <input type="text" name="pp2bs[]"
                                                                        class="dynamic-field-input"
                                                                        value="{{ $val === 'null' ? '' : $val }}"
                                                                        placeholder="PP2BS">
                                                                </div>
                                                            @empty
                                                                <div class="dynamic-field-group">
                                                                    <input type="text" name="pp2bs[]"
                                                                        class="dynamic-field-input" value=""
                                                                        placeholder="PP2BS">
                                                                </div>
                                                            @endforelse
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form">
                                                    <div class="form-col">
                                                        <label for="s_widal">S.widal</label>
                                                        <div class="dynamic-fields-container" id="s-widal-container">
                                                            @forelse ($sWidalValues as $index => $val)
                                                                <div class="dynamic-field-group">
                                                                    <input type="text" name="s_widal[]"
                                                                        class="dynamic-field-input"
                                                                        value="{{ $val === 'null' ? '' : $val }}"
                                                                        placeholder="S.widal">
                                                                </div>
                                                            @empty
                                                                <div class="dynamic-field-group">
                                                                    <input type="text" name="s_widal[]"
                                                                        class="dynamic-field-input" value=""
                                                                        placeholder="S.widal">
                                                                </div>
                                                            @endforelse
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Row 5: USG, X-ray -->
                                            <div class="pro_filed d-sm-block d-md-flex pt-3">
                                                <div class="form">
                                                    <div class="form-col">
                                                        <label for="usg">USG</label>
                                                        <div class="dynamic-fields-container" id="usg-container">
                                                            @forelse ($usgValues as $index => $val)
                                                                <div class="dynamic-field-group">
                                                                    <input type="text" name="usg[]"
                                                                        class="dynamic-field-input"
                                                                        value="{{ $val === 'null' ? '' : $val }}"
                                                                        placeholder="USG">
                                                                </div>
                                                            @empty
                                                                <div class="dynamic-field-group">
                                                                    <input type="text" name="usg[]"
                                                                        class="dynamic-field-input" value=""
                                                                        placeholder="USG">
                                                                </div>
                                                            @endforelse
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form">
                                                    <div class="form-col">
                                                        <label for="x_ray">X-ray</label>
                                                        <div class="dynamic-fields-container" id="x-ray-container">
                                                            @forelse ($xrayValues as $index => $val)
                                                                <div class="dynamic-field-group">
                                                                    <input type="text" name="x_ray[]"
                                                                        class="dynamic-field-input"
                                                                        value="{{ $val === 'null' ? '' : $val }}"
                                                                        placeholder="X-ray">
                                                                </div>
                                                            @empty
                                                                <div class="dynamic-field-group">
                                                                    <input type="text" name="x_ray[]"
                                                                        class="dynamic-field-input" value=""
                                                                        placeholder="X-ray">
                                                                </div>
                                                            @endforelse
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Row 6: SGPT, S. Creatinine -->
                                            <div class="pro_filed d-sm-block d-md-flex pt-3">
                                                <div class="form">
                                                    <div class="form-col">
                                                        <label for="sgpt">SGPT</label>
                                                        <div class="dynamic-fields-container" id="sgpt-container">
                                                            @forelse ($sgptValues as $index => $val)
                                                                <div class="dynamic-field-group">
                                                                    <input type="text" name="sgpt[]"
                                                                        class="dynamic-field-input"
                                                                        value="{{ $val === 'null' ? '' : $val }}"
                                                                        placeholder="SGPT">
                                                                </div>
                                                            @empty
                                                                <div class="dynamic-field-group">
                                                                    <input type="text" name="sgpt[]"
                                                                        class="dynamic-field-input" value=""
                                                                        placeholder="SGPT">
                                                                </div>
                                                            @endforelse
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form">
                                                    <div class="form-col">
                                                        <label for="s_creatinine">S. Creatinine</label>
                                                        <div class="dynamic-fields-container" id="s-creatinine-container">
                                                            @forelse ($sCreatinineValues as $index => $val)
                                                                <div class="dynamic-field-group">
                                                                    <input type="text" name="s_creatinine[]"
                                                                        class="dynamic-field-input"
                                                                        value="{{ $val === 'null' ? '' : $val }}"
                                                                        placeholder="S. Creatinine">
                                                                </div>
                                                            @empty
                                                                <div class="dynamic-field-group">
                                                                    <input type="text" name="s_creatinine[]"
                                                                        class="dynamic-field-input" value=""
                                                                        placeholder="S. Creatinine">
                                                                </div>
                                                            @endforelse
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Row 7: NS1Ag, Dengue IGM -->
                                            <div class="pro_filed d-sm-block d-md-flex pt-3">
                                                <div class="form">
                                                    <div class="form-col">
                                                        <label for="ns1ag">NS1Ag</label>
                                                        <div class="dynamic-fields-container" id="ns1ag-container">
                                                            @forelse ($ns1agValues as $index => $val)
                                                                <div class="dynamic-field-group">
                                                                    <input type="text" name="ns1ag[]"
                                                                        class="dynamic-field-input"
                                                                        value="{{ $val === 'null' ? '' : $val }}"
                                                                        placeholder="NS1Ag">
                                                                </div>
                                                            @empty
                                                                <div class="dynamic-field-group">
                                                                    <input type="text" name="ns1ag[]"
                                                                        class="dynamic-field-input" value=""
                                                                        placeholder="NS1Ag">
                                                                </div>
                                                            @endforelse
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form">
                                                    <div class="form-col">
                                                        <label for="dengue_igm">Dengue IGM</label>
                                                        <div class="dynamic-fields-container" id="dengue-igm-container">
                                                            @forelse ($dengueIgmValues as $index => $val)
                                                                <div class="dynamic-field-group">
                                                                    <input type="text" name="dengue_igm[]"
                                                                        class="dynamic-field-input"
                                                                        value="{{ $val === 'null' ? '' : $val }}"
                                                                        placeholder="Dengue IGM">
                                                                </div>
                                                            @empty
                                                                <div class="dynamic-field-group">
                                                                    <input type="text" name="dengue_igm[]"
                                                                        class="dynamic-field-input" value=""
                                                                        placeholder="Dengue IGM">
                                                                </div>
                                                            @endforelse
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Row 13: S.T3, S.T4 -->
                                            <div class="pro_filed d-sm-block d-md-flex pt-3">
                                                <div class="form">
                                                    <div class="form-col">
                                                        <label for="s_t3">S.T3</label>
                                                        <div class="dynamic-fields-container" id="st3-container">
                                                            @forelse ($st3Values as $index => $val)
                                                                <div class="dynamic-field-group">
                                                                    <input type="text" name="s_t3[]"
                                                                        class="dynamic-field-input"
                                                                        value="{{ $val === 'null' ? '' : $val }}"
                                                                        placeholder="S.T3">
                                                                </div>
                                                            @empty
                                                                <div class="dynamic-field-group">
                                                                    <input type="text" name="s_t3[]"
                                                                        class="dynamic-field-input" value=""
                                                                        placeholder="S.T3">
                                                                </div>
                                                            @endforelse
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form">
                                                    <div class="form-col">
                                                        <label for="s_t4">S.T4</label>
                                                        <div class="dynamic-fields-container" id="st4-container">
                                                            @forelse ($st4Values as $index => $val)
                                                                <div class="dynamic-field-group">
                                                                    <input type="text" name="s_t4[]"
                                                                        class="dynamic-field-input"
                                                                        value="{{ $val === 'null' ? '' : $val }}"
                                                                        placeholder="S.T4">
                                                                </div>
                                                            @empty
                                                                <div class="dynamic-field-group">
                                                                    <input type="text" name="s_t4[]"
                                                                        class="dynamic-field-input" value=""
                                                                        placeholder="S.T4">
                                                                </div>
                                                            @endforelse
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Row 14: S.TSH, S.B12 -->
                                            <div class="pro_filed d-sm-block d-md-flex pt-3">
                                                <div class="form">
                                                    <div class="form-col">
                                                        <label for="s_tsh">S.TSH</label>
                                                        <div class="dynamic-fields-container" id="stsh-container">
                                                            @forelse ($stshValues as $index => $val)
                                                                <div class="dynamic-field-group">
                                                                    <input type="text" name="s_tsh[]"
                                                                        class="dynamic-field-input"
                                                                        value="{{ $val === 'null' ? '' : $val }}"
                                                                        placeholder="S.TSH">
                                                                </div>
                                                            @empty
                                                                <div class="dynamic-field-group">
                                                                    <input type="text" name="s_tsh[]"
                                                                        class="dynamic-field-input" value=""
                                                                        placeholder="S.TSH">
                                                                </div>
                                                            @endforelse
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form">
                                                    <div class="form-col">
                                                        <label for="s_b12">S.B12</label>
                                                        <div class="dynamic-fields-container" id="sb12-container">
                                                            @forelse ($sb12Values as $index => $val)
                                                                <div class="dynamic-field-group">
                                                                    <input type="text" name="s_b12[]"
                                                                        class="dynamic-field-input"
                                                                        value="{{ $val === 'null' ? '' : $val }}"
                                                                        placeholder="S.B12">
                                                                </div>
                                                            @empty
                                                                <div class="dynamic-field-group">
                                                                    <input type="text" name="s_b12[]"
                                                                        class="dynamic-field-input" value=""
                                                                        placeholder="S.B12">
                                                                </div>
                                                            @endforelse
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Row 15: S.D3, ESR -->
                                            <div class="pro_filed d-sm-block d-md-flex pt-3">
                                                <div class="form">
                                                    <div class="form-col">
                                                        <label for="s_d3">S.D3</label>
                                                        <div class="dynamic-fields-container" id="sd3-container">
                                                            @forelse ($sd3Values as $index => $val)
                                                                <div class="dynamic-field-group">
                                                                    <input type="text" name="s_d3[]"
                                                                        class="dynamic-field-input"
                                                                        value="{{ $val === 'null' ? '' : $val }}"
                                                                        placeholder="S.D3">
                                                                </div>
                                                            @empty
                                                                <div class="dynamic-field-group">
                                                                    <input type="text" name="s_d3[]"
                                                                        class="dynamic-field-input" value=""
                                                                        placeholder="S.D3">
                                                                </div>
                                                            @endforelse
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form">
                                                    <div class="form-col">
                                                        <label for="esr">ESR</label>
                                                        <div class="dynamic-fields-container" id="esr-container">
                                                            @forelse ($esrValues as $index => $val)
                                                                <div class="dynamic-field-group">
                                                                    <input type="text" name="esr[]"
                                                                        class="dynamic-field-input"
                                                                        value="{{ $val === 'null' ? '' : $val }}"
                                                                        placeholder="ESR">
                                                                </div>
                                                            @empty
                                                                <div class="dynamic-field-group">
                                                                    <input type="text" name="esr[]"
                                                                        class="dynamic-field-input" value=""
                                                                        placeholder="ESR">
                                                                </div>
                                                            @endforelse
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Row 16: Any Specific Test -->
                                            <div class="pro_filed d-sm-block d-md-flex pt-3">
                                                <div class="form">
                                                    <div class="form-col-2">
                                                        <label for="specific_test">Any Specific Test</label>
                                                        <div class="dynamic-fields-container" id="specific-test-container">
                                                            @forelse ($specificTestValues as $index => $val)
                                                                <div class="dynamic-field-group">
                                                                    <input type="text" name="specific_test[]"
                                                                        class="dynamic-field-input"
                                                                        value="{{ $val === 'null' ? '' : $val }}"
                                                                        placeholder="Any Specific Test">
                                                                </div>
                                                            @empty
                                                                <div class="dynamic-field-group">
                                                                    <input type="text" name="specific_test[]"
                                                                        class="dynamic-field-input" value=""
                                                                        placeholder="Any Specific Test">
                                                                </div>
                                                            @endforelse
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
{{-- 
                                        <div class="pro_filed d-sm-block d-md-flex pt-3">
                                            <!-- Inside Treatment Section -->
                                            <div class="form">
                                                <div class="section-divider mt-4">
                                                    <div class="title">Inside Treatment</div>
                                                    <div class="line"></div>
                                                    <div class="icon-box" onclick="toggleInsideSection(this)">
                                                        <i class="bi bi-dash-lg" id="inside-toggle-icon"></i>
                                                    </div>
                                                </div>

                                                <div class="inside-section">
                                                    <div id="inside-treatment-container">
                                                        <div class="pro_filed d-sm-block d-md-flex pt-3">
                                                            <div class="form">
                                                                <div class="form-col">
                                                                    <label>Medicine</label>
                                                                    <input type="text" name="inside_medicine[]">
                                                                </div>
                                                            </div>
                                                            <div class="form">
                                                                <div class="form-col">
                                                                    <label>Dose</label>
                                                                    <select name="inside_dose[]">
                                                                        <option value="">Select Dose</option>
                                                                        <option>1 – 0 – 0</option>
                                                                        <option>0 – 0 – 1</option>
                                                                        <option>1 – 0 – 1</option>
                                                                        <option>1 – 1 – 0</option>
                                                                        <option>0 – 1 – 0</option>
                                                                        <option>0 – 1 – 1</option>
                                                                        <option>1/2 – 0 – 1/2</option>
                                                                        <option>1/2 – 1/2 – 1/2</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="form">
                                                                <div class="form-col">
                                                                    <label>When</label>
                                                                    <div class="select-with-button">
                                                                        <select name="inside_timing[]">
                                                                            <option value="">Select Timing</option>
                                                                            <option>Before Food</option>
                                                                            <option>After Food</option>
                                                                        </select>
                                                                        <button type="button" class="btn-circle small"
                                                                            onclick="addMedicineRow('inside-treatment-container')">+</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Homeopathic Treatment Section -->
                                            <div class="form">
                                                <div class="section-divider mt-4">
                                                    <div class="title">Homeopathic Treatment</div>
                                                    <div class="line"></div>
                                                    <div class="icon-box" onclick="toggleHomeopathicSection(this)">
                                                        <i class="bi bi-dash-lg" id="homeopathic-toggle-icon"></i>
                                                    </div>
                                                </div>

                                                <div class="homeopathic-section">
                                                    <div id="homeo-treatment-container">
                                                        <div class="pro_filed d-sm-block d-md-flex pt-3">
                                                            <div class="form">
                                                                <div class="form-col">
                                                                    <label>Medicine</label>
                                                                    <input type="text" name="homeo_medicine[]">
                                                                </div>
                                                            </div>
                                                            <div class="form">
                                                                <div class="form-col">
                                                                    <div class="timing-container">
                                                                        <label>When</label>
                                                                        <div class="select-with-button">
                                                                            <select name="homeo_timing[]">
                                                                                <option value="">Select Timing
                                                                                </option>
                                                                                <option>Before Food</option>
                                                                                <option>After Food</option>
                                                                            </select>
                                                                            <button type="button"
                                                                                class="btn-circle small"
                                                                                onclick="addHomeoRow('homeo-treatment-container')">+</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="pro_filed d-sm-block d-md-flex pt-3">
                                            <!-- Prescription Section -->
                                            <div class="form">
                                                <div class="section-divider mt-4">
                                                    <div class="title">Prescription</div>
                                                    <div class="line"></div>
                                                    <div class="icon-box" onclick="togglePrescriptionSection(this)">
                                                        <i class="bi bi-dash-lg" id="prescription-toggle-icon"></i>
                                                    </div>
                                                </div>

                                                <div class="prescription-section">
                                                    <div id="prescription-container">
                                                        <div class="pro_filed d-sm-block d-md-flex pt-3">
                                                            <div class="form">
                                                                <div class="form-col">
                                                                    <label>Medicine</label>
                                                                    <input type="text" name="prescription_medicine[]">
                                                                </div>
                                                            </div>
                                                            <div class="form">
                                                                <div class="form-col">
                                                                    <label>Dose</label>
                                                                    <select name="prescription_dose[]">
                                                                        <option value="">Select Dose</option>
                                                                        <option>1 – 0 – 0</option>
                                                                        <option>0 – 0 – 1</option>
                                                                        <option>1 – 0 – 1</option>
                                                                        <option>1 – 1 – 0</option>
                                                                        <option>0 – 1 – 0</option>
                                                                        <option>0 – 1 – 1</option>
                                                                        <option>1/2 – 0 – 1/2</option>
                                                                        <option>1/2 – 1/2 – 1/2</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="form">
                                                                <div class="form-col">
                                                                    <label>When</label>
                                                                    <div class="select-with-button">
                                                                        <select name="prescription_timing[]">
                                                                            <option value="">Select Timing</option>
                                                                            <option>Before Food</option>
                                                                            <option>After Food</option>
                                                                        </select>
                                                                        <button type="button" class="btn-circle small"
                                                                            onclick="addMedicineRow('prescription-container')">+</button>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Indoor Treatment Section -->
                                            <div class="form">
                                                <div class="section-divider mt-4">
                                                    <div class="title">Indoor Treatment</div>
                                                    <div class="line"></div>
                                                    <div class="icon-box" onclick="toggleIndoorSection(this)">
                                                        <i class="bi bi-dash-lg" id="indoor-toggle-icon"></i>
                                                    </div>
                                                </div>

                                                <div class="indoor-section">
                                                    <div id="indoor-treatment-container">
                                                        <div class="pro_filed d-sm-block d-md-flex pt-3">
                                                            <div class="form">
                                                                <div class="form-col">
                                                                    <label>Medicine</label>
                                                                    <input type="text" name="indoor_medicine[]">
                                                                </div>
                                                            </div>
                                                            <div class="form">
                                                                <div class="form-col">
                                                                    <label>Dose</label>
                                                                    <select name="indoor_dose[]">
                                                                        <option value="">Select Dose</option>
                                                                        <option>1 – 0 – 0</option>
                                                                        <option>0 – 0 – 1</option>
                                                                        <option>1 – 0 – 1</option>
                                                                        <option>1 – 1 – 0</option>
                                                                        <option>0 – 1 – 0</option>
                                                                        <option>0 – 1 – 1</option>
                                                                        <option>1/2 – 0 – 1/2</option>
                                                                        <option>1/2 – 1/2 – 1/2</option>
                                                                    </select>
                                                                </div>
                                                            </div>
                                                            <div class="form">
                                                                <div class="form-col">
                                                                    <div class="note-container">
                                                                        <label>Note</label>
                                                                        <div class="input-with-button">
                                                                            <input type="text" name="indoor_note[]">
                                                                            <button type="button"
                                                                                class="btn-circle small"
                                                                                onclick="addMedicineRow('indoor-treatment-container')">+</button>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="row">
                                            <div class="col-6">
                                                <div class="pro_filed d-sm-block d-md-flex pt-3">
                                                    <!-- Other Treatment Section -->
                                                    <div class="form">
                                                        <div class="section-divider mt-4">
                                                            <div class="title">Other Treatment</div>
                                                            <div class="line"></div>
                                                            <div class="icon-box" onclick="toggleOtherSection(this)">
                                                                <i class="bi bi-dash-lg" id="other-toggle-icon"></i>
                                                            </div>
                                                        </div>

                                                        <div class="other-section">
                                                            <div id="other-treatment-container">
                                                                <div class="pro_filed d-sm-block d-md-flex pt-3">
                                                                    <div class="form">
                                                                        <div class="form-col">
                                                                            <div class="medicine-container">
                                                                                <label>Medicine</label>
                                                                                <div class="input-with-button">
                                                                                    <input type="text"
                                                                                        name="other_medicine[]">
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                    <div class="form">
                                                                        <div class="form-col">
                                                                            <div class="note-container">
                                                                                <label>Note</label>
                                                                                <div class="input-with-button">
                                                                                    <input type="text"
                                                                                        name="other_note[]">
                                                                                    <button type="button"
                                                                                        class="btn-circle small"
                                                                                        onclick="addOtherRow('other-treatment-container')">+</button>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div> --}}
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
                                                        $insideTreatments = $treatments['inside'] ?? [];
                                                        @endphp
                                                        
                                                        @forelse($insideTreatments as $index => $treatment)
                                                        <tr>
                                                            <td><input type="text" name="inside_medicine[]" class="form-control form-control-sm" value="{{ $treatment['medicine'] ?? '' }}" placeholder="Medicine name" autocomplete="off"></td>
                                                            <td>
                                                                <div class="autocomplete-container">
                                                                    <input type="text" name="inside_dose[]" class="form-control form-control-sm dose-input" value="{{ $treatment['dose'] ?? '' }}" placeholder="Dose" autocomplete="off">
                                                                    <div class="autocomplete-dropdown"></div>
                                                                </div>
                                                            </td>
                                                            <td><input type="text" name="inside_days[]" class="form-control form-control-sm" value="{{ $treatment['days'] ?? '' }}" placeholder="Days"></td>
                                                            <td>
                                                                <select name="inside_timing[]" class="form-select form-select-sm">
                                                                    <option value="">Select</option>
                                                                    <option value="Before Food" {{ ($treatment['timing'] ?? '') == 'Before Food' ? 'selected' : '' }}>Before Food</option>
                                                                    <option value="After Food" {{ ($treatment['timing'] ?? '') == 'After Food' ? 'selected' : '' }}>After Food</option>
                                                                    <option value="With Food" {{ ($treatment['timing'] ?? '') == 'With Food' ? 'selected' : '' }}>With Food</option>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                @if($index > 0)
                                                                <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove()"><i class="bi bi-trash"></i> Remove</button>
                                                                @else
                                                                <button type="button" class="btn btn-success btn-sm" onclick="addInsideRow()"><i class="bi bi-plus"></i> Add</button>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        @empty
                                                        <tr>
                                                            <td><input type="text" name="inside_medicine[]" class="form-control form-control-sm" placeholder="Medicine name" autocomplete="off"></td>
                                                            <td>
                                                                <div class="autocomplete-container">
                                                                    <input type="text" name="inside_dose[]" class="form-control form-control-sm dose-input" placeholder="Dose" autocomplete="off">
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
                                                                <button type="button" class="btn btn-success btn-sm" onclick="addInsideRow()"><i class="bi bi-plus"></i> Add</button>
                                                            </td>
                                                        </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <!-- HOMEOPATHIC TREATMENT -->
                                        <div class="treatment-section mb-4">
                                            <div class="section-divider">
                                                <div class="title">Homeopathic Treatment</div>
                                                <div class="line"></div>
                                                <div class="icon-box" onclick="toggleHomeopathicSection(this)">
                                                    <i class="bi bi-dash-lg" id="homeopathic-toggle-icon"></i>
                                                </div>
                                            </div>
                                            
                                            <div id="homeo-section" class="mt-3">
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
                                                    <tbody id="homeo-treatment-body">
                                                        @php
                                                        $homeoTreatments = $treatments['homeo'] ?? [];
                                                        @endphp
                                                        
                                                        @forelse($homeoTreatments as $index => $treatment)
                                                        <tr>
                                                            <td><input type="text" name="homeo_medicine[]" class="form-control form-control-sm" value="{{ $treatment['medicine'] ?? '' }}" placeholder="Medicine name" autocomplete="off"></td>
                                                            <td>
                                                                <div class="autocomplete-container">
                                                                    <input type="text" name="homeo_dose[]" class="form-control form-control-sm dose-input" value="{{ $treatment['dose'] ?? '' }}" placeholder="Dose" autocomplete="off">
                                                                    <div class="autocomplete-dropdown"></div>
                                                                </div>
                                                            </td>
                                                            <td><input type="text" name="homeo_days[]" class="form-control form-control-sm" value="{{ $treatment['days'] ?? '' }}" placeholder="Days"></td>
                                                            <td>
                                                                <select name="homeo_timing[]" class="form-select form-select-sm">
                                                                    <option value="">Select</option>
                                                                    <option value="Before Food" {{ ($treatment['timing'] ?? '') == 'Before Food' ? 'selected' : '' }}>Before Food</option>
                                                                    <option value="After Food" {{ ($treatment['timing'] ?? '') == 'After Food' ? 'selected' : '' }}>After Food</option>
                                                                    <option value="With Food" {{ ($treatment['timing'] ?? '') == 'With Food' ? 'selected' : '' }}>With Food</option>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                @if($index > 0)
                                                                <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove()"><i class="bi bi-trash"></i> Remove</button>
                                                                @else
                                                                <button type="button" class="btn btn-success btn-sm" onclick="addHomeoRow()"><i class="bi bi-plus"></i> Add</button>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        @empty
                                                        <tr>
                                                            <td><input type="text" name="homeo_medicine[]" class="form-control form-control-sm" placeholder="Medicine name" autocomplete="off"></td>
                                                            <td>
                                                                <div class="autocomplete-container">
                                                                    <input type="text" name="homeo_dose[]" class="form-control form-control-sm dose-input" placeholder="Dose" autocomplete="off">
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
                                                                <button type="button" class="btn btn-success btn-sm" onclick="addHomeoRow()"><i class="bi bi-plus"></i> Add</button>
                                                            </td>
                                                        </tr>
                                                        @endforelse
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
                                                        $prescriptionTreatments = $treatments['prescription'] ?? [];
                                                        @endphp
                                                        
                                                        @forelse($prescriptionTreatments as $index => $treatment)
                                                        <tr>
                                                            <td><input type="text" name="prescription_medicine[]" class="form-control form-control-sm" value="{{ $treatment['medicine'] ?? '' }}" placeholder="Medicine name" autocomplete="off"></td>
                                                            <td>
                                                                <div class="autocomplete-container">
                                                                    <input type="text" name="prescription_dose[]" class="form-control form-control-sm dose-input" value="{{ $treatment['dose'] ?? '' }}" placeholder="Dose" autocomplete="off">
                                                                    <div class="autocomplete-dropdown"></div>
                                                                </div>
                                                            </td>
                                                            <td><input type="text" name="prescription_days[]" class="form-control form-control-sm" value="{{ $treatment['days'] ?? '' }}" placeholder="Days"></td>
                                                            <td>
                                                                <select name="prescription_timing[]" class="form-select form-select-sm">
                                                                    <option value="">Select</option>
                                                                    <option value="Before Food" {{ ($treatment['timing'] ?? '') == 'Before Food' ? 'selected' : '' }}>Before Food</option>
                                                                    <option value="After Food" {{ ($treatment['timing'] ?? '') == 'After Food' ? 'selected' : '' }}>After Food</option>
                                                                    <option value="With Food" {{ ($treatment['timing'] ?? '') == 'With Food' ? 'selected' : '' }}>With Food</option>
                                                                </select>
                                                            </td>
                                                            <td>
                                                                @if($index > 0)
                                                                <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove()"><i class="bi bi-trash"></i> Remove</button>
                                                                @else
                                                                <button type="button" class="btn btn-success btn-sm" onclick="addPrescriptionRow()"><i class="bi bi-plus"></i> Add</button>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        @empty
                                                        <tr>
                                                            <td><input type="text" name="prescription_medicine[]" class="form-control form-control-sm" placeholder="Medicine name" autocomplete="off"></td>
                                                            <td>
                                                                <div class="autocomplete-container">
                                                                    <input type="text" name="prescription_dose[]" class="form-control form-control-sm dose-input" placeholder="Dose" autocomplete="off">
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
                                                                <button type="button" class="btn btn-success btn-sm" onclick="addPrescriptionRow()"><i class="bi bi-plus"></i> Add</button>
                                                            </td>
                                                        </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>

                                        <!-- INDOOR TREATMENT -->
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
                                                            <th style="width: 25%">Medicine</th>
                                                            <th style="width: 15%">Dose</th>
                                                            <th style="width: 10%">Days</th>
                                                            <th style="width: 15%">Date</th>
                                                            <th style="width: 10%">Time</th>
                                                            <th style="width: 15%">Note</th>
                                                            <th style="width: 10%"></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody id="indoor-treatment-body">
                                                        @php
                                                        $indoorTreatments = $treatments['indoor'] ?? [];
                                                        @endphp
                                                        
                                                        @forelse($indoorTreatments as $index => $treatment)
                                                        <tr>
                                                            <td><input type="text" name="indoor_medicine[]" class="form-control form-control-sm" value="{{ $treatment['medicine'] ?? '' }}" placeholder="Medicine name" autocomplete="off"></td>
                                                            <td>
                                                                <div class="autocomplete-container">
                                                                    <input type="text" name="indoor_dose[]" class="form-control form-control-sm dose-input" value="{{ $treatment['dose'] ?? '' }}" placeholder="Dose" autocomplete="off">
                                                                    <div class="autocomplete-dropdown"></div>
                                                                </div>
                                                            </td>
                                                            <td><input type="text" name="indoor_days[]" class="form-control form-control-sm" value="{{ $treatment['days'] ?? '' }}" placeholder="Days"></td>
                                                            <td><input type="date" name="indoor_date[]" class="form-control form-control-sm" value="{{ $treatment['date'] ?? '' }}"></td>
                                                            <td><input type="time" name="indoor_time[]" class="form-control form-control-sm" value="{{ $treatment['time'] ?? '' }}"></td>
                                                            <td><input type="text" name="indoor_note[]" class="form-control form-control-sm" value="{{ $treatment['note'] ?? '' }}" placeholder="Note"></td>
                                                            <td>
                                                                @if($index > 0)
                                                                <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove()"><i class="bi bi-trash"></i></button>
                                                                @else
                                                                <button type="button" class="btn btn-success btn-sm" onclick="addMedicineRow()"><i class="bi bi-plus"></i></button>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        @empty
                                                        <tr>
                                                            <td><input type="text" name="indoor_medicine[]" class="form-control form-control-sm" placeholder="Medicine name" autocomplete="off"></td>
                                                            <td>
                                                                <div class="autocomplete-container">
                                                                    <input type="text" name="indoor_dose[]" class="form-control form-control-sm dose-input" placeholder="Dose" autocomplete="off">
                                                                    <div class="autocomplete-dropdown"></div>
                                                                </div>
                                                            </td>
                                                            <td><input type="text" name="indoor_days[]" class="form-control form-control-sm" placeholder="Days"></td>
                                                            <td><input type="date" name="indoor_date[]" class="form-control form-control-sm"></td>
                                                            <td><input type="time" name="indoor_time[]" class="form-control form-control-sm"></td>
                                                            <td><input type="text" name="indoor_note[]" class="form-control form-control-sm" placeholder="Note"></td>
                                                            <td>
                                                                <button type="button" class="btn btn-success btn-sm" onclick="addMedicineRow()"><i class="bi bi-plus"></i></button>
                                                            </td>
                                                        </tr>
                                                        @endforelse
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
                                                        $otherTreatments = $treatments['other'] ?? [];
                                                        @endphp
                                                        
                                                        @forelse($otherTreatments as $index => $treatment)
                                                        <tr>
                                                            <td><input type="text" name="other_medicine[]" class="form-control form-control-sm" value="{{ $treatment['medicine'] ?? '' }}" placeholder="Medicine name" autocomplete="off"></td>
                                                            <td><input type="text" name="other_note[]" class="form-control form-control-sm" value="{{ $treatment['note'] ?? '' }}" placeholder="Note"></td>
                                                            <td>
                                                                @if($index > 0)
                                                                <button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove()"><i class="bi bi-trash"></i> Remove</button>
                                                                @else
                                                                <button type="button" class="btn btn-success btn-sm" onclick="addOtherRow()"><i class="bi bi-plus"></i> Add</button>
                                                                @endif
                                                            </td>
                                                        </tr>
                                                        @empty
                                                        <tr>
                                                            <td><input type="text" name="other_medicine[]" class="form-control form-control-sm" placeholder="Medicine name" autocomplete="off"></td>
                                                            <td><input type="text" name="other_note[]" class="form-control form-control-sm" placeholder="Note"></td>
                                                            <td>
                                                                <button type="button" class="btn btn-success btn-sm" onclick="addOtherRow()"><i class="bi bi-plus"></i> Add</button>
                                                            </td>
                                                        </tr>
                                                        @endforelse
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="pro_filed d-sm-block d-md-flex pt-3">
                                             <div class="form">
                                                 <div class="form-col">
                                                     <label for="reference_by">Reference by</label>
                                                     <input type="text" id="reference_by" name="reference_by"
                                                         placeholder="Reference by" value="{{ $followupMetaValues['reference_by'][0] ?? $patient->getMeta('reference_by') }}">
                                                 </div>
                                             </div>
                                             <div class="form">
                                                 <div class="form-col">
                                                     <label for="referto">Refer to</label>
                                                     <input type="text" id="referto" name="referto"
                                                         placeholder="Refer to" value="{{ $followupMetaValues['referto'][0] ?? $patient->getMeta('referto') }}">
                                                 </div>
                                             </div>
                                         </div>
                                        <div class="pro_filed d-sm-block d-md-flex pt-3">
                                            <div class="form">
                                                <div class="form-col">
                                                    <label for="notes">Notes</label>
                                                    <input type="text" id="notes" name="notes" placeholder="Notes" value="{{ $followupMetaValues['notes'][0] ?? '' }}">
                                                </div>
                                            </div>
                                            <div class="form">
                                                <div class="form-col">
                                                    <label for="next_follow_date">Next follow up date</label>
                                                    <input type="date" id="next_follow_date" name="next_follow_date" placeholder="Next follow up date" value="{{ $followup->next_follow_date ?? '' }}">
                                                </div>
                                            </div>
                                        </div>

                                    <div class="section-divider mt-4">
                                        <div class="title">Payment Information</div>
                                        <div class="line"></div>
                                    </div>
                                    
                                    <div class="d-flex align-items-center bg-light p-3 mb-3">
                                        <input type="checkbox" name="foc" id="foc"
                                            class="form-check-input me-3" {{ !empty($followupMetaValues['foc'][0]) ? 'checked' : '' }}>

                                        <label for="foc" class="mb-0 fw-semibold">
                                            FOC (Free of Charge Inquiry)
                                        </label>
                                    </div>

                                    <div id="payment_section" class="pt-4">
                                        <div class="pro_filed d-sm-block d-md-flex">
                                            <div class="form">
                                                <div class="form-col">
                                                    <label for="charge_id">Followup Charges</label>
                                                    <select id="charge_id" name="charge_id" class="form-control">
                                                        <option value="">Select Charge</option>
                                                        @foreach($charges as $charge)
                                                            <option value="{{ $charge->id }}" 
                                                                    data-price="{{ $charge->charges_price }}"
                                                                    {{ (!empty($followupMetaValues['charge_id'][0]) && $followupMetaValues['charge_id'][0] == $charge->id) ? 'selected' : '' }}>
                                                                {{ $charge->charges_name }} - ₹{{ $charge->charges_price }}
                                                            </option>
                                                        @endforeach
                                                    </select>
                                                    <input type="hidden" id="total_payment" name="total_payment" value="{{ !empty($followupMetaValues['total_payment'][0]) ? $followupMetaValues['total_payment'][0] : 200 }}">
                                                </div>
                                            </div>
                                            <div class="form">
                                                <div class="form-col">
                                                    <label for="given_payment">Paid Amount</label>
                                                    <input type="number" id="given_payment" name="given_payment" placeholder="Enter amount paid" step="0.01" value="{{ $followupMetaValues['given_payment'][0] ?? '' }}">
                                                </div>
                                            </div>
                                            <div class="form">
                                                <div class="form-col">
                                                    <label for="payment_method">Payment Method</label>
                                                    <select id="payment_method" name="payment_method">
                                                        @php $pm = $followupMetaValues['payment_method'][0] ?? ''; @endphp
                                                        <option value="" {{ $pm == '' ? 'selected' : '' }}>Select Type</option>
                                                        <option value="Cash" {{ $pm == 'Cash' ? 'selected' : '' }}>Cash</option>
                                                        <option value="Online" {{ $pm == 'Online' ? 'selected' : '' }}>Online</option>
                                                        <option value="Cheque" {{ $pm == 'Cheque' ? 'selected' : '' }}>Cheque</option>
                                                    </select>
                                                </div>
                                            </div>
                                            <div class="form">
                                                <div class="form-col">
                                                    <label for="due_payment">Due Amount</label>
                                                    @php 
                                                        $totalForDue = !empty($followupMetaValues['total_payment'][0]) ? (float)$followupMetaValues['total_payment'][0] : 200;
                                                        $givenForDue = (float)($followupMetaValues['given_payment'][0] ?? 0);
                                                    @endphp
                                                    <input type="number" id="due_payment" name="due_payment" value="{{ $totalForDue - $givenForDue }}" readonly>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        // Set default time only if value is empty
                                        const now = new Date().toTimeString().split(' ')[0].substring(0, 5);
                                        const followTimeEl = document.getElementById('followups_time');
                                        if (followTimeEl && !followTimeEl.value) followTimeEl.value = now;

                                        // FOC checkbox functionality
                                        const focCheckbox = document.getElementById('foc');
                                        const paymentSection = document.getElementById('payment_section');
                                        const chargeSelect = document.getElementById('charge_id');
                                        const totalPaymentInput = document.getElementById('total_payment');
                                        const givenPaymentInput = document.getElementById('given_payment');
                                        const duePaymentInput = document.getElementById('due_payment');
                                        const paymentMethod = document.getElementById('payment_method');
                                        
                                        if (focCheckbox && paymentSection) {
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
                                            
                                            // Initial state
                                            togglePaymentSection();
                                            
                                            // Add event listener
                                            focCheckbox.addEventListener('change', togglePaymentSection);
                                        }

                                        // Charge selection functionality
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

                                        // Payment calculation
                                        if (givenPaymentInput && totalPaymentInput && duePaymentInput) {
                                            givenPaymentInput.addEventListener('input', function() {
                                                const total = parseFloat(totalPaymentInput.value) || 0;
                                                const given = parseFloat(this.value) || 0;
                                                duePaymentInput.value = (total - given).toFixed(2);
                                            });
                                        }
                                    });
                                </script>

                                        {{-- <div class="d-flex align-items-center bg-light pt-3">
                                            <input type="checkbox" name="foc" id="foc"
                                                class="form-check-input me-3">

                                            <label for="foc" class="mb-0 fw-semibold">
                                                FOC (No payment is collected from patient)
                                            </label>
                                        </div>

                                    

                                        <div id="payment_section">

                                            <div class="pro_filed d-sm-block d-md-flex pt-3">
                                                <div class="form">
                                                    <div class="form-col">
                                                        <label for="total_payment">Total Payment</label>
                                                        <input type="number" id="total_payment" name="total_payment"
                                                            placeholder="Total amount" step="0.01">
                                                    </div>
                                                </div>

                                                <div class="form">
                                                    <div class="form-col">
                                                        <label for="discount_payment">Discount</label>
                                                        <input type="number" id="discount_payment"
                                                            name="discount_payment" placeholder="Discount amount"
                                                            step="0.01">
                                                    </div>
                                                </div>

                                                <div class="form">
                                                    <div class="form-col">
                                                        <label for="given_payment">Given Payment</label>
                                                        <input type="number" id="given_payment" name="given_payment"
                                                            placeholder="Amount paid" step="0.01">
                                                    </div>
                                                </div>

                                                <div class="form">
                                                    <div class="form-col">
                                                        <label for="due_payment">Due Payment</label>
                                                        <input type="number" id="due_payment" name="due_payment"
                                                            placeholder="Due amount" step="0.01" readonly>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="section-divider mt-4">
                                                <div class="title">Given Payment</div>
                                                <div class="line"></div>
                                            </div>

                                            <div class="pro_filed d-sm-block d-md-flex separate_payment">
                                                <div class="form">
                                                    <div class="form-col">
                                                        <label for="cash_payment">Cash Payment</label>
                                                        <input type="number" id="cash_payment" name="cash_payment"
                                                            placeholder="Cash payment" step="0.01">
                                                    </div>
                                                </div>

                                                <div class="form">
                                                    <div class="form-col">
                                                        <label for="gp_payment">Google Pay</label>
                                                        <input type="number" id="gp_payment" name="gp_payment"
                                                            placeholder="Google Pay" step="0.01">
                                                    </div>
                                                </div>

                                                <div class="form">
                                                    <div class="form-col">
                                                        <label for="cheque_payment">Cheque Payment</label>
                                                        <input type="number" id="cheque_payment" name="cheque_payment"
                                                            placeholder="Cheque Payment" step="0.01">
                                                    </div>
                                                </div>
                                            </div>

                                        </div> --}}



                                        <!-- Form Actions -->
                                        <div class="form-actions">
                                            <a href="/search-svc-patient" class="btn btn-secondary">Cancel</a>
                                            <button type="submit" class="btn btn-primary">Save Inquiry</button>
                                        </div>
                                </form>
                            </div>
                        </section>  
                    </div>
                </div>
            </div>
        </div>
    </div>


    </div>
    <div class="modal fade" id="historyModal" tabindex="-1" aria-labelledby="historyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="historyModalLabel">
                        Follow-up History - <span id="modalDate"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="historyContent">
                        <!-- History content will be loaded here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    {{--
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> --}}
    <script>
        // FOC checkbox handler - only if element exists
        const focElement = document.getElementById('foc');
        if (focElement) {
            focElement.addEventListener('change', function() {
                const section = document.getElementById('payment_section');
                if (section) {
                    if (this.checked) {
                        section.style.display = "none";
                    } else {
                        section.style.display = "block";
                    }
                }
            });
        }

        function toggleInsideSection(element) {
            const section = document.getElementById('inside-section');
            const icon = element.querySelector('i');
            if (section.style.display === 'none') {
                section.style.display = 'block';
                icon.classList.replace('bi-plus-lg', 'bi-dash-lg');
            } else {
                section.style.display = 'none';
                icon.classList.replace('bi-dash-lg', 'bi-plus-lg');
            }
        }

        function toggleInsideSection1(element) {
            const section = document.querySelector('.inside-section1');
            const icon = element.querySelector('i');
            if (section.style.display === 'none') {
                section.style.display = 'block';
                icon.classList.replace('bi-plus-lg', 'bi-dash-lg');
            } else {
                section.style.display = 'none';
                icon.classList.replace('bi-dash-lg', 'bi-plus-lg');
            }
        }

        function toggleLipidSection(element) {
            const section = document.getElementById('lipid-section-container');
            const icon = element.querySelector('i');
            if (section.style.display === 'none') {
                section.style.display = 'block';
                icon.classList.replace('bi-plus-lg', 'bi-dash-lg');
            } else {
                section.style.display = 'none';
                icon.classList.replace('bi-dash-lg', 'bi-plus-lg');
            }
        }

        function toggleHomeopathicSection(element) {
            const section = document.getElementById('homeo-section');
            const icon = element.querySelector('i');
            if (section.style.display === 'none') {
                section.style.display = 'block';
                icon.classList.replace('bi-plus-lg', 'bi-dash-lg');
            } else {
                section.style.display = 'none';
                icon.classList.replace('bi-dash-lg', 'bi-plus-lg');
            }
        }

        function togglePrescriptionSection(element) {
            const section = document.getElementById('prescription-section');
            const icon = element.querySelector('i');
            if (section.style.display === 'none') {
                section.style.display = 'block';
                icon.classList.replace('bi-plus-lg', 'bi-dash-lg');
            } else {
                section.style.display = 'none';
                icon.classList.replace('bi-dash-lg', 'bi-plus-lg');
            }
        }

        function toggleIndoorSection(element) {
            const section = document.getElementById('indoor-section');
            const icon = element.querySelector('i');
            if (section.style.display === 'none') {
                section.style.display = 'block';
                icon.classList.replace('bi-plus-lg', 'bi-dash-lg');
            } else {
                section.style.display = 'none';
                icon.classList.replace('bi-dash-lg', 'bi-plus-lg');
            }
        }

        function toggleOtherSection(element) {
            const section = document.getElementById('other-section');
            const icon = element.querySelector('i');
            if (section.style.display === 'none') {
                section.style.display = 'block';
                icon.classList.replace('bi-plus-lg', 'bi-dash-lg');
            } else {
                section.style.display = 'none';
                icon.classList.replace('bi-dash-lg', 'bi-plus-lg');
            }
        }

        function addInsideRow() {
            const tbody = document.getElementById('inside-treatment-body');
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td><input type="text" name="inside_medicine[]" class="form-control form-control-sm" placeholder="Medicine name"></td>
                <td>
                    <div class="autocomplete-container">
                        <input type="text" name="inside_dose[]" class="form-control form-control-sm dose-input" placeholder="Dose">
                        <div class="autocomplete-dropdown"></div>
                    </div>
                </td>
                <td>
                    <select name="inside_timing[]" class="form-select form-select-sm">
                        <option value="">Select</option>
                        <option>Before Food</option>
                        <option>After Food</option>
                        <option>With Food</option>
                    </select>
                </td>
                <td><button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove()"><i class="bi bi-trash"></i></button></td>
            `;
            tbody.appendChild(tr);
            initDoseAutocomplete(tr.querySelector('.dose-input'));
        }

        function addHomeoRow() {
            const tbody = document.getElementById('homeo-treatment-body');
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td><input type="text" name="homeo_medicine[]" class="form-control form-control-sm" placeholder="Medicine name"></td>
                <td>
                    <div class="autocomplete-container">
                        <input type="text" name="homeo_dose[]" class="form-control form-control-sm dose-input" placeholder="Dose">
                        <div class="autocomplete-dropdown"></div>
                    </div>
                </td>
                <td>
                    <select name="homeo_timing[]" class="form-select form-select-sm">
                        <option value="">Select</option>
                        <option>Before Food</option>
                        <option>After Food</option>
                        <option>With Food</option>
                    </select>
                </td>
                <td><button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove()"><i class="bi bi-trash"></i></button></td>
            `;
            tbody.appendChild(tr);
            initDoseAutocomplete(tr.querySelector('.dose-input'));
        }

        function addPrescriptionRow() {
            const tbody = document.getElementById('prescription-treatment-body');
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td><input type="text" name="prescription_medicine[]" class="form-control form-control-sm" placeholder="Medicine name"></td>
                <td>
                    <div class="autocomplete-container">
                        <input type="text" name="prescription_dose[]" class="form-control form-control-sm dose-input" placeholder="Dose">
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
                <td><button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove()"><i class="bi bi-trash"></i> Remove</button></td>
            `;
            tbody.appendChild(tr);
            initDoseAutocomplete(tr.querySelector('.dose-input'));
        }

        function addMedicineRow() {
            const tbody = document.getElementById('indoor-treatment-body');
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td><input type="text" name="indoor_medicine[]" class="form-control form-control-sm" placeholder="Medicine name"></td>
                <td>
                    <div class="autocomplete-container">
                        <input type="text" name="indoor_dose[]" class="form-control form-control-sm dose-input" placeholder="Dose">
                        <div class="autocomplete-dropdown"></div>
                    </div>
                </td>
                <td><input type="text" name="indoor_days[]" class="form-control form-control-sm" placeholder="Days"></td>
                <td><input type="date" name="indoor_date[]" class="form-control form-control-sm"></td>
                <td><input type="time" name="indoor_time[]" class="form-control form-control-sm"></td>
                <td><input type="text" name="indoor_note[]" class="form-control form-control-sm" placeholder="Note"></td>
                <td><button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove()"><i class="bi bi-trash"></i></button></td>
            `;
            tbody.appendChild(tr);
            initDoseAutocomplete(tr.querySelector('.dose-input'));
        }

        function addOtherRow() {
            const tbody = document.getElementById('other-treatment-body');
            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td><input type="text" name="other_medicine[]" class="form-control form-control-sm" placeholder="Medicine name"></td>
                <td><input type="text" name="other_note[]" class="form-control form-control-sm" placeholder="Note"></td>
                <td><button type="button" class="btn btn-danger btn-sm" onclick="this.closest('tr').remove()"><i class="bi bi-trash"></i> Remove</button></td>
            `;
            tbody.appendChild(tr);
        }

        const doseOptions = [
            "1 – 0 – 0", "0 – 0 – 1", "1 – 0 – 1", "1 – 1 – 0",
            "0 – 1 – 0", "0 – 1 – 1", "1 – 1 – 1", "1/2 – 0 – 0",
            "0 – 0 – 1/2", "1/2 – 0 – 1/2", "1/2 – 1/2 – 1/2"
        ];

        function initDoseAutocomplete(input) {
            if (!input) return;
            const dropdown = input.nextElementSibling;
            
            input.addEventListener('input', function() {
                const val = this.value;
                dropdown.innerHTML = '';
                if (!val) {
                    dropdown.style.display = 'none';
                    return;
                }
                
                const matches = doseOptions.filter(opt => opt.includes(val));
                if (matches.length > 0) {
                    matches.forEach(match => {
                        const div = document.createElement('div');
                        div.textContent = match;
                        div.onclick = function() {
                            input.value = match;
                            dropdown.style.display = 'none';
                        };
                        dropdown.appendChild(div);
                    });
                    dropdown.style.display = 'block';
                } else {
                    dropdown.style.display = 'none';
                }
            });

            document.addEventListener('click', function(e) {
                if (!input.contains(e.target) && !dropdown.contains(e.target)) {
                    dropdown.style.display = 'none';
                }
            });
        }

        document.querySelectorAll('.dose-input').forEach(initDoseAutocomplete);

        function openHistoryModal(selectedDate) {
            document.getElementById('modalDate').textContent = formatDate(selectedDate);

            document.getElementById('historyContent').innerHTML = `
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p>Loading history data...</p>
                </div>
            `;

            fetch(`/patient/{{ $patient->patient_id }}/followup-history?date=${selectedDate}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('historyContent').innerHTML = data.html;
                    } else {
                        document.getElementById('historyContent').innerHTML = `
                            <div class="alert alert-warning">
                                No history data found for this date.
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('historyContent').innerHTML = `
                        <div class="alert alert-danger">
                            Error loading history data.
                        </div>
                    `;
                });

            const historyModal = new bootstrap.Modal(document.getElementById('historyModal'));
            historyModal.show();
        }

        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        }

        // In your JavaScript section
function formatTimeFromMeta(timeValue) {
    if (!timeValue) return 'N/A';
    // Assuming timeValue is in HH:MM:SS format
    const [hours, minutes] = timeValue.split(':');
    const date = new Date();
    date.setHours(hours, minutes);
    return date.toLocaleTimeString('en-US', {
        hour: '2-digit',
        minute: '2-digit'
    });
}
    </script>
    <script>
        // Load times for selected date
        function loadTimesForDate(date) {
            if (!date) {
                document.getElementById('time-selector-container').innerHTML = `
                    <label for="time">Select Time:</label>
                    <select name="time" id="time" class="form-select" disabled>
                        <option value="">Select date first</option>
                    </select>
                `;
                return;
            }
            
            // Fetch times for the selected date
            fetch(`/api/patient/{{ $patient->patient_id }}/followup-times?date=${date}`)
                .then(response => response.json())
                .then(data => {
                    let timeSelect = `
                        <label for="time">Select Time:</label>
                        <select name="time" id="time" class="form-select" onchange="this.form.submit()">
                            <option value="">All Times</option>`;
                    
                    if (data.times && data.times.length > 0) {
                        data.times.forEach(time => {
                            timeSelect += `<option value="${time.time}">${time.formatted}</option>`;
                        });
                    } else {
                        timeSelect += `<option value="">No visits on this date</option>`;
                    }
                    
                    timeSelect += `</select>`;
                    
                    document.getElementById('time-selector-container').innerHTML = timeSelect;
                })
                .catch(error => {
                    console.error('Error loading times:', error);
                });
        }
        
        // Open history modal with date and time
        function openHistoryModal(selectedDate, selectedTime) {
            if (!selectedDate) {
                alert('Please select a date first');
                return;
            }
            
            document.getElementById('modalDate').textContent = formatDate(selectedDate);
            if (selectedTime) {
                document.getElementById('modalDate').textContent += ' at ' + formatTime(selectedTime);
            }
        
            document.getElementById('historyContent').innerHTML = `
                <div class="text-center">
                    <div class="spinner-border" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p>Loading history data...</p>
                </div>
            `;
        
            let url = `/patient/{{ $patient->patient_id }}/followup-history?date=${selectedDate}`;
            if (selectedTime) {
                url += `&time=${selectedTime}`;
            }
        
            fetch(url)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById('historyContent').innerHTML = data.html;
                        // Update modal title with visit count
                        if (data.count > 1) {
                            document.getElementById('modalDate').textContent += ` (${data.count} visits)`;
                        }
                    } else {
                        document.getElementById('historyContent').innerHTML = data.html || `
                            <div class="alert alert-warning">
                                No history data found for this date.
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('historyContent').innerHTML = `
                        <div class="alert alert-danger">
                            Error loading history data. Please try again.
                        </div>
                    `;
                });
        
            const historyModal = new bootstrap.Modal(document.getElementById('historyModal'));
            historyModal.show();
        }
        
        function formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('en-US', {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            });
        }
        
        function formatTime(timeString) {
            return new Date('2000-01-01T' + timeString).toLocaleTimeString('en-US', {
                hour: '2-digit',
                minute: '2-digit'
            });
        }
        
        // Function to load single visit details (called from history modal)
        function loadSingleVisit(followupId) {
            // Show loading in the history content area
            const historyContent = document.getElementById('historyContent');
            if (historyContent) {
                historyContent.innerHTML = `
                    <div class="text-center">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p>Loading full history...</p>
                    </div>
                `;
            }
            
            fetch(`/followup/${followupId}/full-details`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && historyContent) {
                        historyContent.innerHTML = data.html;
                    } else {
                        historyContent.innerHTML = `
                            <div class="alert alert-danger">
                                Error loading full history. Please try again.
                            </div>
                        `;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    if (historyContent) {
                        historyContent.innerHTML = `
                            <div class="alert alert-danger">
                                Error loading full history. Please try again.
                            </div>
                        `;
                    }
                });
        }
        
        // Initialize on page load
        document.addEventListener('DOMContentLoaded', function() {
            // Set today's date if not already set
            const today = new Date().toISOString().split('T')[0];
            const now = new Date().toTimeString().split(' ')[0].substring(0, 5);
            
            const followDateInput = document.getElementById('follow_date');
            if (followDateInput && !followDateInput.value) {
                followDateInput.value = today;
            }
            
            const followTimeInput = document.getElementById('followups_time');
            if (followTimeInput && !followTimeInput.value) {
                followTimeInput.value = now;
            }

            // Initialize multi-select for diagnosis
            loadSuggestions();
        });

        let suggestionsData = {
            complaints: [],
            diagnoses: []
        };
        
        // Load suggestions from API and then initialize autocomplete
        function loadSuggestions() {
            console.log('Loading suggestions for follow-up...');
            
            // Get CSRF token from meta tag
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
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    console.log('Suggestions data received:', data);
                    
                    if (data.complaints) suggestionsData.complaints = data.complaints;
                    if (data.diagnoses) suggestionsData.diagnoses = data.diagnoses;
                    
                    // Initialize autocomplete with data
                    initAutocomplete();
                })
                .catch(error => {
                    console.error('Error loading suggestions:', error);
                    // Fallback to empty if API fails
                    initAutocomplete();
                });
        }
        
        function initAutocomplete() {
            console.log('Initializing diagnosis multi-select...');
            setupMultiSelect('diagnosis', suggestionsData.diagnoses);
        }
        
        function setupMultiSelect(fieldId, suggestions) {
            const input = document.getElementById(fieldId);
            const dropdown = document.getElementById(fieldId + '-dropdown');
            const selectedContainer = document.getElementById(fieldId + '-selected');
            const hiddenInput = document.getElementById(fieldId + '-hidden');
            
            if (!input || !dropdown || !selectedContainer || !hiddenInput) {
                console.error(`Missing elements for ${fieldId} multi-select`);
                return;
            }

            let selectedItems = [];
            let selectedIndex = -1;
            
            // Initial population from hidden input
            if (hiddenInput.value) {
                const initialValues = hiddenInput.value.split(',').map(v => v.trim()).filter(v => v);
                initialValues.forEach(val => addItem(val));
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
                        updateBtnSelection(items, selectedIndex);
                        break;
                        
                    case 'ArrowUp':
                        e.preventDefault();
                        selectedIndex = Math.max(selectedIndex - 1, -1);
                        updateBtnSelection(items, selectedIndex);
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
                selectedItems.forEach(item => {
                    const itemElement = document.createElement('div');
                    itemElement.className = 'selected-item';
                    itemElement.innerHTML = `
                        ${item}
                        <button type="button" class="remove-item" data-item="${item}">
                            <i class="fas fa-times"></i>
                        </button>
                    `;
                    
                    itemElement.querySelector('.remove-item').addEventListener('click', function(e) {
                        e.preventDefault();
                        removeItem(item);
                    });
                    
                    selectedContainer.appendChild(itemElement);
                });
            }
            
            function updateHiddenInput() {
                hiddenInput.value = selectedItems.join(', ');
            }
            
            // Store functions in the input element
            input.multiSelect = {
                addItem: addItem,
                removeItem: removeItem,
                selectedItems: () => selectedItems
            };
        }
        
        function showMultiSelectSuggestions(input, dropdown, suggestions, selectedItems, filter = '') {
            dropdown.innerHTML = '';
            
            const filteredSuggestions = (suggestions || []).filter(suggestion => 
                suggestion.toLowerCase().includes(filter) && !selectedItems.includes(suggestion)
            );
            
            if (filteredSuggestions.length === 0 && filter) {
                const addNew = document.createElement('div');
                addNew.className = 'autocomplete-item add-new';
                addNew.innerHTML = `<i class="fas fa-plus"></i> Add "${filter}"`;
                addNew.addEventListener('click', function() {
                    if (input.multiSelect) input.multiSelect.addItem(filter);
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

        function updateBtnSelection(items, selectedIndex) {
            items.forEach((item, index) => {
                if (index === selectedIndex) {
                    item.classList.add('selected');
                } else {
                    item.classList.remove('selected');
                }
            });
        }
    </script>
@endsection
