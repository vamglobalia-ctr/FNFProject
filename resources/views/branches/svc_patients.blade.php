@extends('admin.layouts.layouts')

@section('title', 'Search SVC Patient')

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
            font-family: 'Poppins', sans-serif !important;
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
            font-family: 'Poppins', sans-serif !important;
        }

        .search-field input,
        .search-field select {
            padding: 8px 12px;
            border: 1px solid #bdc3c7;
            border-radius: 4px;
            font-size: 14px;
            font-family: 'Poppins', sans-serif !important;
        }

        .search-btn {
            background: rgb(8, 104, 56);
            color: white;
            border: none;
            padding: 8px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            font-family: 'Poppins', sans-serif !important;
        }

        .add-inquiry-btn {
            background: #28a745;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            margin-left: auto;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            font-family: 'Poppins', sans-serif !important;
            white-space: nowrap;
        }

        .add-inquiry-btn:hover {
            background: #218838;
            color: white !important;
            text-decoration: none;
        }

        .patient-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 14px;
            background: white;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            font-family: 'Poppins', sans-serif !important;
        }

        .patient-table th {
            background: #006637;
            color: white;
            font-weight: bold;
            padding-top: 20px;
            padding-bottom: 20px;
            padding-left: 10px;
            text-align: left;
            border: none;
            font-size: 14px;
            font-family: 'Poppins', sans-serif !important;
        }

        .patient-table td {
            padding: 10px 12px;
            text-align: left;
            border-bottom: 1px solid #e9ecef;
            border-left: none;
            border-right: none;
            border-top: none;
            font-size: 13px;
            font-family: 'Poppins', sans-serif !important;
        }

        .patient-table tr:nth-child(even) {
            background: #f8f9fa;
        }

        .patient-table tr:hover {
            background: #e9f7ef;
        }

        .checkbox {
            width: 16px;
            height: 16px;
        }

        .follow-up-checkbox {
            width: 16px;
            height: 16px;
        }

        .header-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }

        .section-title {
            font-size: 20px;
            font-weight: bold;
            color: #006637;
            font-family: 'Poppins', sans-serif !important;
        }

        .buttons-container {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .empty-state {
            text-align: center;
            padding: 30px;
            color: #6c757d;
            font-family: 'Poppins', sans-serif !important;
        }

        .success-message {
            background: #d4edda;
            color: #155724;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            border: 1px solid #c3e6cb;
            font-family: 'Poppins', sans-serif !important;
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

        .btn-edit-square {
            border: 1px solid #16a34a;
            color: #16a34a !important;
        }

        .btn-edit-square:hover {
            background-color: #16a34a;
            color: white !important;
        }

        .btn-delete-square {
            border: 1px solid #dc3545;
            color: #dc3545 !important;
        }

        .btn-delete-square:hover {
            background-color: #dc3545;
            color: white !important;
        }

        .status-checkbox {
            width: 16px;
            height: 16px;
            accent-color: #28a745;
        }

        .pagination {
            margin-top: 15px;
            display: flex;
            justify-content: flex-end;
            align-items: center;
            padding: 8px 0;
            font-family: 'Poppins', sans-serif !important;
        }

        .pagination-content {
            display: flex;
            gap: 15px;
            align-items: center;
            font-size: 13px;
            color: #6c757d;
            font-family: 'Poppins', sans-serif !important;
        }

        .pagination-buttons {
            display: flex;
            gap: 8px;
            align-items: center;
        }

        .pagination-buttons .btn {
            padding: 5px 10px;
            background: rgb(8, 104, 56);
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 13px;
            text-decoration: none;
            display: inline-block;
            font-family: 'Poppins', sans-serif !important;
        }

        .pagination-buttons .btn:hover {
            background: #067945;
        }

        .pagination-buttons .btn[disabled] {
            background: #6c757d;
            cursor: not-allowed;
            opacity: 0.6;
        }

        .dual-search-container {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
            width: 100%;
        }

        .name-search-container {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .global-search-container {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-left: auto;
        }

        .per-page-container {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .search-input-wrapper {
            position: relative;
        }

        .search-input {
            padding: 6px 30px 6px 10px;
            border: 1px solid #bdc3c7;
            border-radius: 4px;
            font-size: 13px;
            min-width: 250px;
            box-sizing: border-box;
            height: 34px;
            font-family: 'Poppins', sans-serif !important;
        }

        .search-input1 {
            padding: 6px 30px 6px 10px;
            border: 1px solid #bdc3c7;
            border-radius: 4px;
            font-size: 13px;
            min-width: 500px;
            box-sizing: border-box;
            height: 34px;
            font-family: 'Poppins', sans-serif !important;
        }

        .clear-icon {
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
            cursor: pointer;
            color: #6c757d;
            font-size: 16px;
            font-weight: bold;
            display: none;
            background: white;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            text-align: center;
            line-height: 14px;
            z-index: 2;
        }

        .clear-icon:hover {
            color: #dc3545;
            background: #f8f9fa;
        }

        .per-page-select {
            padding: 6px 10px;
            border: 1px solid #bdc3c7;
            border-radius: 4px;
            font-size: 13px;
            height: 34px;
            font-family: 'Poppins', sans-serif !important;
        }

        .search-label {
            font-weight: bold;
            color: green;
            white-space: nowrap;
            min-width: 70px;
            font-size: 13px;
            font-family: 'Poppins', sans-serif !important;
        }

        .profile-icon i {
            color: #28a745;
            font-size: 16px;
        }

        .profile-icon i:hover {
            color: #1e7e34;
        }

        .export-btn {
            background: #007bff;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 80px;
            font-size: 14px;
            font-family: 'Poppins', sans-serif !important;
        }

        .export-btn:hover {
            background: #0056b3;
        }

        /* Status column checkboxes */
        .status-column {
            text-align: center;
        }

        .status-check {
            width: 16px;
            height: 16px;
            accent-color: #28a745;
        }

        /* Remove all table borders except bottom */
        .patient-table {
            border: none;
        }

        .patient-table thead {
            border: none;
        }

        .patient-table tbody tr:last-child td {
            border-bottom: none;
        }

        /* Compact table styling */
        .patient-table th,
        .patient-table td {
            border-right: none;
            border-left: none;
        }

        /* Specific column widths to match image */
        .patient-table th:nth-child(1),
        .patient-table td:nth-child(1) {
            width: 5%;
        }

        .patient-table th:nth-child(2),
        .patient-table td:nth-child(2) {
            width: 10%;
        }

        .patient-table th:nth-child(3),
        .patient-table td:nth-child(3) {
            width: 12%;
        }

        .patient-table th:nth-child(4),
        .patient-table td:nth-child(4) {
            width: 15%;
        }

        .patient-table th:nth-child(5),
        .patient-table td:nth-child(5) {
            width: 5%;
        }

        .patient-table th:nth-child(6),
        .patient-table td:nth-child(6) {
            width: 15%;
        }

        .patient-table th:nth-child(7),
        .patient-table td:nth-child(7) {
            width: 8%;
        }

        .patient-table th:nth-child(8),
        .patient-table td:nth-child(8) {
            width: 10%;
        }

        .patient-table th:nth-child(9),
        .patient-table td:nth-child(9) {
            width: 10%;
        }

        .patient-table th:nth-child(10),
        .patient-table td:nth-child(10) {
            width: 10%;
            text-align: center;
        }

        /* Apply Poppins to all text elements */
        h1, h2, h3, h4, h5, h6,
        p, span, div, a, button,
        input, textarea, select,
        label, th, td, li {
            font-family: 'Poppins', sans-serif !important;
        }

        /* Specific font weights for different elements */
        h1, h2, h3, h4, h5, h6 {
            font-weight: 600 !important;
        }

        .btn {
            font-weight: 500 !important;
        }

        .fw-bold {
            font-weight: 600 !important;
        }

        .fw-semibold {
            font-weight: 500 !important;
        }

        .text-muted {
            font-weight: 400 !important;
        }

        .form-control {
            font-weight: 400 !important;
        }
    </style>

    <div class="header-row">
        <div class="section-title"> SVC Patient Inquiry</div>

        <div class="buttons-container">
            <!-- Export Button -->
            <button type="button" id="exportBtn" class="export-btn">
                Export
            </button>
            <a href="{{ route('add-inquiry-patient') }}" class="add-inquiry-btn">Add Inquiry</a>
        </div>
    </div>

    @if (session('success'))
        <div class="success-message">
            {{ session('success') }}
        </div>
    @endif

    <!-- Dual Search Section -->
    <div class="search-section">
        <form method="GET" action="{{ route('svc-patient') }}" id="searchForm">
            <div class="dual-search-container">

                <!-- Left Side: Name Search -->
                <div class="name-search-container">
                    <div class="search-input-wrapper">
                        <label class="search-label">Name</label>
                        <input type="text" name="name_search" class="search-input" placeholder="Search by name..."
                            value="{{ request('name_search') }}" id="nameSearchInput">
                        <span class="clear-icon" id="clearNameSearch">×</span>
                    </div>
                </div>

                <!-- Middle: Show Per Page -->
                <div class="per-page-container">
                    <select name="per_page" class="per-page-select" id="perPageSelect">
                        <option value="5" {{ request('per_page', 10) == 5 ? 'selected' : '' }}>5 per page</option>
                        <option value="10" {{ request('per_page', 10) == 10 ? 'selected' : '' }}>10 per page</option>
                        <option value="20" {{ request('per_page', 10) == 20 ? 'selected' : '' }}>20 per page</option>
                        <option value="50" {{ request('per_page', 10) == 50 ? 'selected' : '' }}>50 per page</option>
                    </select>
                </div>

                <!-- Right Side: Global Search - Moved to End -->
                <div class="global-search-container">
                    <div class="search-input-wrapper">
                        <label class="search-label">Search</label>

                        <input type="text" name="global_search" class="search-input1" placeholder="Search all fields..."
                            value="{{ request('global_search') }}" id="globalSearchInput">
                        <span class="clear-icon" id="clearGlobalSearch">×</span>
                    </div>
                </div>

            </div>
        </form>
    </div>

    <!-- Patient Table -->
    <table class="patient-table">
        <thead>
            <tr>
                <th>Profile</th>
                <th>Patient Id</th>
                <th>Name</th>
                <th>Address</th>
                <th>Age</th>
                <th>Diagnosis</th>
                <th>Date</th>
                <th>Doctor</th>
                <th>Follow Up Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @if ($patients->count() > 0)
                @foreach ($patients as $patient)
                    <tr>
                        <td class="profile-icon">
                            @php
                                $profileImage = $patient->getMeta('profile_image');
                            @endphp
                            <a href="{{ route('svc.profile', $patient->id) }}" title="View Profile">
                                @if ($profileImage && file_exists(public_path($profileImage)))
                                    <img src="{{ asset($profileImage) }}" alt="Profile"
                                        style="width:32px;height:32px;border-radius:50%;object-fit:cover;">
                                @else
                                    <i class="far fa-address-card"></i>
                                @endif
                            </a>
                        </td>

                        <td class="patient_id">
                            <a href="{{ route('svc.profile', $patient->id) }}"
                                style="color: #28a745; text-decoration: none;" title="View Profile">
                                {{ $patient->patient_id }}
                            </a>
                        </td>
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
                        <td>
                            @if ($patient->inquiry_date)
                                {{ \Carbon\Carbon::parse($patient->inquiry_date)->format('d/m/Y') }}
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            @php
                                $doctorId = $patient->getMeta('doctor_id');
                                $doctor = null;
                                if ($doctorId) {
                                    $doctor = \App\Models\User::find($doctorId);
                                }
                            @endphp
                            {{ $doctor ? $doctor->name : '' }}
                        </td>
                        <td class="status-column">
                            @if ($patient->next_follow_date)
                                {{ \Carbon\Carbon::parse($patient->next_follow_date)->format('d/m/Y') }}
                            @else
                                <input type="checkbox" class="status-check" checked disabled>
                            @endif
                        </td>
                        <td>
                            <div class="action-buttons">
                                <!-- Edit Icon -->
                                <a href="{{ route('edit.svc.inquiry', $patient->id) }}" class="action-btn btn-edit-square" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>

                                <!-- Delete Icon -->
                                <button type="button" class="action-btn btn-delete-square" title="Delete"
                                    onclick="deleteSVCPatient({{ $patient->id }}, '{{ $patient->patient_name }}')">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td colspan="9" class="empty-state">
                        @if (request('name_search') || request('global_search'))
                            No patient records found.
                            @if (request('name_search'))
                                <br>Name search: "{{ request('name_search') }}"
                            @endif
                            @if (request('global_search'))
                                <br>Global search: "{{ request('global_search') }}"
                            @endif
                        @else
                            No patient records found.
                        @endif
                        <br><a href="{{ route('add-inquiry-patient') }}">Add your first inquiry</a>
                    </td>
                </tr>
            @endif
        </tbody>
    </table>

    @if ($patients->hasPages())
        <div class="pagination">
            <div class="pagination-content">
                <span class="pagination-info">Showing {{ $patients->firstItem() }} to {{ $patients->lastItem() }} of
                    {{ $patients->total() }} results</span>

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
        </div>
    @else
        <div class="pagination">
            <div class="pagination-content">
                <span class="pagination-info">Showing {{ $patients->count() }} of {{ $patients->total() }} results</span>
            </div>
        </div>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const nameSearchInput = document.getElementById('nameSearchInput');
            const globalSearchInput = document.getElementById('globalSearchInput');
            const perPageSelect = document.getElementById('perPageSelect');
            const searchForm = document.getElementById('searchForm');
            const clearNameSearch = document.getElementById('clearNameSearch');
            const clearGlobalSearch = document.getElementById('clearGlobalSearch');

            let searchTimeout;

            function toggleClearIcons() {
                // Show/hide clear icon for name search
                if (nameSearchInput.value.trim() !== '') {
                    clearNameSearch.style.display = 'block';
                } else {
                    clearNameSearch.style.display = 'none';
                }

                // Show/hide clear icon for global search
                if (globalSearchInput.value.trim() !== '') {
                    clearGlobalSearch.style.display = 'block';
                } else {
                    clearGlobalSearch.style.display = 'none';
                }
            }

            function submitSearch() {
                clearTimeout(searchTimeout);

                searchTimeout = setTimeout(function() {
                    // Get current URL parameters
                    const urlParams = new URLSearchParams(window.location.search);

                    // Update or remove name_search parameter
                    if (nameSearchInput.value.trim() !== '') {
                        urlParams.set('name_search', nameSearchInput.value.trim());
                    } else {
                        urlParams.delete('name_search');
                    }

                    // Update or remove global_search parameter
                    if (globalSearchInput.value.trim() !== '') {
                        urlParams.set('global_search', globalSearchInput.value.trim());
                    } else {
                        urlParams.delete('global_search');
                    }

                    // Update per_page parameter
                    urlParams.set('per_page', perPageSelect.value);

                    // Remove page parameter when searching to go back to first page
                    urlParams.delete('page');

                    // Build new URL
                    const newUrl = '{{ route('svc-patient') }}' + (urlParams.toString() ? '?' + urlParams.toString() : '');

                    // Navigate to new URL
                    window.location.href = newUrl;
                }, 800); // 800ms delay for better UX
            }

            // Initialize clear icons on page load
            toggleClearIcons();

            // Name search with debounce
            nameSearchInput.addEventListener('input', function() {
                toggleClearIcons();
                submitSearch();
            });

            // Global search with debounce
            globalSearchInput.addEventListener('input', function() {
                toggleClearIcons();
                submitSearch();
            });

            // Clear name search
            clearNameSearch.addEventListener('click', function() {
                nameSearchInput.value = '';
                toggleClearIcons();
                submitSearch();
            });

            // Clear global search
            clearGlobalSearch.addEventListener('click', function() {
                globalSearchInput.value = '';
                toggleClearIcons();
                submitSearch();
            });

            // Per page change - immediate submission
            perPageSelect.addEventListener('change', function() {
                // Get current URL parameters
                const urlParams = new URLSearchParams(window.location.search);

                // Update per_page parameter
                urlParams.set('per_page', perPageSelect.value);

                // Remove page parameter when changing per_page to go back to first page
                urlParams.delete('page');

                // Build new URL
                const newUrl = '{{ route('svc-patient') }}' + (urlParams.toString() ? '?' + urlParams.toString() : '');

                // Navigate to new URL
                window.location.href = newUrl;
            });

            // Handle Enter key in search inputs to prevent form submission
            nameSearchInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    submitSearch();
                }
            });

            globalSearchInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') {
                    e.preventDefault();
                    submitSearch();
                }
            });
        });

        // Export functionality
        document.getElementById('exportBtn').addEventListener('click', function() {
            // Get current search parameters
            const nameSearch = document.getElementById('nameSearchInput').value;
            const globalSearch = document.getElementById('globalSearchInput').value;

            // Create export URL
            let exportUrl = '{{ route('export.svc.patients') }}';

            // Add search parameters if they exist
            const params = new URLSearchParams();
            if (nameSearch) params.append('name_search', nameSearch);
            if (globalSearch) params.append('global_search', globalSearch);

            if (params.toString()) {
                exportUrl += '?' + params.toString();
            }

            // Trigger download
            window.location.href = exportUrl;
        });

        // Delete SVC Patient function
        function deleteSVCPatient(id, name) {
            Swal.fire({
                title: 'Are you sure?',
                text: `Do you want to delete ID: ${id} Name: ${name}? This action cannot be undone.`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Create form and submit
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = '{{ route("delete-inquiry", ":id") }}'.replace(':id', id);
                    
                    // Add CSRF token
                    const csrfToken = document.createElement('input');
                    csrfToken.type = 'hidden';
                    csrfToken.name = '_token';
                    csrfToken.value = '{{ csrf_token() }}';
                    form.appendChild(csrfToken);
                    
                    // Add DELETE method
                    const methodInput = document.createElement('input');
                    methodInput.type = 'hidden';
                    methodInput.name = '_method';
                    methodInput.value = 'DELETE';
                    form.appendChild(methodInput);
                    
                    // Submit form
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>

@endsection
