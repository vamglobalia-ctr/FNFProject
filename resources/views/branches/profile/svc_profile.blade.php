@extends('admin.layouts.layouts')

@section('title', 'Patient Profile')

@section('content')
<style>
    .profile-container {
        max-width: 1742px;
        margin: 0 auto;
        padding: 40px;
        background: var(--bg-main);
        min-height: 100vh;
    }

    .profile-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        padding-bottom: 15px;
        border-bottom: 2px solid var(--border-subtle);
    }

    .profile-title {
        font-size: 20px;
        font-weight: bold;
        color: var(--text-primary);
    }

    .profile-content {
        display: flex;
        gap: 30px;
        margin-bottom: 30px;
    }

    .profile-sidebar {
        flex: 1;
        background: var(--bg-card);
        padding: 25px;
        border-radius: 10px;
        box-shadow: var(--shadow-md);
        text-align: center;
        color: var(--text-primary);
    }

    .profile-main {
        flex: 2;
        background: var(--bg-card);
        padding: 25px;
        border-radius: 10px;
        box-shadow: var(--shadow-md);
        color: var(--text-primary);
        max-height: 800px;
        overflow-y: auto;
    }

    .profile-image {
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .profile-image svg {
        width: 200px;
        height: 200px;
        fill: #6c757d;
    }

    .patient-info {
        text-align: left;
        margin-bottom: 25px;
    }

    .info-group {
        margin-bottom: 15px;
    }

    .info-label {
        font-size: 12px;
        color: var(--text-muted);
        font-weight: bold;
        margin-bottom: 5px;
        text-transform: uppercase;
    }

    .info-value {
        font-size: 16px;
        color: var(--text-primary);
        font-weight: 500;
    }

    .edit-profile-btn {
        background: #28a745;
        color: white;
        border: none;
        padding: 10px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 14px;
        margin-top: 10px;
    }
    .edit-profile-btn:hover {
        background: #006637;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.3);
        text-decoration: none;
        color: white;
    }
    .edit-profile-align-btn {
        display: flex;
        justify-content: end;
        align-items: end;
    }
    .section-title {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0px !important;
    }

    .data-table {
        width: 100%;
        margin-bottom: 15px;
        font-size: 14px;
        white-space: nowrap;
    }

    .data-table th {
        padding: 12px 8px;
        text-align: left;
        font-weight: bold;
        color: white;
        background: #006637;
        font-size: 16px;
    }

    .data-table td {
        border-bottom: 1px solid var(--border-subtle);
        padding: 12px 8px;
        text-align: left;
        font-size: 14px;
        color: var(--text-primary);
    }

    .data-table tr:nth-child(even) {
        background: var(--bg-main);
    }

    .pagination {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 15px;
    }

    .pagination-info {
        color: #6c757d;
        font-size: 13px;
    }

    .pagination-buttons {
        display: flex;
        gap: 8px;
    }

    .pagination-btn {
        background: #6c757d;
        color: white;
        border: none;
        padding: 6px 12px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 11px;
        text-decoration: none;
        display: inline-block;
    }

    .pagination-btn:disabled {
        background: #ccc;
        cursor: not-allowed;
    }

    .pagination-btn:hover:not(:disabled) {
        background: #5a6268;
        color: white;
    }

    .patient-type {
        display: flex;
        gap: 10px;
        margin-top: 15px;
    }

    .type-badge {
        background: #e9ecef;
        color: #495057;
        padding: 5px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 500;
    }

    .back-button {
        background: #6c757d;
        color: white;
        border: none;
        padding: 10px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 14px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 5px;
        margin-bottom: 20px;
    }

    .back-button:hover {
        background: #5a6268;
        color: white;
    }

    .empty-data {
        text-align: center;
        color: #6c757d;
        font-style: italic;
        padding: 20px;
        font-size: 14px;
    }

    .section-divider {
        border-top: 1px solid #dee2e6;
        margin: 20px 0;
    }

    .medicine-prescription {
        font-size: 13px;
        line-height: 1.6;
        white-space: normal !important;
    }

    .prescription-item {
        margin-bottom: 12px;
        padding: 12px;
        border: 1px solid var(--border-subtle);
        border-radius: 8px;
        background: var(--bg-main);
    }

    .prescription-item:last-child {
        margin-bottom: 0;
    }

    .medicine-name {
        font-weight: bold;
        color: var(--accent-solid);
        margin-bottom: 8px;
        font-size: 14px;
        padding-bottom: 5px;
        border-bottom: 1px dashed var(--border-subtle);
    }

    .medicine-detail {
        display: flex;
        align-items: center;
        margin-bottom: 4px;
        color: #495057;
    }

    .medicine-label {
        font-weight: 600;
        min-width: 60px;
        color: #6c757d;
    }

    .medicine-value {
        margin-left: 8px;
    }

    .medicine-note {
        color: #ff4d4d;
        font-size: 12px;
        margin-top: 6px;
        padding: 8px;
        background: rgba(220, 53, 69, 0.1);
        border-radius: 4px;
        border-left: 3px solid #dc3545;
    }

    .lab-table-container {
        overflow-x: auto;
        margin-bottom: 15px;
    }

    .follow-up-btn {
        background: #28a745;
        color: white;
        border: none;
        padding: 12px;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
        font-weight: bold;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }

    .follow-up-btn:hover {
        background: #006637;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0,0,0,0.3);
        text-decoration: none;
        color: white;
    }

    .treatment-section {
        margin-bottom: 25px;
    }

    .treatment-icon {
        margin-right: 8px;
        font-size: 16px;
    }

    .no-treatment {
        text-align: center;
        color: var(--text-muted);
        font-style: italic;
        padding: 30px;
        background: var(--bg-main);
        border-radius: 8px;
        border: 2px dashed var(--border-subtle);
    }

    .compact-table {
        font-size: 12px;
    }

    .compact-table th,
    .compact-table td {
        padding: 12px 15px;
    }

    .action-buttons {
        display: flex;
        gap: 8px;
        justify-content: center;
    }

    .action-btn {
        background: none;
        border: none;
        cursor: pointer;
        font-size: 16px;
        padding: 5px;
        border-radius: 4px;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 30px;
        height: 30px;
    }

    .edit-btn {
        color: var(--color-primary);
    }

    .edit-btn:hover {
        color: var(--color-primary);
        background: rgba(37, 99, 235, 0.15);
    }

    .delete-btn {
        color: var(--color-danger);
    }

    .delete-btn:hover {
        color: var(--color-danger);
        background: rgba(239, 68, 68, 0.15);
    }

    .btn-zoom-start,
    .btn-zoom-join,
    .btn-copy,
    .btn-zoom-create {
        color: var(--icon-on-color);
    }
    .btn-zoom-start { background: var(--color-success); }
    .btn-zoom-join { background: var(--color-primary); }
    .btn-copy { background: var(--color-info); }
    .btn-zoom-create { background: var(--text-secondary); color: var(--icon-on-color); }
    .action-btn:hover { filter: brightness(1.05); transform: translateY(-1px); }

    .btn {
        padding: 8px 16px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 14px;
        transition: background 0.3s;
    }

    .btn-secondary {
        background: #6c757d;
        color: white;
    }

    .btn-secondary:hover {
        background: #5a6268;
    }

    .btn-danger {
        background: #dc3545;
        color: white;
    }

    .btn-danger:hover {
        background: #c82333;
    }

    .empty-field {
        color: #6c757d;
        font-style: italic;
    }

    .delete-form {
        display: inline;
        margin: 0;
        padding: 0;
    }

    .profile_txt_color {
        color: var(--accent-solid);
    }

    .fnf-title {
        font-weight: 600;
        color: var(--accent-solid);
        padding-bottom: 0;
        line-height: 1.3em;
        margin-bottom: 0px !important;
        font-size: 20px !important;
    }

    .data-table thead th {
        background-color: #006637 !important;
        color: white;
    }

    .profile-image-wrapper {
        position: relative;
        display: inline-block;
        margin-bottom: 15px;
    }

    .profile-image-container {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        overflow: hidden;
        border: 4px solid var(--border-subtle);
        background: var(--bg-main);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    .profile-image-container img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .profile-image-container i {
        font-size: 60px;
        color: #adb5bd;
    }

    .upload-label {
        position: absolute;
        bottom: 5px;
        right: 5px;
        background: #007bff;
        color: white;
        width: 35px;
        height: 35px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        border: 2px solid white;
        transition: all 0.2s;
    }

    .upload-label:hover {
        background: #0056b3;
        transform: scale(1.1);
    }

    .save-profile-btn {
        background: #007bff;
        color: white;
        border: none;
        padding: 5px 15px;
        border-radius: 4px;
        font-size: 13px;
        margin-top: 10px;
        display: none;
    }

    /* ===== INDOOR TREATMENT MODAL REDESIGN ===== */
    #indoorTreatmentModal .modal-dialog {
        max-width: 860px !important;
    }

    #indoorTreatmentModal .modal-content {
        border-radius: 16px;
        overflow: hidden;
        border: none;
        box-shadow: 0 20px 60px rgba(0,0,0,0.2);
    }

    #indoorTreatmentModal .modal-header {
        background: #fff;
        border-bottom: 1px solid #e9ecef;
        padding: 20px 28px;
    }

    #indoorTreatmentModal .modal-title {
        font-size: 18px;
        font-weight: 700;
        color: #1a1a1a;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    #indoorTreatmentModal .modal-title i {
        color: #006637;
        font-size: 20px;
    }

    #indoorTreatmentModal .modal-body {
        background: #f8f9fa;
        padding: 24px 28px;
        overflow-y: auto !important;
        max-height: 68vh;
    }

    #indoorTreatmentModal .modal-footer {
        background: #fff;
        border-top: 1px solid #e9ecef;
        padding: 16px 28px;
    }

    /* Patient Info Card inside modal */
    .indoor-patient-info {
        background: #f0f7f2;
        border: 1px solid #c8e6d4;
        border-radius: 12px;
        padding: 16px 20px;
        margin-bottom: 20px;
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 8px 24px;
    }

    .indoor-patient-info .info-item {
        font-size: 14px;
        color: #333;
    }

    .indoor-patient-info .info-item strong {
        color: #006637;
        font-weight: 600;
        margin-right: 6px;
    }

    /* Add New Date Slot Button */
    .add-slot-btn {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: transparent;
        border: 1px solid #006637;
        color: #006637;
        font-weight: 600;
        font-size: 13px;
        padding: 8px 18px;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.2s;
        margin-bottom: 18px;
    }

    .add-slot-btn:hover {
        background: #f0f7f2;
    }

    /* Date Slot Card */
    .date-slot-card {
        background: #fff;
        border-radius: 12px;
        overflow: visible;
        margin-bottom: 14px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }

    .date-slot-header {
        background: #006637;
        color: white;
        padding: 11px 18px;
        display: flex;
        align-items: center;
        gap: 10px;
        border-radius: 12px 12px 0 0;
        flex-wrap: wrap;
    }

    .date-slot-header label {
        font-size: 11px;
        font-weight: 700;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        margin: 0;
        white-space: nowrap;
    }

    .date-slot-header input[type="date"],
    .date-slot-header input[type="time"] {
        background: rgba(255,255,255,0.15);
        border: 1px solid rgba(255,255,255,0.4);
        color: white;
        border-radius: 6px;
        padding: 5px 10px;
        font-size: 13px;
        outline: none;
        max-width: 155px;
    }

    .date-slot-header input[type="date"]:focus,
    .date-slot-header input[type="time"]:focus {
        border-color: rgba(255,255,255,0.9);
        background: rgba(255,255,255,0.25);
    }

    .date-slot-header input[type="date"]::-webkit-calendar-picker-indicator,
    .date-slot-header input[type="time"]::-webkit-calendar-picker-indicator {
        filter: invert(1);
        cursor: pointer;
    }

    .date-slot-header input::placeholder {
        color: rgba(255,255,255,0.65);
    }

    .slot-at-separator {
        color: rgba(255,255,255,0.75);
        font-size: 13px;
        font-weight: 500;
    }

    .medicine-count-badge {
        background: rgba(255,255,255,0.22);
        color: white;
        border-radius: 20px;
        padding: 3px 12px;
        font-size: 12px;
        font-weight: 600;
    }

    .remove-slot-btn {
        margin-left: auto;
        background: rgba(255,255,255,0.15);
        border: 1px solid rgba(255,255,255,0.45);
        color: white;
        border-radius: 6px;
        padding: 5px 13px;
        font-size: 12px;
        font-weight: 600;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 5px;
        transition: all 0.2s;
        white-space: nowrap;
    }

    .remove-slot-btn:hover {
        background: rgba(220,53,69,0.65);
        border-color: rgba(220,53,69,0.8);
    }

    /* Medicine rows */
    .date-slot-body {
        padding: 14px 18px 16px;
    }

    .medicines-header {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 10px;
        padding-bottom: 6px;
        margin-bottom: 6px;
        border-bottom: 1px solid #f0f0f0;
    }

    .medicines-header span {
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #6c757d;
    }

    .medicine-row {
        display: grid;
        grid-template-columns: 1fr 1fr 36px;
        gap: 10px;
        margin-bottom: 8px;
        align-items: center;
    }

    .medicine-row input {
        border: 1px solid #dee2e6;
        border-radius: 6px;
        padding: 7px 10px;
        font-size: 13px;
        width: 100%;
        color: #333;
        background: #fff;
        transition: border-color 0.2s;
    }

    .medicine-row input:focus {
        outline: none;
        border-color: #006637;
        box-shadow: 0 0 0 3px rgba(0,102,55,0.1);
    }

    .medicine-row input::placeholder {
        color: #adb5bd;
    }

    .delete-medicine-btn {
        width: 32px;
        height: 32px;
        background: #fff0f0;
        border: 1px solid #ffcccc;
        color: #dc3545;
        border-radius: 6px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
        flex-shrink: 0;
    }

    .delete-medicine-btn:hover {
        background: #dc3545;
        color: white;
        border-color: #dc3545;
    }

    /* Add Medicine Button */
    .add-medicine-btn {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        background: transparent;
        border: 1.5px solid #006637;
        color: #006637;
        font-size: 12px;
        font-weight: 600;
        padding: 5px 14px;
        border-radius: 6px;
        cursor: pointer;
        margin-top: 6px;
        transition: all 0.2s;
    }

    .add-medicine-btn:hover {
        background: #f0f7f2;
    }

    /* Modal footer buttons */
    .btn-cancel-indoor {
        background: #f8f9fa;
        color: #6c757d;
        border: 1px solid #dee2e6;
        padding: 9px 22px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-cancel-indoor:hover {
        background: #e9ecef;
    }

    .btn-save-indoor {
        background: #006637;
        color: white;
        border: none;
        padding: 9px 26px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-save-indoor:hover {
        background: #004d29;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(0,102,55,0.3);
    }

    /* ===== MOBILE RESPONSIVENESS ===== */
    @media (max-width: 991px) {
        .profile-container {
            padding: 15px !important;
        }

        .profile-header {
            flex-direction: column !important;
            align-items: center !important;
            text-align: center;
            gap: 20px;
            padding-bottom: 20px;
        }

        .header-left {
            width: 100%;
            text-align: center;
        }

        .header-right {
            width: 100%;
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: wrap !important;
            justify-content: center !important;
            gap: 10px !important;
        }

        .header-right .follow-up-btn {
            margin-right: 0 !important;
            flex: 1 1 auto;
            min-width: 140px;
            justify-content: center;
            font-size: 14px;
            padding: 10px;
        }

        .edit-profile-align-btn {
            justify-content: center !important;
        }

        .card-body {
            padding: 15px !important;
        }

        .profile-content {
            flex-direction: column !important;
            gap: 0 !important;
            padding: 0 !important;
        }

        .profile-sidebar, .profile-main {
            width: 100% !important;
            max-height: none !important;
            padding: 0 !important;
            box-shadow: none !important;
            background: transparent !important;
        }

        .row {
            margin: 0 !important;
        }

        .col-lg-4, .col-lg-8, .col-lg-12 {
            padding: 0 !important;
        }

        .data-table {
            display: block !important;
            width: 100% !important;
            overflow-x: auto !important;
            -webkit-overflow-scrolling: touch !important;
        }

        /* Modal Responsiveness */
        #indoorTreatmentModal .modal-dialog {
            margin: 10px !important;
            max-width: calc(100% - 20px) !important;
        }

        .indoor-patient-info {
            grid-template-columns: 1fr !important;
            gap: 5px !important;
        }

        .date-slot-header {
            padding: 10px !important;
            gap: 8px !important;
        }

        .date-slot-header input {
            max-width: 100% !important;
            flex: 1;
        }

        .medicine-row {
            grid-template-columns: 1fr !important;
            gap: 5px !important;
        }

        .medicine-row input {
            width: 100% !important;
        }

        .delete-medicine-btn {
            width: 100% !important;
            margin-top: 5px;
        }

        .fnf-title {
            font-size: 18px !important;
        }

        /* Modal specific button fixes */
        .add-slot-btn {
            width: 100% !important;
            justify-content: center !important;
            padding: 12px !important;
            font-size: 14px !important;
        }

        .remove-slot-btn {
            width: 100% !important;
            justify-content: center !important;
            padding: 8px !important;
            margin-top: 5px !important;
        }

        .date-slot-header {
            padding: 15px !important;
        }

        .modal-footer {
            flex-direction: row !important;
            flex-wrap: nowrap !important;
            padding: 15px !important;
            gap: 10px !important;
        }

        .modal-footer .btn-cancel-indoor,
        .modal-footer .btn-save-indoor {
            flex: 1 !important;
            margin: 0 !important;
            font-size: 14px !important;
            padding: 12px 5px !important;
            text-align: center !important;
            justify-content: center !important;
            display: flex !important;
            align-items: center !important;
        }
    }
</style>

@php
function formatValue($value) {
    return ($value === null || $value === 'null' || $value === '' || $value === '0.00' || $value === '0') ? '' : $value;
}
@endphp

<div class="profile-container">
    <a href="{{ route('svc-patient') }}" class="back-button">
        <i class="bi bi-arrow-left"></i> Back to Patients
    </a>

    <div class="profile-header">
        <div class="header-left">
            <div class="profile-title">Patient Profile</div>
        </div>
        <div class="header-right">
            <button type="button" class="follow-up-btn" data-bs-toggle="modal" data-bs-target="#indoorTreatmentModal" style="background-color: #007bff; border-color: #007bff; margin-right: 10px;">
                <i class="bi bi-hospital"></i> Indoor Treatment
            </button>
            <a href="{{ route('add.follow.up', ['patient_id' => $patient->patient_id]) }}" class="follow-up-btn">Add Follow Up</a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-4 p-0">
            <div class="card mb-4">
                <div class="card-body py-4 text-center">
                    <form action="{{ route('svc.profile.update-image', $patient->id) }}" method="POST" enctype="multipart/form-data" id="profileImageForm">
                        @csrf
                        <div class="profile-image-wrapper">
                            <div class="profile-image-container" id="profileImagePreview">
                                @php $profileImage = $patient->getMeta('profile_image'); @endphp
                                @if($profileImage && file_exists(public_path($profileImage)))
                                    <img src="{{ asset($profileImage) }}" alt="Profile Image">
                                @else
                                    <i class="bi bi-person-fill"></i>
                                @endif
                            </div>
                            <label for="profile_image_input" class="upload-label" title="Change Profile Image">
                                <i class="bi bi-camera-fill"></i>
                            </label>
                            <input type="file" name="profile_image" id="profile_image_input" class="d-none" accept="image/*" onchange="previewPatientImage(this)">
                        </div>
                        <div id="imageSaveContainer" class="text-center">
                            <button type="submit" class="save-profile-btn" id="saveImageBtn">Save Image</button>
                        </div>
                    </form>
                    <p class="my-3 profile_txt_color mb-2 pb-0" style="font-weight: 600;">{{ $patient->patient_name ?? 'N/A' }}</p>
                    <p class="text-muted small">Hospital ID: {{ $patient->patient_id ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        <div class="col-lg-8 pr-0">
            <div class="card mb-4">
                <div class="card-body py-2">
                    <div class="row">
                        <div class="col-sm-3"><p class="mb-0 profile_txt_color">Full Name</p></div>
                        <div class="col-sm-9"><p class="text-muted mb-0">{{ $patient->patient_name ?? '' }}</p></div>
                    </div>
                    <hr class="my-2">
                    <div class="row">
                        <div class="col-sm-3"><p class="mb-0 profile_txt_color">Address</p></div>
                        <div class="col-sm-9"><p class="text-muted mb-0">{{ $patient->address ?? '' }}</p></div>
                    </div>
                    <hr class="my-2">
                    <div class="row">
                        <div class="col-sm-3"><p class="mb-0 profile_txt_color">Assigned Doctor</p></div>
                        <div class="col-sm-9">
                            @php
                                $doctorId = $meta['doctor_id'] ?? null;
                                $doctor = null;
                                if ($doctorId) { $doctor = \App\Models\User::find($doctorId); }
                            @endphp
                            <p class="text-muted mb-0">{{ $doctor ? $doctor->name : 'Not Assigned' }}</p>
                        </div>
                    </div>
                    <hr class="my-2">
                    <div class="row">
                        <div class="col-sm-3"><p class="mb-0 profile_txt_color">Age</p></div>
                        <div class="col-sm-9"><p class="text-muted mb-0">{{ $patient->age ?? '' }}</p></div>
                    </div>
                    <hr class="my-2">
                    <div class="row">
                        <div class="col-sm-3"><p class="mb-0 profile_txt_color">Gender</p></div>
                        <div class="col-sm-9"><p class="text-muted mb-0">{{ $patient->getMeta('gender') ?? '' }}</p></div>
                    </div>
                    <hr class="my-2">
                    <div class="row">
                        <div class="col-sm-3"><p class="mb-0 profile_txt_color">Reference By</p></div>
                        <div class="col-sm-9"><p class="text-muted mb-0">{{ $patient->getMeta('reference_by') ?? '' }}</p></div>
                    </div>
                    <hr class="my-2">
                    <div class="row">
                        <div class="col-sm-3"><p class="mb-0 profile_txt_color">Patient Status</p></div>
                        <div class="col-sm-9"><p class="text-muted mb-0">{{ $patient->getMeta('pt_status') ?? '' }}</p></div>
                    </div>
                    <hr class="my-2">
                    <div class="edit-profile-align-btn">
                        <button class="edit-profile-btn" onclick="window.location.href='{{ route('edit.svc.inquiry', $patient->id) }}'">Edit Profile</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="profile-content">
            <div class="profile-main">

                <!-- Inquiry Details -->
                <div class="row">
                    <div class="col-lg-12 p-0">
                        <div class="card-header mb-2">
                            <div class="section-title"><h3 class="bold font-up fnf-title">Inquiry Details</h3></div>
                        </div>
                        <div class="card mb-4">
                            <div class="card-body py-2">
                                @php $excludedMetaKeys = ['profile_image']; @endphp
                                @if(!empty($meta) && is_array($meta))
                                    @foreach($meta as $k => $v)
                                        @continue(in_array($k, $excludedMetaKeys, true))
                                        @php
                                            $label = ucwords(str_replace(['_', '-'], ' ', (string) $k));
                                            if (is_array($v)) {
                                                $displayValue = implode(', ', array_filter(array_map(function ($x) {
                                                    if (is_array($x)) return json_encode($x);
                                                    return (string) $x;
                                                }, $v), function ($x) { return $x !== ''; }));
                                            } else {
                                                $displayValue = (string) $v;
                                            }
                                            $displayValue = trim($displayValue);
                                        @endphp
                                        @if($displayValue !== '')
                                            <div class="row">
                                                <div class="col-sm-4"><p class="mb-0 profile_txt_color">{{ $label }}</p></div>
                                                <div class="col-sm-8"><p class="text-muted mb-0">{{ $displayValue }}</p></div>
                                            </div>
                                            <hr class="my-2">
                                        @endif
                                    @endforeach
                                @else
                                    <p class="text-muted mb-0">No inquiry details found.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Section -->
                <div class="row">
                    <div class="col-lg-12 p-0">
                        <div class="card-header mb-2">
                            <div class="section-title"><h3 class="bold font-up fnf-title">Payment</h3></div>
                        </div>
                        @php
                            $allPayments = collect();
                            $initialCash = $patient->getMeta('cash_payment');
                            $initialTotal = $patient->getMeta('total_payment');
                            $initialDiscount = $patient->getMeta('discount_payment');
                            $initialGiven = $patient->getMeta('given_payment');
                            $initialDue = $patient->getMeta('due_payment');
                            $initialGp = $patient->getMeta('gp_payment');
                            $initialCheque = $patient->getMeta('cheque_payment');
                            $hasInitialPaymentData = $initialCash || $initialTotal || $initialDiscount || $initialGiven || $initialDue || $initialGp || $initialCheque;
                            if ($hasInitialPaymentData) {
                                $allPayments->push([
                                    'date' => $patient->inquiry_date ? \Carbon\Carbon::parse($patient->inquiry_date)->format('d/m/Y') : '',
                                    'payment_method' => $initialCash ? 'Cash: '.$initialCash : ($initialGp ? 'GPay: '.$initialGp : ($initialCheque ? 'Cheque: '.$initialCheque : '')),
                                    'total' => $initialTotal ?? '', 'discount' => $initialDiscount ?? '',
                                    'given' => $initialGiven ?? '', 'due' => $initialDue ?? '', 'type' => 'initial'
                                ]);
                            }
                            $followUps = $patient->followups()->with('metas')->orderBy('followup_date', 'desc')->get();
                            foreach ($followUps as $followUp) {
                                $followUpMetas = [];
                                foreach ($followUp->metas as $meta) { $followUpMetas[$meta->meta_key] = $meta->meta_value; }
                                $followUpCash = $followUpMetas['cash_payment'] ?? '';
                                $followUpTotal = $followUpMetas['total_payment'] ?? '';
                                $followUpDiscount = $followUpMetas['discount_payment'] ?? '';
                                $followUpGiven = $followUpMetas['given_payment'] ?? '';
                                $followUpDue = $followUpMetas['due_payment'] ?? '';
                                $followUpGp = $followUpMetas['gp_payment'] ?? '';
                                $followUpCheque = $followUpMetas['cheque_payment'] ?? '';
                                $hasFollowUpPaymentData = $followUpCash || $followUpTotal || $followUpDiscount || $followUpGiven || $followUpDue || $followUpGp || $followUpCheque;
                                if ($hasFollowUpPaymentData) {
                                    $paymentMethod = '';
                                    if ($followUpCash) $paymentMethod = 'Cash: '.$followUpCash;
                                    elseif ($followUpGp) $paymentMethod = 'GPay: '.$followUpGp;
                                    elseif ($followUpCheque) $paymentMethod = 'Cheque: '.$followUpCheque;
                                    $allPayments->push([
                                        'date' => $followUp->followup_date ? \Carbon\Carbon::parse($followUp->followup_date)->format('d/m/Y') : '',
                                        'payment_method' => $paymentMethod, 'total' => $followUpTotal,
                                        'discount' => $followUpDiscount, 'given' => $followUpGiven,
                                        'due' => $followUpDue, 'type' => 'followup'
                                    ]);
                                }
                            }
                            $currentPaymentPage = request()->get('payment_page', 1);
                            $paymentPerPage = 3;
                            $paymentChunks = $allPayments->chunk($paymentPerPage);
                            $currentPaymentChunk = $paymentChunks[$currentPaymentPage - 1] ?? collect();
                            $totalPaymentPages = count($paymentChunks);
                        @endphp

                        @if($allPayments->count() > 0)
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>#</th><th>Date</th><th>Payment Method</th>
                                        <th>Total</th><th>Discount</th><th>Given</th><th>Due</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if($currentPaymentChunk->count() > 0)
                                        @foreach($currentPaymentChunk as $index => $payment)
                                        <tr>
                                            <td>{{ ($currentPaymentPage - 1) * $paymentPerPage + $index + 1 }}</td>
                                            <td>{{ formatValue($payment['date']) }}</td>
                                            <td>{{ formatValue($payment['payment_method']) }}</td>
                                            <td>{{ formatValue($payment['total']) }}</td>
                                            <td>{{ formatValue($payment['discount']) }}</td>
                                            <td>{{ formatValue($payment['given']) }}</td>
                                            <td>{{ formatValue($payment['due']) }}</td>
                                        </tr>
                                        @endforeach
                                    @else
                                        <tr><td colspan="7" class="empty-data">No payment records found</td></tr>
                                    @endif
                                </tbody>
                            </table>
                            <div class="pagination">
                                <div class="pagination-info">
                                    @if($currentPaymentChunk->count() > 0)
                                        Showing {{ ($currentPaymentPage - 1) * $paymentPerPage + 1 }} to {{ min($currentPaymentPage * $paymentPerPage, $allPayments->count()) }} of {{ $allPayments->count() }} entries
                                    @else
                                        Showing 0 to 0 of 0 entries
                                    @endif
                                </div>
                                <div class="pagination-buttons">
                                    @if($currentPaymentPage <= 1)
                                        <button class="pagination-btn" disabled>Previous</button>
                                    @else
                                        <a href="?payment_page={{ $currentPaymentPage - 1 }}" class="pagination-btn">Previous</a>
                                    @endif
                                    @if($currentPaymentPage >= $totalPaymentPages)
                                        <button class="pagination-btn" disabled>Next</button>
                                    @else
                                        <a href="?payment_page={{ $currentPaymentPage + 1 }}" class="pagination-btn">Next</a>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="empty-data" style="padding: 40px; text-align: center;">No payment records found</div>
                        @endif
                    </div>
                </div>

                <!-- Follow Up Section -->
                <div class="row pt-5">
                    <div class="col-lg-12 p-0">
                        <div class="card-header mb-2">
                            <div class="section-title"><h3 class="bold font-up fnf-title">Follow Up</h3></div>
                        </div>
                        @php
                            $allFollowUps = collect();
                            $initialFollowUp = [
                                'id' => 'initial_'.$patient->id, 'type' => 'initial',
                                'followup_date' => $patient->inquiry_date,
                                'weight' => $patient->getMeta('weight'),
                                'diagnosis' => $patient->diagnosis,
                                'complain' => $patient->getMeta('complain'),
                                'bp' => $patient->getMeta('blood_pressure'),
                                'investigation' => $patient->getMeta('investigation'),
                                'rbs' => $patient->getMeta('rbs'),
                                'pt_status' => $patient->getMeta('pt_status'),
                                'notes' => $patient->getMeta('notes'),
                                'next_follow_date' => $patient->next_follow_date,
                                'doctor_id' => $meta['doctor_id'] ?? null,
                                'doctor_name' => isset($meta['doctor_id']) ? (\App\Models\User::find($meta['doctor_id'])->name ?? 'N/A') : 'N/A',
                                'zoom_meeting_id' => $meta['zoom_meeting_id'] ?? null,
                                'zoom_start_url' => $meta['zoom_start_url'] ?? null,
                                'zoom_join_url' => $meta['zoom_join_url'] ?? null,
                                'created_at' => $patient->created_at
                            ];
                            $allFollowUps->push((object)$initialFollowUp);
                            $followUps = $patient->followups()->with(['metas', 'doctor'])->orderBy('followup_date', 'desc')->get();
                            foreach ($followUps as $followUp) {
                                $followUpMetas = [];
                                foreach ($followUp->metas as $meta) { $followUpMetas[$meta->meta_key] = $meta->meta_value; }
                                $followUpData = [
                                    'id' => $followUp->id, 'type' => 'followup',
                                    'followup_date' => $followUp->followup_date,
                                    'weight' => $followUpMetas['weight'] ?? ($followUpMetas['weight_0'] ?? ''),
                                    'diagnosis' => $followUpMetas['diagnosis'] ?? ($followUpMetas['diagnosis_0'] ?? ''),
                                    'complain' => $followUpMetas['complain'] ?? '',
                                    'bp' => $followUpMetas['blood_pressure'] ?? ($followUpMetas['blood_pressure_0'] ?? ''),
                                    'investigation' => $followUpMetas['investigation'] ?? '',
                                    'rbs' => $followUpMetas['rbs'] ?? ($followUpMetas['rbs_0'] ?? ''),
                                    'pt_status' => $followUpMetas['pt_status'] ?? ($followUpMetas['pt_status_0'] ?? ''),
                                    'notes' => $followUpMetas['notes'] ?? '',
                                    'next_follow_date' => $followUpMetas['next_follow_date'] ?? '',
                                    'doctor_id' => $followUp->doctor_id,
                                    'doctor_name' => $followUp->doctor ? $followUp->doctor->name : 'N/A',
                                    'zoom_meeting_id' => $followUp->zoom_meeting_id,
                                    'zoom_start_url' => $followUp->zoom_start_url,
                                    'zoom_join_url' => $followUp->zoom_join_url,
                                    'created_at' => $followUp->created_at
                                ];
                                $allFollowUps->push((object)$followUpData);
                            }
                            $currentPage = request()->get('followup_page', 1);
                            $perPage = 3;
                            $currentItems = $allFollowUps->slice(($currentPage - 1) * $perPage, $perPage)->all();
                            $totalPages = ceil($allFollowUps->count() / $perPage);
                        @endphp

                        <div>
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th>#</th><th>Date</th><th>Weight</th><th>Complain</th>
                                        <th>Diagnosis</th><th>BP</th><th>RBS</th><th>Investigation</th>
                                        <th>PT Status</th><th>Note</th><th>Doctor</th>
                                        <th>Next Follow Date</th><th width="120px">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @if(count($currentItems) > 0)
                                        @foreach($currentItems as $index => $followUp)
                                        @php $serialNumber = ($currentPage - 1) * $perPage + $index + 1; @endphp
                                        <tr>
                                            <td>{{ $serialNumber }}</td>
                                            <td>{{ formatValue($followUp->followup_date ? \Carbon\Carbon::parse($followUp->followup_date)->format('d/m/Y') : '') }}</td>
                                            <td>{{ formatValue($followUp->weight) }}</td>
                                            <td>{{ formatValue($followUp->complain) }}</td>
                                            <td>{{ formatValue($followUp->diagnosis) }}</td>
                                            <td>{{ formatValue($followUp->bp) }}</td>
                                            <td>{{ formatValue($followUp->rbs) }}</td>
                                            <td>{{ formatValue($followUp->investigation) }}</td>
                                            <td>{{ formatValue($followUp->pt_status) }}</td>
                                            <td>{{ formatValue($followUp->notes) }}</td>
                                            <td>{{ formatValue($followUp->doctor_name ?? 'N/A') }}</td>
                                            <td>{{ formatValue($followUp->next_follow_date ? \Carbon\Carbon::parse($followUp->next_follow_date)->format('d/m/Y') : '') }}</td>
                                            <td>
                                                <div class="action-buttons">
                                                    @if($followUp->type === 'initial')
                                                        <a href="{{ route('edit.svc.inquiry', $patient->id) }}" class="action-btn edit-btn" title="Edit Initial Inquiry">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <form action="{{ route('delete-inquiry', $patient->id) }}" method="POST" class="delete-form">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="action-btn delete-btn" title="Delete Entire Patient Record" onclick="return confirmDeletePatient()">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    @else
                                                        <a href="{{ route('edit.follow.up', ['patient_id' => $patient->patient_id, 'followup_id' => $followUp->id]) }}" class="action-btn edit-btn" title="Edit Follow Up">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <form action="{{ route('delete.follow.up', $followUp->id) }}" method="POST" class="delete-form">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="action-btn delete-btn" title="Delete Follow Up" onclick="return confirm('Are you sure you want to delete this follow-up record? This action cannot be undone.')">
                                                                <i class="fas fa-trash"></i>
                                                            </button>
                                                        </form>
                                                    @endif

                                                    @php
                                                        if($followUp->type === 'initial') {
                                                            $createZoomRoute = route('zoom.meeting.create', $followUp->id);
                                                        } else {
                                                            $createZoomRoute = route('followup.create-zoom-meeting', $followUp->id);
                                                        }
                                                    @endphp

                                                    @if($followUp->zoom_join_url)
                                                        @php
                                                            $waPhone = preg_replace('/[^0-9]/', '', $patient->getMeta('phone') ?? '');
                                                            if (strlen($waPhone) == 10) $waPhone = '91'.$waPhone;
                                                            $zoomJoinUrl = $followUp->zoom_join_url;
                                                            $internalJoinUrl = $followUp->zoom_start_url ?? $zoomJoinUrl;
                                                            $waMessage = "Hello ".($patient->patient_name ?? 'Patient').", your video consultation is scheduled. You can join the meeting by clicking this link: ".$zoomJoinUrl;
                                                            $waUrl = "https://wa.me/".$waPhone."?text=".urlencode($waMessage);
                                                        @endphp
                                                        <button type="button" class="action-btn btn-zoom-join" title="Zoom Meeting Options" onclick="openZoomModal('{{ $internalJoinUrl }}', '{{ $waUrl }}')">
                                                            <i class="fas fa-video"></i>
                                                        </button>
                                                    @elseif($followUp->doctor_id)
                                                        <form action="{{ $createZoomRoute }}" method="POST" style="display: inline;">
                                                            @csrf
                                                            <button type="submit" class="action-btn btn-zoom-create" title="Create Zoom Meeting">
                                                                <i class="fas fa-video-slash"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                        @endforeach
                                    @else
                                        <tr><td colspan="11" class="empty-data">No follow-up records found.</td></tr>
                                    @endif
                                </tbody>
                            </table>
                        </div>

                        @if($totalPages > 1)
                        <div class="pagination">
                            <div class="pagination-info">Showing {{ ($currentPage - 1) * $perPage + 1 }} to {{ min($currentPage * $perPage, $allFollowUps->count()) }} of {{ $allFollowUps->count() }} entries</div>
                            <div class="pagination-buttons">
                                @if($currentPage <= 1)
                                    <button class="pagination-btn" disabled>Previous</button>
                                @else
                                    <a href="?followup_page={{ $currentPage - 1 }}" class="pagination-btn">Previous</a>
                                @endif
                                @if($currentPage >= $totalPages)
                                    <button class="pagination-btn" disabled>Next</button>
                                @else
                                    <a href="?followup_page={{ $currentPage + 1 }}" class="pagination-btn">Next</a>
                                @endif
                            </div>
                        </div>
                        @else
                        <div class="pagination">
                            <div class="pagination-info">Showing {{ $allFollowUps->count() }} to {{ $allFollowUps->count() }} of {{ $allFollowUps->count() }} entries</div>
                            <div class="pagination-buttons">
                                <button class="pagination-btn" disabled>Previous</button>
                                <button class="pagination-btn" disabled>Next</button>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Lipid Profile Section -->
                <div class="row pt-5">
                    <div class="col-lg-12 p-0">
                        <div class="card-header mb-2">
                            <div class="section-title"><h3 class="bold font-up fnf-title" style="color: #28a745;">Lipid Profile</h3></div>
                        </div>
                        <div class="lab-table-container">
                            <table class="data-table compact-table">
                                <thead>
                                    <tr>
                                        <th>Date</th><th>S.Cholesterol</th><th>S.Triglyceride</th>
                                        <th>HDL</th><th>LDL</th><th>VLDL</th><th>Non-HDL C</th><th>Chol/HDL ratio</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>{{ $patient->inquiry_date ? \Carbon\Carbon::parse($patient->inquiry_date)->format('d/m/Y') : '-' }}</td>
                                        <td>{{ formatValue($patient->getMeta('s_cholesterol')) }}</td>
                                        <td>{{ formatValue($patient->getMeta('STriglyceride')) }}</td>
                                        <td>{{ formatValue($patient->getMeta('HDL')) }}</td>
                                        <td>{{ formatValue($patient->getMeta('LDL')) }}</td>
                                        <td>{{ formatValue($patient->getMeta('VLDL')) }}</td>
                                        <td>{{ formatValue($patient->getMeta('non_hdl_c')) }}</td>
                                        <td>{{ formatValue($patient->getMeta('chol_hdl_ratio')) }}</td>
                                    </tr>
                                    @php
                                        $followUpLipids = collect();
                                        foreach($followUps as $followUp) {
                                            $fMetas = $followUp->metas->pluck('meta_value', 'meta_key')->toArray();
                                            $hasLipid = !empty($fMetas['s_cholesterol']) || !empty($fMetas['STriglyceride']) || 
                                                        !empty($fMetas['HDL']) || !empty($fMetas['LDL']) || 
                                                        !empty($fMetas['VLDL']) || !empty($fMetas['non_hdl_c']) || 
                                                        !empty($fMetas['chol_hdl_ratio']);
                                            if($hasLipid) {
                                                $followUpLipids->push([
                                                    'date' => $followUp->followup_date ? \Carbon\Carbon::parse($followUp->followup_date)->format('d/m/Y') : '-',
                                                    's_cholesterol' => $fMetas['s_cholesterol'] ?? '',
                                                    'STriglyceride' => $fMetas['STriglyceride'] ?? '',
                                                    'HDL' => $fMetas['HDL'] ?? '',
                                                    'LDL' => $fMetas['LDL'] ?? '',
                                                    'VLDL' => $fMetas['VLDL'] ?? '',
                                                    'non_hdl_c' => $fMetas['non_hdl_c'] ?? '',
                                                    'chol_hdl_ratio' => $fMetas['chol_hdl_ratio'] ?? ''
                                                ]);
                                            }
                                        }
                                    @endphp
                                    @foreach($followUpLipids as $lipid)
                                    <tr>
                                        <td>{{ $lipid['date'] }}</td>
                                        <td>{{ formatValue($lipid['s_cholesterol']) }}</td>
                                        <td>{{ formatValue($lipid['STriglyceride']) }}</td>
                                        <td>{{ formatValue($lipid['HDL']) }}</td>
                                        <td>{{ formatValue($lipid['LDL']) }}</td>
                                        <td>{{ formatValue($lipid['VLDL']) }}</td>
                                        <td>{{ formatValue($lipid['non_hdl_c']) }}</td>
                                        <td>{{ formatValue($lipid['chol_hdl_ratio']) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Laboratory Investigation Section -->
                <div class="row pt-5">
                    <div class="col-lg-12 p-0">
                        <div class="card-header mb-2">
                            <div class="section-title"><h3 class="bold font-up fnf-title">Laboratory Investigation</h3></div>
                        </div>
                        @php
                            $allLabInvestigations = collect();
                            $initialLab = [
                                'date' => $patient->inquiry_date ? \Carbon\Carbon::parse($patient->inquiry_date)->format('d/m/Y') : '',
                                'hb' => $patient->getMeta('hb') ?? '', 'tc' => $patient->getMeta('tc') ?? '',
                                'pc' => $patient->getMeta('pc') ?? '', 'mp' => $patient->getMeta('MP') ?? '',
                                'hb1ac' => $patient->getMeta('HB1AC') ?? '', 'fbs' => $patient->getMeta('fbs') ?? '',
                                'pp2bs' => $patient->getMeta('pp2bs') ?? '', 's_widal' => $patient->getMeta('S_widal') ?? '',
                                'usg' => $patient->getMeta('USG') ?? '', 'x_ray' => $patient->getMeta('X_ray') ?? '',
                                'sgpt' => $patient->getMeta('SGPT') ?? '', 's_creatinine' => $patient->getMeta('s_creatinine') ?? '',
                                'ns1ag' => $patient->getMeta('NS1Ag') ?? '', 'dengue_igm' => $patient->getMeta('DengueIGM') ?? '',
                                's_b12' => $patient->getMeta('SB12') ?? '',
                                's_d3' => $patient->getMeta('SD3') ?? '', 'urine' => $patient->getMeta('Urine') ?? '',
                                'crp' => $patient->getMeta('CRP') ?? '', 's_t3' => $patient->getMeta('St3') ?? '',
                                's_t4' => $patient->getMeta('St4') ?? '', 's_tsh' => $patient->getMeta('STSH') ?? '',
                                'esr' => $patient->getMeta('ESR') ?? '', 'specific_test' => $patient->getMeta('specific_test') ?? '',
                                'type' => 'initial'
                            ];
                            $hasInitialLabData = false;
                            foreach ($initialLab as $key => $value) {
                                if ($key !== 'date' && $key !== 'type' && !empty($value) && $value !== 'null') { $hasInitialLabData = true; break; }
                            }
                            if ($hasInitialLabData) { $allLabInvestigations->push($initialLab); }
                            foreach ($followUps as $followUp) {
                                $followUpMetas = [];
                                foreach ($followUp->metas as $meta) { $followUpMetas[$meta->meta_key] = $meta->meta_value; }
                                $followUpLab = [
                                    'date' => $followUp->followup_date ? \Carbon\Carbon::parse($followUp->followup_date)->format('d/m/Y') : '',
                                    'hb' => $followUpMetas['hb'] ?? '', 'tc' => $followUpMetas['tc'] ?? '',
                                    'pc' => $followUpMetas['pc'] ?? '', 'mp' => $followUpMetas['MP'] ?? '',
                                    'hb1ac' => $followUpMetas['HB1AC'] ?? '', 'fbs' => $followUpMetas['fbs'] ?? '',
                                    'pp2bs' => $followUpMetas['pp2bs'] ?? '', 's_widal' => $followUpMetas['S_widal'] ?? '',
                                    'usg' => $followUpMetas['USG'] ?? '', 'x_ray' => $followUpMetas['X_ray'] ?? '',
                                    'sgpt' => $followUpMetas['SGPT'] ?? '', 's_creatinine' => $followUpMetas['s_creatinine'] ?? '',
                                    'ns1ag' => $followUpMetas['NS1Ag'] ?? '', 'dengue_igm' => $followUpMetas['DengueIGM'] ?? '',
                                    's_b12' => $followUpMetas['SB12'] ?? '',
                                    's_d3' => $followUpMetas['SD3'] ?? '', 'urine' => $followUpMetas['Urine'] ?? '',
                                    'crp' => $followUpMetas['CRP'] ?? '', 's_t3' => $followUpMetas['St3'] ?? '',
                                    's_t4' => $followUpMetas['St4'] ?? '', 's_tsh' => $followUpMetas['STSH'] ?? '',
                                    'esr' => $followUpMetas['ESR'] ?? '', 'specific_test' => $followUpMetas['specific_test'] ?? '',
                                    'type' => 'followup'
                                ];
                                $hasFollowUpLabData = false;
                                foreach ($followUpLab as $key => $value) {
                                    if ($key !== 'date' && $key !== 'type' && !empty($value) && $value !== 'null') { $hasFollowUpLabData = true; break; }
                                }
                                if ($hasFollowUpLabData) { $allLabInvestigations->push($followUpLab); }
                            }
                            $currentLabPage = request()->get('lab_page', 1);
                            $labPerPage = 3;
                            $labChunks = $allLabInvestigations->chunk($labPerPage);
                            $currentLabChunk = $labChunks[$currentLabPage - 1] ?? collect();
                            $totalLabPages = count($labChunks);
                        @endphp

                        @if($allLabInvestigations->count() > 0)
                            <div class="lab-table-container">
                                <table class="data-table compact-table">
                                    <thead>
                                        <tr>
                                            <th>#</th><th>Date</th><th>HB</th><th>TC</th><th>PC</th><th>MP</th>
                                            <th>HB1Ac</th><th>FBS</th><th>PP2BS</th><th>S.widal</th><th>USG</th>
                                            <th>X-ray</th><th>SGPT</th><th>S.Creatinine</th><th>NS1Ag</th>
                                            <th>Dengue IGM</th>
                                            <th>S.B12</th><th>S.D3</th>
                                            <th>Urine</th><th>CRP</th><th>S.T3</th><th>S.T4</th><th>S.TSH</th>
                                            <th>ESR</th><th>Any Specific Test</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($currentLabChunk->count() > 0)
                                            @foreach($currentLabChunk as $index => $lab)
                                            <tr>
                                                <td>{{ ($currentLabPage - 1) * $labPerPage + $index + 1 }}</td>
                                                <td>{{ formatValue($lab['date']) }}</td>
                                                <td>{{ formatValue($lab['hb']) }}</td>
                                                <td>{{ formatValue($lab['tc']) }}</td>
                                                <td>{{ formatValue($lab['pc']) }}</td>
                                                <td>{{ formatValue($lab['mp']) }}</td>
                                                <td>{{ formatValue($lab['hb1ac']) }}</td>
                                                <td>{{ formatValue($lab['fbs']) }}</td>
                                                <td>{{ formatValue($lab['pp2bs']) }}</td>
                                                <td>{{ formatValue($lab['s_widal']) }}</td>
                                                <td>{{ formatValue($lab['usg']) }}</td>
                                                <td>{{ formatValue($lab['x_ray']) }}</td>
                                                <td>{{ formatValue($lab['sgpt']) }}</td>
                                                <td>{{ formatValue($lab['s_creatinine']) }}</td>
                                                <td>{{ formatValue($lab['ns1ag']) }}</td>
                                                <td>{{ formatValue($lab['dengue_igm']) }}</td>
                                                <td>{{ formatValue($lab['s_b12']) }}</td>
                                                <td>{{ formatValue($lab['s_d3']) }}</td>
                                                <td>{{ formatValue($lab['urine']) }}</td>
                                                <td>{{ formatValue($lab['crp']) }}</td>
                                                <td>{{ formatValue($lab['s_t3']) }}</td>
                                                <td>{{ formatValue($lab['s_t4']) }}</td>
                                                <td>{{ formatValue($lab['s_tsh']) }}</td>
                                                <td>{{ formatValue($lab['esr']) }}</td>
                                                <td>{{ formatValue($lab['specific_test']) }}</td>
                                            </tr>
                                            @endforeach
                                        @else
                                            <tr><td colspan="30" class="empty-data">No laboratory investigations found</td></tr>
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            <div class="pagination">
                                <div class="pagination-info">
                                    @if($currentLabChunk->count() > 0)
                                        Showing {{ ($currentLabPage - 1) * $labPerPage + 1 }} to {{ min($currentLabPage * $labPerPage, $allLabInvestigations->count()) }} of {{ $allLabInvestigations->count() }} entries
                                    @else
                                        Showing 0 to 0 of 0 entries
                                    @endif
                                </div>
                                <div class="pagination-buttons">
                                    @if($currentLabPage <= 1)
                                        <button class="pagination-btn" disabled>Previous</button>
                                    @else
                                        <a href="?lab_page={{ $currentLabPage - 1 }}" class="pagination-btn">Previous</a>
                                    @endif
                                    @if($currentLabPage >= $totalLabPages)
                                        <button class="pagination-btn" disabled>Next</button>
                                    @else
                                        <a href="?lab_page={{ $currentLabPage + 1 }}" class="pagination-btn">Next</a>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="empty-data" style="padding: 40px; text-align: center;">No laboratory investigations found</div>
                        @endif
                    </div>
                </div>

                <!-- Inside Treatment Section -->
                <div class="row pt-5">
                    <div class="col-lg-12 p-0">
                        <div class="card-header mb-2">
                            <div class="section-title"><h3 class="bold font-up fnf-title">Inside Treatment</h3></div>
                        </div>
                        @php
                            $allInsideTreatments = collect();
                            $initialInsideTreatments = $treatments['inside'] ?? [];
                            foreach ($initialInsideTreatments as $treatment) {
                                $allInsideTreatments->push(array_merge($treatment, ['date' => $patient->inquiry_date ? \Carbon\Carbon::parse($patient->inquiry_date)->format('d/m/Y') : '', 'type' => 'initial']));
                            }
                            foreach ($followUps as $followUp) {
                                $followUpInsideTreatments = \App\Models\PatientTreatment::where('followup_id', $followUp->id)->where('type', 'inside')->get();
                                foreach ($followUpInsideTreatments as $treatment) {
                                    $allInsideTreatments->push(array_merge($treatment->toArray(), ['date' => $followUp->followup_date ? \Carbon\Carbon::parse($followUp->followup_date)->format('d/m/Y') : '', 'type' => 'followup']));
                                }
                            }
                            $currentInsidePage = request()->get('inside_page', 1);
                            $insidePerPage = 3;
                            $insideChunks = $allInsideTreatments->chunk($insidePerPage);
                            $currentInsideChunk = $insideChunks[$currentInsidePage - 1] ?? collect();
                            $totalInsidePages = count($insideChunks);
                        @endphp

                        @if($currentInsideChunk->count() > 0)
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th width="5%">#</th><th width="15%">Date</th>
                                        <th width="40%">Medicine</th><th width="20%">Dose</th><th width="20%">When</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($currentInsideChunk as $index => $medicine)
                                    @php $serialNumber = ($currentInsidePage - 1) * $insidePerPage + $index + 1; @endphp
                                    <tr>
                                        <td>{{ $serialNumber }}</td>
                                        <td>{{ formatValue($medicine['date']) }}</td>
                                        <td class="medicine-name">{{ formatValue($medicine['medicine']) }}</td>
                                        <td>{{ formatValue($medicine['dose']) }}</td>
                                        <td>{{ formatValue($medicine['timing']) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="pagination">
                                <div class="pagination-info">Showing {{ ($currentInsidePage - 1) * $insidePerPage + 1 }} to {{ min($currentInsidePage * $insidePerPage, $allInsideTreatments->count()) }} of {{ $allInsideTreatments->count() }} entries</div>
                                <div class="pagination-buttons">
                                    @if($currentInsidePage <= 1)
                                        <button class="pagination-btn" disabled>Previous</button>
                                    @else
                                        <a href="?inside_page={{ $currentInsidePage - 1 }}" class="pagination-btn">Previous</a>
                                    @endif
                                    @if($currentInsidePage >= $totalInsidePages)
                                        <button class="pagination-btn" disabled>Next</button>
                                    @else
                                        <a href="?inside_page={{ $currentInsidePage + 1 }}" class="pagination-btn">Next</a>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="no-treatment">No inside treatment prescribed</div>
                        @endif
                    </div>
                </div>

                <!-- Homeopathic Treatment Section -->
                <div class="row pt-5">
                    <div class="col-lg-12 p-0">
                        <div class="card-header mb-2">
                            <div class="section-title"><h3 class="bold font-up fnf-title">Homeopathic Treatment</h3></div>
                        </div>
                        @php
                            $allHomeoTreatments = collect();
                            $initialHomeoTreatments = $treatments['homeo'] ?? [];
                            foreach ($initialHomeoTreatments as $treatment) {
                                $allHomeoTreatments->push(array_merge($treatment, ['date' => $patient->inquiry_date ? \Carbon\Carbon::parse($patient->inquiry_date)->format('d/m/Y') : '', 'type' => 'initial']));
                            }
                            foreach ($followUps as $followUp) {
                                $followUpHomeoTreatments = \App\Models\PatientTreatment::where('followup_id', $followUp->id)->where('type', 'homeo')->get();
                                foreach ($followUpHomeoTreatments as $treatment) {
                                    $allHomeoTreatments->push(array_merge($treatment->toArray(), ['date' => $followUp->followup_date ? \Carbon\Carbon::parse($followUp->followup_date)->format('d/m/Y') : '', 'type' => 'followup']));
                                }
                            }
                            $currentHomeoPage = request()->get('homeo_page', 1);
                            $homeoPerPage = 3;
                            $homeoChunks = $allHomeoTreatments->chunk($homeoPerPage);
                            $currentHomeoChunk = $homeoChunks[$currentHomeoPage - 1] ?? collect();
                            $totalHomeoPages = count($homeoChunks);
                        @endphp

                        @if($currentHomeoChunk->count() > 0)
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th width="5%">#</th><th width="20%">Date</th>
                                        <th width="55%">Medicine</th><th width="20%">When</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($currentHomeoChunk as $index => $medicine)
                                    @php $serialNumber = ($currentHomeoPage - 1) * $homeoPerPage + $index + 1; @endphp
                                    <tr>
                                        <td>{{ $serialNumber }}</td>
                                        <td>{{ formatValue($medicine['date']) }}</td>
                                        <td class="medicine-name">{{ formatValue($medicine['medicine']) }}</td>
                                        <td>{{ formatValue($medicine['timing']) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="pagination">
                                <div class="pagination-info">Showing {{ ($currentHomeoPage - 1) * $homeoPerPage + 1 }} to {{ min($currentHomeoPage * $homeoPerPage, $allHomeoTreatments->count()) }} of {{ $allHomeoTreatments->count() }} entries</div>
                                <div class="pagination-buttons">
                                    @if($currentHomeoPage <= 1)
                                        <button class="pagination-btn" disabled>Previous</button>
                                    @else
                                        <a href="?homeo_page={{ $currentHomeoPage - 1 }}" class="pagination-btn">Previous</a>
                                    @endif
                                    @if($currentHomeoPage >= $totalHomeoPages)
                                        <button class="pagination-btn" disabled>Next</button>
                                    @else
                                        <a href="?homeo_page={{ $currentHomeoPage + 1 }}" class="pagination-btn">Next</a>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="no-treatment">No homeopathic treatment prescribed</div>
                        @endif
                    </div>
                </div>

                <!-- Prescription Section -->
                <div class="row pt-5">
                    <div class="col-lg-12 p-0">
                        <div class="card-header mb-2">
                            <div class="section-title"><h3 class="bold font-up fnf-title">Prescription</h3></div>
                        </div>
                        @php
                            $allPrescriptionTreatments = collect();
                            $initialPrescriptionTreatments = $treatments['prescription'] ?? [];
                            foreach ($initialPrescriptionTreatments as $treatment) {
                                $allPrescriptionTreatments->push(array_merge($treatment, ['date' => $patient->inquiry_date ? \Carbon\Carbon::parse($patient->inquiry_date)->format('d/m/Y') : '', 'type' => 'initial']));
                            }
                            foreach ($followUps as $followUp) {
                                $followUpPrescriptionTreatments = \App\Models\PatientTreatment::where('followup_id', $followUp->id)->where('type', 'prescription')->get();
                                foreach ($followUpPrescriptionTreatments as $treatment) {
                                    $allPrescriptionTreatments->push(array_merge($treatment->toArray(), ['date' => $followUp->followup_date ? \Carbon\Carbon::parse($followUp->followup_date)->format('d/m/Y') : '', 'type' => 'followup']));
                                }
                            }
                            $currentPrescriptionPage = request()->get('prescription_page', 1);
                            $prescriptionPerPage = 3;
                            $prescriptionChunks = $allPrescriptionTreatments->chunk($prescriptionPerPage);
                            $currentPrescriptionChunk = $prescriptionChunks[$currentPrescriptionPage - 1] ?? collect();
                            $totalPrescriptionPages = count($prescriptionChunks);
                        @endphp

                        @if($currentPrescriptionChunk->count() > 0)
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th width="5%">#</th><th width="15%">Date</th>
                                        <th width="40%">Medicine</th><th width="20%">Dose</th><th width="20%">When</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($currentPrescriptionChunk as $index => $medicine)
                                    @php $serialNumber = ($currentPrescriptionPage - 1) * $prescriptionPerPage + $index + 1; @endphp
                                    <tr>
                                        <td>{{ $serialNumber }}</td>
                                        <td>{{ formatValue($medicine['date']) }}</td>
                                        <td class="medicine-name">{{ formatValue($medicine['medicine']) }}</td>
                                        <td>{{ formatValue($medicine['dose']) }}</td>
                                        <td>{{ formatValue($medicine['timing']) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="pagination">
                                <div class="pagination-info">Showing {{ ($currentPrescriptionPage - 1) * $prescriptionPerPage + 1 }} to {{ min($currentPrescriptionPage * $prescriptionPerPage, $allPrescriptionTreatments->count()) }} of {{ $allPrescriptionTreatments->count() }} entries</div>
                                <div class="pagination-buttons">
                                    @if($currentPrescriptionPage <= 1)
                                        <button class="pagination-btn" disabled>Previous</button>
                                    @else
                                        <a href="?prescription_page={{ $currentPrescriptionPage - 1 }}" class="pagination-btn">Previous</a>
                                    @endif
                                    @if($currentPrescriptionPage >= $totalPrescriptionPages)
                                        <button class="pagination-btn" disabled>Next</button>
                                    @else
                                        <a href="?prescription_page={{ $currentPrescriptionPage + 1 }}" class="pagination-btn">Next</a>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="no-treatment">No prescription given</div>
                        @endif
                    </div>
                </div>

                <!-- Indoor Treatment Display Section -->
                <div class="row pt-5">
                    <div class="col-lg-12 p-0">
                        <div class="card-header mb-2">
                            <div class="section-title"><h3 class="bold font-up fnf-title">Indoor Treatment</h3></div>
                        </div>
                        @php
                            $allIndoorTreatments = collect();
                            $initialIndoorTreatments = $treatments['indoor'] ?? [];
                            foreach ($initialIndoorTreatments as $treatment) {
                                $allIndoorTreatments->push(array_merge($treatment, [
                                    'display_date' => !empty($treatment['date']) ? \Carbon\Carbon::parse($treatment['date'])->format('d/m/Y') : ($patient->inquiry_date ? \Carbon\Carbon::parse($patient->inquiry_date)->format('d/m/Y') : ''),
                                    'display_time' => !empty($treatment['time']) ? \Carbon\Carbon::parse($treatment['time'])->format('h:i A') : '',
                                    'type' => 'initial'
                                ]));
                            }
                            foreach ($followUps as $followUp) {
                                $followUpIndoorTreatments = \App\Models\PatientTreatment::where('followup_id', $followUp->id)->where('type', 'indoor')->get();
                                foreach ($followUpIndoorTreatments as $treatment) {
                                    $allIndoorTreatments->push(array_merge($treatment->toArray(), [
                                        'display_date' => !empty($treatment->date) ? \Carbon\Carbon::parse($treatment->date)->format('d/m/Y') : ($followUp->followup_date ? \Carbon\Carbon::parse($followUp->followup_date)->format('d/m/Y') : ''),
                                        'display_time' => !empty($treatment->time) ? \Carbon\Carbon::parse($treatment->time)->format('h:i A') : '',
                                        'type' => 'followup'
                                    ]));
                                }
                            }
                            $currentIndoorPage = request()->get('indoor_page', 1);
                            $indoorPerPage = 3;
                            $indoorChunks = $allIndoorTreatments->chunk($indoorPerPage);
                            $currentIndoorChunk = $indoorChunks[$currentIndoorPage - 1] ?? collect();
                            $totalIndoorPages = count($indoorChunks);
                        @endphp

                        @if($currentIndoorChunk->count() > 0)
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th width="5%">#</th><th width="15%">Date</th><th width="10%">Time</th>
                                        <th width="35%">Medicine</th><th width="15%">Note</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($currentIndoorChunk as $index => $medicine)
                                    @php $serialNumber = ($currentIndoorPage - 1) * $indoorPerPage + $index + 1; @endphp
                                    <tr>
                                        <td>{{ $serialNumber }}</td>
                                        <td>{{ formatValue($medicine['display_date']) }}</td>
                                        <td>{{ formatValue($medicine['display_time']) }}</td>
                                        <td class="medicine-name">{{ formatValue($medicine['medicine']) }}</td>
                                        <td>{{ formatValue($medicine['note']) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="pagination">
                                <div class="pagination-info">Showing {{ ($currentIndoorPage - 1) * $indoorPerPage + 1 }} to {{ min($currentIndoorPage * $indoorPerPage, $allIndoorTreatments->count()) }} of {{ $allIndoorTreatments->count() }} entries</div>
                                <div class="pagination-buttons">
                                    @if($currentIndoorPage <= 1)
                                        <button class="pagination-btn" disabled>Previous</button>
                                    @else
                                        <a href="?indoor_page={{ $currentIndoorPage - 1 }}" class="pagination-btn">Previous</a>
                                    @endif
                                    @if($currentIndoorPage >= $totalIndoorPages)
                                        <button class="pagination-btn" disabled>Next</button>
                                    @else
                                        <a href="?indoor_page={{ $currentIndoorPage + 1 }}" class="pagination-btn">Next</a>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="no-treatment">No indoor treatment prescribed</div>
                        @endif
                    </div>
                </div>

                <!-- Other Treatment Section -->
                <div class="row pt-5">
                    <div class="col-lg-12 p-0">
                        <div class="card-header mb-2">
                            <div class="section-title"><h3 class="bold font-up fnf-title">Other Treatment</h3></div>
                        </div>
                        @php
                            $allOtherTreatments = collect();
                            $initialOtherTreatments = $treatments['other'] ?? [];
                            foreach ($initialOtherTreatments as $treatment) {
                                $allOtherTreatments->push(array_merge($treatment, ['date' => $patient->inquiry_date ? \Carbon\Carbon::parse($patient->inquiry_date)->format('d/m/Y') : '', 'type' => 'initial']));
                            }
                            foreach ($followUps as $followUp) {
                                $followUpOtherTreatments = \App\Models\PatientTreatment::where('followup_id', $followUp->id)->where('type', 'other')->get();
                                foreach ($followUpOtherTreatments as $treatment) {
                                    $allOtherTreatments->push(array_merge($treatment->toArray(), ['date' => $followUp->followup_date ? \Carbon\Carbon::parse($followUp->followup_date)->format('d/m/Y') : '', 'type' => 'followup']));
                                }
                            }
                            $currentOtherPage = request()->get('other_page', 1);
                            $otherPerPage = 3;
                            $otherChunks = $allOtherTreatments->chunk($otherPerPage);
                            $currentOtherChunk = $otherChunks[$currentOtherPage - 1] ?? collect();
                            $totalOtherPages = count($otherChunks);
                        @endphp

                        @if($currentOtherChunk->count() > 0)
                            <table class="data-table">
                                <thead>
                                    <tr>
                                        <th width="5%">#</th><th width="20%">Date</th>
                                        <th width="50%">Medicine</th><th width="25%">Note</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($currentOtherChunk as $index => $medicine)
                                    @php $serialNumber = ($currentOtherPage - 1) * $otherPerPage + $index + 1; @endphp
                                    <tr>
                                        <td>{{ $serialNumber }}</td>
                                        <td>{{ formatValue($medicine['date']) }}</td>
                                        <td class="medicine-name">{{ formatValue($medicine['medicine']) }}</td>
                                        <td>{{ formatValue($medicine['note']) }}</td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                            <div class="pagination">
                                <div class="pagination-info">Showing {{ ($currentOtherPage - 1) * $otherPerPage + 1 }} to {{ min($currentOtherPage * $otherPerPage, $allOtherTreatments->count()) }} of {{ $allOtherTreatments->count() }} entries</div>
                                <div class="pagination-buttons">
                                    @if($currentOtherPage <= 1)
                                        <button class="pagination-btn" disabled>Previous</button>
                                    @else
                                        <a href="?other_page={{ $currentOtherPage - 1 }}" class="pagination-btn">Previous</a>
                                    @endif
                                    @if($currentOtherPage >= $totalOtherPages)
                                        <button class="pagination-btn" disabled>Next</button>
                                    @else
                                        <a href="?other_page={{ $currentOtherPage + 1 }}" class="pagination-btn">Next</a>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="no-treatment">No other treatment prescribed</div>
                        @endif
                    </div>
                </div>

                <!-- Reference and Notes Section -->
                <div class="row pt-5">
                    <div class="col-lg-12 p-0">
                        <div class="card-header mb-2">
                            <div class="section-title"><h3 class="bold font-up fnf-title">Reference & Notes</h3></div>
                        </div>
                        <table class="data-table">
                            <thead>
                                <tr><th>Reference By</th><th>Refer To</th><th>Notes</th></tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td>{{ formatValue($patient->getMeta('reference_by')) }}</td>
                                    <td>{{ formatValue($patient->getMeta('referto')) }}</td>
                                    <td>{{ formatValue($patient->getMeta('notes')) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>

<!-- Image Preview Modal -->
<div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-labelledby="imagePreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imagePreviewModalLabel">Image Preview</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div class="image-preview-container">
                    <div class="default-image-preview">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="150" height="150">
                            <path d="M12 12c2.21 0 4-1.79 4-4s-1.79-4-4-4-4 1.79-4 4 1.79 4 4 4zm0 2c-2.67 0-8 1.34-8 4v2h16v-2c0-2.66-5.33-4-8-4z" fill="currentColor"/>
                        </svg>
                        <p class="text-muted mt-3">Profile Image</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

{{-- ============================================================
     INDOOR TREATMENT MODAL — NEW REDESIGNED VERSION
     ============================================================ --}}
<div class="modal fade" id="indoorTreatmentModal" tabindex="-1" aria-labelledby="indoorTreatmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('svc.profile.indoor-treatment', $patient->id) }}" method="POST" id="indoorTreatmentForm">
                @csrf

                {{-- Modal Header --}}
                <div class="modal-header">
                    <h5 class="modal-title" id="indoorTreatmentModalLabel">
                        <i class="bi bi-hospital-fill"></i> Add Indoor Treatment Details
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                {{-- Modal Body --}}
                <div class="modal-body">

                    {{-- Patient Info --}}
                    <div class="indoor-patient-info">
                        <div class="info-item"><strong>Name:</strong> {{ $patient->patient_name }}</div>
                        <div class="info-item"><strong>Age:</strong> {{ $patient->age }}</div>
                        <div class="info-item"><strong>Diagnosis:</strong> {{ $patient->diagnosis ?? 'N/A' }}</div>
                        <div class="info-item"><strong>Complaints:</strong> {{ $patient->getMeta('complain') ?? 'N/A' }}</div>
                    </div>

                    {{-- Add New Date Slot Button --}}
                    <button type="button" class="add-slot-btn" onclick="addIndoorDateSlot()">
                        <i class="bi bi-plus-lg"></i> Add New Date Slot
                    </button>

                    {{-- Slots Container --}}
                    <div id="indoorSlotsContainer">
                        @php
                            // Group existing indoor treatments by date+time
                            $indoorGroups = [];
                            if(isset($treatments['indoor']) && count($treatments['indoor']) > 0) {
                                foreach($treatments['indoor'] as $t) {
                                    $key = ($t['date'] ?? '') . '||' . ($t['time'] ?? '');
                                    $indoorGroups[$key][] = $t;
                                }
                            }
                        @endphp

                        @if(!empty($indoorGroups))
                            @foreach($indoorGroups as $groupKey => $medicines)
                                @php
                                    [$gDate, $gTime] = explode('||', $groupKey);
                                    $slotIndex = $loop->index;
                                @endphp
                                <div class="date-slot-card" data-slot="{{ $slotIndex }}">
                                    <div class="date-slot-header">
                                        <label>Date &amp; Time</label>
                                        <input type="date" name="slot_date[{{ $slotIndex }}]" value="{{ $gDate }}" required>
                                        <span class="slot-at-separator">@</span>
                                        <input type="time" name="slot_time[{{ $slotIndex }}]" value="{{ $gTime }}">
                                        <span class="medicine-count-badge">
                                            {{ count($medicines) }} {{ count($medicines) === 1 ? 'medicine' : 'medicines' }}
                                        </span>
                                        <button type="button" class="remove-slot-btn" onclick="removeIndoorSlot(this)">
                                            <i class="bi bi-x-lg"></i> Remove Slot
                                        </button>
                                    </div>
                                    <div class="date-slot-body">
                                        <div class="medicines-header">
                                            <span>Medicine</span>
                                            <span>Note</span>
                                        </div>
                                        <div class="medicine-rows-container">
                                            @foreach($medicines as $med)
                                            <div class="medicine-row">
                                                <input type="text"
                                                       name="slot_medicine[{{ $slotIndex }}][]"
                                                       value="{{ $med['medicine'] ?? '' }}"
                                                       placeholder="Medicine name"
                                                       autocomplete="off">
                                                <input type="text"
                                                       name="slot_note[{{ $slotIndex }}][]"
                                                       value="{{ $med['note'] ?? '' }}"
                                                       placeholder="Note">
                                                <button type="button" class="delete-medicine-btn" onclick="deleteMedicineRow(this)" title="Remove">
                                                    <i class="bi bi-trash3-fill" style="font-size:12px;"></i>
                                                </button>
                                            </div>
                                            @endforeach
                                        </div>
                                        <button type="button" class="add-medicine-btn" onclick="addMedicineRow(this, {{ $slotIndex }})">
                                            <i class="bi bi-plus-lg"></i> Add Medicine
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>

                {{-- Modal Footer --}}
                <div class="modal-footer justify-content-end gap-2">
                    <button type="button" class="btn-cancel-indoor" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn-save-indoor">
                        <i class="bi bi-check-lg me-1"></i> Save Treatment
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

{{-- Zoom Actions Modal --}}
<div class="modal fade" id="zoomActionsModal" tabindex="-1" aria-labelledby="zoomActionsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="max-width: 500px !important;">
            <div class="modal-header">
                <h5 class="modal-title" id="zoomActionsModalLabel">Zoom Meeting Options</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-4">
                    <label class="form-label fw-bold mb-2 text-primary">Internal Access</label>
                    <a href="" id="modalZoomJoinBtn" target="_blank" class="btn btn-primary w-100 d-flex align-items-center justify-content-center py-2" style="background: var(--color-primary); border: none;">
                        <i class="fas fa-video me-2"></i> Join Meeting Now
                    </a>
                </div>
                <hr class="my-4">
                <div>
                    <label class="form-label fw-bold mb-2 text-primary">Share With Patient</label>
                    <div class="input-group mb-3">
                        <input type="text" id="modalZoomLinkInput" class="form-control" readonly style="font-size: 13px; background: var(--bg-main); color: var(--text-primary);">
                        <button class="btn btn-outline-secondary" type="button" onclick="copyModalZoomLink()" title="Copy Link">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                    <a href="" id="modalZoomWaBtn" target="_blank" class="btn btn-success w-100 d-flex align-items-center justify-content-center py-2" style="background: #25D366; border: none; color: white;">
                        <i class="fab fa-whatsapp me-2"></i> Share via WhatsApp
                    </a>
                    <p class="text-muted small mt-2 text-center">
                        <i class="fas fa-info-circle me-1"></i> This will open WhatsApp with a pre-filled message.
                    </p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
// ===== INDOOR TREATMENT MODAL — SLOT-BASED LOGIC =====
let indoorSlotCounter = {{ isset($indoorGroups) && !empty($indoorGroups) ? count($indoorGroups) : 0 }};

/**
 * Add a new date slot card
 */
function addIndoorDateSlot() {
    const container = document.getElementById('indoorSlotsContainer');
    const slotIndex = indoorSlotCounter++;

    const card = document.createElement('div');
    card.className = 'date-slot-card';
    card.dataset.slot = slotIndex;

    card.innerHTML = `
        <div class="date-slot-header">
            <label>Date &amp; Time</label>
            <input type="date" name="slot_date[${slotIndex}]" required>
            <span class="slot-at-separator">@</span>
            <input type="time" name="slot_time[${slotIndex}]">
            <span class="medicine-count-badge">1 medicine</span>
            <button type="button" class="remove-slot-btn" onclick="removeIndoorSlot(this)">
                <i class="bi bi-x-lg"></i> Remove Slot
            </button>
        </div>
        <div class="date-slot-body">
            <div class="medicines-header">
                <span>Medicine</span>
                <span>Note</span>
            </div>
            <div class="medicine-rows-container">
                <div class="medicine-row">
                    <input type="text" name="slot_medicine[${slotIndex}][]" placeholder="Medicine name" autocomplete="off">
                    <input type="text" name="slot_note[${slotIndex}][]" placeholder="Note">
                    <button type="button" class="delete-medicine-btn" onclick="deleteMedicineRow(this)" title="Remove">
                        <i class="bi bi-trash3-fill" style="font-size:12px;"></i>
                    </button>
                </div>
            </div>
            <button type="button" class="add-medicine-btn" onclick="addMedicineRow(this, ${slotIndex})">
                <i class="bi bi-plus-lg"></i> Add Medicine
            </button>
        </div>
    `;

    container.appendChild(card);
    card.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
}

/**
 * Add a medicine row inside a slot
 */
function addMedicineRow(btn, slotIndex) {
    const card = btn.closest('.date-slot-card');
    const rowsContainer = card.querySelector('.medicine-rows-container');

    const row = document.createElement('div');
    row.className = 'medicine-row';
    row.innerHTML = `
        <input type="text" name="slot_medicine[${slotIndex}][]" placeholder="Medicine name" autocomplete="off">
        <input type="text" name="slot_note[${slotIndex}][]" placeholder="Note">
        <button type="button" class="delete-medicine-btn" onclick="deleteMedicineRow(this)" title="Remove">
            <i class="bi bi-trash3-fill" style="font-size:12px;"></i>
        </button>
    `;
    rowsContainer.appendChild(row);
    updateBadge(card);
    row.querySelector('input').focus();
}

/**
 * Delete a single medicine row
 */
function deleteMedicineRow(btn) {
    const card = btn.closest('.date-slot-card');
    const rowsContainer = card.querySelector('.medicine-rows-container');
    const rows = rowsContainer.querySelectorAll('.medicine-row');

    if (rows.length > 1) {
        btn.closest('.medicine-row').remove();
        updateBadge(card);
    } else {
        // Last row: clear inputs instead of removing
        btn.closest('.medicine-row').querySelectorAll('input').forEach(i => i.value = '');
    }
}

/**
 * Remove an entire date slot card
 */
function removeIndoorSlot(btn) {
    const container = document.getElementById('indoorSlotsContainer');
    const card = btn.closest('.date-slot-card');
    const slots = container.querySelectorAll('.date-slot-card');

    if (slots.length > 1) {
        Swal.fire({
            ...getSwalConfig('question'),
            title: 'Remove Slot?',
            text: 'Are you sure you want to remove this date slot and all its medicines?',
            showCancelButton: true,
            confirmButtonText: 'Yes, remove it',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                card.remove();
                Swal.fire({
                    ...getSwalConfig('success'),
                    title: 'Removed!',
                    text: 'The date slot has been removed.',
                    timer: 1500,
                    showConfirmButton: false
                });
            }
        });
    } else {
        // Last slot: clear all inputs
        card.querySelectorAll('input').forEach(i => i.value = '');
        updateBadge(card);
    }
}

/**
 * Update medicine count badge on a slot card
 */
function updateBadge(card) {
    const rows = card.querySelectorAll('.medicine-rows-container .medicine-row');
    const badge = card.querySelector('.medicine-count-badge');
    if (!badge) return;
    const count = rows.length;
    badge.textContent = count + (count === 1 ? ' medicine' : ' medicines');
}

// ===== OTHER FUNCTIONS =====

function openZoomModal(joinUrl, waUrl) {
    const joinBtn = document.getElementById('modalZoomJoinBtn');
    const waBtn = document.getElementById('modalZoomWaBtn');
    const linkInput = document.getElementById('modalZoomLinkInput');
    if (joinBtn) joinBtn.href = joinUrl;
    if (waBtn) waBtn.href = waUrl;
    if (linkInput) linkInput.value = joinUrl;
    const modalEl = document.getElementById('zoomActionsModal');
    if (modalEl) { const modal = new bootstrap.Modal(modalEl); modal.show(); }
}

function copyModalZoomLink() {
    const linkInput = document.getElementById('modalZoomLinkInput');
    if (!linkInput) return;
    linkInput.select();
    linkInput.setSelectionRange(0, 99999);
    document.execCommand('copy');
}

function previewPatientImage(input) {
    if (input.files && input.files[0]) {
        var reader = new FileReader();
        reader.onload = function(e) {
            var previewContainer = document.getElementById('profileImagePreview');
            previewContainer.innerHTML = '<img src="' + e.target.result + '" alt="Profile Image">';
            document.getElementById('saveImageBtn').style.display = 'inline-block';
        }
        reader.readAsDataURL(input.files[0]);
    }
}

document.addEventListener('DOMContentLoaded', function () {
    const editProfileBtn = document.querySelector('.edit-profile-btn');
    if (editProfileBtn) {
        editProfileBtn.addEventListener('click', function() {
            window.location.href = "{{ route('edit.svc.inquiry', $patient->id) }}";
        });
    }
});
</script>
@endsection