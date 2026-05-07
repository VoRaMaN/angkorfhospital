<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>FET Report</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #000; padding: 25px 35px; }

        .header { display: table; width: 100%; margin-bottom: 10px; border-bottom: 2px solid #000; padding-bottom: 8px; }
        .header-left { display: table-cell; vertical-align: top; width: 55%; }
        .header-left img { width: 110px; display: block; margin-bottom: 4px; }
        .header-left p { font-size: 9px; margin: 1px 0; color: #333; }
        .header-right { display: table-cell; vertical-align: top; text-align: right; width: 45%; font-size: 10px; }
        .header-right p { margin: 2px 0; }
        .header-right .label { font-weight: bold; }

        .lab-title { text-align: center; margin: 6px 0 10px; }
        .lab-title p { font-weight: bold; font-size: 13px; }
        .lab-title .sub { font-size: 12px; font-weight: normal; }

        .section-label { font-weight: bold; font-size: 9px; text-transform: uppercase; letter-spacing: 0.5px; color: #555; border-bottom: 1px solid #999; padding-bottom: 2px; margin-bottom: 5px; margin-top: 8px; }

        .two-col { display: table; width: 100%; }
        .col-l { display: table-cell; width: 50%; vertical-align: top; padding-right: 10px; }
        .col-r { display: table-cell; width: 50%; vertical-align: top; }

        .field { margin-bottom: 3px; font-size: 10px; }
        .field .lbl { font-weight: bold; }

        table.embryo { width: 100%; border-collapse: collapse; font-size: 10px; }
        table.embryo th { text-align: center; background: #f0f0f0; padding: 2px 4px; border: 1px solid #ccc; font-size: 9px; }
        table.embryo td { text-align: center; padding: 3px 4px; border: 1px solid #ccc; }

        .sig-row { display: table; width: 100%; margin-top: 10px; padding-top: 6px; border-top: 1px solid #ccc; font-size: 10px; }
        .sig-cell { display: table-cell; width: 33%; }
        .sig-cell .sig-label { font-weight: bold; font-size: 9px; color: #555; text-transform: uppercase; margin-bottom: 2px; }

        .remark { margin-top: 6px; font-size: 10px; }
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
            <p><span class="label">Date:</span> {{ $reportDate }}</p>
            <p><span class="label">Doctor:</span> {{ $doctorName }}</p>
            <p><span class="label">Procedure:</span> {{ $report->procedure ?? '—' }}</p>
            <p><span class="label">FET Date:</span> {{ $report->fet_date ?? '—' }}</p>
        </div>
    </div>

    <!-- Lab Title -->
    <div class="lab-title">
        <p>IVF LAB</p>
        <p class="sub">( Frozen Embryo Transfer Report )</p>
    </div>

    <!-- Patient Info -->
    <p class="section-label">Patient Information</p>
    <div class="two-col">
        <div class="col-l">
            <p style="font-size:9px;font-weight:bold;color:#666;margin-bottom:2px;">FEMALE PATIENT</p>
            <div class="field"><span class="lbl">Name:</span> {{ $report->female_patient_name ?? '—' }}</div>
            <div class="field"><span class="lbl">H.N:</span> {{ $report->female_hn ?? '—' }}</div>
            <div class="field"><span class="lbl">DOB:</span> {{ $report->female_dob ?? '—' }}</div>
        </div>
        <div class="col-r">
            <p style="font-size:9px;font-weight:bold;color:#666;margin-bottom:2px;">MALE PATIENT (HUSBAND)</p>
            <div class="field"><span class="lbl">Name:</span> {{ $report->male_patient_name ?? '—' }}</div>
            <div class="field"><span class="lbl">H.N:</span> {{ $report->male_hn ?? '—' }}</div>
            <div class="field"><span class="lbl">DOB:</span> {{ $report->male_dob ?? '—' }}</div>
        </div>
    </div>

    <!-- Freeze / Thaw Info -->
    <p class="section-label">Freeze / Thaw Information</p>
    <div class="two-col">
        <div class="col-l">
            <div class="field"><span class="lbl">Freeze Date/Time:</span> {{ $report->freeze_datetime ?? '—' }}</div>
            <div class="field"><span class="lbl">Thaw Date/Time:</span> {{ $report->thaw_datetime ?? '—' }}</div>
            <div class="field"><span class="lbl">Thawing Media:</span> {{ $report->thawing_media ?? '—' }}</div>
            <div class="field"><span class="lbl">No. of Freeze:</span> {{ $report->no_of_freeze ?? '—' }}</div>
            <div class="field"><span class="lbl">No. of Thaw:</span> {{ $report->no_of_thaw ?? '—' }}</div>
        </div>
        <div class="col-r">
            <div class="field"><span class="lbl">Lot No.:</span> {{ $report->lot_no ?? '—' }}</div>
            <div class="field"><span class="lbl">Stage of Freeze:</span> {{ $report->stage_of_freeze ?? '—' }}</div>
            <div class="field"><span class="lbl">No. of Survival:</span> {{ $report->no_of_survival ?? '—' }}</div>
            <div class="field"><span class="lbl">Exp. Date:</span> {{ $report->exp_date ?? '—' }}</div>
            <div class="field"><span class="lbl">No. of Remaining:</span> {{ $report->no_of_remaining ?? '—' }}</div>
            <div class="field"><span class="lbl">Thawing By:</span> {{ $report->thawing_by ?? '—' }}</div>
        </div>
    </div>

    <!-- Day 3 Embryos -->
    <p class="section-label">Day 3 Embryos</p>
    <div class="field"><span class="lbl">Date/Time:</span> {{ $report->day3_datetime ?? '—' }}</div>
    <table class="embryo">
        <thead>
            <tr>
                <th>E1</th><th>E2</th><th>E3</th><th>E4</th><th>E5</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $report->day3_embryo_1 ?? '—' }}</td>
                <td>{{ $report->day3_embryo_2 ?? '—' }}</td>
                <td>{{ $report->day3_embryo_3 ?? '—' }}</td>
                <td>{{ $report->day3_embryo_4 ?? '—' }}</td>
                <td>{{ $report->day3_embryo_5 ?? '—' }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Day 5 Embryos -->
    <p class="section-label">Day 5 Embryos</p>
    <div class="field"><span class="lbl">Date/Time:</span> {{ $report->day5_datetime ?? '—' }}</div>
    <table class="embryo">
        <thead>
            <tr>
                <th>E1</th><th>E2</th><th>E3</th><th>E4</th><th>E5</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $report->day5_embryo_1 ?? '—' }}</td>
                <td>{{ $report->day5_embryo_2 ?? '—' }}</td>
                <td>{{ $report->day5_embryo_3 ?? '—' }}</td>
                <td>{{ $report->day5_embryo_4 ?? '—' }}</td>
                <td>{{ $report->day5_embryo_5 ?? '—' }}</td>
            </tr>
        </tbody>
    </table>

    <!-- Embryo Transfer (ET) -->
    <p class="section-label">Embryo Transfer (ET)</p>
    <div class="two-col">
        <div class="col-l">
            <div class="field"><span class="lbl">ET Date/Time:</span> {{ $report->et_datetime ?? '—' }}</div>
            <div class="field"><span class="lbl">ET Day:</span> {{ $report->et_day ?? '—' }}</div>
            <div class="field"><span class="lbl">No. of ET:</span> {{ $report->no_of_et ?? '—' }}</div>
            <div class="field"><span class="lbl">ET Volume:</span> {{ $report->et_volume ?? '—' }}</div>
            <div class="field"><span class="lbl">ET Catheter:</span> {{ $report->et_catheter ?? '—' }}</div>
        </div>
        <div class="col-r">
            <div class="field"><span class="lbl">No. of Transfer:</span> {{ $report->number_of_transfer ?? '—' }}</div>
            <div class="field"><span class="lbl">No. Freeze after ET:</span> {{ $report->number_of_freeze_et ?? '—' }}</div>
            <div class="field"><span class="lbl">No. of Discard:</span> {{ $report->number_of_discard ?? '—' }}</div>
            <div class="field"><span class="lbl">Assisted Hatching:</span> {{ $report->assisted_hatching ?? '—' }}</div>
            <div class="field"><span class="lbl">ET Doctor:</span> {{ $report->et_doctor ?? '—' }}</div>
        </div>
    </div>

    <!-- Remark -->
    @if($report->remark)
    <div class="remark"><strong>Remark:</strong> {{ $report->remark }}</div>
    @endif

    <!-- Signatures -->
    <div class="sig-row">
        <div class="sig-cell">
            <p class="sig-label">Embryologist</p>
            <p>{{ $report->et_embryologist ?? '—' }}</p>
        </div>
        <div class="sig-cell">
            <p class="sig-label">Reported By</p>
            <p>{{ $report->embryologist_report ?? '—' }}</p>
        </div>
        <div class="sig-cell">
            <p class="sig-label">Approved By</p>
            <p>{{ $report->embryologist_approve ?? '—' }}</p>
        </div>
    </div>

</body>
</html>
