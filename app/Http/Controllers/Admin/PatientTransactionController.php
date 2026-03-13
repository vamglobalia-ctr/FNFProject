<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PatientTransaction;
use App\Models\PatientInquiry;
use App\Models\Invoice;
use App\Models\LHRInquiry;
use App\Models\HydraInquiry;
use App\Models\AccInquiry;
use App\Models\Branch;
use App\Models\Opt;
use App\Models\OptMeta;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;

class PatientTransactionController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $branchId = !$user->hasRole('Superadmin') ? $user->user_branch : null;

        // Note: Direct grouping on PatientTransaction might lose relationship data easily
        // We aggregate by patient_id and branch (via invoice relation)
        $query = PatientTransaction::query()
            ->select(
                'patient_id',
                DB::raw('MAX(created_at) as last_transaction'),
                DB::raw('SUM(CASE WHEN type = "debit" THEN amount ELSE 0 END) as total_billed'),
                DB::raw('SUM(CASE WHEN type = "credit" THEN amount ELSE 0 END) as total_paid'),
                DB::raw('SUM(CASE WHEN type = "credit" THEN 1 ELSE 0 END) as payment_count')
            );

        // Apply Branch Filtering based on Role/Branch (Collision-Aware)
        $branchFilter = function($q) use ($branchId) {
            $q->whereHas('patient', function($subQ) use ($branchId) {
                $subQ->where('branch_id', $branchId);
            })
            ->orWhereHas('invoice', function($subQ) use ($branchId) {
                $subQ->where('branch_id', $branchId);
            });
        };

        if ($branchId) {
            $query->where($branchFilter);
        }

        // Advanced Date Filtering
        $startDate = null;
        $endDate = null;

        if ($request->filled('date_filter')) {
            switch ($request->date_filter) {
                case 'today':
                    $startDate = Carbon::today()->startOfDay();
                    $endDate = Carbon::today()->endOfDay();
                    break;
                case 'week':
                    $startDate = Carbon::now()->startOfWeek();
                    $endDate = Carbon::now()->endOfWeek();
                    break;
                case 'month':
                    $startDate = Carbon::now()->startOfMonth();
                    $endDate = Carbon::now()->endOfMonth();
                    break;
                case 'custom':
                    if ($request->filled('start_date') && $request->filled('end_date')) {
                        $startDate = Carbon::parse($request->start_date)->startOfDay();
                        $endDate = Carbon::parse($request->end_date)->endOfDay();
                    }
                    break;
            }
        }

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->whereHas('patient', function($q) use ($search) {
                $q->where('patient_name', 'like', "%{$search}%")
                  ->orWhere('patient_id', 'like', "%{$search}%");
            });
        }

        $summary = $query->groupBy('patient_id')
            ->orderBy('last_transaction', 'desc')
            ->paginate(10);

        // Attach resolved patient and correct branch to each summary row
        $summary->getCollection()->transform(function($row) use ($branchId) {
            // Find an invoice to determine the correct branch for this patient session
            $sampleInvoice = Invoice::where('patient_id', $row->patient_id)
                ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                ->latest()
                ->first();
            
            $row->branch_id = $sampleInvoice->branch_id ?? 'Unknown';
            
            // We need a temporary model instance to use the resolution logic
            $tempInvoice = new Invoice();
            $tempInvoice->patient_id = $row->patient_id;
            $tempInvoice->branch_id = $row->branch_id;
            
            $row->patient = $tempInvoice->resolved_patient;
            $row->balance = $row->total_billed - $row->total_paid;

            // Resolve profile image url (if available) for consistent list avatars
            $row->profile_image_url = null;
            if ($row->patient) {
                // Hydra/LHR store profile_image in public storage disk
                if ($row->patient instanceof HydraInquiry || $row->patient instanceof LHRInquiry) {
                    $img = $row->patient->profile_image ?? null;
                    if ($img && Storage::disk('public')->exists($img)) {
                        $row->profile_image_url = asset('storage/' . $img);
                    }
                }
                // SVC PatientInquiry stores profile_image as meta in public/uploads
                elseif ($row->patient instanceof PatientInquiry) {
                    $img = $row->patient->getMeta('profile_image');
                    if ($img && file_exists(public_path($img))) {
                        $row->profile_image_url = asset($img);
                    }
                }
                // AccInquiry and other fallbacks store profile_image in Opt meta
                else {
                    $pid = $row->patient->patient_id ?? null;
                    if ($pid) {
                        $optIds = Opt::where('patient_id', $pid)
                            ->where(function ($q) {
                                $q->whereNull('delete_status')
                                  ->orWhere('delete_status', '')
                                  ->orWhere('delete_status', '0');
                            })
                            ->pluck('id');

                        if ($optIds->isNotEmpty()) {
                            $img = OptMeta::whereIn('opt_id', $optIds)
                                ->where('meta_key', 'profile_image')
                                ->orderByDesc('id')
                                ->value('meta_value');

                            if ($img && file_exists(public_path($img))) {
                                $row->profile_image_url = asset($img);
                            }
                        }
                    }
                }
            }
            
            return $row;
        });
        
        return view('admin.finance.transactions', compact('summary'));
    }

    public function ledger($patient_id, $branch_id)
    {
        // Security check
        $user = auth()->user();
        if (!$user->hasRole('Superadmin') && $user->user_branch !== $branch_id) {
            return abort(403);
        }

        // Resolve Patient info
        $tempInvoice = new Invoice();
        $tempInvoice->patient_id = $patient_id;
        $tempInvoice->branch_id = $branch_id;
        $patient = $tempInvoice->resolved_patient;

        if (!$patient) return abort(404, 'Patient not found');

        // Fetch all transactions
        $transactions = PatientTransaction::with(['program', 'invoice'])
            ->where('patient_id', $patient_id)
            ->where(function($q) use ($branch_id) {
                $q->whereHas('invoice', function($sq) use ($branch_id) {
                    $sq->where('branch_id', $branch_id);
                })->orWhereHas('patient', function($sq) use ($branch_id) {
                    $sq->where('branch_id', $branch_id);
                });
            })
            ->orderBy('created_at', 'asc')
            ->get();

        // Calculate running balance and totals
        $totalBilled = 0;
        $totalPaid = 0;
        $runningBalance = 0;

        // Grouping logic for "Program Receipts"
        $programGroups = [];

        foreach ($transactions as $t) {
            if ($t->type == 'debit') {
                $totalBilled += $t->amount;
                $runningBalance += $t->amount;
            } else {
                $totalPaid += $t->amount;
                $runningBalance -= $t->amount;
            }
            $t->running_balance = $runningBalance;

            // Grouping by Program for better analysis
            $progId = $t->program_id ?? 'general';
            if (!isset($programGroups[$progId])) {
                // Determine Default Service Label based on Branch
                $defaultServiceName = 'FNF Service';
                if ($branch_id === 'LB-0007') {
                    $defaultServiceName = 'LHR Service';
                } elseif ($branch_id === 'BH-00023') {
                    $defaultServiceName = 'Hydra Service';
                } elseif ($branch_id === 'SVC-0005') {
                    $defaultServiceName = 'SVC Service';
                }

                $programGroups[$progId] = [
                    'program_name' => $t->program ? $t->program->program_name : $defaultServiceName,
                    'actual_price' => 0,
                    'total_received' => 0,
                    'payment_count' => 0,
                    'last_payment' => null,
                    'is_completed' => false
                ];
            }

            if ($t->type == 'debit') {
                $programGroups[$progId]['actual_price'] += $t->amount;
            } else {
                $programGroups[$progId]['total_received'] += $t->amount;
                $programGroups[$progId]['payment_count']++;
                $programGroups[$progId]['last_payment'] = $t->created_at;
            }

            // Check completion
            if ($programGroups[$progId]['actual_price'] > 0 && $programGroups[$progId]['total_received'] >= $programGroups[$progId]['actual_price']) {
                $programGroups[$progId]['is_completed'] = true;
            }
        }

        // REVERSE transactions for "Latest First" display
        $transactions = $transactions->reverse();

        return view('admin.finance.ledger', compact(
            'patient', 
            'transactions', 
            'totalBilled', 
            'totalPaid', 
            'branch_id',
            'programGroups'
        ));
    }
}
