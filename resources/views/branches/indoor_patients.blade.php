@extends('admin.layouts.layouts')

@section('title', 'Indoor Patients (IPD)')

@section('content')

    <style>
        /* Import Poppins font */
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap');

        /* Apply Poppins to entire page */
        body {
            font-family: 'Poppins', sans-serif !important;
        }

        .search-section {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 15px;
            border: 1px solid #dee2e6;
        }

        .search-row {
            display: flex;
            gap: 15px;
            align-items: center;
            margin-bottom: 15px;
            flex-wrap: wrap;
        }   

        .search-field {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .search-field label {
            font-weight: bold;
            color: #2c3e50;
            white-space: nowrap;
        }

        .search-field input,
        .search-field select {
            padding: 8px 12px;
            border: 1px solid #bdc3c7;
            border-radius: 4px;
            font-size: 14px;
        }

        .search-btn {
            background: rgb(8, 104, 56);
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
        }

        .patient-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 14px;
            background: white;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .patient-table th {
            background: #006637;
            color: white;
            font-weight: bold;
            padding: 15px 10px;
            text-align: left;
            border: none;
            font-size: 14px;
        }

        .patient-table td {
            padding: 12px 10px;
            text-align: left;
            border-bottom: 1px solid #e9ecef;
            font-size: 13px;
        }

        .patient-table tr:nth-child(even) {
            background: #f8f9fa;
        }

        .patient-table tr:hover {
            background: #e9f7ef;
        }

        .header-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .section-title {
            font-size: 24px;
            font-weight: bold;
            color: #006637;
        }

        .empty-state {
            text-align: center;
            padding: 40px;
            color: #6c757d;
        }

        .action-buttons {
            display: flex;
            gap: 8px;
            justify-content: center;
        }

        .action-btn {
            width: 32px;
            height: 32px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 4px;
            background: transparent;
            border: 1px solid transparent;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
        }

        .btn-profile-square {
            border: 1px solid #28a745;
            color: #28a745 !important;
        }

        .btn-profile-square:hover {
            background-color: #28a745;
            color: white !important;
        }

        .pagination {
            margin-top: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 13px;
            color: #6c757d;
        }

        .pagination-buttons {
            display: flex;
            gap: 8px;
        }

        .pagination-buttons .btn {
            padding: 6px 12px;
            background: #006637;
            color: white;
            border-radius: 4px;
            text-decoration: none;
        }

        .pagination-buttons .btn[disabled] {
            background: #ccc;
            cursor: not-allowed;
        }

        .dual-search-container {
            display: flex;
            align-items: center;
            gap: 15px;
            width: 100%;
        }

        .search-input {
            padding: 8px 12px;
            border: 1px solid #bdc3c7;
            border-radius: 4px;
            font-size: 14px;
            width: 100%;
            max-width: 400px;
        }

        .search-label {
            font-weight: 600;
            color: #006637;
            margin-bottom: 5px;
            display: block;
        }

        .badge-ipd {
            background-color: rgba(0, 102, 55, 0.1);
            color: #006637;
            padding: 4px 12px;
            border-radius: 50px;
            font-weight: 600;
            font-size: 11px;
            text-transform: uppercase;
        }

        /* Apply Poppins to entire page but exclude icons */
        body, .main-content, .card, .table, .modal-content, input, select, textarea, button {
            font-family: 'Poppins', sans-serif !important;
        }
        
        /* Ensure FontAwesome and Bootstrap Icons preserve their font families */
        .fas, .far, .fal, .fab, .fa, .fa-solid, .fa-regular, .fa-brands, .bi, 
        [class^="fa-"], [class*=" fa-"], [class^="bi-"], [class*=" bi-"] {
            font-family: "Font Awesome 6 Free", "Font Awesome 6 Brands", "bootstrap-icons", "FontAwesome" !important;
        }
    </style>

    <div class="header-row">
        <div class="section-title">
            <i class="bi bi-hospital"></i> Indoor Patients (IPD)
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="search-section">
        <form method="GET" action="{{ route('indoor.patients') }}" id="searchForm">
            <div class="dual-search-container">
                <div style="flex: 1;">
                    <!-- <label class="search-label"> Search</label> -->
                    <input type="text" name="global_search" class="search-input" placeholder="Search by name, ID, or diagnosis..."
                        value="{{ request('global_search') }}" id="globalSearchInput">
                </div>
                <div style="width: 150px;">
                    <label class="search-label">Show</label>
                    <select name="per_page" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="10" {{ request('per_page') == 10 ? 'selected' : '' }}>10 entries</option>
                        <option value="25" {{ request('per_page') == 25 ? 'selected' : '' }}>25 entries</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 entries</option>
                    </select>
                </div>
                <div style="margin-top: 25px;">
                    <button type="submit" class="btn btn-primary" style="background-color: #006637; border: none;">
                        <i class="bi bi-search"></i> Search
                    </button>
                </div>
            </div>
        </form>
    </div>

    <div class="table-responsive">
        <table class="patient-table">
            <thead>
                <tr>
                    <th>Patient Id</th>
                    <th>Name</th>
                    <th>Address</th>
                    <th>Age</th>
                    <th>Diagnosis</th>
                    <th>Inquiry Date</th>
                    <th>Added On</th>
                    <th>Status</th>
                    <th style="text-align: center;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($patients as $patient)
                    <tr>
                        <td style="font-weight: 600; color: #006637;">{{ $patient->patient_id }}</td>
                        <td>{{ $patient->patient_name }}</td>
                        <td>{{ $patient->address }}</td>
                        <td>{{ $patient->age }}</td>
                        <td>
    @if($patient->diagnosis)
        @php
            $diagnoses = explode(', ', $patient->diagnosis);
            $diagnoses = array_filter($diagnoses);
            if (!empty($diagnoses)) {
                echo '<span class="badge bg-info me-1">' . implode('</span><span class="badge bg-info me-1">', array_slice($diagnoses, 0, 3)) . '</span>';
                if (count($diagnoses) > 3) {
                    echo '<span class="badge bg-secondary">+' . (count($diagnoses) - 3) . ' more</span>';
                }
            }
        @endphp
    @else
        -
    @endif
</td>
                        <td>{{ $patient->inquiry_date ? \Carbon\Carbon::parse($patient->inquiry_date)->format('d/m/Y') : '-' }}</td>
                        <td>{{ $patient->created_at->format('d/m/Y') }}</td>
                        <td><span class="badge-ipd">Indoor (IPD)</span></td>
                        <td>
                            <div class="action-buttons">
                                <button type="button" class="action-btn btn-profile-square" 
                                    onclick="openIndoorModal({{ json_encode([
                                        'id' => $patient->id,
                                        'name' => $patient->patient_name,
                                        'age' => $patient->age,
                                        'diagnosis' => $patient->diagnosis ?? 'N/A',
                                        'complaints' => $patient->getMeta('complain') ?? 'N/A',
                                        'treatments' => $patient->treatments
                                    ]) }})" 
                                    title="Manage Treatment">
                                    <i class="bi bi-hospital"></i>
                                </button>
                                <a href="{{ route('svc.profile', $patient->id) }}" class="action-btn btn-profile-square" title="View Profile">
                                    <i class="fas fa-address-card"></i>
                                </a>
                                <a href="{{ route('edit.svc.inquiry', $patient->id) }}" class="action-btn btn-profile-square" title="Edit Inquiry" style="border-color: #007bff; color: #007bff !important;">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="empty-state">
                            <i class="bi bi-info-circle" style="font-size: 24px; display: block; margin-bottom: 10px;"></i>
                            No indoor patients found.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if ($patients->hasPages())
        <div class="pagination">
            <div>
                Showing {{ $patients->firstItem() }} to {{ $patients->lastItem() }} of {{ $patients->total() }} patients
            </div>
            <div class="pagination-buttons">
                @if ($patients->onFirstPage())
                    <span class="btn" disabled>Previous</span>
                @else
                    <a href="{{ $patients->previousPageUrl() }}" class="btn">Previous</a>
                @endif

                @if ($patients->hasMorePages())
                    <a href="{{ $patients->nextPageUrl() }}" class="btn">Next</a>
                @else
                    <span class="btn" disabled>Next</span>
                @endif
            </div>
        </div>
    @endif

    {{-- Indoor Treatment Modal --}}
    <div class="modal fade" id="indoorTreatmentModal" tabindex="-1" aria-labelledby="indoorTreatmentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <form id="indoorTreatmentForm" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title" id="indoorTreatmentModalLabel">
                            <i class="bi bi-hospital"></i> Manage Indoor Treatment
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="modal-patient-info" style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #dee2e6;">
                            <div class="row">
                                <div class="col-md-6"><strong>Name:</strong> <span id="modal-patient-name"></span></div>
                                <div class="col-md-6"><strong>Age:</strong> <span id="modal-patient-age"></span></div>
                                <div class="col-md-6"><strong>Diagnosis:</strong> <span id="modal-patient-diagnosis"></span></div>
                                <div class="col-md-6"><strong>Complaints:</strong> <span id="modal-patient-complaints"></span></div>
                            </div>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered modal-table">
                                <thead style="background: #006637; color: white;">
                                    <tr>
                                        <th width="35%">Medicine</th>
                                        <th width="25%">Dose</th>
                                        <th width="15%">Days</th>
                                        <th width="20%">Note</th>
                                        <th width="5%">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="indoor-treatment-modal-body">
                                </tbody>
                            </table>
                        </div>
                        <button type="button" class="btn btn-success btn-sm mt-2" onclick="addIndoorRow()">
                            <i class="bi bi-plus-lg"></i> Add Row
                        </button>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" style="background-color: #006637; border-color: #006637;">Save Treatment</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        const doseSuggestions = [
            "1 – 0 – 0", "0 – 0 – 1", "1 – 0 – 1", "1 – 1 – 1", 
            "1 – 1 – 0", "0 – 1 – 0", "0 – 1 – 1", "1/2 – 0 – 1/2", 
            "1/2 – 1/2 – 1/2", "2 – 0 – 0", "0 – 0 – 2", "2 – 0 – 2", 
            "1 – 1 – 1 – 1"
        ];

        function openIndoorModal(data) {
            document.getElementById('modal-patient-name').textContent = data.name;
            document.getElementById('modal-patient-age').textContent = data.age;
            document.getElementById('modal-patient-diagnosis').textContent = data.diagnosis;
            document.getElementById('modal-patient-complaints').textContent = data.complaints;
            
            const form = document.getElementById('indoorTreatmentForm');
            form.action = `/svc-profile/${data.id}/indoor-treatment`;
            
            const tbody = document.getElementById('indoor-treatment-modal-body');
            tbody.innerHTML = '';
            
            if (data.treatments && data.treatments.length > 0) {
                data.treatments.forEach(t => {
                    addRowToModal(t.medicine, t.dose, t.days, t.note);
                });
            } else {
                addRowToModal();
            }
            
            const modal = new bootstrap.Modal(document.getElementById('indoorTreatmentModal'));
            modal.show();
        }

        function addRowToModal(medicine = '', dose = '', days = '', note = '') {
            const tbody = document.getElementById('indoor-treatment-modal-body');
            const newRow = document.createElement('tr');
            newRow.innerHTML = `
                <td><input type="text" name="indoor_medicine[]" value="${medicine}" class="form-control form-control-sm" placeholder="Medicine name" autocomplete="off"></td>
                <td>
                    <div class="autocomplete-container" style="position: relative;">
                        <input type="text" name="indoor_dose[]" value="${dose}" class="form-control form-control-sm dose-input" placeholder="Select or type dose" autocomplete="off">
                        <div class="autocomplete-dropdown" style="position: absolute; top: 100%; left: 0; right: 0; background: white; border: 1px solid #ccc; z-index: 1000; display: none; max-height: 200px; overflow-y: auto;"></div>
                    </div>
                </td>
                <td><input type="text" name="indoor_days[]" value="${days}" class="form-control form-control-sm" placeholder="Days"></td>
                <td><input type="text" name="indoor_note[]" value="${note}" class="form-control form-control-sm" placeholder="Note"></td>
                <td>
                    <button type="button" class="btn btn-danger btn-sm" onclick="deleteIndoorRow(this)">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            `;
            tbody.appendChild(newRow);
            
            const doseInput = newRow.querySelector('.dose-input');
            const dropdown = newRow.querySelector('.autocomplete-dropdown');
            setupDoseAutocomplete(doseInput, dropdown);
        }

        function addIndoorRow() {
            addRowToModal();
        }

        function deleteIndoorRow(button) {
            Swal.fire({
                title: 'Are you sure?',
                text: "Do you want to delete this treatment row?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    button.closest('tr').remove();
                    Swal.fire(
                        'Deleted!',
                        'Treatment row removed.',
                        'success'
                    );
                }
            });
        }

        function setupDoseAutocomplete(input, dropdown) {
            input.addEventListener('focus', function() {
                showDoseSuggestions(input, dropdown, input.value.toLowerCase());
            });

            input.addEventListener('input', function() {
                showDoseSuggestions(input, dropdown, this.value.toLowerCase());
            });

            document.addEventListener('click', function(e) {
                if (!input.contains(e.target) && !dropdown.contains(e.target)) {
                    dropdown.style.display = 'none';
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
                    item.style.padding = '8px';
                    item.style.cursor = 'pointer';
                    item.addEventListener('mouseover', () => item.style.background = '#f0f0f0');
                    item.addEventListener('mouseout', () => item.style.background = 'white');
                    item.addEventListener('click', function() {
                        input.value = suggestion;
                        dropdown.style.display = 'none';
                    });
                    dropdown.appendChild(item);
                });
                dropdown.style.display = 'block';
            } else {
                dropdown.style.display = 'none';
            }
        }
    </script>
@endsection
