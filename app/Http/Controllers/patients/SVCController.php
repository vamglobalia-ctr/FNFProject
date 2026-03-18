<?php

namespace App\Http\Controllers\patients;


use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\ACCUsers;
use App\Models\Branch;
use App\Models\Charges;
use App\Models\FollowupMeta;
use App\Models\Followups;
use App\Models\PatientInquiry;
use App\Models\PatientTreatment;
use App\Models\User;
use App\Models\Invoice;
use App\Models\PatientTransaction;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class SVCController extends Controller
{
    //  public function __construct()
    // {
    //     $this->middleware('auth');
    // }
    public function addInquiry()
    {
        $accUser = User::where('email', auth()->user()->email)->first();
        // dd($accUser);
        if (!$accUser) {
            dd("ACC user not found");
        }
        $branches = Branch::all();   // <-- missing
 
        $branchName = optional($accUser->branch)->branch_name;
 
        $branchId = auth()->user()->user_branch;
 
        // Get doctors (users with doctor role)
        $doctors = User::where('user_role', 6)
            ->orWhereHas('roles', function ($query) {
                $query->where('name', 'Doctor');
            })
            ->select('id', 'name', 'email')
            ->orderBy('name')
            ->get();
 
        // Fetch charges for registration charges dropdown
        $charges = \App\Models\Charges::where(function($query) {
                $query->where('delete_status', '');
            })
            ->orderBy('charges_name', 'asc')
            ->get(['id', 'charges_name', 'charges_price']);
 
        return view('branches.add_inquiry', compact('branchId', 'branchName', 'branches', 'doctors', 'charges'));
    }
 




    public function searchSvcPatient(Request $request)
    {
        $user = auth()->user();
        $query = PatientInquiry::query();

        if ($user->user_role == 2) {
            $query->where('branch', 'SVC');
        } elseif (!empty($user->user_branch)) {
            $query->where('branch_id', $user->user_branch);
        }

        if (!empty($request->name_search)) {
            $query->where('patient_name', 'like', '%' . $request->name_search . '%');
        }

        if (!empty($request->global_search)) {
            $search = $request->global_search;

            $query->where(function ($q) use ($search) {
                $q->where('patient_id', 'like', "%$search%")
                    ->orWhere('patient_name', 'like', "%$search%")
                    ->orWhere('address', 'like', "%$search%")
                    ->orWhere('diagnosis', 'like', "%$search%")
                    ->orWhere('age', 'like', "%$search%");
            });
        }

        $patients = $query
            ->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 5));

        return view('branches.svc_patients', compact('patients'));
    }

    public function indoorPatients(Request $request)
    {
        $user = auth()->user();
        $query = PatientInquiry::with([
            'metas',
            'treatments' => function ($q) {
                $q->where('type', 'indoor')->whereNull('followup_id');
            }
        ]);

        // Filter by IPD status in metas
        $query->whereHas('metas', function ($q) {
            $q->where('meta_key', 'pt_status')->where('meta_value', 'IPD');
        });

        if ($user->user_role == 2) {
            $query->where('branch', 'SVC');
        } elseif (!empty($user->user_branch)) {
            $query->where('branch_id', $user->user_branch);
        }

        if (!empty($request->global_search)) {
            $search = $request->global_search;
            $query->where(function ($q) use ($search) {
                $q->where('patient_id', 'like', "%$search%")
                    ->orWhere('patient_name', 'like', "%$search%")
                    ->orWhere('address', 'like', "%$search%")
                    ->orWhere('diagnosis', 'like', "%$search%")
                    ->orWhere('age', 'like', "%$search%");
            });
        }

        $patients = $query->orderBy('created_at', 'desc')
            ->paginate($request->get('per_page', 10));

        return view('branches.indoor_patients', compact('patients'));
    }

    // This is for /dashboard route (branch dashboard)
    public function index()
    {
        $user = auth()->user();

        if ($user->hasRole('Superadmin')) {
            // Superadmin sees all branches - redirect to admin dashboard

            return redirect()->route('admin.dashboard');
        } else {
            // Non-superadmin users see only their branch
            $branch = Branch::where('branch_id', $user->user_branch)->first();

            if (!$branch) {
                // If no branch assigned, show error or redirect
                return view('dashboard')->with('error', 'No branch assigned to your account.');
            }

            // Pass single branch as collection
            $branches = collect([$branch]);

            return view('dashboard', compact('branches'));
        }
    }

    public function getSuggestions()
    {
        try {
            // Get complaints from medical_conditions table
            $complaints = \App\Models\MedicalCondition::getComplaints()
                ->pluck('name')
                ->toArray();

            // Get diagnoses from medical_conditions table
            $diagnoses = \App\Models\MedicalCondition::getDiagnoses()
                ->pluck('name')
                ->toArray();

            return response()->json([
                'complaints' => $complaints,
                'diagnoses' => $diagnoses
            ]);

        } catch (\Exception $e) {
            \Log::error('Error fetching suggestions: ' . $e->getMessage());
            return response()->json([
                'complaints' => [],
                'diagnoses' => []
            ]);
        }
    }

    public function saveMedicalCondition(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required|string|max:255',
                'type' => 'required|in:complaint,diagnosis'
            ]);

            $name = trim($request->name);
            $type = $request->type;

            // Check if condition already exists
            if (\App\Models\MedicalCondition::exists($name, $type)) {
                return response()->json([
                    'success' => false,
                    'message' => 'This ' . $type . ' already exists'
                ]);
            }

            // Add new condition
            $condition = \App\Models\MedicalCondition::addIfNotExists($name, $type);

            return response()->json([
                'success' => true,
                'message' => $type . ' added successfully',
                'condition' => [
                    'id' => $condition->id,
                    'name' => $condition->name,
                    'type' => $condition->type
                ]
            ]);

        } catch (\Exception $e) {
            \Log::error('Error saving medical condition: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error saving ' . $request->type . ': ' . $e->getMessage()
            ]);
        }
    }

   public function store(Request $request)
    {
        return DB::transaction(function () use ($request) {
            try {
                $user = auth()->user();
                if ($user->hasRole('Superadmin')) {

                    if (!$request->branch_id) {
                        throw new \Exception("Please select a branch.");
                    }

                    $branchId = $request->branch_id;
                } else {

                    if (!$user->user_branch) {
                        throw new \Exception("User has no branch assigned.");
                    }

                    $branchId = $user->user_branch;
                }


                $branch = Branch::where('branch_id', $branchId)->first();

                if (!$branch) {
                    throw new \Exception("Branch not found.");
                }

                $prefix = $branch->branch_name;

                $maxNumber = PatientInquiry::withTrashed()
                    ->where('branch_id', $branch->branch_id)
                    ->where('patient_id', 'LIKE', $prefix . '-%')
                    ->lockForUpdate()
                    ->max(DB::raw('CAST(SUBSTRING(patient_id, LOCATE("-", patient_id) + 1) AS UNSIGNED)'));

                $nextNumber = $maxNumber ? (int) $maxNumber + 1 : 1;
                $patientId = $prefix . '-' . str_pad($nextNumber, strlen((string) $nextNumber) + 4, '0', STR_PAD_LEFT);
                // Debug: Log all request data before validation
                \Log::info('SVC Inquiry Request Data:', [
                    'all_data' => $request->all(),
                    'diagnosis_value' => $request->input('diagnosis'),
                    'complain_value' => $request->input('complain'),
                ]);

                $validated = $request->validate([
                    'branch_id' => 'required|string',
                    'patient_name' => 'required|string|max:255',
                    'address' => 'required|string',
                    'age' => 'required|integer',
                    'diagnosis' => 'required|string',
                    'inquiry_date' => 'nullable|date',
                    'next_follow_date' => 'nullable|date',
                ]);

                if (!empty($validated['inquiry_date']) && $request->filled('inquiry_time')) {
                    try {
                        $validated['inquiry_date'] = \Carbon\Carbon::createFromFormat(
                            'Y-m-d H:i',
                            $validated['inquiry_date'] . ' ' . $request->input('inquiry_time'),
                            'Asia/Kolkata'
                        )->format('Y-m-d H:i:s');
                    } catch (\Exception $e) {
                        // Keep original inquiry_date if parsing fails
                    }
                }

                $validated['patient_id'] = $patientId;
                $validated['branch_id'] = $branch->branch_id;
                $validated['branch'] = $branch->branch_name;

                $patient = PatientInquiry::create($validated);

                // Debug: Log the created patient data
                \Log::info('Patient Created Successfully:', [
                    'patient_id' => $patient->id,
                    'patient_name' => $patient->patient_name,
                    'diagnosis' => $patient->diagnosis,
                    'all_patient_data' => $patient->toArray()
                ]);

                $metaFields = $request->except(array_merge(array_keys($validated), [
                    '_token', 
                    // Payment-related fields to exclude from meta
                    'total_payment',
                    'given_payment', 
                    'due_payment',
                    'discount_payment',
                    'payment_method',
                    'inquiry_foc',
                    // Other non-meta fields
                    'inquiry_time'
                ]));

                // Debug: Log what meta fields are being processed
                \Log::info('Meta fields being processed:', [
                    'meta_fields' => $metaFields,
                    'validated_keys' => array_keys($validated),
                    'all_request_data' => $request->all(),
                    'payment_data' => [
                        'total_payment' => $request->input('total_payment'),
                        'given_payment' => $request->input('given_payment'),
                        'due_payment' => $request->input('due_payment'),
                        'payment_method' => $request->input('payment_method'),
                        'inquiry_foc' => $request->input('inquiry_foc'),
                    ]
                ]);

                foreach ($metaFields as $key => $value) {
                    if (is_array($value)) {
                        foreach ($value as $index => $item) {
                            $patient->setMeta("{$key}_{$index}", $item);
                        }
                    } else {
                        $patient->setMeta($key, $value);
                        \Log::info("Setting meta: {$key} = {$value}");
                    }
                }

                $groups = [
                    'inside' => ['dose', 'timing', 'days'],
                    'homeo' => ['timing', 'days'],
                    'prescription' => ['dose', 'timing', 'days'],
                    'indoor' => ['dose', 'note', 'days', 'date', 'time'],
                    'other' => ['note'],
                ];

                // Check if any indoor treatment is provided to set IPD status
                if ($request->has('indoor_medicine')) {
                    $indoorMedicines = $request->input('indoor_medicine', []);
                    $hasIndoor = false;
                    foreach ($indoorMedicines as $med) {
                        if (!empty(trim($med))) {
                            $hasIndoor = true;
                            break;
                        }
                    }
                    if ($hasIndoor) {
                        $patient->setMeta('pt_status', 'IPD');
                    }
                }

                foreach ($groups as $type => $fields) {
                    $medicineKey = $type . '_medicine';

                    if ($request->has($medicineKey)) {
                        foreach ($request->$medicineKey as $i => $medicine) {
                            if (!empty($medicine)) {
                                $data = [
                                    'patient_id' => $patient->patient_id,
                                    'inquiry_id' => $patient->id,
                                    'followup_id' => null,
                                    'type' => $type,
                                    'medicine' => $medicine,
                                ];

                                foreach ($fields as $f) {
                                    // Handle dose fields with dual input (textbox + dropdown)
                                    if ($f === 'dose') {
                                        $textboxField = $type . '_dose';
                                        $dropdownField = $type . '_dose_dropdown';

                                        // Get textbox value (manual entry)
                                        $textboxValues = $request->input($textboxField, []);
                                        $textboxValue = $textboxValues[$i] ?? null;

                                        // Get dropdown value
                                        $dropdownValues = $request->input($dropdownField, []);
                                        $dropdownValue = $dropdownValues[$i] ?? null;

                                        // Use textbox value if available, otherwise use dropdown value
                                        $data[$f] = !empty($textboxValue) ? $textboxValue : $dropdownValue;

                                        \Log::info("Dose processing for {$type}[{$i}]: textbox={$textboxValue}, dropdown={$dropdownValue}, final={$data[$f]}");
                                    } else {
                                        // Handle other fields normally
                                        $fieldName = $type . '_' . $f;
                                        $fieldValues = $request->input($fieldName, []);
                                        if (isset($fieldValues[$i])) {
                                            $data[$f] = $fieldValues[$i];
                                            \Log::info("Field processing for {$type}[{$i}]: {$fieldName} = " . $fieldValues[$i]);
                                        }
                                    }
                                }

                                PatientTreatment::create($data);
                            }
                        }
                    }
                }

                // Create Invoice and Transactions for Registration Charges
                $totalPayment = $request->input('total_payment', 0);
                if ($totalPayment > 0) {
                    $invoiceNo = 'INV-' . $patientId;

                    // Check if invoice number already exists (unlikely for new patient but safe)
                    $counter = 1;
                    $finalInvoiceNo = $invoiceNo;
                    while (Invoice::where('invoice_no', $finalInvoiceNo)->exists()) {
                        $finalInvoiceNo = $invoiceNo . '-' . $counter;
                        $counter++;
                    }

                    // Generate Filename
                    $pNameClean = preg_replace('/[^A-Za-z0-9]/', '', $patient->patient_name ?? 'Patient');
                    $bNameClean = preg_replace('/[^A-Za-z0-9]/', '', $branch->branch_name ?? 'Branch');
                    $invoiceFile = $pNameClean . $bNameClean . '-' . $finalInvoiceNo . '-' . now()->format('d-m-Y') . '.pdf';

                    $chargesData = [
                        [
                            'charge_id' => null,
                            'charge_name' => 'Registration & Consultation Charges',
                            'price' => $totalPayment
                        ]
                    ];

                    $givenPayment = $request->input('given_payment', 0);
                    $duePayment = $request->input('due_payment', $totalPayment - $givenPayment);

                    $invoice = Invoice::create([
                        'branch_id' => $branch->branch_id,
                        'patient_id' => $patient->id,
                        'invoice_no' => $finalInvoiceNo,
                        'invoice_date' => now()->format('Y-m-d'),
                        'address' => $patient->address,
                        'phone' => $patient->getMeta('phone'),
                        'price' => $totalPayment,
                        'total_payment' => $totalPayment,
                        'given_payment' => $givenPayment,
                        'due_payment' => $duePayment,
                        'invoice_file' => $invoiceFile,
                        'charges_data' => $chargesData,
                    ]);

                    // Determine branch prefix
                    if ($branch->branch_id === 'LB-0007') {
                        $descPrefix = 'LHR Service';
                    } elseif ($branch->branch_id === 'BH-00023') {
                        $descPrefix = 'Hydra Service';
                    } elseif ($branch->branch_id === 'SVC-0005') {
                        $descPrefix = 'SVC Service';
                    } else {
                        $descPrefix = 'FNF Service';
                    }

                    // Debit Transaction
                    PatientTransaction::create([
                        'patient_id' => $patient->id,
                        'invoice_id' => $invoice->id,
                        'type' => 'debit',
                        'amount' => $totalPayment,
                        'description' => $descPrefix . ' (Registration & Consultation) - Invoice Generated: ' . $invoice->invoice_no,
                    ]);

                    // Credit Transaction
                    if ($givenPayment > 0) {
                        PatientTransaction::create([
                            'patient_id' => $patient->id,
                            'invoice_id' => $invoice->id,
                            'type' => 'credit',
                            'amount' => $givenPayment,
                            'description' => $descPrefix . ' (Registration & Consultation) Payment Received (' . ($request->input('payment_method') ?? 'Cash') . ') for Invoice: ' . $invoice->invoice_no,
                        ]);
                    }
                }
                // dd($request->branch_id, Branch::pluck('branch_id'));

                return redirect()
                    ->route('svc-patient')
                    ->with('success', 'Patient inquiry and treatments saved successfully. Patient ID: ' . $patientId);
            } catch (\Exception $e) {
                \Log::error('Error saving SVC inquiry: ' . $e->getMessage());
                \Log::error('Trace: ' . $e->getTraceAsString());

                return back()
                    ->with('error', 'Error saving inquiry: ' . $e->getMessage())
                    ->withInput();
            }
        });
    }




  public function editSvcInquiry($id)
    {
        $patient = PatientInquiry::with(['metas'])->findOrFail($id);

        $meta = [];
        foreach ($patient->metas as $m) {
            $decoded = json_decode($m->meta_value, true);
            $meta[$m->meta_key] = json_last_error() === JSON_ERROR_NONE ? $decoded : $m->meta_value;
        }

        $treatments = [];
        $groups = ['inside', 'homeo', 'prescription', 'indoor', 'other'];

        foreach ($groups as $group) {
            $treatments[$group] = PatientTreatment::where('patient_id', $patient->patient_id)
                ->where('inquiry_id', $patient->id)
                ->where('type', $group)
                ->whereNull('followup_id')
                ->get()
                ->toArray();
        }

        $doctors = User::where('user_role', 6)
            ->orWhereHas('roles', function ($query) {
                $query->where('name', 'Doctor');
            })
            ->select('id', 'name', 'email')
            ->orderBy('name')
            ->get();

        // Fetch charges for registration charges dropdown
        $charges = \App\Models\Charges::where(function($query) {
                $query->where('delete_status', '0')
                      ->orWhere('delete_status', '');
            })
            ->orderBy('charges_name', 'asc')
            ->get(['id', 'charges_name', 'charges_price']);

        return view('branches.edit_svc_inquiry', [
            'patient' => $patient,
            'meta' => $meta,
            'treatments' => $treatments,
            'doctors' => $doctors,
            'charges' => $charges,
        ]);
    }


    public function updateSvcInquiry(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'patient_id' => 'required|string',
                'branch' => 'nullable|string',
                'branch_id' => 'nullable|string',
                'patient_name' => 'required|string|max:255',
                'address' => 'nullable|string',
                'age' => 'nullable|integer',
                'diagnosis' => 'nullable|string',
                'inquiry_date' => 'nullable|date',
                'next_follow_date' => 'nullable|date',
            ]);

            $patient = PatientInquiry::findOrFail($id);
            $patient->update($validated);

            $metaFields = [
                'complain',
                'investigation',
                'past_history',
                'family_history',
                'gender',
                'weight',
                'phone',
                'pt_status',
                'temperature',
                'pulse',
                'blood_pressure',
                'spo2',
                'rbs',
                'hb',
                'tc',
                'pc',
                'MP',
                'HB1AC',
                'fbs',
                'pp2bs',
                'S_widal',
                'USG',
                'X_ray',
                'SGPT',
                's_creatinine',
                'NS1Ag',
                'DengueIGM',
                's_cholesterol',
                'STriglyceride',
                'HDL',
                'LDL',
                'VLDL',
                'SB12',
                'SD3',
                'Urine',
                'CRP',
                'St3',
                'St4',
                'STSH',
                'ESR',
                'specific_test',
                'reference_by',
                'referto',
                'doctor_id',
                'notes',
                'total_payment',
                'discount_payment',
                'given_payment',
                'due_payment',
                'cash_payment',
                'gp_payment',
                'cheque_payment',
                'payment_method',
                'inquiry_time'
            ];

            foreach ($metaFields as $field) {
                if ($request->has($field)) {
                    $patient->setMeta($field, $request->input($field));
                }
            }

            $treatmentGroups = [
                'inside' => ['dose', 'timing', 'days'],
                'homeo' => ['timing', 'days'],
                'prescription' => ['dose', 'timing', 'days'],
                'indoor' => ['dose', 'note', 'days', 'date', 'time'],
                'other' => ['note'],
            ];

            foreach ($treatmentGroups as $key => $fields) {
                $medicineKey = "{$key}_medicine";

                PatientTreatment::where('patient_id', $patient->patient_id)
                    ->where('inquiry_id', $patient->id)
                    ->where('type', $key)
                    ->delete();

                $medicines = $request->input($medicineKey, []);

                foreach ($medicines as $index => $medicine) {
                    if (!empty(trim($medicine))) {
                        $treatmentData = [
                            'patient_id' => $patient->patient_id,
                            'inquiry_id' => $patient->id,
                            'type' => $key,
                            'medicine' => trim($medicine),
                        ];

                        foreach ($fields as $field) {
                            $fieldName = "{$key}_{$field}";
                            $fieldValues = $request->input($fieldName, []);
                            if (isset($fieldValues[$index])) {
                                $treatmentData[$field] = $fieldValues[$index];
                                \Log::info("Update field processing for {$key}[{$index}]: {$fieldName} = " . $fieldValues[$index]);
                            }
                        }

                        PatientTreatment::create($treatmentData);
                    }
                }
            }

            return redirect()
                ->route('svc-patient')
                ->with('success', 'Patient inquiry updated successfully.');
        } catch (Exception $e) {
            return back()->with('error', 'Error updating inquiry: ' . $e->getMessage());
        }
    }
    public function deleteSvcInquiry($id)
    {
        try {
            $patient = PatientInquiry::findOrFail($id);


            $patient->delete_by = auth()->check() ? auth()->user()->name : 'system';
            $patient->delete_status = 'deleted';
            $patient->save();


            $patient->metas()->delete();


            $patient->delete();

            return redirect()
                ->route('svc-patient')
                ->with('success', 'Patient inquiry and related meta deleted successfully.');
        } catch (ModelNotFoundException $e) {
            Log::error("Patient not found for deleteSvcInquiry: ID {$id}");
            return redirect()->route('svc-patient')->with('error', 'Patient not found.');
        } catch (Exception $e) {
            Log::error('Error in deleteSvcInquiry', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return back()->with('error', 'Unexpected error while deleting inquiry.');
        }
    }
    public function viewSvcProfile($id)
    {
        try {
            $patient = PatientInquiry::with(['metas'])->findOrFail($id);

            // Build meta array like in editSvcInquiry
            $meta = [];
            foreach ($patient->metas as $m) {
                $decoded = json_decode($m->meta_value, true);
                $meta[$m->meta_key] = json_last_error() === JSON_ERROR_NONE ? $decoded : $m->meta_value;
            }

            $treatments = [];
            $groups = ['inside', 'homeo', 'prescription', 'indoor', 'other'];

            foreach ($groups as $group) {
                $treatments[$group] = PatientTreatment::where('patient_id', $patient->patient_id)
                    ->where('inquiry_id', $patient->id)
                    ->where('type', $group)
                    ->whereNull('followup_id')
                    ->get()
                    ->toArray();
            }

            // Get doctors for display
            $doctors = User::where('user_role', 6)
                ->orWhereHas('roles', function ($query) {
                    $query->where('name', 'Doctor');
                })
                ->select('id', 'name', 'email')
                ->orderBy('name')
                ->get();

            return view('branches.profile.svc_profile', compact('patient', 'meta', 'treatments', 'doctors'));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('svc-patient')->with('error', 'Patient not found.');
        } catch (Exception $e) {
            return redirect()->route('svc-patient')->with('error', 'Error loading patient profile.');
        }
    }

    /**
     * Update the patient's profile image.
     */
    public function updateProfileImage(Request $request, $id)
    {
        try {
            $patient = PatientInquiry::findOrFail($id);

            $request->validate([
                'profile_image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            if ($request->hasFile('profile_image')) {
                // Delete old image if exists
                $oldImage = $patient->getMeta('profile_image');
                if ($oldImage && file_exists(public_path($oldImage))) {
                    unlink(public_path($oldImage));
                }

                $image = $request->file('profile_image');
                $filename = 'patient_' . $id . '_' . time() . '.' . $image->getClientOriginalExtension();
                $image->move(public_path('uploads/patients'), $filename);
                $path = 'uploads/patients/' . $filename;

                $patient->setMeta('profile_image', $path);

                return back()->with('success', 'Profile image updated successfully.');
            }

            return back()->with('error', 'No image file provided.');
        } catch (Exception $e) {
            return back()->with('error', 'Error updating profile image: ' . $e->getMessage());
        }
    }

   public function saveProfileIndoorTreatment(Request $request, $id)
    {
        try {
            $patient = PatientInquiry::findOrFail($id);
 
            // Set status to IPD so they show up on the Indoor Patients page
            $patient->setMeta('pt_status', 'IPD');
 
            // Clear existing indoor treatments for this inquiry to avoid duplicates
            PatientTreatment::where('patient_id', $patient->patient_id)
                ->where('inquiry_id', $patient->id)
                ->where('type', 'indoor')
                ->whereNull('followup_id')
                ->delete();
 
            // New slot-based inputs
            // slot_date[N], slot_time[N], slot_medicine[N][], slot_note[N][]
            $slotDates    = $request->input('slot_date', []);
            $slotTimes    = $request->input('slot_time', []);
            $slotMedicines = $request->input('slot_medicine', []);
            $slotNotes    = $request->input('slot_note', []);
 
            foreach ($slotMedicines as $slotIndex => $medicines) {
                if (!is_array($medicines)) continue;
 
                $date = isset($slotDates[$slotIndex]) && !empty($slotDates[$slotIndex])
                    ? $slotDates[$slotIndex]
                    : null;
 
                $time = isset($slotTimes[$slotIndex]) && !empty($slotTimes[$slotIndex])
                    ? $slotTimes[$slotIndex]
                    : null;
 
                $notes = $slotNotes[$slotIndex] ?? [];
 
                foreach ($medicines as $rowIndex => $medicine) {
                    if (!empty(trim($medicine))) {
                        PatientTreatment::create([
                            'patient_id' => $patient->patient_id,
                            'inquiry_id' => $patient->id,
                            'type'       => 'indoor',
                            'medicine'   => trim($medicine),
                            'dose'       => null, // Dose removed from modal
                            'days'       => null, // Days removed from modal
                            'date'       => $date,
                            'time'       => $time,
                            'note'       => isset($notes[$rowIndex]) && !empty(trim($notes[$rowIndex]))
                                                ? trim($notes[$rowIndex])
                                                : null,
                        ]);
                    }
                }
            }
 
            return back()->with('success', 'Indoor treatment saved successfully.');
 
        } catch (\Exception $e) {
            return back()->with('error', 'Error saving indoor treatment: ' . $e->getMessage());
        }
    }



    public function editFollowUp($patient_id, $followup_id)
    {
        try {
            $patient = PatientInquiry::where('patient_id', $patient_id)->firstOrFail();
            $followup = Followups::with('metas')->findOrFail($followup_id);

            $followupMetaValues = [];
            $metaKeys = [
                'pt_status',
                'temperature',
                'weight',
                'spo2',
                'blood_pressure',
                'pulse',
                'rbs',
                'diagnosis',
                'hb',
                'tc',
                'pc',
                'mp',
                'hb1ac',
                'fbs',
                'pp2bs',
                's_widal',
                'usg',
                'x_ray',
                'sgpt',
                's_creatinine',
                'ns1ag',
                'dengue_igm',
                's_cholesterol',
                's_triglyceride',
                'hdl',
                'ldl',
                'vldl',
                's_b12',
                's_d3',
                'urine',
                's_t3',
                'crp',
                's_t4',
                's_tsh',
                'esr',
                'complain',
                'investigation',
                'past_history',
                'family_history',
                'specific_test',
                'reference_by',
                'referto',
                'notes',
                'total_payment',
                'discount_payment',
                'given_payment',
                'due_payment',
                'cash_payment',
                'gp_payment',
                'cheque_payment'
            ];

            foreach ($metaKeys as $key) {
                $followupMetaValues[$key] = [];
            }

            foreach ($followup->metas as $meta) {
                $key = $meta->meta_key;

                if (preg_match('/^(.+)_(\d+)$/', $key, $matches)) {
                    $baseKey = $matches[1];
                    $index = (int) $matches[2];

                    if (in_array($baseKey, $metaKeys)) {
                        $followupMetaValues[$baseKey][$index] = $meta->meta_value;
                    }
                } else {
                    if (in_array($key, $metaKeys)) {
                        $followupMetaValues[$key][] = $meta->meta_value;
                    }
                }
            }

            foreach ($followupMetaValues as $key => $values) {
                if (count($values) > 0) {
                    ksort($followupMetaValues[$key]);
                    $followupMetaValues[$key] = array_values($followupMetaValues[$key]);
                }
            }

            $treatments = [];
            $groups = ['inside', 'homeo', 'prescription', 'indoor', 'other'];

            foreach ($groups as $group) {
                $treatments[$group] = PatientTreatment::where('patient_id', $patient->patient_id)
                    ->where('followup_id', $followup->id)
                    ->where('type', $group)
                    ->get()
                    ->toArray();
            }

            return view('branches.profile.edit_follow_up', compact(
                'patient',
                'followup',
                'treatments',
                'followupMetaValues'
            ));
        } catch (\Exception $e) {
            Log::error('Error in editFollowUp: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Error loading follow-up: ' . $e->getMessage());
        }
    }

    public function updateFollowUp(Request $request, $patient_id, $followup_id)
    {
        try {
            $patient = PatientInquiry::where('patient_id', $patient_id)->firstOrFail();
            $followup = Followups::findOrFail($followup_id);

            $followup->update([
                'followup_date' => $request->followup_date,
            ]);


            $followup->metas()->delete();
            $excluded = [
                '_token',
                '_method',
                'followup_date',
                'inside_medicine',
                'homeo_medicine',
                'prescription_medicine',
                'indoor_medicine',
                'other_medicine',
                'inside_dose',
                'inside_timing',
                'homeo_timing',
                'prescription_dose',
                'prescription_timing',
                'indoor_dose',
                'indoor_note',
                'other_note'
            ];

            $metaFields = $request->except($excluded);

            foreach ($metaFields as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $index => $item) {
                        if (!empty($item) || $item === '0') {
                            $followup->setMeta("{$key}_{$index}", $item);
                        }
                    }
                } else {
                    if (!empty($value) || $value === '0') {
                        $followup->setMeta($key, $value);
                    }
                }
            }

            PatientTreatment::where('followup_id', $followup->id)->delete();

            $groups = [
                'inside' => ['dose', 'timing', 'days'],
                'homeo' => ['timing', 'days'],
                'prescription' => ['dose', 'timing', 'days'],
                'indoor' => ['dose', 'note', 'days', 'date', 'time'],
                'other' => ['note'],
            ];

            foreach ($groups as $type => $fields) {
                $medicineKey = $type . '_medicine';

                if ($request->has($medicineKey)) {
                    foreach ($request->$medicineKey as $i => $medicine) {
                        if (!empty(trim($medicine))) {
                            $data = [
                                'followup_id' => $followup->id,
                                'patient_id' => $patient->patient_id,
                                'type' => $type,
                                'medicine' => trim($medicine),
                            ];

                            foreach ($fields as $f) {
                                // Handle dose fields with dual input (textbox + dropdown)
                                if ($f === 'dose') {
                                    $textboxField = $type . '_dose';
                                    $dropdownField = $type . '_dose_dropdown';

                                    // Get textbox value (manual entry)
                                    $textboxValues = $request->input($textboxField, []);
                                    $textboxValue = $textboxValues[$i] ?? null;

                                    // Get dropdown value
                                    $dropdownValues = $request->input($dropdownField, []);
                                    $dropdownValue = $dropdownValues[$i] ?? null;

                                    // Use textbox value if available, otherwise use dropdown value
                                    $data[$f] = !empty($textboxValue) ? $textboxValue : $dropdownValue;

                                    \Log::info("Followup dose processing for {$type}[{$i}]: textbox={$textboxValue}, dropdown={$dropdownValue}, final={$data[$f]}");
                                } else {
                                    // Handle other fields normally
                                    $fieldName = $type . '_' . $f;
                                    $fieldValues = $request->input($fieldName, []);
                                    if (isset($fieldValues[$i])) {
                                        $data[$f] = $fieldValues[$i];
                                    }
                                }
                            }

                            PatientTreatment::create($data);
                        }
                    }
                }
            }

            return redirect()
                ->route('svc.profile', ['id' => $patient->id])
                ->with('success', 'Follow-up updated successfully.');
        } catch (\Exception $e) {
            Log::error('Error in updateFollowUp: ' . $e->getMessage());
            return back()->with('error', 'Error updating follow-up: ' . $e->getMessage());
        }
    }

    public function deleteFollowUp($id)
    {
        try {
            $followup = Followups::findOrFail($id);


            $patientId = $followup->patient_id;
            $patientInquiry = PatientInquiry::where('patient_id', $patientId)->first();

            if (!$patientInquiry) {
                return redirect()->route('svc-patient')->with('error', 'Patient not found.');
            }

            PatientTreatment::where('followup_id', $id)->delete();
            $followup->metas()->delete();

            $followup->delete();

            return redirect()
                ->route('svc.profile', ['id' => $patientInquiry->id])
                ->with('success', 'Follow-up record deleted successfully.');
        } catch (\Exception $e) {
            Log::error('Error deleting follow-up: ' . $e->getMessage());

            return redirect()
                ->route('svc.profile', ['id' => $patientInquiry->id ?? null])
                ->with('error', 'Error deleting follow-up record: ' . $e->getMessage());
        }
    }

    public function exportSvcPatients(Request $request)
    {
        try {
            $query = PatientInquiry::where('branch', 'SVC');

            if ($request->has('name_search') && !empty($request->name_search)) {
                $query->where('patient_name', 'like', '%' . $request->name_search . '%');
            }

            if ($request->has('global_search') && !empty($request->global_search)) {
                $searchTerm = $request->global_search;
                $query->where(function ($q) use ($searchTerm) {
                    $q->where('patient_id', 'like', '%' . $searchTerm . '%')
                        ->orWhere('address', 'like', '%' . $searchTerm . '%')
                        ->orWhere('diagnosis', 'like', '%' . $searchTerm . '%')
                        ->orWhere('age', 'like', '%' . $searchTerm . '%')
                        ->orWhereRaw("DATE_FORMAT(inquiry_date, '%d/%m/%Y') LIKE ?", ['%' . $searchTerm . '%'])
                        ->orWhereRaw("DATE_FORMAT(next_follow_date, '%d/%m/%Y') LIKE ?", ['%' . $searchTerm . '%'])
                        ->orWhere('inquiry_date', 'like', '%' . $searchTerm . '%')
                        ->orWhere('next_follow_date', 'like', '%' . $searchTerm . '%');
                });
            }

            $patients = $query->orderBy('created_at', 'desc')->get();

            $csvData = "Patient ID,Name,Address,Age,Diagnosis,Inquiry Date,Follow Up Date\n";

            foreach ($patients as $patient) {
                $csvData .= '"' .
                    $patient->patient_id . '","' .
                    $patient->patient_name . '","' .
                    $patient->address . '","' .
                    $patient->age . '","' .
                    $patient->diagnosis . '","' .
                    ($patient->inquiry_date ? \Carbon\Carbon::parse($patient->inquiry_date)->format('d/m/Y') : '') . '","' .
                    ($patient->next_follow_date ? \Carbon\Carbon::parse($patient->next_follow_date)->format('d/m/Y') : '') . '"' .
                    "\n";
            }

            $filename = 'svc_patients_' . date('Y-m-d_H-i-s') . '.csv';

            return response($csvData)
                ->header('Content-Type', 'text/csv')
                ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
        } catch (Exception $e) {
            Log::error('Export error: ' . $e->getMessage());
            return back()->with('error', 'Error exporting data: ' . $e->getMessage());
        }
    }

    // public function addFollowUp(Request $request, $patient_id)
    // {
    //     $patient = PatientInquiry::with(['followups.metas', 'treatments'])
    //         ->where('patient_id', $patient_id)
    //         ->firstOrFail();

    //     $followupDates = $patient->followups()
    //         ->select('followup_date')
    //         ->distinct()
    //         ->orderBy('followup_date', 'desc')
    //         ->pluck('followup_date')
    //         ->map(fn($d) => \Carbon\Carbon::parse($d)->format('Y-m-d'));

    //     $selectedDate = $request->query('date');

    //     // If no date is selected, default to today
    //     if (!$selectedDate) {
    //         $selectedDate = now()->format('Y-m-d');
    //     }

    //     // Rest of your existing code remains the same...
    //     $followupMetaValues = [];
    //     $metaKeys = [
    //         'pt_status','temperature','weight','spo2','blood_pressure','pulse','rbs',
    //         'diagnosis','hb','tc','pc','mp','hb1ac','fbs','pp2bs','s_widal','usg',
    //         'x_ray','sgpt','s_creatinine','ns1ag','dengue_igm','s_cholesterol',
    //         's_triglyceride','hdl','ldl','vldl','s_b12','s_d3','urine','s_t3','crp',
    //         's_t4','s_tsh','esr'
    //     ];

    //     foreach ($metaKeys as $key) {
    //         $followupMetaValues[$key] = [''];
    //     }

    //     $treatments = [
    //         'inside' => [],
    //         'homeo' => [],
    //         'prescription' => [],
    //         'indoor' => [],
    //         'other' => []
    //     ];

    //     if ($selectedDate) {
    //         $followup = $patient->followups()
    //             ->whereDate('followup_date', $selectedDate)
    //             ->latest('created_at')
    //             ->first();

    //         if ($followup) {
    //             $allFollowupMetas = $followup->metas()->get();

    //             foreach ($metaKeys as $key) {
    //                 $values = $allFollowupMetas
    //                     ->filter(function($meta) use ($key) {
    //                         return $meta->meta_key === $key ||
    //                                Str::startsWith($meta->meta_key, $key . '_');
    //                     })
    //                     ->sortBy(function($meta) {
    //                         if (preg_match('/_(\d+)$/', $meta->meta_key, $matches)) {
    //                             return (int)$matches[1];
    //                         }
    //                         return 0;
    //                     })
    //                     ->pluck('meta_value')
    //                     ->values()
    //                     ->toArray();

    //                 $followupMetaValues[$key] = $values;
    //             }

    //             $followupTreatments = PatientTreatment::where('followup_id', $followup->id)->get();

    //             foreach ($followupTreatments as $treatment) {
    //                 $type = $treatment->type;
    //                 if (array_key_exists($type, $treatments)) {
    //                     $treatments[$type][] = [
    //                         'medicine' => $treatment->medicine,
    //                         'dose' => $treatment->dose,
    //                         'timing' => $treatment->timing,
    //                         'note' => $treatment->note
    //                     ];
    //                 }
    //             }
    //         }
    //     }

    //     return view('branches.profile.add_follow_up', compact(
    //         'patient',
    //         'metaKeys',
    //         'selectedDate',
    //         'followupDates',
    //         'treatments',
    //         'followupMetaValues'
    //     ));
    // }
    // public function storeFollowUp(Request $request, $patient_id)
    // {
    //      try {
    //         $patient = PatientInquiry::where('patient_id', $patient_id)->firstOrFail();

    //         $followup = Followups::create([
    //             'patient_id'   => $patient->patient_id,
    //             'inquiry_id'   => $patient->id,
    //             'followup_date' => $request->followup_date,
    //             'next_follow_date' => $request->next_follow_date,
    //             'followups_time' => $request->followups_time, 
    //             'created_at' => now(),
    //             'updated_at' => now(),
    //         ]);

    //         $followup->metas()->delete();

    //         $excluded = [
    //             '_token',
    //             'followup_date',
    //             'followups_time', // Add this
    //             'inside_medicine', 'homeo_medicine',
    //             'prescription_medicine', 'indoor_medicine',
    //             'other_medicine'
    //         ];

    //         $metaFields = $request->except($excluded);

    //         foreach ($metaFields as $key => $value) {
    //             if (is_array($value)) {
    //                 foreach ($value as $index => $item) {
    //                     if ($item === null || $item === '') {
    //                         continue;
    //                     }

    //                     FollowupMeta::create([
    //                         'followup_id' => $followup->id,
    //                         'meta_key'    => "{$key}_{$index}",
    //                         'meta_value'  => $item
    //                     ]);
    //                 }
    //             } else {
    //                 if ($value !== null && $value !== '') {
    //                     FollowupMeta::create([
    //                         'followup_id' => $followup->id,
    //                         'meta_key'    => $key,
    //                         'meta_value'  => $value
    //                     ]);
    //                 }
    //             }
    //         }

    //         PatientTreatment::where('followup_id', $followup->id)->delete();

    //         $groups = [
    //             'inside'        => ['dose', 'timing'],
    //             'homeo'         => ['timing'],
    //             'prescription'  => ['dose', 'timing'],
    //             'indoor'        => ['dose', 'note'],
    //             'other'         => ['note'],
    //         ];

    //         foreach ($groups as $type => $fields) {
    //             $medicineKey = $type . '_medicine';

    //             if ($request->has($medicineKey)) {
    //                 foreach ($request->$medicineKey as $i => $medicine) {
    //                     if (!empty($medicine)) {
    //                         $data = [
    //                             'followup_id' => $followup->id,
    //                             'inquiry_id' => null,
    //                             'patient_id'  => $patient->patient_id,
    //                             'type'        => $type,
    //                             'medicine'    => $medicine,
    //                         ];

    //                         foreach ($fields as $f) {
    //                             $fieldName = $type . '_' . $f;
    //                             $fieldValues = $request->input($fieldName, []);
    //                             if (isset($fieldValues[$i])) {
    //                                 $data[$f] = $fieldValues[$i];
    //                             }
    //                         }

    //                         PatientTreatment::create($data);
    //                     }
    //                 }
    //             }
    //         }

    //         // FIX: Redirect back to the follow-up page with the selected date
    //         return redirect()
    //             ->route('add.follow.up', [
    //                 'patient_id' => $patient->patient_id,
    //                 'date' => $request->followup_date // Pass the date as query parameter
    //             ])
    //             ->with('success', 'Follow-up data saved successfully.');

    //     } catch (\Exception $e) {
    //         return redirect()
    //             ->back()
    //             ->with('error', 'Error saving follow-up data: ' . $e->getMessage());
    //     }
    // }
    public function addFollowUp(Request $request, $patient_id)
    {
        $patient = PatientInquiry::with(['followups.metas', 'treatments'])
            ->where('patient_id', $patient_id)
            ->firstOrFail();

        // Get unique followup dates with time from meta
        $followupDates = $patient->followups()
            ->with([
                'metas' => function ($query) {
                    $query->where('meta_key', 'followups_time');
                }
            ])
            ->orderBy('followup_date', 'desc')
            ->get()
            ->map(function ($followup) {
                // Get time from meta
                $timeMeta = $followup->metas->firstWhere('meta_key', 'followups_time');
                $followup->followups_time = $timeMeta ? $timeMeta->meta_value : '00:00:00';
                return $followup;
            })
            ->groupBy('followup_date');

        $selectedDate = $request->query('date');
        $selectedTime = $request->query('time');

        // If no date is selected, default to today
        if (!$selectedDate) {
            $selectedDate = now()->format('Y-m-d');
        }

        // Initialize variables
        $followupMetaValues = [];
        $metaKeys = [
            'pt_status',
            'temperature',
            'weight',
            'spo2',
            'blood_pressure',
            'pulse',
            'rbs',
            'diagnosis',
            'hb',
            'tc',
            'pc',
            'mp',
            'hb1ac',
            'fbs',
            'pp2bs',
            's_widal',
            'usg',
            'x_ray',
            'sgpt',
            's_creatinine',
            'ns1ag',
            'dengue_igm',
            's_cholesterol',
            's_triglyceride',
            'hdl',
            'ldl',
            'vldl',
            's_b12',
            's_d3',
            'urine',
            's_t3',
            'crp',
            's_t4',
            's_tsh',
            'esr',
            'notes',
            'reference_by',
            'referto',
            'specific_test',
            'total_payment',
            'given_payment',
            'payment_method',
            'due_payment'
        ];

        foreach ($metaKeys as $key) {
            $followupMetaValues[$key] = [''];
        }

        $treatments = [
            'inside' => [],
            'homeo' => [],
            'prescription' => [],
            'indoor' => [],
            'other' => []
        ];

        if ($selectedDate) {
            $query = $patient->followups()
                ->whereDate('followup_date', $selectedDate);

            $followup = null;

            // If specific time is selected, filter by time from meta
            if ($selectedTime) {
                $followups = $query->with('metas')->get();
                foreach ($followups as $f) {
                    $timeMeta = $f->metas->firstWhere('meta_key', 'followups_time');
                    if ($timeMeta && $timeMeta->meta_value == $selectedTime) {
                        $followup = $f;
                        break;
                    }
                }
            } else {
                $followup = $query->latest('created_at')->first();
            }

            if ($followup) {
                $allFollowupMetas = $followup->metas()->get();

                foreach ($metaKeys as $key) {
                    $values = $allFollowupMetas
                        ->filter(function ($meta) use ($key) {
                            return $meta->meta_key === $key ||
                                Str::startsWith($meta->meta_key, $key . '_');
                        })
                        ->sortBy(function ($meta) {
                            if (preg_match('/_(\d+)$/', $meta->meta_key, $matches)) {
                                return (int) $matches[1];
                            }
                            return 0;
                        })
                        ->pluck('meta_value')
                        ->values()
                        ->toArray();

                    $followupMetaValues[$key] = $values;
                }

                $followupTreatments = PatientTreatment::where('followup_id', $followup->id)->get();

                foreach ($followupTreatments as $treatment) {
                    $type = $treatment->type;
                    if (array_key_exists($type, $treatments)) {
                        $treatments[$type][] = [
                            'medicine' => $treatment->medicine,
                            'dose' => $treatment->dose,
                            'timing' => $treatment->timing,
                            'note' => $treatment->note
                        ];
                    }
                }
            }
        }

        $doctors = User::where('user_role', 6)->get();

        // Get active charges
        $charges = Charges::where('delete_status', '0')
            ->orderBy('charges_name')
            ->get();

        return view('branches.profile.add_follow_up', compact(
            'patient',
            'metaKeys',
            'selectedDate',
            'selectedTime',
            'followupDates',
            'treatments',
            'followupMetaValues',
            'doctors',
            'followup',
            'charges'
        ));
    }

    public function getFollowupHistory(Request $request, $patient_id)
    {
        try {
            $patient = PatientInquiry::where('patient_id', $patient_id)->firstOrFail();
            $date = $request->query('date');
            $time = $request->query('time');

            if (!$date) {
                return response()->json([
                    'success' => false,
                    'html' => '<div class="alert alert-warning">Please select a date first.</div>'
                ]);
            }

            // Get all followups for the selected date with their metas
            $followups = Followups::with(['metas', 'treatments'])
                ->where('patient_id', $patient->patient_id)
                ->whereDate('followup_date', $date)
                ->orderBy('created_at', 'asc')
                ->get()
                ->map(function ($followup) {
                    // Add followups_time from meta to the followup object
                    $timeMeta = $followup->metas->firstWhere('meta_key', 'followups_time');
                    $followup->followups_time = $timeMeta ? $timeMeta->meta_value : '00:00:00';
                    return $followup;
                });

            // If specific time is selected, filter by time
            if ($time) {
                $followups = $followups->filter(function ($followup) use ($time) {
                    return $followup->followups_time == $time;
                });
            }

            if ($followups->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'html' => '<div class="alert alert-warning">No follow-up data found for this date.</div>'
                ]);
            }

            // Prepare meta keys for the view
            $metaKeys = [
                'pt_status',
                'temperature',
                'weight',
                'spo2',
                'blood_pressure',
                'pulse',
                'rbs',
                'diagnosis',
                'hb',
                'tc',
                'pc',
                'mp',
                'hb1ac',
                'fbs',
                'pp2bs',
                's_widal',
                'usg',
                'x_ray',
                'sgpt',
                's_creatinine',
                'ns1ag',
                'dengue_igm',
                's_cholesterol',
                's_triglyceride',
                'hdl',
                'ldl',
                'vldl',
                's_b12',
                's_d3',
                'urine',
                's_t3',
                'crp',
                's_t4',
                's_tsh',
                'esr'
            ];

            $html = view('branches.profile.followup_history_time', [
                'patient' => $patient,
                'followups' => $followups,
                'selectedDate' => $date,
                'selectedTime' => $time,
                'metaKeys' => $metaKeys  // Pass metaKeys to the view
            ])->render();

            return response()->json([
                'success' => true,
                'html' => $html,
                'count' => $followups->count()
            ]);
        } catch (\Exception $e) {
            \Log::error('Followup History Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'html' => '<div class="alert alert-danger">Error loading history data: ' . $e->getMessage() . '</div>'
            ]);
        }
    }

    public function storeFollowUp(Request $request, $patient_id)
    {
        try {
            $patient = PatientInquiry::where('patient_id', $patient_id)->firstOrFail();

            // Check if a followup already exists for this date and time
            $existingFollowups = Followups::with('metas')
                ->where('patient_id', $patient->patient_id)
                ->whereDate('followup_date', $request->followup_date)
                ->get();

            $existingFollowup = null;
            foreach ($existingFollowups as $followup) {
                $timeMeta = $followup->metas->firstWhere('meta_key', 'followups_time');
                if ($timeMeta && $timeMeta->meta_value == $request->followups_time) {
                    $existingFollowup = $followup;
                    break;
                }
            }

            if ($existingFollowup) {
                // Update existing followup
                $followup = $existingFollowup;
                $followup->doctor_id = $request->doctor_id;
                $followup->next_follow_date = $request->next_follow_date;
                $followup->updated_at = now();
                $followup->save();
            } else {
                // Create new followup
                $followup = Followups::create([
                    'patient_id' => $patient->patient_id,
                    'inquiry_id' => $patient->id,
                    'doctor_id' => $request->doctor_id,
                    'followup_date' => $request->followup_date,
                    'next_follow_date' => $request->next_follow_date,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $followup->metas()->delete();

            // Store followups_time as meta
            if ($request->followups_time) {
                FollowupMeta::create([
                    'followup_id' => $followup->id,
                    'meta_key' => 'followups_time',
                    'meta_value' => $request->followups_time
                ]);
            }

            $excluded = [
                '_token',
                'followup_date',
                'followups_time',
                'next_follow_date',
                'inside_medicine',
                'homeo_medicine',
                'prescription_medicine',
                'indoor_medicine',
                'other_medicine'
            ];

            $metaFields = $request->except($excluded);

            foreach ($metaFields as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $index => $item) {
                        if ($item === null || $item === '') {
                            continue;
                        }

                        FollowupMeta::create([
                            'followup_id' => $followup->id,
                            'meta_key' => "{$key}_{$index}",
                            'meta_value' => $item
                        ]);
                    }
                } else {
                    if ($value !== null && $value !== '') {
                        FollowupMeta::create([
                            'followup_id' => $followup->id,
                            'meta_key' => $key,
                            'meta_value' => $value
                        ]);
                    }
                }
            }

            PatientTreatment::where('followup_id', $followup->id)->delete();

            $groups = [
                'inside' => ['dose', 'timing', 'days'],
                'homeo' => ['timing', 'days'],
                'prescription' => ['dose', 'timing', 'days'],
                'indoor' => ['dose', 'note', 'days', 'date', 'time'],
                'other' => ['note'],
            ];

            foreach ($groups as $type => $fields) {
                $medicineKey = $type . '_medicine';

                if ($request->has($medicineKey)) {
                    foreach ($request->$medicineKey as $i => $medicine) {
                        if (!empty($medicine)) {
                            $data = [
                                'followup_id' => $followup->id,
                                'inquiry_id' => null,
                                'patient_id' => $patient->patient_id,
                                'type' => $type,
                                'medicine' => $medicine,
                            ];

                            foreach ($fields as $f) {
                                $fieldName = $type . '_' . $f;
                                $fieldValues = $request->input($fieldName, []);
                                if (isset($fieldValues[$i])) {
                                    $data[$f] = $fieldValues[$i];
                                }
                            }

                            PatientTreatment::create($data);
                        }
                    }
                }
            }

            // Create Invoice and Transactions for Followup Charges
            $totalPayment = $request->input('total_payment', 0);
            if ($totalPayment > 0) {
                // Check if an invoice already exists for this followup to avoid duplicates
                $existingInvoice = Invoice::where('invoice_no', 'LIKE', 'INV-FOL-' . $followup->id . '%')->first();

                if (!$existingInvoice) {
                    // Generate unique invoice number
                    $invoiceNo = 'INV-FOL-' . $followup->id;
                    $counter = 1;
                    $finalInvoiceNo = $invoiceNo;
                    while (Invoice::where('invoice_no', $finalInvoiceNo)->exists()) {
                        $finalInvoiceNo = $invoiceNo . '-' . $counter;
                        $counter++;
                    }

                    // Use the patient's branch_id
                    $branchId = $patient->branch_id ?? 'SVC-0005';

                    // Generate Filename
                    $pNameClean = preg_replace('/[^A-Za-z0-9]/', '', $patient->patient_name ?? 'Patient');
                    $invoiceFile = $pNameClean . 'SVC-' . $finalInvoiceNo . '-' . now()->format('d-m-Y') . '.pdf';

                    $chargesData = [
                        [
                            'charge_id' => null,
                            'charge_name' => 'Followup Charges',
                            'price' => $totalPayment
                        ]
                    ];

                    $givenPayment = $request->input('given_payment', 0);
                    $duePayment = $request->input('due_payment', $totalPayment - $givenPayment);

                    $invoice = Invoice::create([
                        'branch_id' => $branchId,
                        'patient_id' => $patient->id,
                        'invoice_no' => $finalInvoiceNo,
                        'invoice_date' => now()->format('Y-m-d'),
                        'address' => $request->address ?? $patient->address,
                        'phone' => $patient->getMeta('phone'),
                        'price' => $totalPayment,
                        'total_payment' => $totalPayment,
                        'given_payment' => $givenPayment,
                        'due_payment' => $duePayment,
                        'invoice_file' => $invoiceFile,
                        'charges_data' => $chargesData,
                    ]);

                    // Determine branch prefix
                    if ($branchId === 'LB-0007') {
                        $descPrefix = 'LHR Service';
                    } elseif ($branchId === 'BH-00023') {
                        $descPrefix = 'Hydra Service';
                    } elseif ($branchId === 'SVC-0005') {
                        $descPrefix = 'SVC Service';
                    } else {
                        $descPrefix = 'FNF Service';
                    }

                    // Debit Transaction
                    PatientTransaction::create([
                        'patient_id' => $patient->id,
                        'invoice_id' => $invoice->id,
                        'type' => 'debit',
                        'amount' => $totalPayment,
                        'description' => $descPrefix . ' (Followup) - Invoice Generated: ' . $invoice->invoice_no,
                    ]);

                    // Credit Transaction
                    if ($givenPayment > 0) {
                        PatientTransaction::create([
                            'patient_id' => $patient->id,
                            'invoice_id' => $invoice->id,
                            'type' => 'credit',
                            'amount' => $givenPayment,
                            'description' => $descPrefix . ' (Followup) Payment Received (' . ($request->input('payment_method') ?? 'Cash') . ') for Invoice: ' . $invoice->invoice_no,
                        ]);
                    }
                }
            }

            // Redirect back with both date and time
            return redirect()
                ->route('add.follow.up', [
                    'patient_id' => $patient->patient_id,
                    'date' => $request->followup_date,
                    'time' => $request->followups_time
                ])
                ->with('success', 'Follow-up data saved successfully.');
        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'Error saving follow-up data: ' . $e->getMessage());
        }
    }


    public function getFollowupDetails($followup_id)
    {
        try {
            $followup = Followups::with(['metas', 'treatments'])->findOrFail($followup_id);

            // Add followups_time from meta
            $timeMeta = $followup->metas->firstWhere('meta_key', 'followups_time');
            $followup->followups_time = $timeMeta ? $timeMeta->meta_value : '00:00:00';

            // Meta keys for display
            $metaKeys = [
                'pt_status',
                'temperature',
                'weight',
                'spo2',
                'blood_pressure',
                'pulse',
                'rbs',
                'diagnosis',
                'hb',
                'tc',
                'pc',
                'mp',
                'hb1ac',
                'fbs',
                'pp2bs',
                's_widal',
                'usg',
                'x_ray',
                'sgpt',
                's_creatinine',
                'ns1ag',
                'dengue_igm',
                's_cholesterol',
                's_triglyceride',
                'hdl',
                'ldl',
                'vldl',
                's_b12',
                's_d3',
                'urine',
                's_t3',
                'crp',
                's_t4',
                's_tsh',
                'esr'
            ];

            $html = view('branches.profile.partials.single_followup_details', [
                'followup' => $followup,
                'metaKeys' => $metaKeys
            ])->render();

            return response()->json([
                'success' => true,
                'html' => $html
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'html' => '<div class="alert alert-danger">Error loading visit details: ' . $e->getMessage() . '</div>'
            ]);
        }
    }
    public function getFullFollowupDetails($followup_id)
    {
        try {
            $followup = Followups::with(['metas', 'treatments'])->findOrFail($followup_id);
            $patient = PatientInquiry::where('patient_id', $followup->patient_id)->first();

            if (!$patient) {
                return response()->json([
                    'success' => false,
                    'html' => '<div class="alert alert-danger">Patient not found</div>'
                ]);
            }

            // Add followups_time from meta
            $timeMeta = $followup->metas->firstWhere('meta_key', 'followups_time');
            $followup->followups_time = $timeMeta ? $timeMeta->meta_value : '00:00:00';

            $html = view('branches.profile.full_followup_details', [
                'followup' => $followup,
                'patient' => $patient
            ])->render();

            return response()->json([
                'success' => true,
                'html' => $html
            ]);
        } catch (\Exception $e) {
            \Log::error('Full Followup Details Error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'html' => '<div class="alert alert-danger">Error loading full details: ' . $e->getMessage() . '</div>'
            ]);
        }
    }

    // Get all meta values as an array
    public function getAllMetaValues()
    {
        $metas = $this->metas->pluck('meta_value', 'meta_key')->toArray();

        // Handle array values (like weight_0, weight_1)
        $result = [];
        foreach ($metas as $key => $value) {
            if (preg_match('/^(.+)_(\d+)$/', $key, $matches)) {
                $baseKey = $matches[1];
                $index = $matches[2];
                if (!isset($result[$baseKey])) {
                    $result[$baseKey] = [];
                }
                $result[$baseKey][$index] = $value;
            } else {
                $result[$key] = $value;
            }
        }

        // Convert arrays to comma-separated strings for display
        foreach ($result as $key => $value) {
            if (is_array($value)) {
                $result[$key] = implode(', ', array_filter($value));
            }
        }

        return $result;
    }

    // Get meta value with fallback
    public function getMeta($key, $default = null)
    {
        $meta = $this->metas->firstWhere('meta_key', $key);

        if (!$meta) {
            // Check for array values
            $arrayValues = $this->metas
                ->filter(function ($item) use ($key) {
                    return str_starts_with($item->meta_key, $key . '_');
                })
                ->sortBy(function ($item) {
                    if (preg_match('/_(\d+)$/', $item->meta_key, $matches)) {
                        return (int) $matches[1];
                    }
                    return 0;
                })
                ->pluck('meta_value')
                ->values();

            if ($arrayValues->isNotEmpty()) {
                return $arrayValues->implode(', ');
            }

            return $default;
        }

        return $meta->meta_value ?: $default;
    }

     public function updateCharges(Request $request, $id)
    {
        Log::info('updateCharges method called for patient ID: ' . $id);
        Log::info('Request data: ' . json_encode($request->all()));
        
        try {
            DB::beginTransaction();
            
            $patient = PatientInquiry::findOrFail($id);
            Log::info('Patient found: ' . $patient->patient_id . ' with branch: ' . $patient->branch_id);
            
            // Validate input
            $validated = $request->validate([
                'total_payment' => 'nullable|numeric|min:0',
                'given_payment' => 'nullable|numeric|min:0',
                'discount_payment' => 'nullable|numeric|min:0',
                'payment_method' => 'nullable|string|in:Cash,Online,Cheque',
                'due_payment' => 'nullable|numeric|min:0',
            ]);

            // Calculate amounts
            $totalAmount = $validated['total_payment'] ?? 0;
            $paidAmount = $validated['given_payment'] ?? 0;
            $discountAmount = $validated['discount_payment'] ?? 0;
            $dueAmount = $totalAmount - $paidAmount - $discountAmount;

            // Check if patient has existing invoice
            $existingInvoice = Invoice::where('patient_id', $patient->id) // Use database ID for lookup
                ->where('branch_id', $patient->branch_id)
                ->orderBy('created_at', 'desc')
                ->first();

            if ($existingInvoice) {
                // Update existing invoice
                Log::info('Updating existing invoice: ' . $existingInvoice->id . ' for patient: ' . $patient->patient_id);
                
                $existingInvoice->total_payment = $totalAmount;
                $existingInvoice->given_payment = $paidAmount;
                $existingInvoice->discount = $discountAmount;
                $existingInvoice->due_payment = $dueAmount;
                $existingInvoice->save();
                
                $invoice = $existingInvoice;
                Log::info('Invoice updated successfully: ' . $invoice->id);
            } else {
                // Create new invoice - use database ID for patient_id column
                Log::info('Creating new invoice for patient: ' . $patient->patient_id . ' with total: ' . $totalAmount);
                
                // Generate invoice filename
                $branch = Branch::where('branch_id', $patient->branch_id)->first();
                $invoiceFile = $this->generateInvoiceFilename($patient, $branch, 'IPD-' . date('Y') . '-' . str_pad(Invoice::count() + 1, 6, '0', STR_PAD_LEFT));
                
                $invoice = Invoice::create([
                    'branch_id' => $patient->branch_id,
                    'patient_id' => $patient->id, // Use database ID for column
                    'program_id' => 1, // Default program
                    'invoice_no' => 'IPD-' . date('Y') . '-' . str_pad(Invoice::count() + 1, 6, '0', STR_PAD_LEFT),
                    'invoice_date' => now(),
                    'total_payment' => $totalAmount,
                    'given_payment' => $paidAmount,
                    'discount' => $discountAmount,
                    'due_payment' => $dueAmount,
                    'price' => $totalAmount,
                    'invoice_file' => $invoiceFile, // Add invoice file
                ]);
                
                Log::info('New invoice created: ' . $invoice->id . ' with invoice_no: ' . $invoice->invoice_no . ' and file: ' . $invoiceFile);
            }

            // Create transaction if payment was made
            if ($paidAmount > 0) {
                Log::info('Creating transaction for payment: ' . $paidAmount);
                
                PatientTransaction::create([
                    'patient_id' => $patient->id, // Use database ID for transaction
                    'invoice_id' => $invoice->id,
                    'program_id' => $invoice->program_id,
                    'type' => 'credit',
                    'amount' => $paidAmount,
                    'description' => 'IPD Patient Payment - ' . ucfirst($validated['payment_method'] ?? 'Cash'),
                    'created_at' => now(),
                ]);
                
                Log::info('Transaction created successfully');
            }

            DB::commit();

            return redirect()->route('ipd.profile', $id)->with('success', 'Charges updated and invoice synchronized successfully!');
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating charges: ' . $e->getMessage());
            return redirect()->route('ipd.profile', $id)->with('error', 'Error updating charges');
        }
    }
    
    /**
     * Generate invoice filename
     */
    private function generateInvoiceFilename($patient, $branch, $invoiceNo, $timestamp = '')
    {
        $patientName = preg_replace('/[^A-Za-z0-9]/', '', $patient->patient_name ?? 'Patient');
        $branchName = preg_replace('/[^A-Za-z0-9]/', '', $branch->branch_name ?? 'Branch');
        $currentDate = now()->format('d-m-Y');

        // Add timestamp to make filename unique
        $uniquePart = $timestamp ? '-' . $timestamp : '';

        return $patientName . $branchName . '-' . $invoiceNo . $uniquePart . '-' . $currentDate . '.pdf';
    }


    public function viewIpdProfile($id)
    {
        try {
            $patient = PatientInquiry::with(['metas', 'followups' => function($query) {
                $query->with(['metas', 'doctor'])->orderBy('followup_date', 'desc');
            }])->findOrFail($id);
 
            // Build meta array like in editSvcInquiry - needed for Inquiry Details
            $meta = [];
            foreach ($patient->metas as $m) {
                $decoded = json_decode($m->meta_value, true);
                $meta[$m->meta_key] = json_last_error() === JSON_ERROR_NONE ? $decoded : $m->meta_value;
            }
 
            // Get invoice data for charges display
            $invoice = Invoice::where('patient_id', $patient->id)
                ->where('branch_id', $patient->branch_id)
                ->orderBy('created_at', 'desc')
                ->first();
 
            // Only load Indoor Treatment data - other treatments removed
            $treatments = [];
            $treatments['indoor'] = PatientTreatment::where('patient_id', $patient->patient_id)
                ->where('inquiry_id', $patient->id)
                ->where('type', 'indoor')
                ->whereNull('followup_id')
                ->get()
                ->toArray();
 
            // Get doctors for display - needed for Follow Up section
            $doctors = User::where('user_role', 6)
                ->orWhereHas('roles', function ($query) {
                    $query->where('name', 'Doctor');
                })
                ->select('id', 'name', 'email')
                ->orderBy('name')
                ->get();
 
            return view('branches.profile.ipd_profile', compact('patient', 'meta', 'treatments', 'doctors', 'invoice'));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('indoor.patients')->with('error', 'Patient not found.');
        } catch (Exception $e) {
            return redirect()->route('indoor.patients')->with('error', 'Error loading patient profile.');
        }
    }
}