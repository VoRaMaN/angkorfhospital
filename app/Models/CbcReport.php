<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CbcReport extends Model
{
    protected $fillable = [
        'medical_order_id',
        'patient_id',
        'lab_id',
        'requested_by',
        'requested_date',
        'analysis_date',
        'wbc',
        'rbc',
        'hemoglobin',
        'hematocrit',
        'mcv',
        'mch',
        'mchc',
        'platelets',
        'rdw',
        'neutrophils',
        'lymphocytes',
        'monocytes',
        'eosinophils',
        'basophils',
        'remark',
        'reported_by',
        'reported_date',
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
