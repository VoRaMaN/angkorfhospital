<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SA Report</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 12px; color: #000; padding: 30px 40px; }

        .header { display: table; width: 100%; margin-bottom: 12px; border-bottom: 2px solid #000; padding-bottom: 10px; }
        .header-left { display: table-cell; vertical-align: top; width: 55%; }
        .header-left img { width: 120px; display: block; margin-bottom: 4px; }
        .header-left p { font-size: 10px; margin: 1px 0; color: #333; }
        .header-right { display: table-cell; vertical-align: top; text-align: right; width: 45%; font-size: 11px; }
        .header-right p { margin: 2px 0; }
        .header-right .label { font-weight: bold; }

        .lab-title { text-align: center; margin: 8px 0; }
        .lab-title p { font-weight: bold; font-size: 13px; }
        .lab-title .sub { font-size: 12px; font-weight: normal; }

        .section-title { font-weight: bold; font-size: 12px; text-decoration: underline; margin-bottom: 6px; }

        .wife-row { display: table; width: 100%; margin-bottom: 6px; }
        .wife-cell { display: table-cell; width: 50%; font-size: 11px; padding: 2px 0; }
        .underline-val { border-bottom: 1px solid #000; display: inline-block; min-width: 140px; }

        .data-table { display: table; width: 100%; margin-top: 8px; }
        .data-left { display: table-cell; width: 55%; vertical-align: top; padding-right: 10px; }
        .data-right { display: table-cell; width: 45%; vertical-align: top; text-align: center; }

        table.params { width: 100%; border-collapse: collapse; font-size: 11px; }
        table.params th { text-align: left; font-weight: normal; color: #666; font-size: 10px; padding: 2px 4px; }
        table.params td { padding: 3px 4px; border-bottom: 1px solid #e0e0e0; }
        table.params td.indent { padding-left: 16px; }
        table.params td.indent2 { padding-left: 28px; }
        table.params td.bold { font-weight: bold; }
        table.params td.norm { color: #555; font-size: 10px; }

        .img-label { font-size: 10px; font-weight: bold; color: #555; margin-bottom: 3px; }
        .sperm-img { max-width: 110px; border: 1px solid #ccc; margin-bottom: 12px; }

        .times-row { display: table; width: 100%; margin-top: 10px; padding-top: 8px; border-top: 1px solid #ccc; font-size: 11px; }
        .times-cell { display: table-cell; width: 50%; padding: 2px 0; }

        .remark { margin-top: 8px; font-size: 11px; }

        .sig-row { display: table; width: 100%; margin-top: 12px; padding-top: 8px; border-top: 1px solid #ccc; font-size: 11px; }
        .sig-cell { display: table-cell; width: 50%; }
        .sig-cell .sig-label { font-weight: bold; font-size: 10px; color: #555; text-transform: uppercase; margin-bottom: 2px; }
        .sig-cell .sig-date { font-size: 10px; color: #555; margin-top: 2px; }
    </style>
</head>
<body>

    <!-- Header -->
    <div class="header">
        <div class="header-left">
            <img src="{{ public_path('images/logo1.png') }}" alt="Angkor-F Hospital" />
            <p>#National Road 6A, Salakonseng Village,</p>
            <p>Sangkat Svay Dangkum, Siem Reap, Cambodia</p>
            <p>Tel: (855) 31 3 5555 88 | (855) 12 881 307</p>
            <p>E-mail: angkorfhospital@gmail.com</p>
        </div>
        <div class="header-right">
            <p><span class="label">Name:</span> {{ $report->patient_name ?? '—' }}</p>
            <p><span class="label">HN:</span> {{ $report->patient_hn ?? '—' }} &nbsp;&nbsp; <span class="label">SEX:</span> MALE</p>
            <p><span class="label">DOB:</span> {{ $report->patient_dob ?? '—' }} &nbsp;&nbsp; <span class="label">Age:</span> {{ $report->patient_age ? $report->patient_age.' Yrs.' : '—' }}</p>
            <p><span class="label">Date:</span> {{ $reportDate }}</p>
            <p><span class="label">Doctor:</span> {{ $report->doctor_name ?? '—' }}</p>
        </div>
    </div>

    <!-- Lab Title -->
    <div class="lab-title">
        <p>IVF LAB</p>
        <p class="sub">( Semen Analysis Report )</p>
    </div>

    <!-- Section title -->
    <p class="section-title">Semen Analysis</p>

    <!-- Wife row -->
    <div class="wife-row">
        <div class="wife-cell">
            <strong>Wife's name:</strong>
            <span class="underline-val">{{ $report->wife_name ?? '' }}</span>
        </div>
        <div class="wife-cell">
            <strong>H.N:</strong> —
        </div>
    </div>

    <!-- Parameters + Images side by side -->
    <div class="data-table">
        <div class="data-left">
            <table class="params">
                <thead>
                    <tr>
                        <th style="width:50%">Parameter</th>
                        <th style="width:25%">Value</th>
                        <th style="width:25%">Normal Range</th>
                    </tr>
                </thead>
                <tbody>
                    <tr><td>Abstinence day</td><td>{{ $report->abstinence_days ? $report->abstinence_days.' Days' : '—' }}</td><td></td></tr>
                    <tr><td>Appearance</td><td>{{ $report->appearance ?? '—' }}</td><td></td></tr>
                    <tr><td>Liquefaction</td><td>{{ $report->liquefaction ?? '—' }}</td><td class="norm">30 mins</td></tr>
                    <tr><td>Viscosity</td><td>{{ $report->viscosity ?? '—' }}</td><td></td></tr>
                    <tr><td>pH</td><td>{{ $report->ph ?? '—' }}</td><td class="norm">7.2 – 8.0</td></tr>
                    <tr><td>Viability</td><td>{{ $report->viability !== null ? $report->viability.'%' : '—' }}</td><td class="norm">&gt; 75</td></tr>
                    <tr><td>Volume</td><td>{{ $report->volume !== null ? $report->volume.' ml.' : '—' }}</td><td class="norm">&gt; 2</td></tr>
                    <tr><td>Count</td><td>{{ $report->count_per_ml !== null ? $report->count_per_ml.' x10⁶/ml.' : '—' }}</td><td class="norm">&gt; 20</td></tr>
                    <tr><td>Total count</td><td>{{ $report->total_count !== null ? $report->total_count.' x10⁶' : '—' }}</td><td class="norm">&gt; 40</td></tr>
                    <tr><td>Motile</td><td>{{ $report->motile !== null ? $report->motile.' x10⁶/ml.' : '—' }}</td><td></td></tr>
                    <tr><td>Total motile</td><td>{{ $report->total_motile !== null ? $report->total_motile.' x10⁶' : '—' }}</td><td></td></tr>
                    <tr><td>Motility</td><td>{{ $report->motility !== null ? $report->motility.'%' : '—' }}</td><td class="norm">&gt; 50</td></tr>
                    <tr><td class="bold" colspan="3">Motility rate <span style="font-weight:normal;font-size:10px">(WHO 1999)</span></td></tr>
                    <tr><td class="indent">4 rapid</td><td>{{ $report->motility_4_rapid !== null ? $report->motility_4_rapid.'%' : '—' }}</td><td class="norm">&gt; 25</td></tr>
                    <tr><td class="indent">3 medium</td><td>{{ $report->motility_3_medium !== null ? $report->motility_3_medium.'%' : '—' }}</td><td></td></tr>
                    <tr><td class="indent">2 slow</td><td>{{ $report->motility_2_slow !== null ? $report->motility_2_slow.'%' : '—' }}</td><td></td></tr>
                    <tr><td class="indent">1 static</td><td>{{ $report->motility_1_static !== null ? $report->motility_1_static.'%' : '—' }}</td><td></td></tr>
                    <tr><td>WBC</td><td>{{ $report->wbc !== null ? $report->wbc.' /HPF' : '—' }}</td><td></td></tr>
                    <tr><td class="bold" colspan="3">Morphology <span style="font-weight:normal;font-size:10px">(Strict criteria)</span></td></tr>
                    <tr><td class="indent">Normal</td><td>{{ $report->morphology_normal !== null ? $report->morphology_normal.'%' : '—' }}</td><td class="norm">&gt; 14</td></tr>
                    <tr><td class="indent">Abnormal</td><td>{{ $report->morphology_abnormal !== null ? $report->morphology_abnormal.'%' : '—' }}</td><td></td></tr>
                    <tr><td class="indent2">Head Defect</td><td>{{ $report->head_defect !== null ? $report->head_defect.'%' : '—' }}</td><td></td></tr>
                    <tr><td class="indent2">Neck Defect</td><td>{{ $report->neck_defect !== null ? $report->neck_defect.'%' : '—' }}</td><td></td></tr>
                    <tr><td class="indent2">Tail Defect</td><td>{{ $report->tail_defect !== null ? $report->tail_defect.'%' : '—' }}</td><td></td></tr>
                </tbody>
            </table>
        </div>
        <div class="data-right">
            <p class="img-label">Normal Form</p>
            <img class="sperm-img" src="{{ public_path('images/sperm-forms/normal_form.png') }}" alt="Normal Form" /><br>
            <p class="img-label">Abnormal Form</p>
            <img class="sperm-img" src="{{ public_path('images/sperm-forms/abnormal_form.png') }}" alt="Abnormal Form" />
        </div>
    </div>

    <!-- Times -->
    <div class="times-row">
        <div class="times-cell"><strong>Ejaculation time:</strong> {{ $report->ejaculation_time ?? '—' }}</div>
        <div class="times-cell"><strong>Examination time:</strong> {{ $report->examination_time ?? '—' }}</div>
        <div class="times-cell"><strong>Receive time:</strong> {{ $report->receive_time ?? '—' }}</div>
        <div class="times-cell"><strong>Finish time:</strong> {{ $report->finish_time ?? '—' }}</div>
    </div>

    <!-- Remark -->
    @if($report->remark)
    <div class="remark"><strong>Remark:</strong> {{ $report->remark }}</div>
    @endif

    <!-- Signatures -->
    <div class="sig-row">
        <div class="sig-cell">
            <p class="sig-label">Reported by</p>
            <p>{{ $report->reported_by ?? '—' }}</p>
            <p class="sig-date">
                {{ $report->reported_date ? 'Date: '.$report->reported_date : '' }}
                {{ $report->reported_time ? ' Time: '.$report->reported_time : '' }}
                @if(!$report->reported_date && !$report->reported_time) — @endif
            </p>
        </div>
        <div class="sig-cell">
            <p class="sig-label">Approved by</p>
            <p>{{ $report->approved_by ?? '—' }}</p>
            <p class="sig-date">
                {{ $report->approved_date ? 'Date: '.$report->approved_date : '' }}
                {{ $report->approved_time ? ' Time: '.$report->approved_time : '' }}
                @if(!$report->approved_date && !$report->approved_time) — @endif
            </p>
        </div>
    </div>

</body>
</html>
