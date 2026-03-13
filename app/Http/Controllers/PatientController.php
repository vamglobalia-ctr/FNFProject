<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class PatientController extends Controller
{


    /**
     * Detect branch type from branch ID and name
     */
    private function detectBranchType(?string $branchId, ?string $branchName): string
    {
        $branchPrefix = $branchId ? strtoupper(explode('-', $branchId)[0]) : '';
        $branchNameUpper = strtoupper((string) $branchName);
        
        // First check by prefix
        if (in_array($branchPrefix, ['SVC', 'LHR', 'HYDRA'], true)) {
            return $branchPrefix;
        }
        
        // Fallback to name-based detection
        if (str_contains($branchNameUpper, 'LHR')) {
            return 'LHR';
        } elseif (str_contains($branchNameUpper, 'HYDRA')) {
            return 'HYDRA';
        } elseif (str_contains($branchNameUpper, 'SVC')) {
            return 'SVC';
        }
        
        return 'OTHER'; // All other branches
    }

    /**
     * Apply branch filtering for SVC inquiries
     */
    private function applySvcBranchFilter($query, ?string $branchId)
    {
        if (!$branchId) return $query;
        
        return $query->where('branch_id', $branchId);
    }

    /**
     * Apply branch filtering for LHR inquiries
     */
    private function applyLhrBranchFilter($query, ?string $branchId)
    {
        if (!$branchId) return $query;
        
        return $query->where(function ($q) use ($branchId) {
            $q->where('branch_id', $branchId)
              ->orWhere('branch', 'LIKE', '%LHR%')
              ->orWhere('branch_id', 'LIKE', '%LHR%');
        });
    }

    /**
     * Apply branch filtering for Hydra inquiries
     */
    private function applyHydraBranchFilter($query, ?string $branchId)
    {
        if (!$branchId) return $query;
        
        return $query->where(function ($q) use ($branchId) {
            $q->where('branch_id', $branchId)
              ->orWhere('branch', 'LIKE', '%HYDRA%')
              ->orWhere('branch_id', 'LIKE', '%HYDRA%');
        });
    }

    /**
     * Apply branch filtering for Other Branch inquiries
     */
    private function applyOtherBranchFilter($query, ?string $branchId)
    {
        if (!$branchId) return $query;
        
        return $query->where('branch_id', $branchId);
    }

    /**
     * Get diet chart metrics for Other Branch
     */
    private function getOtherBranchMetrics(?string $branchId): array
    {
        // Diet Chart (DC) count
        $dietChartCount = DB::table('diet_plans')
            ->when($branchId, function ($query) use ($branchId) {
                return $query->where('branch_id', $branchId);
            })
            ->count();
        
        // Followup count (patients with next_follow_up_date)
        $followupCount = DB::table('diet_plans')
            ->when($branchId, function ($query) use ($branchId) {
                return $query->where('branch_id', $branchId);
            })
            ->whereNotNull('next_follow_up_date')
            ->distinct('patient_id')
            ->count('patient_id');
        
        // Joined count (unique patients with diet plans)
        $joinedCount = DB::table('diet_plans')
            ->when($branchId, function ($query) use ($branchId) {
                return $query->where('branch_id', $branchId);
            })
            ->distinct('patient_id')
            ->count('patient_id');
        
        // Pending count (joined - followup)
        $pendingCount = $joinedCount - $followupCount;
        
        return [
            'diet_chart_count' => $dietChartCount,
            'followup_count' => $followupCount,
            'joined_count' => $joinedCount,
            'pending_count' => max(0, $pendingCount)
        ];
    }

    public function getTotalPatients(Request $request)
    {
        try {
            $user = Auth::user();
            $branchId = $request->input('branch_id');
            $branchName = $request->input('branch_name');

            // If branch_name is provided, convert it to branch_id
            if (!$branchId && $branchName) {
                $branch = DB::table('branches')
                    ->where('branch_name', $branchName)
                    ->first();

                if ($branch) {
                    $branchId = $branch->branch_id;
                }
            }

            // If branch_id is provided but branch_name is missing, resolve it
            if ($branchId && !$branchName) {
                $branch = DB::table('branches')->where('branch_id', $branchId)->first();
                if ($branch) {
                    $branchName = $branch->branch_name;
                }
            }

            // Get branch ID based on user role
            if (!$user->hasRole('Superadmin')) {
                $userBranch = $user->user_branch;
                $branch = DB::table('branches')
                    ->where('branch_name', $userBranch)
                    ->orWhere('branch_id', $userBranch)
                    ->first();

                if ($branch) {
                    $branchId = $branch->branch_id;
                    $branchName = $branch->branch_name;
                } else {
                    return response()->json([
                        'success' => true,
                        'patient_count' => 0,
                        'branch_id' => $branchId,
                        'message' => 'No branch found for user'
                    ]);
                }
            }


            $totalCount = 0;

            $newPatientCount = 0;
            $followupPatientCount = 0;
            $ipdPatientCount = 0;
            $opdPatientCount = 0;


            $patientInquiryCount = DB::table('patient_inquiry')
                ->when($branchId, function ($query) use ($branchId) {
                    return $query->where('branch_id', $branchId);
                })
                ->count();


            $lhrCount = DB::table('lhr_inquiries')
                ->when($branchId, function ($query) use ($branchId) {
                    return $this->applyLhrBranchFilter($query, $branchId);
                })
                ->count();


            $hydraCount = DB::table('hydra_inquiries')
                ->when($branchId, function ($query) use ($branchId) {
                    return $this->applyHydraBranchFilter($query, $branchId);
                })
                ->count();
            $acccount = DB::table('acc_inquirys')
                ->when($branchId, function ($query) use ($branchId) {
                    $prefix = explode('-', $branchId)[0];

                    $query->where(function ($q) use ($branchId, $prefix) {
                        $q->where('branch_id', $branchId)
                            ->orWhere('branch', 'LIKE', '%' . $prefix . '%');
                    });
                })
                ->count();

            $totalCount = $patientInquiryCount + $lhrCount + $hydraCount + $acccount;

            $branchPrefix = $this->detectBranchType($branchId, $branchName);

            if ($branchPrefix === 'LHR') {
                $lhrBase = DB::table('lhr_inquiries')
                    ->when($branchId, function ($query) use ($branchId) {
                        return $this->applyLhrBranchFilter($query, $branchId);
                    });

                $newPatientCount = (clone $lhrBase)
                    ->whereNotExists(function ($sub) {
                        $sub->select(DB::raw(1))
                            ->from('lhr_followups as lf')
                            ->whereColumn('lf.patient_id', 'lhr_inquiries.patient_id')
                            ->whereColumn('lf.branch_id', 'lhr_inquiries.branch_id');
                    })
                    ->count();

                $followupPatientCount = (clone $lhrBase)
                    ->whereExists(function ($sub) {
                        $sub->select(DB::raw(1))
                            ->from('lhr_followups as lf')
                            ->whereColumn('lf.patient_id', 'lhr_inquiries.patient_id')
                            ->whereColumn('lf.branch_id', 'lhr_inquiries.branch_id');
                    })
                    ->count();
            } elseif ($branchPrefix === 'HYDRA') {
                $hydraBase = DB::table('hydra_inquiries')
                    ->when($branchId, function ($query) use ($branchId) {
                        return $this->applyHydraBranchFilter($query, $branchId);
                    });

                $newPatientCount = (clone $hydraBase)
                    ->whereNotExists(function ($sub) {
                        $sub->select(DB::raw(1))
                            ->from('hydra_patient_followups as hf')
                            ->whereColumn('hf.hydra_inquiry_id', 'hydra_inquiries.id');
                    })
                    ->count();

                $followupPatientCount = (clone $hydraBase)
                    ->whereExists(function ($sub) {
                        $sub->select(DB::raw(1))
                            ->from('hydra_patient_followups as hf')
                            ->whereColumn('hf.hydra_inquiry_id', 'hydra_inquiries.id');
                    })
                    ->count();
            } elseif ($branchPrefix === 'SVC') {
                $svcBase = DB::table('patient_inquiry')
                    ->when($branchId, function ($query) use ($branchId) {
                        return $this->applySvcBranchFilter($query, $branchId);
                    });

                $newPatientCount = (clone $svcBase)
                    ->whereNotExists(function ($sub) {
                        $sub->select(DB::raw(1))
                            ->from('patient_followups as pf')
                            ->whereColumn('pf.inquiry_id', 'patient_inquiry.id');
                    })
                    ->count();

                $followupPatientCount = (clone $svcBase)
                    ->whereExists(function ($sub) {
                        $sub->select(DB::raw(1))
                            ->from('patient_followups as pf')
                            ->whereColumn('pf.inquiry_id', 'patient_inquiry.id');
                    })
                    ->count();

                $ipdPatientCount = (clone $svcBase)
                    ->join('patients_metas as pm', 'pm.patient_id', '=', 'patient_inquiry.id')
                    ->where('pm.meta_key', 'pt_status')
                    ->where('pm.meta_value', 'IPD')
                    ->count();
                                                                                
                $opdPatientCount = (clone $svcBase)
                    ->join('patients_metas as pm', 'pm.patient_id', '=', 'patient_inquiry.id')
                    ->where('pm.meta_key', 'pt_status')
                    ->where('pm.meta_value', 'OPD')
                    ->count();
            } elseif ($branchPrefix === 'OTHER') {
                // For Other Branch, get diet chart metrics
                $otherMetrics = $this->getOtherBranchMetrics($branchId);
                
                return response()->json([
                    'success' => true,
                    'patient_count' => $otherMetrics['joined_count'],
                    'diet_chart_count' => $otherMetrics['diet_chart_count'],
                    'followup_count' => $otherMetrics['followup_count'],
                    'joined_count' => $otherMetrics['joined_count'],
                    'pending_count' => $otherMetrics['pending_count'],
                    'branch_id' => $branchId,
                    'branch_name' => $branchName,
                    'message' => 'Other Branch metrics from diet plans'
                ]);
            }

            return response()->json([
                'success' => true,
                'patient_count' => $totalCount,
                'new_patient_count' => $newPatientCount,
                'followup_patient_count' => $followupPatientCount,
                'ipd_patient_count' => $ipdPatientCount,
                'opd_patient_count' => $opdPatientCount,
                'branch_id' => $branchId,
                'branch_name' => $branchName,
                'message' => 'Total patients count from all sources'
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getTotalPatients: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'patient_count' => 0,
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function getFilteredPatients(Request $request)
    {
        try {
            $user = Auth::user();
            $branchId = $request->input('branch_id');
            $branchName = $request->input('branch_name');
            $fromDate = $request->input('from_date');
            $toDate = $request->input('to_date');


            if (!$branchId && $branchName) {
                $branch = DB::table('branches')
                    ->where('branch_name', $branchName)
                    ->first();

                if ($branch) {
                    $branchId = $branch->branch_id;
                    $branchName = $branch->branch_name;
                }
            }

            // If branch_id is provided but branch_name is missing, resolve it
            if ($branchId && !$branchName) {
                $branch = DB::table('branches')->where('branch_id', $branchId)->first();
                if ($branch) {
                    $branchName = $branch->branch_name;
                }
            }


            if (!$user->hasRole('Superadmin')) {
                $userBranch = $user->user_branch;
                $branch = DB::table('branches')
                    ->where('branch_name', $userBranch)
                    ->orWhere('branch_id', $userBranch)
                    ->first();

                if ($branch) {
                    $branchId = $branch->branch_id;
                    $branchName = $branch->branch_name;
                } else {
                    return response()->json([
                        'success' => true,
                        'patient_count' => 0,
                        'branch_id' => $branchId,
                        'from_date' => $fromDate,
                        'to_date' => $toDate,
                        'breakdown' => []
                    ]);
                }
            }


            $patientInquiryCount = 0;
            $lhrCount = 0;
            $hydraCount = 0;
            $totalCount = 0;
            $acccount = 0;

            $newPatientCount = 0;
            $followupPatientCount = 0;
            $ipdPatientCount = 0;
            $opdPatientCount = 0;

            $patientInquiryQuery = DB::table('patient_inquiry');

            if ($branchId) {
                $patientInquiryQuery->where('branch_id', $branchId);
            }

            if (!empty($fromDate) && !empty($toDate)) {
                $patientInquiryQuery->whereBetween('inquiry_date', [$fromDate, $toDate]);
            }

            $patientInquiryCount = $patientInquiryQuery->count();


            $lhrQuery = DB::table('lhr_inquiries');

            if ($branchId) {
                $lhrQuery->where(function ($q) use ($branchId) {
                    $q->where('branch_id', $branchId)
                        ->orWhere('branch', 'LIKE', '%LHR%')
                        ->orWhere('branch_id', 'LIKE', '%LHR%');
                });
            }

            if (!empty($fromDate) && !empty($toDate)) {
                $lhrQuery->whereBetween('inquiry_date', [$fromDate, $toDate]);
            }

            $lhrCount = $lhrQuery->count();


            $hydraQuery = DB::table('hydra_inquiries');

            if ($branchId) {
                $hydraQuery->where(function ($q) use ($branchId) {
                    $q->where('branch_id', $branchId)
                        ->orWhere('branch', 'LIKE', '%HYDRA%')
                        ->orWhere('branch_id', 'LIKE', '%HYDRA%');
                });
            }

            if (!empty($fromDate) && !empty($toDate)) {
                $hydraQuery->whereBetween('inquiry_date', [$fromDate, $toDate]);
            }

            $hydraCount = $hydraQuery->count();

            $acccount = DB::table('acc_inquirys');

            if ($branchId) {
                $acccount->where(function ($q) use ($branchId) {
                    $q->where('branch_id', $branchId)
                        ->orWhere('branch', 'LIKE', '%' . explode('-', $branchId)[0] . '%');
                });
            }

            if (!empty($fromDate) && !empty($toDate)) {
                $acccount->whereBetween('inquiry_date', [$fromDate, $toDate]);
            }

            $acccount = $acccount->count();


            $totalCount = $patientInquiryCount + $lhrCount + $hydraCount + $acccount;

            $branchPrefix = $branchId ? strtoupper(explode('-', $branchId)[0]) : '';
            $branchNameUpper = strtoupper((string) $branchName);
            if (!in_array($branchPrefix, ['SVC', 'LHR', 'HYDRA'], true)) {
                if (str_contains($branchNameUpper, 'LHR')) {
                    $branchPrefix = 'LHR';
                } elseif (str_contains($branchNameUpper, 'HYDRA')) {
                    $branchPrefix = 'HYDRA';
                } elseif (str_contains($branchNameUpper, 'SVC')) {
                    $branchPrefix = 'SVC';
                }
            }

            if ($branchPrefix === 'LHR') {
                $lhrBase = DB::table('lhr_inquiries');

                if ($branchId) {
                    $lhrBase = $this->applyLhrBranchFilter($lhrBase, $branchId);
                }

                if (!empty($fromDate) && !empty($toDate)) {
                    $lhrBase->whereBetween('inquiry_date', [$fromDate, $toDate]);
                }

                $newPatientCount = (clone $lhrBase)
                    ->whereNotExists(function ($sub) {
                        $sub->select(DB::raw(1))
                            ->from('lhr_followups as lf')
                            ->whereColumn('lf.patient_id', 'lhr_inquiries.patient_id')
                            ->whereColumn('lf.branch_id', 'lhr_inquiries.branch_id');
                    })
                    ->count();

                $followupPatientCount = (clone $lhrBase)
                    ->whereExists(function ($sub) {
                        $sub->select(DB::raw(1))
                            ->from('lhr_followups as lf')
                            ->whereColumn('lf.patient_id', 'lhr_inquiries.patient_id')
                            ->whereColumn('lf.branch_id', 'lhr_inquiries.branch_id');
                    })
                    ->count();
            } elseif ($branchPrefix === 'HYDRA') {
                $hydraBase = DB::table('hydra_inquiries');

                if ($branchId) {
                    $hydraBase = $this->applyHydraBranchFilter($hydraBase, $branchId);
                }

                if (!empty($fromDate) && !empty($toDate)) {
                    $hydraBase->whereBetween('inquiry_date', [$fromDate, $toDate]);
                }

                $newPatientCount = (clone $hydraBase)
                    ->whereNotExists(function ($sub) {
                        $sub->select(DB::raw(1))
                            ->from('hydra_patient_followups as hf')
                            ->whereColumn('hf.hydra_inquiry_id', 'hydra_inquiries.id');
                    })
                    ->count();

                $followupPatientCount = (clone $hydraBase)
                    ->whereExists(function ($sub) {
                        $sub->select(DB::raw(1))
                            ->from('hydra_patient_followups as hf')
                            ->whereColumn('hf.hydra_inquiry_id', 'hydra_inquiries.id');
                    })
                    ->count();
            } elseif ($branchPrefix === 'SVC') {
                $svcBase = DB::table('patient_inquiry');

                if ($branchId) {
                    $svcBase->where('branch_id', $branchId);
                }

                if (!empty($fromDate) && !empty($toDate)) {
                    $svcBase->whereBetween('inquiry_date', [$fromDate, $toDate]);
                }

                $newPatientCount = (clone $svcBase)
                    ->whereNotExists(function ($sub) {
                        $sub->select(DB::raw(1))
                            ->from('patient_followups as pf')
                            ->whereColumn('pf.inquiry_id', 'patient_inquiry.id');
                    })
                    ->count();

                $followupPatientCount = (clone $svcBase)
                    ->whereExists(function ($sub) {
                        $sub->select(DB::raw(1))
                            ->from('patient_followups as pf')
                            ->whereColumn('pf.inquiry_id', 'patient_inquiry.id');
                    })
                    ->count();

                $ipdPatientCount = (clone $svcBase)
                    ->join('patients_metas as pm', 'pm.patient_id', '=', 'patient_inquiry.id')
                    ->where('pm.meta_key', 'pt_status')
                    ->where('pm.meta_value', 'IPD')
                    ->count();

                $opdPatientCount = (clone $svcBase)
                    ->join('patients_metas as pm', 'pm.patient_id', '=', 'patient_inquiry.id')
                    ->where('pm.meta_key', 'pt_status')
                    ->where('pm.meta_value', 'OPD')
                    ->count();
            } elseif ($branchPrefix === 'OTHER') {
                // For Other Branch, get diet chart metrics with date filtering
                $dietChartQuery = DB::table('diet_plans')
                    ->when($branchId, function ($query) use ($branchId) {
                        return $query->where('branch_id', $branchId);
                    });
                
                if (!empty($fromDate) && !empty($toDate)) {
                    $dietChartQuery->whereBetween('date', [$fromDate, $toDate]);
                }
                
                $dietChartCount = $dietChartQuery->count();
                
                // Followup count (patients with next_follow_up_date)
                $followupQuery = DB::table('diet_plans')
                    ->when($branchId, function ($query) use ($branchId) {
                        return $query->where('branch_id', $branchId);
                    })
                    ->whereNotNull('next_follow_up_date');
                
                if (!empty($fromDate) && !empty($toDate)) {
                    $followupQuery->whereBetween('date', [$fromDate, $toDate]);
                }
                
                $followupCount = $followupQuery->distinct('patient_id')->count('patient_id');
                
                // Joined count (unique patients with diet plans)
                $joinedQuery = DB::table('diet_plans')
                    ->when($branchId, function ($query) use ($branchId) {
                        return $query->where('branch_id', $branchId);
                    });
                
                if (!empty($fromDate) && !empty($toDate)) {
                    $joinedQuery->whereBetween('date', [$fromDate, $toDate]);
                }
                
                $joinedCount = $joinedQuery->distinct('patient_id')->count('patient_id');
                
                // Pending count (joined - followup)
                $pendingCount = max(0, $joinedCount - $followupCount);
                
                return response()->json([
                    'success' => true,
                    'patient_count' => $joinedCount,
                    'diet_chart_count' => $dietChartCount,
                    'followup_count' => $followupCount,
                    'joined_count' => $joinedCount,
                    'pending_count' => $pendingCount,
                    'branch_id' => $branchId,
                    'branch_name' => $branchName,
                    'from_date' => $fromDate,
                    'to_date' => $toDate
                ]);
            }

            return response()->json([
                'success' => true,
                'patient_count' => $totalCount,
                'new_patient_count' => $newPatientCount,
                'followup_patient_count' => $followupPatientCount,
                'ipd_patient_count' => $ipdPatientCount,
                'opd_patient_count' => $opdPatientCount,
                'branch_id' => $branchId,
                'branch_name' => $branchName,
                'from_date' => $fromDate,
                'to_date' => $toDate
            ]);
        } catch (\Exception $e) {
            Log::error('Error in getFilteredPatients: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'patient_count' => 0,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
