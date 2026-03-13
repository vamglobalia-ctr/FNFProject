@extends('admin.layouts.layouts')
@section('title', 'Follow Up Patients')
@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-3">
                <h2 class="mb-0" style="color: var(--accent-solid);">
                    <i class="fas fa-calendar-check"></i> Follow Up Patients
                </h2>
                <a href="{{ route('add.inquiry') }}" class="btn btn-primary w-auto mt-2 mt-sm-0">
                    <i class="fas fa-plus"></i> Add Inquiry
                </a>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-8">
            <form method="GET" action="{{ route('followup.patients.appointment') }}">
                <div class="input-group">
                    <input type="text" class="form-control" name="search_name" placeholder="Search follow up patients..." value="{{ request('search_name') }}">
                    <button class="btn btn-outline-secondary" type="submit">
                        <i class="fas fa-search"></i> Search
                    </button>
                    @if(request('search_name'))
                        <a href="{{ route('followup.patients.appointment') }}" class="btn btn-outline-danger">
                            <i class="fas fa-times"></i> Clear
                        </a>
                    @endif
                </div>
            </form>
        </div>
        <div class="col-md-4 text-end">
            <span class="badge bg-primary px-3 py-2" style="font-size: 14px;">
                {{ $followupPatients->total() }} Patients
            </span>
        </div>
    </div>

    <div class="card">
        <div class="card-body p-0">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show m-3" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead style="background-color: #086838; color: white;">
                        <tr>
                            <th class="text-center">#</th>
                            <th class="text-center">Profile</th>
                            <th>Patient ID</th>
                            <th>Name</th>
                            <th>Follow-up Date</th>
                            <th>Phone</th>
                            <th>Diagnosis</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($followupPatients as $index => $patient)
                        @php
                            $nextFollowupDate = $patient->next_followup_date;
                            $today = \Carbon\Carbon::today();
                            $followupCarbon = $nextFollowupDate ? \Carbon\Carbon::parse($nextFollowupDate) : null;
                            $daysCount = $followupCarbon ? $today->diffInDays($followupCarbon, false) : null;
                            $isToday = $daysCount === 0;
                            $isTomorrow = $daysCount === 1;
                            $isPast = $daysCount !== null && $daysCount < 0;
                        @endphp
                        <tr>
                            <td class="text-center py-2.5 px-3">
                                <strong>{{ ($followupPatients->currentPage() - 1) * $followupPatients->perPage() + $index + 1 }}</strong>
                            </td>
                            <td class="text-center py-2.5 px-3">
                                <div class="d-flex justify-content-center">
                                    <div class="position-relative">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center profile-avatar"
                                            style="width: 38px; height: 38px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); cursor: pointer; box-shadow: 0 2px 8px rgba(102, 126, 234, 0.3);"
                                            onclick="window.location.href='{{ route('patient.profile', $patient->id) }}'">
                                            <span style="color: white; font-weight: bold; font-size: 13px;">
                                                {{ strtoupper(substr($patient->patient_f_name, 0, 1)) }}{{ strtoupper(substr($patient->patient_l_name, 0, 1)) }}
                                            </span>
                                        </div>
                                        @if($isToday)
                                        <div class="position-absolute" style="top: -4px; right: -4px;">
                                            <span class="badge-pulse" style="background-color: #dc3545; color: white; font-size: 8px; padding: 1px 4px; border-radius: 3px; font-weight: 600;">Today</span>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="py-2.5 px-3">
                                <a href="{{ route('patient.profile', $patient->id) }}" style="text-decoration: none; color: var(--accent-solid); font-weight: 600;">
                                    {{ $patient->patient_id ?? 'N/A' }}
                                </a>
                            </td>
                            <td class="py-2.5 px-3">
                                <div style="font-weight: 600; color: var(--text-primary);">
                                    {{ $patient->patient_f_name }} {{ $patient->patient_m_name }} {{ $patient->patient_l_name }}
                                </div>
                                @if($patient->age)
                                <div style="font-size: 11px; color: var(--text-muted);">
                                    {{ $patient->age }} years @if($patient->gender) • {{ $patient->gender }} @endif
                                </div>
                                @endif
                            </td>
                            <td class="py-2.5 px-3">
                                @if($nextFollowupDate)
                                    <div class="d-flex align-items-center">
                                        <div class="me-2 p-1.5 rounded" style="background: {{ $isToday ? 'rgba(220, 53, 69, 0.1)' : ($isTomorrow ? 'rgba(255, 193, 7, 0.15)' : 'rgba(13, 110, 253, 0.08)') }};">
                                            <i class="fas fa-calendar-alt" style="font-size: 14px; color: {{ $isToday ? '#dc3545' : ($isTomorrow ? '#f59e0b' : '#0d6efd') }};"></i>
                                        </div>
                                        <div>
                                            <div style="font-weight: 600; color: var(--text-primary); font-size: 13px;">
                                                {{ \Carbon\Carbon::parse($nextFollowupDate)->format('d M Y') }}
                                            </div>
                                            @if($isToday)
                                                <span class="status-badge badge-pending" style="font-size: 9px; padding: 2px 6px;">Today</span>
                                            @elseif($isTomorrow)
                                                <span class="status-badge badge-diet" style="font-size: 9px; padding: 2px 6px; background-color: rgba(255, 193, 7, 0.1); color: #f59e0b; border-color: rgba(255, 193, 7, 0.2);">In 1 day</span>
                                            @elseif($daysCount > 1)
                                                <span class="status-badge badge-joined" style="font-size: 9px; padding: 2px 6px;">In {{ $daysCount }} days</span>
                                            @elseif($isPast)
                                                <span class="status-badge badge-pending" style="font-size: 9px; padding: 2px 6px; background-color: rgba(108, 117, 125, 0.1); color: #6c757d; border-color: rgba(108, 117, 125, 0.2);">{{ abs($daysCount) }} days ago</span>
                                            @endif
                                        </div>
                                    </div>
                                @else
                                    <span class="text-muted">No date</span>
                                @endif
                            </td>
                            <td class="py-2.5 px-3">
                                @if($patient->phone_no)
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-phone me-2" style="color: #28a745; font-size: 12px;"></i>
                                    <a href="tel:{{ $patient->phone_no }}" style="text-decoration: none; color: var(--text-primary);">
                                        {{ $patient->phone_no }}
                                    </a>
                                </div>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="py-2.5 px-3">
                                @if($patient->diagnosis)
                                <span class="status-badge badge-diagnosis" title="{{ $patient->diagnosis }}">
                                    {{ Str::limit($patient->diagnosis, 20) }}
                                </span>
                                @else
                                <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-center py-2.5 px-3">
                                <div class="d-flex justify-content-center gap-2">
                                    <button onclick="editFollowupDate({{ $patient->id }}, '{{ $nextFollowupDate ?? '' }}')" class="action-btn btn-edit-square" title="Edit Date">
                                        <i class="fa-regular fa-pen-to-square"></i>
                                    </button>
                                    <button onclick="deleteFollowupDate({{ $patient->id }})" class="action-btn btn-delete-square" title="Remove Date">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center">
                                <div class="py-5">
                                    <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No follow-up patients found</h5>
                                    <p class="text-muted mb-0">There are no patients with upcoming follow-up dates.</p>
                                    <a href="{{ route('add.inquiry') }}" class="btn btn-primary mt-3">
                                        <i class="fas fa-plus"></i> Add New Inquiry
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($followupPatients->hasPages())
            <div class="d-flex justify-content-between align-items-center p-3 mt-3 border-top">
                <div style="font-size: 13px; color: var(--text-secondary);">
                    Showing {{ $followupPatients->firstItem() }} to {{ $followupPatients->lastItem() }} of {{ $followupPatients->total() }} entries
                </div>
                <div>
                    {{ $followupPatients->appends(request()->query())->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

<script>
    // Edit follow-up date function
    function editFollowupDate(patientId, currentDate) {
        Swal.fire({
            title: 'Edit Follow-up Date',
            html: `
                <div style="text-align: left;">
                    <label for="followupDate" style="display: block; margin-bottom: 10px; font-weight: 600; color: var(--text-primary);">Select New Follow-up Date:</label>
                    <input type="date" id="followupDate" class="swal2-input" value="${currentDate}" style="width: 100%; margin: 0;">
                </div>
            `,
            icon: 'info',
            showCancelButton: true,
            confirmButtonColor: '#086838',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Update Date',
            cancelButtonText: 'Cancel',
            customClass: {
                popup: 'premium-swal-popup'
            },
            preConfirm: () => {
                const newDate = document.getElementById('followupDate').value;
                if (!newDate) {
                    Swal.showValidationMessage('Please select a date');
                    return false;
                }
                return newDate;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                updateFollowupDate(patientId, result.value);
            }
        });
    }

    // Delete follow-up date function
    function deleteFollowupDate(patientId) {
        Swal.fire({
            title: 'Remove Follow-up Date?',
            text: "This patient will be removed from the follow-up list.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, remove it!',
            cancelButtonText: 'Cancel',
            customClass: {
                popup: 'premium-swal-popup'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                updateFollowupDate(patientId, null);
            }
        });
    }

    // Update follow-up date in database
    function updateFollowupDate(patientId, newDate) {
        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('patient_id', patientId);
        formData.append('next_followup_date', newDate || '');

        fetch('{{ route("update.followup.date") }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: data.message,
                    confirmButtonColor: '#086838',
                    timer: 2000,
                    timerProgressBar: true,
                    customClass: {
                        popup: 'premium-swal-popup'
                    }
                }).then(() => {
                    location.reload();
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error!',
                    text: data.message || 'Failed to update follow-up date',
                    confirmButtonColor: '#dc3545',
                    customClass: {
                        popup: 'premium-swal-popup'
                    }
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                icon: 'error',
                title: 'Error!',
                text: 'An error occurred while updating the follow-up date',
                confirmButtonColor: '#dc3545',
                customClass: {
                    popup: 'premium-swal-popup'
                }
            });
        });
    }
</script>

<style>
    .card {
        background-color: var(--bg-card);
        border: none;
        box-shadow: var(--shadow-md);
    }

    .card-body {
        color: var(--text-primary);
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
 
    .table th {
        font-weight: 600;
        white-space: nowrap;
        background-color: #086838 !important;
        color: white !important;
        vertical-align: middle;
    }

    .table td {
        color: var(--text-primary);
        vertical-align: middle;
        font-size: 13px;
    }

    .dark .table-striped tbody tr:nth-of-type(odd) {
        background-color: rgba(255, 255, 255, 0.02);
    }

    .table tbody tr:hover {
        background-color: var(--bg-hover) !important;
    }

    /* Action Buttons Styling */
    .action-btn {
        width: 32px;
        height: 32px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 4px;
        transition: all 0.3s ease;
        background: transparent;
        border: 1px solid transparent;
        cursor: pointer;
    }

    .btn-edit-square {
        border-color: #16a34a;
        color: #16a34a;
    }

    .btn-edit-square:hover {
        background-color: #16a34a;
        color: white;
    }

    .btn-delete-square {
        border-color: #dc3545;
        color: #dc3545;
    }

    .btn-delete-square:hover {
        background-color: #dc3545;
        color: white;
    }
 
    .pagination {
        margin-bottom: 0;
    }

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

    .badge-diagnosis {
        background-color: rgba(8, 104, 56, 0.1);
        color: #086838;
        border-color: rgba(8, 104, 56, 0.3);
        font-weight: 700;
        text-transform: none;
        letter-spacing: normal;
    }

    /* Dark Mode Specific Badge Overrides */
    .dark .badge-pending { background-color: rgba(245, 158, 11, 0.15) !important; color: #fbbf24 !important; }
    .dark .badge-diet { background-color: rgba(34, 211, 238, 0.15) !important; color: #67e8f9 !important; }
    .dark .badge-joined { background-color: rgba(74, 222, 128, 0.15) !important; color: #86efac !important; }
    .dark .badge-active { background-color: rgba(167, 139, 250, 0.15) !important; color: #c4b5fd !important; }
    .dark .badge-diagnosis { background-color: rgba(52, 211, 153, 0.15) !important; color: #34d399 !important; }

    /* Profile avatar animation */
    .profile-avatar {
        transition: all 0.2s ease;
    }

    .profile-avatar:hover {
        transform: scale(1.08);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4) !important;
    }

    /* Badge pulse animation for Today */
    .badge-pulse {
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0.4); }
        70% { box-shadow: 0 0 0 8px rgba(220, 53, 69, 0); }
        100% { box-shadow: 0 0 0 0 rgba(220, 53, 69, 0); }
    }

    .alert-success {
        background-color: rgba(22, 163, 74, 0.1);
        border-color: rgba(22, 163, 74, 0.2);
        color: #4ade80;
    }
</style>
@endsection