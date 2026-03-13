<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccInquiry;
use App\Models\Branch;
use App\Models\DietPlan;
use App\Models\HydraInquiry;
use App\Models\LHRInquiry;
use App\Models\MonthlyAssessment;
use App\Models\Opt;
use App\Models\OptMeta;
use App\Models\PatientInquiry;
use App\Models\Progress;
use App\Models\Nutrition;
use App\Models\Invoice;
use App\Models\PatientTransaction;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class InquiryDietChartController extends Controller
{
   public function dietChart(Request $request)
{
    $query = AccInquiry::where(function ($q) {
            $q->whereNull('delete_status')
              ->orWhere('delete_status', '0');
        })
        ->where(function ($q) {
            $q->whereNull('status_history')   
              ->orWhereJsonContains('status_history', 'Diet Chart')
              ->orWhereJsonContains('status_history', 'Active');
        });

    if ($request->filled('search')) {
        $search = $request->search;
        $query->where(function ($q) use ($search) {
            $q->where('patient_id', 'like', "%$search%")
              ->orWhere('patient_f_name', 'like', "%$search%")
              ->orWhere('phone_no', 'like', "%$search%")
              ->orWhere('address', 'like', "%$search%")
              ->orWhere('diagnosis', 'like', "%$search%");
        });
    }

    $inquiries = $query->orderBy('id', 'desc')->paginate(10);

    return view('admin.inquiry.patient_diet_chart', compact('inquiries'));
}

public function create(Request $request)
{
    $user = auth()->user();
    $userBranch = $user->user_branch;
    $isSuperadmin = $user->hasRole('Superadmin');

    $lead = null;
    $selectedStatuses = [];

    $optMeta = [];
    if ($request->id) {
        $lead = AccInquiry::find($request->id);

        if ($lead) {
            // For single status selection, use user_status as primary, fallback to status_history
            $selectedStatuses = [];
            $primaryStatus = $lead->user_status ?? null;
            
            if ($primaryStatus) {
                $selectedStatuses = [$primaryStatus];
            } else {
                // Fallback to status_history for backward compatibility
                $statusHistory = $lead->status_history ?? [];
                if (is_string($statusHistory)) {
                    $statusHistory = json_decode($statusHistory, true) ?? [];
                }
                $selectedStatuses = is_array($statusHistory) ? $statusHistory : [];
            }

            // Fetch metadata from Opt
            $opt = Opt::where('patient_id', $lead->patient_id)->first();
            if ($opt) {
                $optMetaRecords = OptMeta::where('opt_id', $opt->id)->get();
                foreach ($optMetaRecords as $meta) {
                    $optMeta[$meta->meta_key] = $meta->meta_value;
                }
            }
        }
    }

    
    $branches = Branch::where(function ($q) {
            $q->where('delete_status', '0')
              ->orWhere('delete_status', '');
        })
        ->when(!$isSuperadmin, function ($q) use ($userBranch) {
            $q->where('branch_id', $userBranch);
        })
        ->orderBy('branch_name', 'asc')
        ->get(['branch_id', 'branch_name']);

    // Fetch doctors by finding the role ID for 'Doctor' and matching it in user_role column
    $doctorRole = \DB::table('roles')->where('name', 'Doctor')->first();
    $doctorRoleId = $doctorRole ? $doctorRole->id : 6; // Fallback to 6 if not found
    $doctors = \App\Models\User::where('user_role', $doctorRoleId)->orderBy('name')->get(['id', 'name']);

    return view(
        'admin.inquiry.add_inquiry',
        compact('branches', 'lead', 'selectedStatuses', 'doctors', 'optMeta')
    );
}

    // public function create(Request $request)
    // {
    //     // dd('create method called');
    //     $lead = null;
    //     $selectedStatuses = [];

    //     if ($request->id) {
    //         $lead = AccInquiry::find($request->id);

    //         // Load existing statuses - use the casted array directly
    //         if ($lead) {
    //             $selectedStatuses = $lead->status_history ?? [];

    //             // Ensure it's always an array
    //             if (is_string($selectedStatuses)) {
    //                 $selectedStatuses = json_decode($selectedStatuses, true) ?? [];
    //             } elseif (!is_array($selectedStatuses)) {
    //                 $selectedStatuses = [];
    //             }
    //         }
    //     }

    //     $branches = Branch::all(['branch_id', 'branch_name']);

    //     return view('admin.inquiry.add_inquiry', compact('branches', 'lead', 'selectedStatuses'));
    // }

    public function getPatientsByBranch(Request $request)
{
    $branchId = $request->branch_id;
    $user = auth()->user();
    $isSuperadmin = $user->hasRole('Superadmin');
 
    if (!$branchId && !$isSuperadmin) {
        return response()->json(['success' => false], 400);
    }
 
    $patients = collect();
 
    $patients = $patients->merge(
        AccInquiry::where('delete_status', '0')
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->get([
                'id',
                'patient_id',
                'branch_id',
                'age',
                DB::raw("CONCAT(patient_f_name,' ',patient_l_name) as patient_name")
            ])
    );
 
    $patients = $patients->merge(
        PatientInquiry::withTrashed()
            ->where(function ($q) {
                $q->where('delete_status', '0')
                  ->orWhere('delete_status', '');
            })
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->get([
                'id',
                'patient_id',
                'branch_id',
                'age',
                'patient_name'
            ])
    );
    
 
 
    // dd($patients);
 
    $patients = $patients->merge(
        LHRInquiry::whereNull('deleted_at')
            ->when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->get([
                'id',
                'patient_id',
                'branch_id',
                'age',
                'patient_name'
            ])
    );
 
 
    $patients = $patients->merge(
        HydraInquiry::when($branchId, fn ($q) => $q->where('branch_id', $branchId))
            ->get([
                'id',
                'patient_id',
                'branch_id',
                'age',
                'patient_name'
            ])
    );
 
    if ($patients->isEmpty()) {
        return response()->json([
            'success' => true,
            'patients' => []
        ]);
    }
 
    return response()->json([
        'success' => true,
        'patients' => $patients->sortBy('patient_name')->values()
    ]);
}

public function store(Request $request)
    {
        try {
            $now = Carbon::now('Asia/Kolkata');

            $inquiryDateInput = $request->input('inquiry_date');
            if (is_string($inquiryDateInput) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $inquiryDateInput)) {
                try {
                    $inquiryDateInput = Carbon::createFromFormat('Y-m-d', $inquiryDateInput)->format('d/m/Y');
                } catch (\Exception $e) {
                }
            }

            $branchId = $request->input('branch') ?: (auth()->check() ? auth()->user()->user_branch : null);
            $validated = [
                'branch' => $branchId,
                'patient_f_name' => trim((string) ($request->input('patient_f_name') ?? '')) ?: 'NA',
                'patient_m_name' => trim((string) ($request->input('patient_m_name') ?? '')) ?: '',
                'patient_l_name' => trim((string) ($request->input('patient_l_name') ?? '')) ?: 'NA',
                'refrance' => trim((string) ($request->input('reference_by') ?? '')) ?: '',
                'reference_to' => trim((string) ($request->input('reference_to') ?? '')) ?: '',
                'gender' => $request->input('gender') ?: '',
                'email' => $request->input('email') ?: '',
                'phone_no' => $request->input('phone_no') ?? '',
                'age' => $request->input('age') !== null && $request->input('age') !== '' ? (int) $request->input('age') : null,
                'height' => $request->input('height') !== null && $request->input('height') !== '' ? (float) $request->input('height') : null,
                'weight' => $request->input('weight') !== null && $request->input('weight') !== '' ? (float) $request->input('weight') : null,
                'address' => $request->input('address') ?: '',
                'inquiry_date' => $inquiryDateInput ?: $now->format('d/m/Y'),
                'inquiry_time' => $request->input('inquiry_time') ?: $now->format('H:i'),
                'inquery_given_by' => $request->input('inquery_given_by') ?: '',
                'payment' => $request->input('total_payment') !== null && $request->input('total_payment') !== '' ? (float) $request->input('total_payment') : 0,
                'given_payment' => $request->input('given_payment') !== null && $request->input('given_payment') !== '' ? (float) $request->input('given_payment') : 0,
                'payment_method' => $request->input('payment_method') ?: 'Cash',
                'inquiry_foc' => $request->input('inquiry_foc') ?: null,
                'diagnosis' => $request->input('diagnosis') ?: '',
                'pod_vld_date' => $request->input('pod_vld_date') ?: null,
                'client_old_new' => $request->input('client_old_new') ?: 'New',
                'existing_patient_id' => $request->input('existing_patient_id') ?: null,
            ];

            $leadId = $request->lead_id;
            $selectedStatuses = $request->user_status ?? [];
            $existingPatientId = $request->existing_patient_id;

            // Handle single-select behavior (only one status allowed)
            if (empty($selectedStatuses)) {
                $selectedStatuses = ['Pending']; // Default to Pending if nothing selected
            }
            
            // For single-select, take only the first selected status
            $primaryStatus = $selectedStatuses[0] ?? 'Pending';
            $selectedStatuses = [$primaryStatus]; // Ensure only one status is saved

            // For editing, if branch is readonly in form, it should still pass the branch ID
            $branch = null;
            if (! empty($validated['branch'])) {
                $branch = Branch::where('branch_id', $validated['branch'])
                    ->where('delete_status', 0)
                    ->first();
            }

            if (! $branch) {
                $branch = Branch::where('delete_status', 0)->orderBy('branch_name', 'asc')->first();
            }

            if (! $branch) {
                return back()->with('error', 'Selected branch not found!')->withInput();
            }

            $bmi = null;
            if (! empty($validated['height']) && ! empty($validated['weight']) && $validated['height'] > 0) {
                $heightMeter = $validated['height'] / 100;
                $bmi = round($validated['weight'] / ($heightMeter * $heightMeter), 2);
            }

            $inquiryFoc = $request->has('inquiry_foc') ? 'Yes' : 'No';
            $payment = $inquiryFoc === 'Yes' ? 0 : ($validated['payment'] ?? 0);
            $givenPayment = $inquiryFoc === 'Yes' ? 0 : ($validated['given_payment'] ?? 0);
            $paymentMethod = $validated['payment_method'];
            $duePayment = $payment - $givenPayment;
            $clientType = $request->client_old_new ?? 'New';

            // Build patient name from parts
            $patientName = trim($validated['patient_f_name'].' '.
                           ($validated['patient_m_name'] ? $validated['patient_m_name'].' ' : '').
                           $validated['patient_l_name']);

            $inquiryData = [
                'branch' => $branch->branch_name,
                'branch_id' => $validated['branch'],
                'patient_f_name' => $validated['patient_f_name'],
                'patient_m_name' => $validated['patient_m_name'] ?? null,
                'patient_l_name' => $validated['patient_l_name'],
                'patient_name' => $patientName,
                'gender' => $validated['gender'] ?? null,
                'email' => $validated['email'] ?? null,
                'phone_no' => $validated['phone_no'] ?? null,
                'age' => $validated['age'] ?? null,
                'height' => $validated['height'] ?? null,
                'weight' => $validated['weight'] ?? null,
                'bmi' => $bmi,
                'address' => $validated['address'] ?? null,
                'refrance' => $validated['refrance'] ?? null,
                'reference_to' => $validated['reference_to'] ?? null,
                'inquiry_date' => $validated['inquiry_date'],
                'inquiry_time' => $validated['inquiry_time'],
                'inquery_given_by' => $validated['inquery_given_by'] ?? null,
                'payment' => $payment,
                'inquiry_foc' => $inquiryFoc,
                'diagnosis' => $validated['diagnosis'] ?? null,
                'pod_vld_date' => $validated['pod_vld_date'] ?? null,
                'next_followup_date' => $request->input('next_followup_date') ?: null,
                'client_old_new' => $clientType,
                'user_status' => $primaryStatus,
                'status_history' => $selectedStatuses,
                'delete_status' => '0',
            ];

            DB::beginTransaction();

            if ($leadId) {
                // UPDATE EXISTING INQUIRY
                $inquiry = AccInquiry::find($leadId);

                if (! $inquiry) {
                    DB::rollBack();

                    return back()->with('error', 'Inquiry not found!')->withInput();
                }

                $inquiry->update($inquiryData);

                // SAVE CLINICAL METADATA TO OPT/OPTMETA
                $optData = Opt::firstOrCreate(
                    ['patient_id' => $inquiry->patient_id],
                    [
                        'patient_name' => $patientName,
                        'branch_id' => $branch->branch_id,
                        'branch' => $branch->branch_name
                    ]
                );

                $clinicalFields = [
                    'pod_bmr', 'pod_calories', 'pod_undr_weight', 'pod_ovr_weight', 'pod_trg_weight',
                    'pod_bdy_lmp', 'habit', 'alcohol', 'pod_hb', 'bg_rh', 'pod_bd_date', 'pod_hbac',
                    'pod_pah', 'pa_h', 'pod_medication', 'pod_s_sholesterol', 'pod_s_triglyceride',
                    'pod_hdl', 'pod_ldl', 'pod_vldl', 's_cholesterol', 's_triglycerides', 'hdl', 'ldl', 'vldl', 'non_hdl_c', 'chol_hdl_ratio', 'pod_tsh', 'pod_t3', 'pod_t4', 'pod_vit_bp',
                    'pod_vit_d3', 'ra_test', 's_uric_acid', 'pod_sugar_fbs', 'pod_sugar_pp2bs',
                    'lead_body_weight', 'target_weight', 'over_weight', 'under_weight', 'birth_date', 'validity_date',
                    'pod_b12'
                ];

                foreach ($clinicalFields as $field) {
                    if ($request->has($field)) {
                        $optData->setMetaValue($field, $request->input($field));
                    }
                }
                
                $optData->setMetaValue('height', $validated['height'] ?? null);
                $optData->setMetaValue('weight', $validated['weight'] ?? null);
                $optData->setMetaValue('bmi', $bmi);

                // Add payment fields to optmeta
                $optData->setMetaValue('given_payment', $givenPayment);
                $optData->setMetaValue('payment_method', $paymentMethod);
                $optData->setMetaValue('due_payment', $duePayment);

                // Logic to create Invoice and Transactions
                if ($payment > 0) {
                    $this->createInquiryInvoice($inquiry, $branch, $payment, $givenPayment, $paymentMethod, $duePayment);
                }

                DB::commit();

                // Redirection logic for single-select behavior
                if ($primaryStatus === 'Pending') {
                    return redirect()->route('pending.inquiry')->with('success', 'Inquiry updated successfully!');
                } elseif ($primaryStatus === 'Joined') {
                    return redirect()->route('joined.inquiry')->with('success', 'Patient updated successfully!');
                } elseif ($primaryStatus === 'Diet Chart') {
                    return redirect()->route('diet.chart')->with('success', 'Inquiry updated successfully!');
                } else {
                    return redirect()->back()->with('success', 'Inquiry updated successfully!');
                }
            }

            // CREATE NEW INQUIRY
            if ($existingPatientId) {
                $patientId = $existingPatientId;

                $existingAccInquiry = AccInquiry::where('patient_id', $patientId)->first();
                if ($existingAccInquiry) {
                    $existingAccInquiry->update($inquiryData);
                    DB::commit();

                    return redirect()
                        ->route('diet.chart')
                        ->with('success', 'Patient updated successfully! Patient ID: '.$patientId);
                }
            } else {
                // Generate new patient ID with format SVC-00001, SVC-00002, etc.
                $branchCode = explode('-', $validated['branch'])[0]; // SVC-0001 से SVC निकालें

                // Get the last patient ID for this branch
                $lastPatient = AccInquiry::where('patient_id', 'like', $branchCode.'-%')
                    ->orderByRaw('CAST(SUBSTRING(patient_id, LOCATE("-", patient_id) + 1) AS UNSIGNED) DESC')
                    ->first();

                if ($lastPatient && $lastPatient->patient_id) {
                    // Extract the number part from last patient ID
                    $lastNumber = (int) substr($lastPatient->patient_id, strpos($lastPatient->patient_id, '-') + 1);
                    $nextNumber = $lastNumber + 1;
                } else {
                    // First patient for this branch
                    $nextNumber = 1;
                }

                // Format: SVC-00001 (5 digits with leading zeros)
                $patientId = $branchCode.'-'.str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
            }

            $inquiryData['patient_id'] = $patientId;
            $inquiry = AccInquiry::create($inquiryData);

            // SAVE CLINICAL METADATA TO OPT/OPTMETA
            $optData = Opt::firstOrCreate(
                ['patient_id' => $patientId],
                [
                    'patient_name' => $patientName,
                    'branch_id' => $branch->branch_id,
                    'branch' => $branch->branch_name
                ]
            );

            $clinicalFields = [
                'pod_bmr', 'pod_calories', 'pod_undr_weight', 'pod_ovr_weight', 'pod_trg_weight',
                'pod_bdy_lmp', 'habit', 'alcohol', 'pod_hb', 'bg_rh', 'pod_bd_date', 'pod_hbac',
                'pod_pah', 'pa_h', 'pod_medication', 'pod_s_sholesterol', 'pod_s_triglyceride',
                'pod_hdl', 'pod_ldl', 'pod_vldl', 's_cholesterol', 's_triglycerides', 'hdl', 'ldl', 'vldl', 'non_hdl_c', 'chol_hdl_ratio', 'pod_tsh', 'pod_t3', 'pod_t4', 'pod_vit_bp',
                'pod_vit_d3', 'ra_test', 's_uric_acid', 'pod_sugar_fbs', 'pod_sugar_pp2bs',
                'lead_body_weight', 'target_weight', 'over_weight', 'under_weight', 'birth_date', 'validity_date',
                'pod_b12'
            ];

            foreach ($clinicalFields as $field) {
                if ($request->has($field)) {
                    $optData->setMetaValue($field, $request->input($field));
                }
            }
            
            // Also store height, weight, bmi in optmeta for consistency
            $optData->setMetaValue('height', $validated['height'] ?? null);
            $optData->setMetaValue('weight', $validated['weight'] ?? null);
            $optData->setMetaValue('bmi', $bmi);

            // Add payment fields to optmeta
            $optData->setMetaValue('given_payment', $givenPayment);
            $optData->setMetaValue('payment_method', $paymentMethod);
            $optData->setMetaValue('due_payment', $duePayment);

            // Logic to create Invoice and Transactions
            if ($payment > 0) {
                $this->createInquiryInvoice($inquiry, $branch, $payment, $givenPayment, $paymentMethod, $duePayment);
            }

            DB::commit();

            // Redirection for new inquiries (single-select behavior)
            if ($primaryStatus === 'Pending') {
                return redirect()
                    ->route('pending.inquiry')
                    ->with('success', 'Inquiry added successfully! Patient ID: '.$patientId);
            } elseif ($primaryStatus === 'Joined') {
                return redirect()
                    ->route('joined.inquiry')
                    ->with('success', 'Patient added successfully! Patient ID: '.$patientId);
            } else { // Diet Chart
                return redirect()
                    ->route('diet.chart')
                    ->with('success', 'Inquiry added successfully! Patient ID: '.$patientId);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error saving inquiry: '.$e->getMessage());
            \Log::error('Trace: '.$e->getTraceAsString());

            return back()
                ->with('error', 'Error saving inquiry: '.$e->getMessage())
                ->withInput();
        }
    }

    public function destroy($id)
    {
        $delete = AccInquiry::where('id', $id)->update([
            'delete_status' => '1',
            'delete_by' => auth()->id(),
        ]);

        if ($delete) {
            return redirect()->back()->with('success', 'Inquiry deleted successfully');
        }

        return redirect()->back()->with('error', 'Failed to delete inquiry');
    }


    public function export(Request $request)
    {
        $query = AccInquiry::where('delete_status', '0')
            ->where(function ($q) {
                $q->where('user_status', 'Diet Chart')
                    ->orWhere('user_status', 'Active');
            });

        if ($request->has('search') && !empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('patient_id', 'like', '%' . $search . '%')
                    ->orWhere('patient_name', 'like', '%' . $search . '%')
                    ->orWhere('phone_no', 'like', '%' . $search . '%')
                    ->orWhere('address', 'like', '%' . $search . '%')
                    ->orWhere('diagnosis', 'like', '%' . $search . '%');
            });
        }

        $inquiries = $query->orderBy('id', 'desc')->get();

        if ($inquiries->isEmpty()) {
            return redirect()->route('diet.chart')->with('error', 'No inquiries found to export.');
        }

        $filename = 'diet_chart_inquiries_export_' . date('Y-m-d_H-i') . '.csv';

        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $file = fopen('php://output', 'w');

        $headers = ['Patient ID', 'Patient Name', 'Phone Number', 'Address', 'Branch', 'Diagnosis', 'Reference By', 'Inquiry By', 'Status'];
        fputcsv($file, $headers);

        foreach ($inquiries as $inquiry) {
            $row = [
                $inquiry->patient_id ?? 'N/A',
                $inquiry->patient_name ?? 'N/A',
                $inquiry->phone_no ?? 'N/A',
                $inquiry->address ?? 'N/A',
                $inquiry->branch ?? 'N/A',
                $inquiry->diagnosis ?? 'N/A',
                $inquiry->refrance ?? 'N/A',
                $inquiry->inquery_given_by ?? 'N/A',
                $inquiry->user_status ?? 'N/A',
            ];
            fputcsv($file, $row);
        }

        fclose($file);
        exit;
    }

    // public function patientProfile($id)
    // {
    //     try {
    //         // Find the patient by ID in AccInquiry
    //         $patient = AccInquiry::where('id', $id)
    //             ->where('delete_status', '0')
    //             ->firstOrFail();

    //         // Initialize variables with default values
    //         $optData = null;
    //         $optMeta = [];
    //         $programDetails = [];

    //         // Find the corresponding diet chart data from Opt table
    //         $optData = Opt::where('patient_id', $id)
    //             ->where('delete_status', '0')
    //             ->first();

    //         // Get all meta data for this Opt record if exists
    //         if ($optData) {
    //             // Fetch all meta data for this Opt record
    //             $metaRecords = OptMeta::where('opt_id', $optData->id)->get();
    //             foreach ($metaRecords as $meta) {
    //                 $optMeta[$meta->meta_key] = $meta->meta_value;
    //             }

    //             // Fetch payment program details from meta data
    //             if (isset($optMeta['selected_program']) && $optMeta['selected_program']) {
    //                 $programDetails[] = [
    //                     'program_name' => $optMeta['selected_program'] ?? '',
    //                     'session' => $optMeta['session'] ?? '',
    //                     'months' => $optMeta['months'] ?? '',
    //                     'payment_date' => $optMeta['pod_bd_date'] ?? '', // Using diet chart date as payment date
    //                     'payment_method' => $optMeta['payment_method'] ?? '',
    //                     'total' => $optMeta['total_payment'] ?? '0.00',
    //                     'discount' => $optMeta['discount_payment'] ?? '0.00',
    //                     'given' => $optMeta['given_payment'] ?? '0.00',
    //                     'due' => $optMeta['due_payment'] ?? '0.00',
    //                 ];
    //             }
    //         }

    //         // Return the patient profile view with all data
    //         return view('admin.inquiry.patient-profile', compact(
    //             'patient',
    //             'optData',
    //             'optMeta',
    //             'programDetails'
    //         ));

    //     } catch (\Exception $e) {
    //         return redirect()->route('diet.chart')
    //             ->with('error', 'Patient not found or has been deleted.');
    //     }
    // }
    
    public function dietJoinPatient($id)
    {
        try {
            // Find the patient by ID
            $patient = AccInquiry::where('id', $id)
                ->where('delete_status', '0')
                ->firstOrFail();

            // Get the first entry of this patient to fetch height, weight, and BMI
            $firstEntry = AccInquiry::where('patient_id', $patient->patient_id)
                ->where('delete_status', '0')
                ->orderBy('id', 'asc')
                ->first();

            // Extract initial measurements from first entry
            $initialMeasurements = [];
            if ($firstEntry) {
                $initialMeasurements = [
                    'height' => $firstEntry->height,
                    'weight' => $firstEntry->weight,
                    'bmi' => $firstEntry->bmi,
                    'entry_date' => $firstEntry->inquiry_date,
                ];
            }

            // Get all previous diet charts for history
            $dietHistory = Opt::where('patient_id', $patient->patient_id)
                ->where('delete_status', '0')
                ->with('meta')
                ->orderBy('created_at', 'desc')
                ->get();

            // Get latest diet chart for pre-filling
            $latestOpt = $dietHistory->first();
            $latestMeta = [];
            if ($latestOpt) {
                foreach ($latestOpt->meta as $meta) {
                    $latestMeta[$meta->meta_key] = $meta->meta_value;
                }
            }

            // Get monthly assessments for measurement history
            $measurements = MonthlyAssessment::where(function($q) use ($patient) {
                    $q->where('patient_id', $patient->patient_id)
                      ->orWhere('patient_inquiry_id', $patient->id);
                })
                ->where('delete_status', '0')
                ->orderBy('assessment_date', 'asc')
                ->get();

            // Get available programs (excluding LHR branch)
            $available_programs = \App\Models\ManageProgram::where('delete_status', '0')
                ->where(function($query) {
                    $query->where('branch', '!=', 'LHR')
                          ->orWhereNull('branch');
                })
                ->get();

            // Return the diet chart form view with all data
            return view('admin.inquiry.diet-join-patient', 
                compact('patient', 'dietHistory', 'latestOpt', 'latestMeta', 'measurements', 'available_programs', 'initialMeasurements'));
        } catch (\Exception $e) {
            return redirect()->route('diet.chart')
                ->with('error', 'Patient not found or error loading data.');
        }
    }

    public function saveDietChart(Request $request)
    {
        DB::beginTransaction();

        try {
            $optData = [
                'patient_id' => $request->patient_id,
                'patient_name' => $request->patient_name,
                'branch_id' => $request->branch_id,
                'branch' => $request->branch,
                'blood_group' => $request->blood_group,
                'delete_status' => '0',
            ];

            $latestOptId = $request->input('latest_opt_id');
            
            if ($latestOptId) {
                $opt = Opt::find($latestOptId);
                if ($opt) {
                    $opt->update($optData);
                } else {
                    $opt = Opt::create($optData);
                }
            } else {
                $opt = Opt::create($optData);
            }

            // Save all basic fields
            $basicFields = [
            'pod_bd_date',
            'pod_bmr',
            'pod_bmr_value',
            'pod_ovr_weight',
            'pod_undr_weight',
            'pod_trg_weight',
            'pod_bdy_lmp',
            'pod_calories',
            'pod_fh',
            'pod_pah', 
            'pa_h',
            'over_weight',
            'under_weight',
            'target_weight',
            'bg_rh',
            'validity_date',
            'pod_data',
            'pod_bdy_weight',
            'pod_medication',
            's_cholesterol',
            's_triglycerides',
            'hdl',
            'ldl',
            'vldl',
            'non_hdl_c',
            'chol_hdl_ratio',
            'pod_tsh',
            'pod_t3',
            'pod_t4',
            'pod_b12',
            'pod_vit_d3',
            'pod_hb',
            'pod_vit_bp',
            'pod_hbac',
            'pod_sugar_rbs',
            'pod_sugar_fbs',
            'pod_sugar_pp2bs',
            'time',
            'activity',
            'early_morning',
            'bed_time',
            'occupation',
            'breakfast',
            'lunch',
            'dinner',
            'brunch',
            'snacks',
            'early_morning_meal',
            'water_intake',
            'water_unit',
            'fasting_day',
            'habit',
            'food_choices',
            'milk',
            'salt',
            'food_allergy',
            'walking_time',
            'sleeping_time',
            'oil',
            'anything_else',
            'alcohol',
            'waking_time',
            'physical_activity',
            'fast_food',
            'position',
            'total_payment',
            'discount_payment',
            'given_payment',
            'due_payment',
            'payment_method',
            'due_date',
            'lead_body_weight',
            'birth_date',
            'next_followup_date',
            // Laboratory Investigation Fields
            's_insulin',
            'sgpt',
            's_creatinine',
            's_uric_acid',
            'ra_test',
            'usg_abdomen',
            'chest_xray',
            'mri_ct_scan'
            ];

            foreach ($basicFields as $field) {
                if ($request->has($field) && !empty($request->input($field))) {
                    $opt->setMetaValue($field, $request->input($field));
                }
            }

            if ($request->has('selected_program') && is_array($request->selected_program)) {
                $allPrograms = [];

                foreach ($request->selected_program as $index => $program) {
                    if (!empty($program)) {
                        $session = $request->session[$index] ?? '';
                        $months = $request->months[$index] ?? '';

                        $opt->setMetaValue("selected_program_{$index}", $program);
                        $opt->setMetaValue("session_{$index}", $session); 
                        $opt->setMetaValue("months_{$index}", $months);

                        // Add to programs array
                        $allPrograms[] = [
                            'program' => $program,
                            'session' => $session,
                            'months' => $months,
                            'index' => $index,
                            'created_at' => now()->format('Y-m-d H:i:s')
                        ];

                        if ($index == 0) {
                            $opt->setMetaValue('selected_program', $program);
                            $opt->setMetaValue('session', $session);
                            $opt->setMetaValue('months', $months);
                        }
                    }
                }

                // Save the complete programs array as JSON
                if (!empty($allPrograms)) {
                    $opt->setMetaValue('programs_array', json_encode($allPrograms));
                }
            }

            // Handle file uploads
            $this->handleFileUploads($request, $opt);

            DB::commit();


            $message = $request->has('latest_opt_id') && !empty($request->latest_opt_id) ? 'Diet chart updated successfully!' : 'Diet chart saved successfully!';
            
            return redirect()->route('diet.chart')
                ->with('success', $message);
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error: ' . $e->getMessage())
                ->withInput();
        }
    }

    public function editDietJoinPatient($id)
    {
        try {
            $patient = AccInquiry::where('id', $id)
                ->where('delete_status', '0')
                ->firstOrFail();

            // Get existing diet chart data
            $optData = Opt::where('patient_id', $patient->patient_id)->first();
            
            if (!$optData) {
                return redirect()->route('diet.join.patient', ['id' => $id])
                    ->with('error', 'No diet chart found for this patient. Please create one first.');
            }

            // Get all meta data
            $optMeta = [];
            $optMetaRecords = $optData->meta()->get();
            foreach ($optMetaRecords as $meta) {
                $optMeta[$meta->meta_key] = $meta->meta_value;
            }

            // Get all previous diet charts for history
            $dietHistory = Opt::where('patient_id', $patient->patient_id)
                ->where('delete_status', '0')
                ->with('meta')
                ->orderBy('created_at', 'desc')
                ->get();

            // Get monthly assessments for measurement history
            $measurements = MonthlyAssessment::where(function($q) use ($patient) {
                    $q->where('patient_id', $patient->patient_id)
                      ->orWhere('patient_inquiry_id', $patient->id);
                })
                ->where('delete_status', '0')
                ->orderBy('assessment_date', 'asc')
                ->get();

            // Get available programs
            $available_programs = \App\Models\ManageProgram::where('delete_status', '0')->get();

            // Return the edit diet chart form view with all data
            return view('admin.inquiry.edit-diet-join-patient', compact('patient', 'optData', 'optMeta', 'dietHistory', 'measurements', 'available_programs'));
        } catch (\Exception $e) {
            return redirect()->route('diet.chart')
                ->with('error', 'Patient not found or has been deleted.');
        }
    }

    public function updateDietChart(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            // Find existing diet chart record
            $opt = Opt::findOrFail($id);

            // Update basic opt data if needed
            $optData = [
                'patient_id' => $request->patient_id,
                'patient_name' => $request->patient_name,
                'branch_id' => $request->branch_id,
                'branch' => $request->branch,
                'blood_group' => $request->blood_group,
            ];

            $opt->update($optData);

            // Save all basic fields including laboratory investigation
            $basicFields = [
                'pod_bd_date',
                'pod_bmr',
                'pod_bmr_value',
                'pod_ovr_weight',
                'pod_undr_weight',
                'pod_trg_weight',
                'pod_bdy_lmp',
                'pod_calories',
                'pod_fh',
                'pod_pah', 
                'pa_h',
                'over_weight',
                'under_weight',
                'target_weight',
                'bg_rh',
                'validity_date',
                'pod_data',
                'pod_bdy_weight',
                'pod_medication',
                's_cholesterol',
                's_triglycerides',
                'hdl',
                'ldl',
                'vldl',
                'non_hdl_c',
                'chol_hdl_ratio',
                'pod_tsh',
                'pod_t3',
                'pod_t4',
                'pod_b12',
                'pod_vit_d3',
                'pod_hb',
                'pod_vit_bp',
                'pod_hbac',
                'pod_sugar_rbs',
                'pod_sugar_fbs',
                'pod_sugar_pp2bs',
                'time',
                'activity',
                'early_morning',
                'bed_time',
                'occupation',
                'breakfast',
                'lunch',
                'dinner',
                'brunch',
                'snacks',
                'early_morning_meal',
                'water_intake',
                'water_unit',
                'fasting_day',
                'habit',
                'food_choices',
                'milk',
                'salt',
                'food_allergy',
                'walking_time',
                'sleeping_time',
                'oil',
                'anything_else',
                'alcohol',
                'position',
                'total_payment',
                'discount_payment',
                'given_payment',
                'due_payment',
                'payment_method',
                'due_date',
                'lead_body_weight',
                'birth_date',
                'next_followup_date',
                // Laboratory Investigation Fields
                's_insulin',
                'sgpt',
                's_creatinine',
                's_uric_acid',
                'ra_test',
                'usg_abdomen',
                'chest_xray',
                'mri_ct_scan'
                ];

            foreach ($basicFields as $field) {
                if ($request->has($field)) {
                    // Update meta value - even if empty, to allow clearing values
                    $opt->setMetaValue($field, $request->input($field));
                }
            }

            // Handle program updates
            if ($request->has('selected_program') && is_array($request->selected_program)) {
                // Clear existing program meta data
                for ($i = 0; $i < 10; $i++) {
                    $opt->deleteMeta("selected_program_{$i}");
                    $opt->deleteMeta("session_{$i}");
                    $opt->deleteMeta("months_{$i}");
                }

                // Add new program data
                foreach ($request->selected_program as $index => $program) {
                    if (!empty($program)) {
                        $session = $request->session[$index] ?? '';
                        $months = $request->months[$index] ?? '';

                        $opt->setMetaValue("selected_program_{$index}", $program);
                        $opt->setMetaValue("session_{$index}", $session); 
                        $opt->setMetaValue("months_{$index}", $months);

                        if ($index == 0) {
                            $opt->setMetaValue('selected_program', $program);
                            $opt->setMetaValue('session', $session);
                            $opt->setMetaValue('months', $months);
                        }
                    }
                }
            }

            // Handle file uploads
            $this->handleFileUploads($request, $opt);

            DB::commit();

            return redirect()->route('patient.profile', ['id' => $opt->patient_id])
                ->with('success', 'Diet chart updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Error updating diet chart: ' . $e->getMessage())
                ->withInput();
        }
    }

    private function handleFileUploads($request, $opt)
    {
        $beforeFolder = public_path('before');
        $afterFolder = public_path('after');

        if (!file_exists($beforeFolder)) mkdir($beforeFolder, 0755, true);
        if (!file_exists($afterFolder)) mkdir($afterFolder, 0755, true);



        // Additional pictures
        for ($i = 1; $i <= 5; $i++) {
            // Before pictures
            $beforeKey = 'before_picture_' . $i;
            if ($request->hasFile($beforeKey)) {
                $file = $request->file($beforeKey);
                $fileName = 'before_' . $i . '_' . time() . '_' . $opt->id . '.' . $file->getClientOriginalExtension();
                $file->move($beforeFolder, $fileName);
                $opt->setMetaValue($beforeKey, $fileName);
            }

            // After pictures
            $afterKey = 'after_picture_' . $i;
            if ($request->hasFile($afterKey)) {
                $file = $request->file($afterKey);
                $fileName = 'after_' . $i . '_' . time() . '_' . $opt->id . '.' . $file->getClientOriginalExtension();
                $file->move($afterFolder, $fileName);
                $opt->setMetaValue($afterKey, $fileName);
            }
        }
    }
// public function patientProfile($id)
// {
//     try {
//         // Find the patient by ID in AccInquiry
//         $patient = AccInquiry::where('id', $id)
//             ->where('delete_status', '0')
//             ->firstOrFail();

//         $optData = null;
//         $optMeta = [];
//         $programDetails = [];
//         $monthlyAssessments = [];
//         $progressReports = [];
//         $beforeImages = [];
//         $afterImages = [];

//         // IMPORTANT: paginator default
//         $dietPlans = DietPlan::whereRaw('1 = 0')->paginate(5);

//         // $optData = OptMeta::where('id', $id)->first();
//         $optData = Opt::where('patient_id', $patient->patient_id)->first();


//         if ($optData) {
//    $optMetaRecords = $optData->meta()->get();
//     foreach ($optMetaRecords as $meta) {
//         $optMeta[$meta->meta_key] = $meta->meta_value;
//     }

//     $programDetails = $this->getAllProgramDetails($optData->id, $optMeta);
//     $beforeImages = $this->getAllImages($optData->id, 'before');
//     $afterImages = $this->getAllImages($optData->id, 'after');
//         }

//         $patientId = $patient->patient_id ?? null;

//         if ($patientId) {
//             $monthlyAssessments = MonthlyAssessment::where('patient_inquiry_id', $id)
//                 ->active()
//                 ->get();
//         }

//         if ($patientId) {
//             $progressReports = Progress::where('patient_id', $patientId)
//                 ->where('delete_status', '0')
//                 ->orderBy('date', 'desc')
//                 ->orderBy('time', 'desc')
//                 ->get();
//         }

//         // Method 1
//         $dietPlans = DietPlan::where('patient_id', $id)
//             ->orderBy('date', 'desc')
//             ->orderBy('created_at', 'desc')
//             ->paginate(5)
//             ->withQueryString();

//         // Method 2
//         if ($dietPlans->isEmpty() && $patientId) {
//             $dietPlans = DietPlan::where('patient_id', $patientId)
//                 ->orderBy('date', 'desc')
//                 ->orderBy('created_at', 'desc')
//                 ->paginate(5)
//                 ->withQueryString();
//         }

//         // Method 3
//         if ($dietPlans->isEmpty() && $patient->patient_name) {
//             $dietPlans = DietPlan::where('patient_name', $patient->patient_name)
//                 ->orderBy('date', 'desc')
//                 ->orderBy('created_at', 'desc')
//                 ->paginate(5)
//                 ->withQueryString();
//         }

//         // Method 4
//         if ($dietPlans->isEmpty()) {
//             $dietPlans = DietPlan::where(function ($query) use ($id, $patientId) {
//                 $query->where('patient_id', $id)
//                     ->orWhere('patient_id', $patientId)
//                     ->orWhere('patient_id', 'like', '%' . $id . '%');
//             })
//                 ->orderBy('date', 'desc')
//                 ->orderBy('created_at', 'desc')
//                 ->paginate(5)
//                 ->withQueryString();
//         }

//         // Debug log
//         \Log::info('Patient Profile - Diet Plans:', [
//             'patient_inquiry_id' => $id,
//             'patient_id_from_table' => $patientId,
//             'patient_name' => $patient->patient_name,
//             'found_diet_plans_count' => $dietPlans->count(),
//         ]);


//         return view('admin.inquiry.patient-profile', compact(
//             'patient',
//             'optData',
//             'optMeta',
//             'programDetails',
//             'monthlyAssessments',
//             'progressReports',
//             'beforeImages',
//             'afterImages',
//             'dietPlans'
//         ));
//     } catch (\Exception $e) {
//         \Log::error('Patient profile error: ' . $e->getMessage());
//         return redirect()->route('diet.chart')
//             ->with('error', 'Patient not found or has been deleted.');
//     }
// }

public function patientProfile($id)
{
    try {
        // Find the patient by ID in AccInquiry
        $patient = AccInquiry::where('id', $id)
            ->where('delete_status', '0')
            ->firstOrFail();
        
        $patientId = $patient->patient_id;
 
        $optData = null;
        $optMeta = [];
        $programDetails = [];
        $monthlyAssessments = [];
        $progressReports = [];
        $beforeImages = [];
        $afterImages = [];
        $nutritionData = null;
 
        // Get patient's diet plans with pagination
        $dietPlans = DietPlan::where(function ($query) use ($id, $patientId) {
            $query->where('patient_id', $id)
                  ->orWhere('patient_id', $patientId);
        })->orderBy('date', 'desc')
        ->paginate(5);
 
        // Always use the latest (most recently created) diet chart record for this patient
        $optData = Opt::where('patient_id', $patient->patient_id)
            ->where(function ($q) {
                $q->whereNull('delete_status')
                  ->orWhere('delete_status', '')
                  ->orWhere('delete_status', '0');
            })
            ->orderBy('created_at', 'desc')
            ->first();

 
        if ($optData) {
            $optMetaRecords = $optData->meta()->get();
            foreach ($optMetaRecords as $meta) {
                $optMeta[$meta->meta_key] = $meta->meta_value;
            }

            $programDetails = $this->getAllProgramDetails($optData->id, $optMeta);
            $beforeImages = $this->getAllImages($optData->id, 'before');
            $afterImages = $this->getAllImages($optData->id, 'after');
        }

        // If latest opt doesn't have profile_image, fall back to any opt's profile_image for this patient_id
        if (empty($optMeta['profile_image'])) {
            $optIds = Opt::where('patient_id', $patient->patient_id)
                ->where(function ($q) {
                    $q->whereNull('delete_status')
                      ->orWhere('delete_status', '')
                      ->orWhere('delete_status', '0');
                })
                ->pluck('id');

            if ($optIds->isNotEmpty()) {
                $fallbackProfileImage = OptMeta::whereIn('opt_id', $optIds)
                    ->where('meta_key', 'profile_image')
                    ->orderByDesc('id')
                    ->value('meta_value');

                if ($fallbackProfileImage) {
                    $optMeta['profile_image'] = $fallbackProfileImage;

                    // Also sync it onto the latest Opt so list pages (which read latest Opt) show it too.
                    if ($optData) {
                        $optData->setMetaValue('profile_image', $fallbackProfileImage);
                    }
                }
            }
        }

        $patientId = $patient->patient_id ?? null;

        // Fetch nutrition data for ALL diet plans with menu breakdown
        $dietPlansWithNutrition = [];
        
        // Get ALL patient's diet plans
        $allPatientDietPlans = DietPlan::where(function ($query) use ($id, $patientId) {
            $query->where('patient_id', $id)
                  ->orWhere('patient_id', $patientId);
        })->whereNotNull('time_search_menus')
        ->orderBy('date', 'desc')
        ->get(); // Get ALL diet plans

        if ($allPatientDietPlans->isNotEmpty()) {
            foreach ($allPatientDietPlans as $dietPlan) {
                $planNutrition = [
                    'diet_plan_id' => $dietPlan->id,
                    'diet_name' => $dietPlan->diet_name ?? 'Unnamed Diet Plan',
                    'date' => $dietPlan->date,
                    'total_nutrition' => [
                        'protein' => 0,
                        'total_folates' => 0,
                        'carbohydrate' => 0,
                        'calcium' => 0,
                        'insoluable_fiber' => 0,
                    ],
                    'menu_items' => []
                ];
                
                if (!empty($dietPlan->time_search_menus)) {
                    $menuData = json_decode($dietPlan->time_search_menus, true);
                    
                    if (isset($menuData['menu'])) {
                        $menuItems = json_decode($menuData['menu'], true);
                        
                        if (is_array($menuItems)) {
                            foreach ($menuItems as $timeSlot) {
                                if (isset($timeSlot['menu']) && is_array($timeSlot['menu'])) {
                                    foreach ($timeSlot['menu'] as $foodItem) {
                                        if (!empty($foodItem)) {
                                            // Find nutrition data for this food item
                                            $foodNutrition = Nutrition::where('nutrition_name', 'like', '%' . $foodItem . '%')
                                                ->where(function($query) {
                                                    $query->whereNull('delete_status')
                                                          ->orWhere('delete_status', '')
                                                          ->orWhere('delete_status', '0');
                                                })
                                                ->first();
                                            
                                            if ($foodNutrition) {
                                                // Get quantity from menu data (default to 1 if not specified)
                                                $quantity = 1;
                                                if (isset($menuData['menu'][$mealType]['quantity']) && is_numeric($menuData['menu'][$mealType]['quantity'])) {
                                                    $quantity = $menuData['menu'][$mealType]['quantity'];
                                                }
                                                
                                                // Add to plan totals (multiply by quantity)
                                                $planNutrition['total_nutrition']['protein'] += ($foodNutrition->protein ?? 0) * $quantity;
                                                $planNutrition['total_nutrition']['total_folates'] += ($foodNutrition->total_folates ?? 0) * $quantity;
                                                $planNutrition['total_nutrition']['carbohydrate'] += ($foodNutrition->carbohydrate ?? 0) * $quantity;
                                                $planNutrition['total_nutrition']['calcium'] += ($foodNutrition->calcium ?? 0) * $quantity;
                                                $planNutrition['total_nutrition']['insoluable_fiber'] += ($foodNutrition->insoluable_fiber ?? 0) * $quantity;
                                                
                                                // Add to menu items list (multiply by quantity)
                                                $planNutrition['menu_items'][] = [
                                                    'name' => $foodItem,
                                                    'quantity' => $quantity,
                                                    'protein' => ($foodNutrition->protein ?? 0) * $quantity,
                                                    'total_folates' => ($foodNutrition->total_folates ?? 0) * $quantity,
                                                    'carbohydrate' => ($foodNutrition->carbohydrate ?? 0) * $quantity,
                                                    'calcium' => ($foodNutrition->calcium ?? 0) * $quantity,
                                                    'insoluable_fiber' => ($foodNutrition->insoluable_fiber ?? 0) * $quantity,
                                                ];
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                
                $dietPlansWithNutrition[] = $planNutrition;
            }
        }
 
        if ($patientId) {
            $monthlyAssessments = MonthlyAssessment::where('patient_inquiry_id', $id)
                ->active()
                ->get();
        }

        if ($patientId) {
            $progressReports = Progress::where('patient_id', $patientId)
                ->where('delete_status', '0')
                ->orderBy('date', 'desc')
                ->orderBy('time', 'desc')
                ->get();
        }
 
        // Debug log
        \Log::info('Patient Profile - Diet Plans:', [
            'patient_inquiry_id' => $id,
            'patient_id_from_table' => $patientId,
            'patient_name' => $patient->patient_name,
            'found_diet_plans_count' => $dietPlans->count(),
        ]);
// dd($beforeImages, $afterImages);
 
        return view('admin.inquiry.patient-profile', compact(
            'patient',
            'optData',
            'optMeta',
            'programDetails',
            'monthlyAssessments',
            'progressReports',
            'beforeImages',
            'afterImages',
            'dietPlans',
            'dietPlansWithNutrition'
        ));
    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        \Log::error('Patient not found: ' . $e->getMessage());
        \Log::error('Looking for ID: ' . $id);
        return redirect()->route('diet.chart')
            ->with('error', "Patient with ID {$id} not found or has been deleted.");
    } catch (\Exception $e) {
        \Log::error('Patient profile error: ' . $e->getMessage());
        \Log::error('Stack trace: ' . $e->getTraceAsString());
        return redirect()->route('diet.chart')
            ->with('error', 'Error loading patient profile: ' . $e->getMessage());
    }
}

/**
 * Update the patient's profile image.
 */
public function updateProfileImage(Request $request, $id)
{
    try {
        $patient = \App\Models\AccInquiry::findOrFail($id);

        $request->validate([
            'profile_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('profile_image')) {
            // Store the profile image on the latest Opt (so patient profile page + list stay in sync)
            $optData = \App\Models\Opt::where('patient_id', $patient->patient_id)
                ->where(function ($q) {
                    $q->whereNull('delete_status')
                      ->orWhere('delete_status', '')
                      ->orWhere('delete_status', '0');
                })
                ->orderBy('created_at', 'desc')
                ->first();

            if (! $optData) {
                $optData = \App\Models\Opt::create([
                    'patient_id' => $patient->patient_id,
                    'patient_name' => $patient->patient_name,
                    'branch_id' => $patient->branch_id,
                    'branch' => $patient->branch,
                    'delete_status' => '0',
                ]);
            } else {
                // Keep opt basic info updated (optional but helpful)
                $optData->update([
                    'patient_name' => $patient->patient_name,
                    'branch_id' => $patient->branch_id,
                    'branch' => $patient->branch,
                ]);
            }

            // Delete old image if exists
            $oldImage = $optData->getMetaValue('profile_image');
            if ($oldImage && file_exists(public_path($oldImage))) {
                unlink(public_path($oldImage));
            }

            $image = $request->file('profile_image');
            $filename = 'patient_' . $id . '_' . time() . '.' . $image->getClientOriginalExtension();
            $image->move(public_path('uploads/patients'), $filename);
            $path = 'uploads/patients/' . $filename;

            $optData->setMetaValue('profile_image', $path);

            return back()->with('success', 'Profile image updated successfully.');
        }

        return back()->with('error', 'No image file provided.');
    } catch (\Exception $e) {
        \Log::error('Error updating patient profile image: ' . $e->getMessage());
        return back()->with('error', 'Error updating profile image: ' . $e->getMessage());
    }
}
 
private function getAllImages($optId, $type = 'before')
{
    \Log::info('getAllImages() START', [
        'opt_id' => $optId,
        'type' => $type
    ]);

    $images = [];

    // 🔍 LOG ALL META FOR THIS OPT
    $allMeta = OptMeta::where('opt_id', $optId)->get(['meta_key', 'meta_value']);
    \Log::info('ALL META RECORDS', $allMeta->toArray());
// dd($allMeta);
    // 🔹 PROFILE IMAGE (before_profile_photo / after_profile_photo)
    $profileKey = $type === 'before' ? 'before_profile_photo' : 'after_profile_photo';

    $profileImage = OptMeta::where('opt_id', $optId)
        ->where('meta_key', $profileKey)
        ->first();

    \Log::info('PROFILE IMAGE META', [
        'key' => $profileKey,
        'record' => $profileImage
    ]);

    if ($profileImage) {
        $folder = $type === 'before' ? 'before' : 'after';
        $fullPath = public_path($folder . '/' . $profileImage->meta_value);

        \Log::info('PROFILE IMAGE FILE CHECK', [
            'path' => $fullPath,
            'exists' => file_exists($fullPath)
        ]);

        if (file_exists($fullPath)) {
            $images[] = [
                'path' => asset($folder . '/' . $profileImage->meta_value),
                'weight' => null,
                'height' => null,
                'date' => null,
                'notes' => null,
                'filename' => $profileImage->meta_value,
                'index' => 0
            ];
        }
    }

    // 🔁 MULTI IMAGES LOOP
    for ($i = 1; $i <= 20; $i++) {

        $imageKey  = "{$type}_picture_{$i}";
        $weightKey = "{$type}_weight_{$i}";
        $heightKey = "{$type}_height_{$i}";
        $dateKey   = "{$type}_date_{$i}";
        $notesKey  = "{$type}_notes_{$i}";

        $imageMeta = OptMeta::where('opt_id', $optId)
            ->where('meta_key', $imageKey)
            ->first();

        \Log::info("CHECK IMAGE META {$imageKey}", [
            'exists' => (bool) $imageMeta,
            'value' => $imageMeta?->meta_value
        ]);

        if (!$imageMeta || empty($imageMeta->meta_value)) {
            continue;
        }

        $folder = $type === 'before' ? 'before' : 'after';
        $fullPath = public_path($folder . '/' . $imageMeta->meta_value);

        \Log::info("FILE CHECK {$imageKey}", [
            'path' => $fullPath,
            'exists' => file_exists($fullPath)
        ]);

        if (!file_exists($fullPath)) {
            continue;
        }

        $images[] = [
            'path' => asset($folder . '/' . $imageMeta->meta_value),
            'weight' => OptMeta::where('opt_id', $optId)->where('meta_key', $weightKey)->value('meta_value'),
            'height' => OptMeta::where('opt_id', $optId)->where('meta_key', $heightKey)->value('meta_value'),
            'date' => OptMeta::where('opt_id', $optId)->where('meta_key', $dateKey)->value('meta_value'),
            'notes' => OptMeta::where('opt_id', $optId)->where('meta_key', $notesKey)->value('meta_value'),
            'filename' => $imageMeta->meta_value,
            'index' => $i
        ];
    }

    // ✅ FINAL RESULT LOG
    \Log::info('FINAL IMAGES RETURNED', [
        'count' => count($images),
        'images' => $images
    ]);

    return $images;
}


    // public function patientProfile($id)
    // {
    //     try {
    //         // Find the patient by ID in AccInquiry
    //         $patient = AccInquiry::where('id', $id)
    //             ->where('delete_status', '0')
    //             ->firstOrFail();
          
    //         $optData = null;
    //         $optMeta = [];
    //         $programDetails = [];
    //         $monthlyAssessments = [];
    //         $progressReports = [];
    //         $beforeImages = [];
    //         $afterImages = [];
    //         $dietPlans = collect(); // Initialize as empty collection

    //         $optData = Opt::where('id', $id)->first();

    //         if ($optData) {
    //             $metaRecords = OptMeta::where('opt_id', $optData->id)->get();
    //             foreach ($metaRecords as $meta) {
    //                 $optMeta[$meta->meta_key] = $meta->meta_value;
    //             }

    //             $programDetails = $this->getAllProgramDetails($optData->id, $optMeta);

    //             $beforeImages = $this->getAllImages($optData->id, 'before');

    //             $afterImages = $this->getAllImages($optData->id, 'after');
    //         }

    //         $patientId = $patient->patient_id ?? null;

    //         if ($patientId) {
    //             $monthlyAssessments = MonthlyAssessment::where('patient_inquiry_id', $id)
    //                 ->active()
    //                 ->get();
    //         }

    //         if ($patientId) {
    //             // Get progress reports
    //             $progressReports = Progress::where('patient_id', $patientId)
    //                 ->where('delete_status', '0')
    //                 ->orderBy('date', 'desc')
    //                 ->orderBy('time', 'desc')
    //                 ->get();
    //         }
    //         // dd($patientId);
    //         //    dd($patientId);
    //         $dietPlans = DietPlan::where('patient_id', $id)
    //             ->orderBy('date', 'desc')
    //             ->orderBy('created_at', 'desc')
    //             ->get();
    //         // Method 2: If no results, try searching by patient_id (from patient_inquiry table)
    //         if ($dietPlans->isEmpty() && $patientId) {
    //             $dietPlans = DietPlan::where('patient_id', $patientId)
    //                 ->orderBy('date', 'desc')
    //                 ->orderBy('created_at', 'desc')
    //                 ->get();
    //         }

    //         // Method 3: If still no results, try searching by patient name
    //         if ($dietPlans->isEmpty() && $patient->patient_name) {
    //             $dietPlans = DietPlan::where('patient_name', $patient->patient_name)
    //                 ->orderBy('date', 'desc')
    //                 ->orderBy('created_at', 'desc')
    //                 ->get();
    //         }

    //         // Method 4: As a last resort, check if patient_id matches any string in database
    //         if ($dietPlans->isEmpty()) {
    //             $dietPlans = DietPlan::where(function ($query) use ($id, $patientId) {
    //                 $query->where('patient_id', $id)
    //                     ->orWhere('patient_id', $patientId)
    //                     ->orWhere('patient_id', 'like', '%' . $id . '%');
    //             })
    //                 ->orderBy('date', 'desc')
    //                 ->orderBy('created_at', 'desc')
    //                 ->get();
    //         }

    //         // Debug logging to check what we found
    //         \Log::info('Patient Profile - Diet Plans:', [
    //             'patient_inquiry_id' => $id,
    //             'patient_id_from_table' => $patientId,
    //             'patient_name' => $patient->patient_name,
    //             'found_diet_plans_count' => $dietPlans->count(),
    //             'diet_plans' => $dietPlans->map(function ($plan) {
    //                 return [
    //                     'id' => $plan->id,
    //                     'patient_id' => $plan->patient_id,
    //                     'patient_name' => $plan->patient_name,
    //                     'diet_name' => $plan->diet_name,
    //                     'date' => $plan->date
    //                 ];
    //             })->toArray()
    //         ]);

    //         // Return the patient profile view with all data
    //         return view('admin.inquiry.patient-profile', compact(
    //             'patient',
    //             'optData',
    //             'optMeta',
    //             'programDetails',
    //             'monthlyAssessments',
    //             'progressReports',
    //             'beforeImages',
    //             'afterImages',
    //             'dietPlans'
    //         ));
    //     } catch (\Exception $e) {
    //         \Log::error('Patient profile error: ' . $e->getMessage());
    //         return redirect()->route('diet.chart')
    //             ->with('error', 'Patient not found or has been deleted.');
    //     }
    // }
    // New method to get all images with dimensions
    // private function getAllImages($optId, $type = 'before')
    // {
    //     $images = [];
    // $profileImage = OptMeta::where('opt_id', $optId)
    //     ->where('meta_key', 'before_profile_photo')
    //     ->first();

    // if ($profileImage && file_exists(public_path('before/' . $profileImage->meta_value))) {
    //     $images[] = asset('before/' . $profileImage->meta_value);
    // }

    //     for ($i = 1; $i <= 20; $i++) {
    //         $imageKey = $type . '_picture_' . $i;
    //         $weightKey = $type . '_weight_' . $i;
    //         $heightKey = $type . '_height_' . $i;
    //         $dateKey = $type . '_date_' . $i;
    //         $notesKey = $type . '_notes_' . $i;

    //         // Get image filename
    //         $imageMeta = OptMeta::where('opt_id', $optId)
    //             ->where('meta_key', $imageKey)
    //             ->first();
    //             // dd($imageMeta);

    //         if ($imageMeta && !empty($imageMeta->meta_value)) {
    //             $imagePath = null;
    //             $folder = $type == 'before' ? 'before' : 'after';

    //             // Check if file exists
    //             if (file_exists(public_path($folder . '/' . $imageMeta->meta_value))) {
    //                 $imagePath = asset($folder . '/' . $imageMeta->meta_value);
    //             }

    //             // Get dimensions and other data
    //             $weight = OptMeta::where('opt_id', $optId)
    //                 ->where('meta_key', $weightKey)
    //                 ->value('meta_value');

    //             $height = OptMeta::where('opt_id', $optId)
    //                 ->where('meta_key', $heightKey)
    //                 ->value('meta_value');

    //             $date = OptMeta::where('opt_id', $optId)
    //                 ->where('meta_key', $dateKey)
    //                 ->value('meta_value');

    //             $notes = OptMeta::where('opt_id', $optId)
    //                 ->where('meta_key', $notesKey)
    //                 ->value('meta_value');

    //             if ($imagePath) {
    //                 $images[] = [
    //                     'path' => $imagePath,
    //                     'weight' => $weight,
    //                     'height' => $height,
    //                     'date' => $date,
    //                     'notes' => $notes,
    //                     'filename' => $imageMeta->meta_value,
    //                     'index' => $i
    //                 ];
    //             }
    //         }
    //     }

    //     // For backward compatibility, also check single image
    //     if (empty($images) && $type == 'before') {
    //         $singleImage = OptMeta::where('opt_id', $optId)
    //             ->where('meta_key', 'before_profile_photo')
    //             ->first();

    //         if ($singleImage && !empty($singleImage->meta_value)) {
    //             if (file_exists(public_path('before/' . $singleImage->meta_value))) {
    //                 $images[] = [
    //                     'path' => asset('before/' . $singleImage->meta_value),
    //                     'weight' => null,
    //                     'height' => null,
    //                     'date' => null,
    //                     'notes' => null,
    //                     'filename' => $singleImage->meta_value,
    //                     'index' => 1
    //                 ];
    //             }
    //         }
    //     } elseif (empty($images) && $type == 'after') {
    //         $singleImage = OptMeta::where('opt_id', $optId)
    //             ->where('meta_key', 'after_profile_photo')
    //             ->first();

    //         if ($singleImage && !empty($singleImage->meta_value)) {
    //             if (file_exists(public_path('after/' . $singleImage->meta_value))) {
    //                 $images[] = [
    //                     'path' => asset('after/' . $singleImage->meta_value),
    //                     'weight' => null,
    //                     'height' => null,
    //                     'date' => null,
    //                     'notes' => null,
    //                     'filename' => $singleImage->meta_value,
    //                     'index' => 1
    //                 ];
    //             }
    //         }
    //     }

    //     return $images;
    // }

    // Helper method to get all program details
    private function getAllProgramDetails($optId, $optMeta)
    {
        $programDetails = [];

        $indexedPrograms = OptMeta::where('opt_id', $optId)
            ->where('meta_key', 'LIKE', 'selected_program_%')
            ->orderBy('meta_key')
            ->get();

        foreach ($indexedPrograms as $programMeta) {
            $key = $programMeta->meta_key;
            if (strpos($key, 'selected_program_') === 0) {
                $index = substr($key, strlen('selected_program_'));

                if (is_numeric($index)) {
                    $sessionMeta = OptMeta::where('opt_id', $optId)
                        ->where('meta_key', 'session_' . $index)
                        ->first();

                    $session = $sessionMeta ? $sessionMeta->meta_value : '';

                    $monthsMeta = OptMeta::where('opt_id', $optId)
                        ->where('meta_key', 'months_' . $index)
                        ->first();

                    $months = $monthsMeta ? $monthsMeta->meta_value : '';

                    $programDetails[] = [
                        'program_name' => $programMeta->meta_value,
                        'session' => $session,
                        'months' => $months,
                        'position' => $optMeta['position'] ?? '',
                        'payment_date' => $optMeta['pod_bd_date'] ?? date('Y-m-d'),
                        'payment_method' => $optMeta['payment_method'] ?? '',
                        'total' => $optMeta['total_payment'] ?? '0.00',
                        'discount' => $optMeta['discount_payment'] ?? '0.00',
                        'given' => $optMeta['given_payment'] ?? '0.00',
                        'due' => $optMeta['due_payment'] ?? '0.00',
                        'due_date' => $optMeta['due_date'] ?? '',
                        'original_index' => $index,
                    ];
                }
            }
        }

        if (empty($programDetails)) {
            $singleProgram = OptMeta::where('opt_id', $optId)
                ->where('meta_key', 'selected_program')
                ->first();

            if ($singleProgram) {
                $session = OptMeta::where('opt_id', $optId)
                    ->where('meta_key', 'session')
                    ->value('meta_value');

                $months = OptMeta::where('opt_id', $optId)
                    ->where('meta_key', 'months')
                    ->value('meta_value');

                $programDetails[] = [
                    'program_name' => $singleProgram->meta_value,
                    'session' => $session ?? '',
                    'months' => $months ?? '',
                    'position' => $optMeta['position'] ?? '',
                    'payment_date' => $optMeta['pod_bd_date'] ?? date('Y-m-d'),
                    'payment_method' => $optMeta['payment_method'] ?? '',
                    'total' => $optMeta['total_payment'] ?? '0.00',
                    'discount' => $optMeta['discount_payment'] ?? '0.00',
                    'given' => $optMeta['given_payment'] ?? '0.00',
                    'due' => $optMeta['due_payment'] ?? '0.00',
                    'due_date' => $optMeta['due_date'] ?? '',
                    'original_index' => 0,
                ];
            }
        }

        return $programDetails;
    }

    // Add these methods to your InquiryDietChartController
    public function updatePaymentProgram(Request $request)
    {
        try {
            \Log::info('Update Payment Program Request:', $request->all());

            $patientId = $request->patient_id;
            $paymentIndex = $request->payment_index;

            // Find the patient
            $patient = AccInquiry::where('id', $patientId)
                ->where('delete_status', '0')
                ->firstOrFail();

            // Find the opt record
            $optData = Opt::where('patient_id', $patientId)
                ->where('delete_status', '0')
                ->first();

            if (!$optData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Patient diet chart not found'
                ], 404);
            }

            // Update payment program meta data
            $optData->setMetaValue('selected_program', $request->program_name);
            $optData->setMetaValue('session', $request->session);
            $optData->setMetaValue('months', $request->months);
            $optData->setMetaValue('payment_method', $request->payment_method);
            $optData->setMetaValue('total_payment', $request->total);
            $optData->setMetaValue('discount_payment', $request->discount);
            $optData->setMetaValue('given_payment', $request->given);
            $optData->setMetaValue('due_payment', $request->due);
            $optData->setMetaValue('due_date', $request->due_date);


            if ($request->payment_date) {
                $optData->setMetaValue('pod_bd_date', $request->payment_date);
            }


            if ($request->payment_status == '1') {
                $optData->setMetaValue('due_payment', '0.00');
            }

            return response()->json([
                'success' => true,
                'message' => 'Payment program updated successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error updating payment program: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error updating payment program: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deletePaymentProgram(Request $request)
    {
        try {
            \Log::info('Delete Payment Program Request:', $request->all());

            $patientId = $request->patient_id;

            // Find the opt record
            $optData = Opt::where('patient_id', $patientId)
                ->where('delete_status', '0')
                ->first();

            if (!$optData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Patient diet chart not found'
                ], 404);
            }

            // Clear all payment-related meta data
            $paymentMetaKeys = [
                'selected_program',
                'session',
                'months',
                'position',
                'total_payment',
                'discount_payment',
                'given_payment',
                'due_payment',
                'payment_method',
                'due_date',
                'pod_bd_date'
            ];

            foreach ($paymentMetaKeys as $key) {
                $optData->meta()->where('meta_key', $key)->delete();
            }

            return response()->json([
                'success' => true,
                'message' => 'Payment program deleted successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error deleting payment program: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error deleting payment program: ' . $e->getMessage()
            ], 500);
        }
    }

    // Add this method to your InquiryDietChartController
    // public function addProgressReport(Request $request)
    // {
    //     try {
    //         \Log::info('Add Progress Report Request:', $request->all());

    //         // Debug: Check all incoming data
    //         \Log::info('All Request Data:', [
    //             'all_data' => $request->all(),
    //             'report_type' => $request->report_type,
    //             'patient_id' => $request->patient_id
    //         ]);

    //         $request->validate([
    //             'patient_id' => 'required|integer', // Changed to integer
    //             'date' => 'required|date',
    //             'time' => 'required',
    //             'report_type' => 'required|in:lymphysis,detox,breast_reshaping,face_program,relaxation,progress',
    //         ]);

    //         // Find the patient - CORRECTED: Use AccInquiry table with id
    //         $patient = AccInquiry::where('id', $request->patient_id)
    //             ->where('delete_status', '0')
    //             ->first();

    //         if (!$patient) {
    //             \Log::error('Patient not found with ID: ' . $request->patient_id);
    //             return response()->json([
    //                 'success' => false,
    //                 'message' => 'Patient not found!'
    //             ], 404);
    //         }

    //         // Get branch info
    //         $branch = Branch::where('branch_name', $patient->branch)
    //             ->where('delete_status', 0)
    //             ->first();

    //         // Prepare base data
    //         $progressData = [
    //             'patient_id' => $patient->id, // Use the AccInquiry ID, not patient_id
    //             'branch_name' => $patient->branch,
    //             'branch_id' => $branch ? $branch->branch_id : '',
    //             'patient_name' => $patient->patient_name,
    //             'date' => $request->date,
    //             'time' => $request->time,
    //             'weight' => $request->weight ?? null,
    //             'councilor_doctor' => $request->councilor_doctor ?? null,
    //             'exercise' => $request->exercise ?? null,
    //             'delete_status' => '0',
    //             // Initialize all possible fields with null
    //             'body_part' => null,
    //             'lypolysis_treatment' => null,
    //             'detox' => null,
    //             'breast_reshaping' => null,
    //             'face_program' => null,
    //             'relaxation' => null,
    //             'bp_p' => $request->bp ?? null,
    //             'pulse' => $request->pulse ?? null,
    //         ];

    //         // Map form fields to database columns
    //         switch ($request->report_type) {
    //             case 'lymphysis':
    //                 $progressData['lypolysis_treatment'] = $request->lypolysis_treatment ?? '';
    //                 break;

    //             case 'detox':
    //                 // Check both field names for compatibility
    //                 $progressData['detox'] = $request->detox ?? $request->detox_treatment ?? '';
    //                 break;

    //             case 'breast_reshaping':
    //                 $progressData['breast_reshaping'] = $request->breast_reshaping ?? '';
    //                 break;

    //             case 'face_program':
    //                 $progressData['face_program'] = $request->face_program ?? '';
    //                 break;

    //             case 'relaxation':
    //                 $progressData['relaxation'] = $request->relaxation ?? '';
    //                 break;

    //             case 'progress':
    //                 $progressData['body_part'] = $request->body_part ?? '';
    //                 break;
    //         }

    //         \Log::info('Progress Data to be saved:', $progressData);

    //         // Create progress report
    //         $progressReport = Progress::create($progressData);

    //         \Log::info('Progress report created successfully with ID: ' . $progressReport->id);

    //         return response()->json([
    //             'success' => true,
    //             'message' => ucfirst(str_replace('_', ' ', $request->report_type)) . ' report added successfully!',
    //             'report_id' => $progressReport->id
    //         ]);
    //     } catch (\Exception $e) {
    //         \Log::error('Error adding progress report: ' . $e->getMessage());
    //         \Log::error('Error trace: ' . $e->getTraceAsString());

    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Error adding report: ' . $e->getMessage(),
    //             'error_details' => $e->getTraceAsString()
    //         ], 500);
    //     }
    // }


    public function addProgressReport(Request $request)
    {
        try {
            \Log::info('Add Progress Report Request:', $request->all());

            \Log::info('All Request Data:', [
                'all_data' => $request->all(),
                'report_type' => $request->report_type,
                'patient_id' => $request->patient_id
            ]);

            // ❌ REMOVED VALIDATION
            // $request->validate([...]);

            // Find the patient - CORRECTED: Use AccInquiry table with id
            $patient = AccInquiry::where('id', $request->patient_id)
                ->where('delete_status', '0')
                ->first();

            if (!$patient) {
                \Log::error('Patient not found with ID: ' . $request->patient_id);
                return response()->json([
                    'success' => false,
                    'message' => 'Patient not found!'
                ], 404);
            }

            // Get branch info
            $branch = Branch::where('branch_name', $patient->branch)
                ->where('delete_status', 0)
                ->first();

            // Prepare base data
            $progressData = [
                'patient_id' => $patient->id,
                'branch_name' => $patient->branch,
                'branch_id' => $branch ? $branch->branch_id : '',
                'patient_name' => $patient->patient_name,
                'date' => $request->date,
                'time' => $request->time,
                'weight' => $request->weight ?? null,
                'councilor_doctor' => $request->councilor_doctor ?? null,
                'exercise' => $request->exercise ?? null,
                'delete_status' => '0',
                'body_part' => null,
                'lypolysis_treatment' => null,
                'detox' => null,
                'breast_reshaping' => null,
                'face_program' => null,
                'relaxation' => null,
                'bp_p' => $request->bp ?? null,
                'pulse' => $request->pulse ?? null,
            ];

            // Map form fields to database columns
            switch ($request->report_type) {
                case 'lymphysis':
                    $progressData['lypolysis_treatment'] = $request->lypolysis_treatment ?? '';
                    break;

                case 'detox':
                    $progressData['detox'] = $request->detox ?? $request->detox_treatment ?? '';
                    break;

                case 'breast_reshaping':
                    $progressData['breast_reshaping'] = $request->breast_reshaping ?? '';
                    break;

                case 'face_program':
                    $progressData['face_program'] = $request->face_program ?? '';
                    break;

                case 'relaxation':
                    $progressData['relaxation'] = $request->relaxation ?? '';
                    break;

                case 'progress':
                    $progressData['body_part'] = $request->body_part ?? '';
                    break;
            }

            \Log::info('Progress Data to be saved:', $progressData);

            // Create progress report
            $progressReport = Progress::create($progressData);

            \Log::info('Progress report created successfully with ID: ' . $progressReport->id);

            return response()->json([
                'success' => true,
                'message' => ucfirst(str_replace('_', ' ', $request->report_type)) . ' report added successfully!',
                'report_id' => $progressReport->id
            ]);
        } catch (\Exception $e) {
            dd($e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error adding report: ' . $e->getMessage(),
                'error_details' => $e->getTraceAsString()
            ], 500);
        }
    }

    // Get progress report details for editing
    public function getProgressReportDetails($id)
    {
        try {
            $report = Progress::where('id', $id)
                ->where('delete_status', '0')
                ->firstOrFail();

            return response()->json([
                'success' => true,
                'report' => $report
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Report not found'
            ], 404);
        }
    }

    // Update progress report
    public function updateProgressReport(Request $request)
    {
        try {
            \Log::info('Update Progress Report Request:', $request->all());

            $request->validate([
                'report_id' => 'required',
                'report_type' => 'required',
                'date' => 'required|date',
                'time' => 'required',
            ]);

            $report = Progress::where('id', $request->report_id)
                ->where('delete_status', '0')
                ->firstOrFail();

            // Update common fields
            $report->date = $request->date;
            $report->time = $request->time;
            $report->weight = $request->weight;
            $report->councilor_doctor = $request->councilor_doctor;
            $report->exercise = $request->exercise;
            $report->bp_p = $request->bp;
            $report->pulse = $request->pulse;

            // Update type-specific field
            switch ($request->report_type) {
                case 'lymphysis':
                    $report->lypolysis_treatment = $request->lypolysis_treatment;
                    break;
                case 'detox':
                    $report->detox = $request->detox_treatment;
                    break;
                case 'breast_reshaping':
                    $report->breast_reshaping = $request->breast_reshaping;
                    break;
                case 'face_program':
                    $report->face_program = $request->face_program;
                    break;
                case 'relaxation':
                    $report->relaxation = $request->relaxation;
                    break;
                case 'progress':
                    $report->body_part = $request->body_part;
                    break;
            }

            $report->save();

            return response()->json([
                'success' => true,
                'message' => ucfirst(str_replace('_', ' ', $request->report_type)) . ' report updated successfully!'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error updating progress report: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error updating report: ' . $e->getMessage()
            ], 500);
        }
    }

    // Delete progress report
    public function deleteProgressReport(Request $request)
    {
        try {
            \Log::info('Delete Progress Report Request:', $request->all());

            $report = Progress::where('id', $request->report_id)
                ->where('delete_status', '0')
                ->firstOrFail();

            $report->delete_status = '1';
            $report->delete_by = auth()->id();
            $report->save();

            return response()->json([
                'success' => true,
                'message' => ucfirst(str_replace('_', ' ', $request->report_type)) . ' report deleted successfully!'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error deleting progress report: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Error deleting report: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteDietHistory($id)
    {
        try {
            $opt = Opt::findOrFail($id);
            $opt->update([
                'delete_status' => '1',
                'delete_by' => auth()->user()->name ?? 'System'
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Diet history entry removed successfully.'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting diet history: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateDietHistoryMeta(Request $request)
    {
        try {
            $historyId = $request->input('history_id');
            if (!$historyId) {
                return response()->json(['success' => false, 'message' => 'History ID is required']);
            }

            $opt = \App\Models\Opt::findOrFail($historyId);
            $fieldsToUpdate = $request->except(['_token', 'history_id']);

            foreach ($fieldsToUpdate as $key => $value) {
                // If it's empty, we should still store it or update to empty
                $opt->setMetaValue($key, $value);
            }

            return response()->json([
                'success' => true, 
                'message' => 'Diet history updated successfully'
            ]);
        } catch (\Exception $e) {
            \Log::error('Error updating diet history meta: ' . $e->getMessage());
            return response()->json([
                'success' => false, 
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Helper to create or update invoice and transactions for inquiry registration charges.
     */
    private function createInquiryInvoice($inquiry, $branch, $totalPayment, $givenPayment, $paymentMethod, $duePayment)
    {
        $invoiceNo = 'INV-' . $inquiry->patient_id;
        
        // Use existing invoice if available (checking by patient record ID)
        $invoice = Invoice::where('patient_id', $inquiry->id)
            ->where('invoice_no', 'NOT LIKE', 'INV-FOL-%')
            ->first();

        if ($invoice) {
            $invoice->update([
                'price' => $totalPayment,
                'total_payment' => $totalPayment,
                'given_payment' => $givenPayment,
                'due_payment' => $duePayment,
                'branch_id' => $branch->branch_id, // Ensure branch is correct
            ]);
            
                if ($branch->branch_id === 'LB-0007') {
                    $chargeLabel = 'LHR Service';
            } elseif ($branch->branch_id === 'SVC-0005') {
                $chargeLabel = 'SVC Service';
            } elseif ($branch->branch_id === 'BH-00023') {
                $chargeLabel = 'Hydra Service';
            } else {
                $chargeLabel = 'FNF Service';
            }
            PatientTransaction::where('invoice_id', $invoice->id)->where('type', 'credit')->update([
                'amount' => $givenPayment,
                'description' => "$chargeLabel Payment Received (" . ($paymentMethod ?? 'Cash') . ") for Invoice: " . $invoice->invoice_no
            ]);
        } else {
            // Check for duplicate invoice number (unlikely for new patient)
            $counter = 1;
            $finalInvoiceNo = $invoiceNo;
            while (Invoice::where('invoice_no', $finalInvoiceNo)->exists()) {
                $finalInvoiceNo = $invoiceNo . '-' . $counter;
                $counter++;
            }

            // Generate Filename
            $pNameClean = preg_replace('/[^A-Za-z0-9]/', '', $inquiry->patient_name ?? 'Patient');
            $bNameClean = preg_replace('/[^A-Za-z0-9]/', '', $branch->branch_name ?? 'Branch');
            $invoiceFile = $pNameClean . $bNameClean . '-' . $finalInvoiceNo . '-' . now()->format('d-m-Y') . '.pdf';

            if ($branch->branch_id === 'LB-0007') {
                $chargeName = 'LHR Service';
            } elseif ($branch->branch_id === 'BH-00023') {
                $chargeName = 'Hydra Service';
            } elseif ($branch->branch_id === 'SVC-0005') {
                $chargeName = 'SVC Service';
            } else {
                $chargeName = 'FNF Service';
            }

            $chargesData = [[
                'charge_id' => null,
                'charge_name' => $chargeName,
                'price' => $totalPayment
            ]];

            $invoice = Invoice::create([
                'branch_id' => $branch->branch_id,
                'patient_id' => $inquiry->id,
                'invoice_no' => $finalInvoiceNo,
                'invoice_date' => now()->format('Y-m-d'),
                'address' => $inquiry->address,
                'phone' => $inquiry->phone_no,
                'price' => $totalPayment,
                'total_payment' => $totalPayment,
                'given_payment' => $givenPayment,
                'due_payment' => $duePayment,
                'invoice_file' => $invoiceFile,
                'charges_data' => $chargesData,
            ]);

            // Debit Transaction
            if ($branch->branch_id === 'LB-0007') {
                $chargeLabel = 'LHR Service';
            } elseif ($branch->branch_id === 'BH-00023') {
                $chargeLabel = 'Hydra Service';
            } elseif ($branch->branch_id === 'SVC-0005') {
                $chargeLabel = 'SVC Service';
            } else {
                $chargeLabel = 'FNF Service';
            }
            PatientTransaction::create([
                'patient_id' => $inquiry->id,
                'invoice_id' => $invoice->id,
                'type' => 'debit',
                'amount' => $totalPayment,
                'description' => "$chargeLabel Generated: " . $invoice->invoice_no,
            ]);

            // Credit Transaction
            if ($givenPayment > 0) {
                if ($branch->branch_id === 'LB-0007') {
                    $chargeLabel = 'LHR Service';
                } elseif ($branch->branch_id === 'BH-00023') {
                    $chargeLabel = 'Hydra Service';
                } elseif ($branch->branch_id === 'SVC-0005') {
                    $chargeLabel = 'SVC Service';
                } else {
                    $chargeLabel = 'FNF Service';
                }
                PatientTransaction::create([
                    'patient_id' => $inquiry->id,
                    'invoice_id' => $invoice->id,
                    'type' => 'credit',
                    'amount' => $givenPayment,
                    'description' => "$chargeLabel Payment Received (" . ($paymentMethod ?? 'Cash') . ") for Invoice: " . $invoice->invoice_no,
                ]);
            }
        }
    }
}
