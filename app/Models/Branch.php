<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Branch extends Model
{
    // use SoftDeletes;

    protected $table = 'branches';

    protected $fillable = [
        'branch_id',
        'branch_name',
        'show_branch',
        'address',
        'delete_status',
        'delete_by'
    ];

    // Add this relationship if you need to access invoices
    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'branch_id', 'branch_id');
    }

    
}
