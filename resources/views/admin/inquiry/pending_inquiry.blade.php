@extends('admin.layouts.layouts')
@section('title', 'Pending Inquiries')
@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-start align-items-sm-center mb-3">
                <h2 class="mb-0" style="color: var(--accent-solid);">
                    <i class="fas fa-clock"></i> Pending Inquiries
                </h2>
                <a href="{{ route('add.inquiry') }}" class="btn btn-primary w-auto mt-2 mt-sm-0">
                    <i class="fas fa-plus"></i> Add Inquiry
                </a>
            </div>
        </div>
    </div>
 
    <div class="row mb-4">
        <div class="col-md-8">
            <form method="GET" action="{{ route('pending.inquiry') }}">
                <div class="input-group">
                    <input type="text" class="form-control" name="search" placeholder="Search pending inquiries..." value="{{ request('search') }}">
                    <button class="btn btn-outline-secondary" type="submit">
                        <i class="fas fa-search"></i> Search
                    </button>
                    @if(request('search'))
                        <a href="{{ route('pending.inquiry') }}" class="btn btn-outline-danger">
                            <i class="fas fa-times"></i> Clear
                        </a>
                    @endif
                </div>
            </form>
        </div>
        <div class="col-md-4 text-end">
            <form method="GET" action="{{ route('export.pending.inquiries') }}" class="d-inline" id="exportForm">
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
                            <th>Patient Id</th>
                            <th>Date</th>
                            <th>Patient Name</th>
                            <th>Phone no.</th>
                            <th>Address</th>
                            <th>Disposals</th>
                            <th>Status</th>
                            <th>Edit</th>
                            <th>Delete</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($inquiries as $index => $inquiry)
                        <tr>
                            <td class="py-2.5 px-3">{{ $inquiries->firstItem() + $index }}</td>
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
                                @if($inquiry->diagnosis)
                                <span class="status-badge badge-diagnosis" title="{{ $inquiry->diagnosis }}">
                                    {{ Str::limit($inquiry->diagnosis, 20) }}
                                </span>
                                @else
                                <span class="text-muted">N/A</span>
                                @endif
                            </td>
                            <td class="py-2.5 px-3">
                                @if($inquiry->display_statuses)
                                    @foreach($inquiry->display_statuses as $status)
                                        @if($status == 'Pending')
                                            <span class="status-badge badge-pending me-1 mb-1">{{ $status }}</span>
                                        @elseif($status == 'Diet Chart')
                                            <span class="status-badge badge-diet me-1 mb-1">{{ $status }}</span>
                                        @elseif($status == 'Joined')
                                            <span class="status-badge badge-joined me-1 mb-1">{{ $status }}</span>
                                        @elseif($status == 'Active')
                                            <span class="status-badge badge-active me-1 mb-1">{{ $status }}</span>
                                        @endif
                                    @endforeach
                                @else
                                    <span class="text-muted">No status</span>
                                @endif
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
                                            onclick="confirmDelete(this.closest('form'), '{{ addslashes($inquiry->patient_name ?? 'this patient') }}')">
                                        <i class="fa-regular fa-trash-can"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="10" class="text-center">
                                <div class="py-5">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">No pending inquiries found</h5>
                                    <p class="text-muted mb-0">All inquiries have been processed or add a new inquiry</p>
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
 
            @if($inquiries->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div>
                    @if($inquiries->total() > 0)
                        Showing {{ $inquiries->firstItem() }} to {{ $inquiries->lastItem() }} of {{ $inquiries->total() }} pending inquiries
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
        if({{ $inquiries->total() }} === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'No Data',
                text: 'No data to export. Please add inquiries first.'
            });
            return;
        }
        window.location.href = "{{ route('pending.inquiry') }}?export=excel&search={{ request('search') }}";
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
        color: white !important;
    }

    .table td {
        color: var(--text-primary);
        vertical-align: middle;
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

    .table th {
        font-weight: 600;
        white-space: nowrap;
        background-color: #086838 !important;
        color: white !important;
        vertical-align: middle;
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

    /* Theme colors override */
    .btn-success {
        background-color: #086838;
        border-color: #086838;
    }

    .btn-success:hover {
        background-color: #06502b;
        border-color: #06502b;
    }
 
    .alert-success {
        background-color: rgba(22, 163, 74, 0.1);
        border-color: rgba(22, 163, 74, 0.2);
        color: #4ade80;
    }
 
    .table td {
        vertical-align: middle;
    }
 
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }
 
    .badge {
        font-size: 0.85em;
        padding: 0.4em 0.8em;
    }
</style>
@endsection