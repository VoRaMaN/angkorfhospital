<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FetReport extends Model
{
    protected $fillable = [
        'medical_order_id',
        'female_patient_id', 'female_patient_name', 'female_hn', 'female_dob',
        'male_patient_id', 'male_patient_name', 'male_hn', 'male_dob',
        'procedure', 'fet_date', 'doctor',
        'freeze_datetime', 'thaw_datetime', 'thawing_media',
        'no_of_freeze', 'no_of_thaw', 'lot_no',
        'stage_of_freeze', 'no_of_survival', 'exp_date',
        'no_of_remaining', 'thawing_by',
        'day3_datetime', 'day3_embryo_1', 'day3_embryo_2', 'day3_embryo_3', 'day3_embryo_4', 'day3_embryo_5',
        'day5_datetime', 'day5_embryo_1', 'day5_embryo_2', 'day5_embryo_3', 'day5_embryo_4', 'day5_embryo_5',
        'no_of_et', 'et_volume', 'number_of_transfer', 'et_day',
        'et_catheter', 'number_of_freeze_et', 'et_datetime',
        'et_doctor', 'number_of_discard', 'assisted_hatching',
        'et_embryologist', 'embryologist_report', 'embryologist_approve',
        'remark',
    ];

    public function medicalOrder(): BelongsTo
    {
        return $this->belongsTo(MedicalOrder::class);
    }
}
