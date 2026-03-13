@extends('admin.layouts.layouts')
@section('title' , 'Admin Dashboard')
@section('content')

<div class="container-fluids display_data">
    <div class="row mb-5">
        <div class="col-12 mb-3">
            <div class="card border-bottom-0">
                <div class="card-header">
                    <div class="heading-action">
                        <h3 class="bold font-up fnf-title">Patient Summary</h3>
                        <div>
                            @auth
                                @if(auth()->user()->hasRole('Superadmin'))
                                    <a href="{{ route('followup.calendar') }}" class="fnf-btn btn btn-primary calander_btn">Follow Up Calendar</a>
                                @endif
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @if($branches->isNotEmpty())
        @foreach ($branches as $branch)
        @if($branch) 

            <div class="col-lg-4 col-md-6 mb-3">
                <div class="card">
                    <div class="card-header">
                        <div class="heading-action">
                            <h3 class="bold font-up fnf-title">{{ $branch->branch_name }}</h3>
                        </div>
                    </div>

                    <div class="card-body">
                        <form class="filterForm" data-branch="{{ $branch->branch_id }}">
                            @csrf

                            <div class="filter_fields border-bottom mb-3 pb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <span class="fw-semibold">Filter</span>
                                    <button type="button" class="btn btn-sm btn-outline-secondary toggle-filter">
                                        <i class="bi bi-chevron-down"></i>
                                    </button>
                                </div>

                                <div class="show_filter_field pt-3 mt-3 border-top" style="display:none;">
                                    <input type="hidden" name="branch_id" value="{{ $branch->branch_id }}">
                                    <input type="hidden" name="branch_name" value="{{ $branch->branch_name }}">

                                    <div class="pro_filed filter_date_field">
                                        <div class="form">
                                            <label class="form-label small text-muted">From Date</label>
                                            <input type="date" name="from_date" class="form-control mb-3">
                                        </div>

                                        <div class="form">
                                            <label class="form-label small text-muted">To Date</label>
                                            <input type="date" name="to_date" class="form-control mb-3">
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-end">
                                        <button type="button" class="fnf-btn btn btn-primary submitFilter">
                                            Submit
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>

                        <div class="display_filter_data">
                            <p class="d-flex justify-content-between p-1 mb-1 date_label">
                                Total Patients Data
                            </p>
                            <div class="patient_breakdown_counts">
                                <p class="d-flex justify-content-between p-1 mb-1">
                                    <span>New Patients:</span>
                                    <span class="new_patient_count fw-bold">0</span>
                                </p>
                                <p class="d-flex justify-content-between p-1 mb-1">
                                    <span>Followup Patients:</span>
                                    <span class="followup_patient_count fw-bold">0</span>
                                </p>
                            </div>
                            <p class="d-flex justify-content-between total p-1 mb-1">
                                <span>Total Patients:</span>
                                <span class="patient_count fw-bold" id="patientCount{{ $branch->branch_id }}">0</span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
            @endif
        @endforeach
        @else
        <div class="col-12">
            <div class="alert alert-warning">
                No branch assigned to your account. Please contact administrator.
            </div>
        </div>
        @endif
    </div>
</div>

<style>
.display_data {
    padding: 20px;
}

.card {
    background-color: var(--bg-card);
    border: 1px solid var(--border-subtle);
    border-radius: 8px;
    box-shadow: var(--shadow-md);
}

.card-header {
    background-color: var(--bg-main);
    border-bottom: 1px solid var(--border-subtle);
    padding: 15px 20px;
}

.heading-action {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.fnf-title {
    color: var(--text-primary);
    font-weight: 600;
    margin: 0;
}

.calander_btn {
    background-color: #4bab35 !important;
    border: none;
    padding: 8px 16px;
    border-radius: 4px;
    color: white;
    text-decoration: none;
    font-weight: 500;
}

.calander_btn:hover {
    background-color: #4bab35 !important;
    color: white;
}

.card-body {
    padding: 20px;
}

.filter_fields {
    margin-bottom: 15px;
}

.toggle-filter {
    background: none !important;
    border: none !important;
}
.toggle-filter i {
    color: var(--text-primary) !important;
}
.toggle-filter:hover{
    background: none !important;
    border: none !important;
}
.pro_filed.filter_date_field {
    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

.pro_filed.filter_date_field .form {
    flex: 1;
    min-width: 120px;
}

.date-input-group {
    display: flex;
    flex-direction: column;
}

.form-control {
    background-color: var(--bg-main);
    color: var(--text-primary);
    border: 1px solid var(--border-subtle);
    border-radius: 4px;
    padding: 8px 12px;
    font-size: 14px;
}

.form-label {
    font-size: 12px;
    margin-bottom: 4px;
}

.fnf-btn {
    background-color: #4bab35 !important;
    border: none;
    padding: 8px 20px;
    border-radius: 4px;
    color: white;
    font-weight: 500;
    cursor: pointer;
}

.fnf-btn:hover {
    background-color: #4bab35 !important;
}

.display_filter_data {
    background-color: var(--bg-main);
    color: var(--text-primary);
    border-radius: 6px;
    padding: 15px;
    margin-top: 15px;
    border: 1px solid var(--border-subtle);
}

.date_label {
    color: #6c757d;
    font-weight: 500;
    font-size: 14px;
}

.total {
    color: var(--text-primary);
    font-size: 15px;
}

.patient_count {
    color: #007bff;
    font-size: 16px;
}

.border-bottom-0 {
    border-bottom: none !important;
}
.toggle-filter:focus,
.toggle-filter:active {
    outline: none !important;
    box-shadow: none !important;
    border: none !important;
}
input{
    outline: none;
}
input:focus{
    outline: none !important;
    box-shadow: none !important;
     border: 1px solid #ced4da !important;
}
.loading {
    opacity: 0.6;
    pointer-events: none;
}

.submit-btn:disabled {
    background-color: #6c757d !important;
    cursor: not-allowed;
}

/* Responsive Design */
@media (max-width: 768px) {
    .heading-action {
        flex-direction: column;
        gap: 10px;
        align-items: flex-start;
    }

    .pro_filed.filter_date_field {
        flex-direction: column;
    }

    .pro_filed.filter_date_field .form {
        width: 100%;
    }
}
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Toggle filter visibility
    $(document).on('click', '.toggle-filter', function() {
        const filterField = $(this).closest('.filter_fields').find('.show_filter_field');
        const icon = $(this).find('i');

        if (filterField.is(':visible')) {
            filterField.hide();
            icon.removeClass('bi-chevron-up').addClass('bi-chevron-down');
        } else {
            filterField.show();
            icon.removeClass('bi-chevron-down').addClass('bi-chevron-up');
        }
    });

    // Function to load total patient count for all branches
    function loadTotalPatientCounts() {
        $(".card").each(function() {
            const card = $(this);
            const branchId = card.find("form").data("branch");
            const branchName = card.find(".fnf-title").text().trim();
            const patientCountElement = card.find(".patient_count");
            const newPatientCountElement = card.find(".new_patient_count");
            const followupPatientCountElement = card.find(".followup_patient_count");
            const breakdownBlock = card.find(".patient_breakdown_counts");

            const branchPrefix = (branchId || '').toString().split('-')[0].toUpperCase();
            const branchLabel = (branchName || '').toString().toUpperCase();
            const isSvcLhrHydra = branchPrefix === 'SVC' || branchPrefix === 'LHR' || branchPrefix === 'HYDRA' ||
                branchLabel.includes('SVC') || branchLabel.includes('LHR') || branchLabel.includes('HYDRA');
            if (isSvcLhrHydra) {
                breakdownBlock.show();
            } else {
                breakdownBlock.hide();
            }

            // Show loading
            patientCountElement.text('Loading...');
            newPatientCountElement.text('...');
            followupPatientCountElement.text('...');

            // AJAX request to get total patient count
            $.ajax({
                url: '{{ route("get.total.patients") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    branch_id: branchId,
                    branch_name: branchName
                },
                success: function(response) {
                    console.log('Response for branch ' + branchName + ':', response);
                    if (response.success) {
                        patientCountElement.text(response.patient_count);
                        if (isSvcLhrHydra) {
                            newPatientCountElement.text(response.new_patient_count ?? 0);
                            followupPatientCountElement.text(response.followup_patient_count ?? 0);
                        }
                    } else {
                        patientCountElement.text('0');
                        newPatientCountElement.text('0');
                        followupPatientCountElement.text('0');
                        console.error('Error for ' + branchName + ':', response.error);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('AJAX Error for ' + branchName + ':', error);
                    patientCountElement.text('0');
                    newPatientCountElement.text('0');
                    followupPatientCountElement.text('0');
                }
            });
        });
    }

    // Apply filter for specific branch
    $(document).on("click", ".submitFilter", function() {
        const card = $(this).closest(".card");
        const form = card.find(".filterForm");
        const patientCountElement = card.find(".patient_count");
        const newPatientCountElement = card.find(".new_patient_count");
        const followupPatientCountElement = card.find(".followup_patient_count");
        const branchPrefix = (form.data("branch") || '').toString().split('-')[0].toUpperCase();
        const branchLabel = (card.find(".fnf-title").text() || '').toString().toUpperCase();
        const isSvcLhrHydra = branchPrefix === 'SVC' || branchPrefix === 'LHR' || branchPrefix === 'HYDRA' ||
            branchLabel.includes('SVC') || branchLabel.includes('LHR') || branchLabel.includes('HYDRA');
        const dateLabel = card.find(".date_label");
        const submitBtn = $(this);
        
        const fromDate = form.find("input[name='from_date']").val();
        const toDate = form.find("input[name='to_date']").val();

        // If no dates are selected, show total count
        if (!fromDate || !toDate) {
            // Show loading
            patientCountElement.text('Loading...');
            newPatientCountElement.text('...');
            followupPatientCountElement.text('...');
            submitBtn.prop('disabled', true).text('Loading...');
            
            const branchId = form.data("branch");
            const branchName = card.find(".fnf-title").text().trim();
            
            $.ajax({
                url: '{{ route("get.total.patients") }}',
                type: 'POST',
                data: {
                    _token: '{{ csrf_token() }}',
                    branch_id: branchId,
                    branch_name: branchName
                },
                success: function(response) {
                    if (response.success) {
                        patientCountElement.text(response.patient_count);
                        if (isSvcLhrHydra) {
                            newPatientCountElement.text(response.new_patient_count ?? 0);
                            followupPatientCountElement.text(response.followup_patient_count ?? 0);
                        }
                        dateLabel.text('Total Patients Data');
                        
                        if ((fromDate && !toDate) || (!fromDate && toDate)) {
                            alert('Please select both From Date and To Date for filtering, or leave both empty for total count.');
                        }
                    } else {
                        patientCountElement.text('0');
                        newPatientCountElement.text('0');
                        followupPatientCountElement.text('0');
                        dateLabel.text('Total Patients Data');
                    }
                },
                error: function() {
                    patientCountElement.text('0');
                    newPatientCountElement.text('0');
                    followupPatientCountElement.text('0');
                    dateLabel.text('Total Patients Data');
                },
                complete: function() {
                    submitBtn.prop('disabled', false).text('Submit');
                }
            });
            return;
        }

        // Check if from date is greater than to date
        if (fromDate > toDate) {
            alert('From date cannot be greater than To date');
            return;
        }

        // Show loading state
        patientCountElement.text('Loading...');
        newPatientCountElement.text('...');
        followupPatientCountElement.text('...');
        submitBtn.prop('disabled', true).text('Loading...');

        // AJAX request to get filtered patient count
        $.ajax({
            url: '{{ route("get.filtered.patients") }}',
            type: 'POST',
            data: form.serialize(),
            success: function(response) {
                console.log('Filter response:', response);
                if (response.success) {
                    patientCountElement.text(response.patient_count);
                    if (isSvcLhrHydra) {
                        newPatientCountElement.text(response.new_patient_count ?? 0);
                        followupPatientCountElement.text(response.followup_patient_count ?? 0);
                    }
                    
                    // Update date range label
                    if (response.from_date && response.to_date) {
                        const fromFormatted = formatDate(response.from_date);
                        const toFormatted = formatDate(response.to_date);
                        dateLabel.text(`Data from ${fromFormatted} to ${toFormatted}`);
                    } else {
                        dateLabel.text('Total Patients Data');
                    }
                } else {
                    patientCountElement.text('0');
                    newPatientCountElement.text('0');
                    followupPatientCountElement.text('0');
                    dateLabel.text('Error loading data');
                    console.error('Filter API error:', response.error);
                }
            },
            error: function(xhr, status, error) {
                console.error('Filter AJAX Error:', error);
                patientCountElement.text('0');
                newPatientCountElement.text('0');
                followupPatientCountElement.text('0');
                dateLabel.text('Error loading data');
            },
            complete: function() {
                submitBtn.prop('disabled', false).text('Submit');
            }
        });
    });

    // Format date to readable format
    function formatDate(dateString) {
        if (!dateString) return '';
        const date = new Date(dateString);
        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const year = date.getFullYear();
        return `${day}-${month}-${year}`;
    }

    // Load total patient counts on page load
    loadTotalPatientCounts();
});
</script>
@endsection