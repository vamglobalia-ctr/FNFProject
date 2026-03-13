<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AccInquiry extends Model
{
    use HasFactory;
    protected $table = 'acc_inquirys';
    protected $primaryKey = 'id';

    protected $fillable = [
        'patient_id',
        'branch',
        'branch_id',
        'patient_f_name',
        'patient_m_name',
        'patient_l_name',
        'gender',
        'phone_no',
        'age',
        'height',
        'weight',
        'bmi',
        'address',
        'refrance',
        'reference_to',
        'email',
        'inquiry_date',
        'inquiry_time',
        'inquery_given_by',
        'payment',
        'inquiry_foc',
        'diagnosis',
        'pod_vld_date',
        'user_status',
        'status_history',
        'client_old_new',
        'next_followup_date',
        'delete_status',
        'delete_by',
    ];

    protected $casts = [
        'height' => 'decimal:2',
        'weight' => 'decimal:2',
        'bmi' => 'decimal:2',

        'age' => 'integer',
        'branch_id' => 'string',
        'status_history' => 'array', 
        // remove 'inquiry_date' and 'inquiry_time' from here
    ];

    // Add this method inside the model
    public function getInquiryDateAttribute($value)
    {
        if (!$value) return null;

        try {
            return \Carbon\Carbon::createFromFormat('d/m/Y', $value);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getInquiryTimeAttribute($value)
    {
        if (!$value) return null;

        try {
            return \Carbon\Carbon::createFromFormat('H:i', $value);
        } catch (\Exception $e) {
            return null;
        }
    }

    public function getStatusHistoryAttribute($value)
    {
        if (is_array($value)) {
            return $value;
        }
        
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [];
        }
        
        return [];
    }

    public function setStatusHistoryAttribute($value)
    {
        if (is_array($value)) {
            $this->attributes['status_history'] = json_encode($value);
        } elseif (is_string($value)) {
            $this->attributes['status_history'] = $value;
        } else {
            $this->attributes['status_history'] = json_encode([]);
        }
    }

    public function getDisplayStatusesAttribute()
    {
        return $this->status_history ?? [];
    }

    public function hasStatus($status)
    {
        return in_array($status, $this->status_history ?? []);
    }

    public function hasAnyStatus(array $statuses)
    {
        return !empty(array_intersect($statuses, $this->status_history ?? []));
    }

    public function getFormattedDateAttribute()
    {
        return $this->date ? $this->date->format('d/m/Y') : 'N/A';
    }

    public function getFormattedTimeAttribute()
    {
        return $this->time ? $this->time->format('H:i') : 'N/A';
    }

    public function getPatientNameAttribute()
    {
        return trim($this->patient_f_name . ' ' . $this->patient_m_name . ' ' . $this->patient_l_name);
    }
    
    public $timestamps = false;
    
}