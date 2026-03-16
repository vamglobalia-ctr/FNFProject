@extends('admin.layouts.layouts')
@section('title', 'Joined Patients')
@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-3">
                <h2 class="mb-0" style="color: var(--accent-solid);">
                    <i class="fas fa-users"></i> Joined Patients
                </h2>
                <a href="{{ route('add.inquiry') }}?default_status=Joined" class="btn btn-primary w-auto mt-2 mt-sm-0">
                    <i class="fas fa-plus"></i> Add Patient
                </a>
            </div>
        </div>
    </div>
 
    <div class="row mb-4">
        <div class="col-md-8">
            <form method="GET" action="{{ route('joined.inquiry') }}">
                <div class="input-group">
                    <input type="text" class="form-control" name="search" placeholder="Search joined patients..." value="{{ request('search') }}">
                    <button class="btn btn-outline-secondary" type="submit">
                        <i class="fas fa-search"></i> Search
                    </button>
                    @if(request('search'))
                        <a href="{{ route('joined.inquiry') }}" class="btn btn-outline-danger">
                            <i class="fas fa-times"></i> Clear
                        </a>
                    @endif
                </div>
            </form>
        </div>
        <div class="col-md-4 text-end">
            <form method="GET" action="{{ route('export.joined.inquiries') }}" class="d-inline" id="exportForm">
                <input type="hidden" name="search" value="{{ request('search') }}">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-download"></i> Export All
                </button>
            </form>
        </div>
    </div>
 
    <div class="card">
        <div class="card-body">

 
            <div class="table-responsive">
                <table class="table table-hover table-striped mb-0">
                    <thead style="background-color: #086838; color: white;">
                        <tr>
                            <th>#</th>
                            <th>Patient ID</th>
                            <th>Date</th>
                            <th>Patient Name</th>
                            <th>Phone no.</th>
                            <th>Address</th>
                            <th>Program</th>
                            <th>Diagnosis</th>
                            <th>Diet HVO</th>
                            {{-- <th>Status</th> --}}
                            <th class="text-center">Edit</th>
                            <th class="text-center">Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($inquiries as $index => $inquiry)
                        <tr>
                            <td class="py-2.5 px-3">
                                <a href="{{ route('patient.profile', $inquiry->id) }}" class="text-decoration-none">
                                    <div class="profile-circle" title="View Patient Profile">
                                        @php
                                            $opt = \App\Models\Opt::where('patient_id', $inquiry->patient_id)
                                                ->where(function ($q) {
                                                    $q->whereNull('delete_status')
                                                      ->orWhere('delete_status', '')
                                                      ->orWhere('delete_status', '0');
                                                })
                                                ->orderByDesc('created_at')
                                                ->first();
                                            $profileImage = $opt ? $opt->getMetaValue('profile_image') : null;

                                            // Fallback: some old records store profile_image on an older Opt
                                            if (empty($profileImage)) {
                                                $optIds = \App\Models\Opt::where('patient_id', $inquiry->patient_id)
                                                    ->where(function ($q) {
                                                        $q->whereNull('delete_status')
                                                          ->orWhere('delete_status', '')
                                                          ->orWhere('delete_status', '0');
                                                    })
                                                    ->pluck('id');

                                                if ($optIds->isNotEmpty()) {
                                                    $profileImage = \App\Models\OptMeta::whereIn('opt_id', $optIds)
                                                        ->where('meta_key', 'profile_image')
                                                        ->orderByDesc('id')
                                                        ->value('meta_value');
                                                }
                                            }
                                        @endphp
                                        @php
                                            // Initial is used as a fallback if image missing/broken
                                            $fullName = '';
                                            if(!empty($inquiry->patient_name)) {
                                                $fullName = $inquiry->patient_name;
                                            } else {
                                                $nameParts = array_filter([
                                                    $inquiry->patient_f_name ?? '',
                                                    $inquiry->patient_m_name ?? '',
                                                    $inquiry->patient_l_name ?? ''
                                                ]);
                                                $fullName = implode(' ', $nameParts);
                                            }
                                            $initial = !empty($fullName) ? strtoupper(substr($fullName, 0, 1)) : 'N';
                                        @endphp

                                        <span class="profile-initial">{{ $initial }}</span>
                                        @if($profileImage)
                                            <img src="{{ asset($profileImage) }}" alt="Profile Image"
                                                 onload="this.closest('.profile-circle').querySelector('.profile-initial').style.display='none';"
                                                 onerror="this.remove();">
                                        @endif
                                    </div>
                                </a>
                            </td>
                            {{-- <td>{{ $inquiries->firstItem() + $index }}</td> --}}
                            <td class="py-2.5 px-3">{{ $inquiry->patient_id ?? 'N/A' }}</td>
                            {{-- <td>{{ $inquiry->date ? $inquiry->date->format('d/m/Y') : 'N/A' }}</td> --}}
                            <td class="py-2.5 px-3">
                                @if(!empty($inquiry->inquiry_date))
                                {{ \Carbon\Carbon::parse($inquiry->inquiry_date)->format('d/m/Y') }}
                                @elseif(!empty($inquiry->created_at))
                                {{ \Carbon\Carbon::parse($inquiry->created_at)->format('d/m/Y') }}
                                @else
                                N/A
                                @endif
                            </td>
                            <td class="py-2.5 px-3">{{ $inquiry->patient_name ?? 'N/A' }}</td>
                            <td class="py-2.5 px-3">{{ $inquiry->phone_no ?? 'N/A' }}</td>
                            <td class="py-2.5 px-3">{{ Str::limit($inquiry->address ?? 'N/A', 30) }}</td>
                            <td class="py-2.5 px-3">
                                @php
                                    $programs = [];
                                    if (isset($opt)) {
                                        $pArray = $opt->getMetaValue('programs_array');
                                        if ($pArray) {
                                            $decodedProgs = json_decode($pArray, true) ?: [];
                                            $programs = collect($decodedProgs)->pluck('program')->filter()->toArray();
                                        } else {
                                            $single = $opt->getMetaValue('selected_program');
                                            if($single) $programs = [$single];
                                        }
                                    }
                                    
                                    $colorClasses = ['badge-pg-1', 'badge-pg-2', 'badge-pg-3', 'badge-pg-4', 'badge-pg-5', 'badge-pg-6', 'badge-pg-7', 'badge-pg-8'];
                                @endphp
                                
                                @forelse($programs as $prog)
                                    <span class="status-badge {{ $colorClasses[array_rand($colorClasses)] }}" title="{{ $prog }}">
                                        {{ Str::limit($prog, 20) }}
                                    </span>
                                @empty
                                    <span class="text-muted">-</span>
                                @endforelse
                            </td>
                            <td class="py-2.5 px-3">
                                @if($inquiry->diagnosis)
                                <span class="status-badge badge-diagnosis" title="{{ $inquiry->diagnosis }}">
                                    {{ Str::limit($inquiry->diagnosis, 20) }}
                                </span>
                                @else
                                <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td class="py-2.5 px-3">
                                <a href="{{ route('diet.join.patient', $inquiry->id) }}"
                                    style="color: #28a745; text-decoration: none;" title="View/Edit Diet Chart">
                                    Diet H/O
                                </a>
                            </td>
                             <td class="text-center py-2.5 px-3">
                                <button onclick="editInquiry({{ $inquiry->id }})" class="action-btn btn-edit-square" title="Edit">
                                    <i class="fa-regular fa-pen-to-square"></i>
                                </button>
                            </td>
                            <td class="text-center py-2.5 px-3">
                                <form action="{{ route('delete.inquiry', $inquiry->id) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="button" class="action-btn btn-delete-square"
                                            onclick="confirmDelete(this.closest('form'), '{{ addslashes($inquiry->patient_name ?? ($inquiry->patient_f_name ?? "this patient")) }}')">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center">
                                <div class="py-5">
                                    <i class="fas fa-users fa-3x text-muted mb-3"></i>0
                                    <h5 class="text-muted">No joined patients found</h5>
                                    <p class="text-muted mb-0">No patients have joined yet or add a new patient</p>
                                    <a href="{{ route('add.inquiry') }}?default_status=Joined" class="btn btn-primary mt-3">
                                        <i class="fas fa-plus"></i> Add New Patient
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
 
            @if($inquiries->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    @if($inquiries->total() > 0)
                        Showing {{ $inquiries->firstItem() }} to {{ $inquiries->lastItem() }} of {{ $inquiries->total() }} joined patients
                    @else
                        Showing 0 to 0 of 0 entries
                    @endif
                </div>
 
                <div>
                    {{ $inquiries->links() }}
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
 
<script>
    function editInquiry(id) {
        console.log("Editing ID:", id);
        window.location.href = "{{ route('add.inquiry') }}" + "?id=" + id;
    }
 
    function exportData() {
        if({{ $inquiries->total() ?? 0 }} === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'No Data',
                text: 'No data to export. Please add joined patients first.'
            });
            return;
        }
        window.location.href = "{{ route('joined.inquiry') }}?export=excel&search={{ request('search') }}";
    }

    function confirmDelete(form, patientName) {
        Swal.fire({
            title: 'Are you sure?',
            text: "Do you want to delete inquiry for " + patientName + "?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    }
 
    // Auto-dismiss alerts after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const closeButton = alert.querySelector('.btn-close');
                if (closeButton) {
                    closeButton.click();
                }
            });
        }, 5000);
    });
</script>
 
<style>
    .card {
        background-color: var(--bg-card);
        border: 1px solid var(--border-subtle);
        box-shadow: var(--shadow-md);
        color: var(--text-primary);
        border-radius: 15px;
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
        color: #ffffff !important;
        border: none;
    }

    .table td {
        vertical-align: middle;
        color: var(--text-primary);
    }

    .table tbody tr:hover {
        background-color: var(--bg-hover) !important;
    }

    .pagination {
        margin-bottom: 0;
    }

    .badge-active {
        background-color: rgba(124, 58, 237, 0.1);
        color: #a78bfa;
        border-color: rgba(124, 58, 237, 0.2);
    }

    .status-badge {
        padding: 3px 10px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
        display: inline-block;
        border: 1px solid transparent;
    }

    .badge-diagnosis {
        background-color: #e8f5e9;
        color: #086838;
        border: 1px solid #c8e6c9;
    }

    .dark .badge-diagnosis { background-color: rgba(52, 211, 153, 0.15) !important; color: #34d399 !important; }

    /* Program Badge Styles */
    .status-badge {
        display: block;
        width: fit-content;
        padding: 6px 14px;
        border-radius: 8px;
        font-size: 0.875rem;
        font-weight: 700;
        border: 1px solid;
        white-space: nowrap;
        margin-bottom: 6px;
        line-height: 1.2;
    }

    /* Program Badge Colors */
    .badge-pg-1 { background-color: #e3f2fd; color: #1e88e5; border-color: #bbdefb; }
    .badge-pg-2 { background-color: #f3e5f5; color: #8e24aa; border-color: #e1bee7; }
    .badge-pg-3 { background-color: #fff3e0; color: #fb8c00; border-color: #ffe0b2; }
    .badge-pg-4 { background-color: #fce4ec; color: #d81b60; border-color: #f8bbd0; }
    .badge-pg-5 { background-color: #e0f2f1; color: #00897b; border-color: #b2dfdb; }
    .badge-pg-6 { background-color: #e8f5e9; color: #43a047; border-color: #c8e6c9; }
    .badge-pg-7 { background-color: #e8eaf6; color: #3949ab; border-color: #c5cae9; }
    .badge-pg-8 { background-color: #fbe9e7; color: #f4511e; border-color: #ffccbc; }

    /* Dark Mode Specific Badge Overrides */
    .dark .badge-pending { background-color: rgba(245, 158, 11, 0.15) !important; color: #fbbf24 !important; }
    .dark .badge-diet { background-color: rgba(34, 211, 238, 0.15) !important; color: #67e8f9 !important; }
    .dark .badge-joined { background-color: rgba(74, 222, 128, 0.15) !important; color: #86efac !important; }
    .dark .badge-active { background-color: rgba(167, 139, 250, 0.15) !important; color: #c4b5fd !important; }
    .dark .badge-diagnosis { 
        background-color: rgba(52, 211, 153, 0.1) !important; 
        color: #6ee7b7 !important; 
        border-color: rgba(52, 211, 153, 0.2) !important; 
    }

    .alert-success {
        background-color: rgba(22, 163, 74, 0.1);
        border-color: rgba(22, 163, 74, 0.2);
        color: #4ade80;
    }

    .alert-danger {
        background-color: rgba(220, 53, 69, 0.1);
        border-color: rgba(220, 53, 69, 0.2);
        color: #f87171;
    }

    .profile-circle {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: var(--bg-hover);
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        color: var(--text-primary);
        cursor: pointer;
        border: 1px solid var(--border-subtle);
        margin: 0 auto;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .profile-circle:hover {
        background-color: var(--accent-solid);
        color: white;
        transform: scale(1.1);
    }

    .profile-circle .profile-initial {
        position: absolute;
        inset: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 1;
    }

    .profile-circle img {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 50%;
        z-index: 2;
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

    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }

    .badge {
        font-size: 0.85em;
        padding: 0.4em 0.8em;
    }

    .badge.bg-success {
        background-color: #28a745 !important;
    }

    .dark .table-striped tbody tr:nth-of-type(odd) {
        background-color: rgba(255, 255, 255, 0.02);
    }
</style>
@endsection
