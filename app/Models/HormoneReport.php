<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class HormoneReport extends Model
{
    protected $fillable = [
        'medical_order_id',
        'patient_id',
        'specimen',
        'collection_date',
        'collection_time',
        'received_date',
        'received_time',
        'lh',
        'fsh',
        'prolactin',
        'estradiol',
        'progesterone',
        'testosterone',
        'tsh',
        'amh',
        'beta_hcg',
        'remark',
        'reported_by',
        'reported_date',
        'reported_time',
        'approved_by',
        'approved_date',
        'approved_time',
    ];

    public function medicalOrder()
    {
        return $this->belongsTo(MedicalOrder::class);
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
