<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'patient_id',
        'program_id',
        'invoice_no',
        'invoice_date',
        'address',
        'phone',
        'price',
        'pending_due',
        'total_payment',
        'discount',
        'given_payment',
        'due_payment',
        'invoice_file',
        'charges_data',
        'programs_data'
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'price' => 'decimal:2',
        'pending_due' => 'decimal:2',
        'total_payment' => 'decimal:2',
        'discount' => 'decimal:2',
        'given_payment' => 'decimal:2',
        'due_payment' => 'decimal:2',
        'charges_data' => 'array',
        'programs_data' => 'array',
    ];

    // Relationships
    public function patient()
    {
        return $this->belongsTo(PatientInquiry::class, 'patient_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'branch_id');
    }

    public function program()
    {
        return $this->belongsTo(ManageProgram::class, 'program_id');
    }

    /**
     * Resolve the patient from the correct table based on branch
     */
    public function getResolvedPatientAttribute()
    {
        $branchId = $this->branch_id;

        // LHR Branch
        if ($branchId === 'LB-0007') {
            return LHRInquiry::find($this->patient_id);
        }

        // Hydra Branch
        if ($branchId === 'BH-00023') {
            return HydraInquiry::find($this->patient_id);
        }

        // SVC Branch
        if ($branchId === 'SVC-0005') {
            // First check PatientInquiry, then fallback to AccInquiry
            $patient = PatientInquiry::find($this->patient_id);
            if ($patient) return $patient;
            
            $acc = AccInquiry::find($this->patient_id);
            if ($acc) {
                // Map patient_f_name for consistency
                $acc->patient_name = $acc->patient_f_name . ' ' . $acc->patient_l_name;
                return $acc;
            }
        }

        // General Fallback
        $acc = AccInquiry::find($this->patient_id);
        if ($acc) {
            $acc->patient_name = $acc->patient_f_name . ' ' . $acc->patient_l_name;
            return $acc;
        }

        return PatientInquiry::find($this->patient_id);
    }

    public function getTotalDueAttribute()
    {
        return $this->due_payment;
    }
}


