<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sperm Freezing Report</title>
    <style>
        @page { margin: 14mm 16mm; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; line-height: 1.5; color: #000; }

        .box { border: 1.2px solid #222; border-radius: 4px; padding: 9.7px 13.6px; margin-bottom: 11.6px; }

        /* Header box */
        .header { display: table; width: 100%; }
        .header-left { display: table-cell; vertical-align: top; width: 48%; }
        .header-left img { width: 60px; float: left; margin-right: 8px; }
        .hosp-name { font-weight: bold; font-size: 14px; padding-top: 3.9px; }
        .hosp-info { font-size: 7px; color: #333; }
        .lab-line { font-weight: bold; font-size: 11px; margin-top: 15.5px; clear: both; }
        .header-right { display: table-cell; vertical-align: top; width: 52%; }
        .hr-row { display: table; width: 100%; margin-bottom: 3.9px; font-size: 10px; }
        .hr-row .c { display: table-cell; }
        .hr-row .lbl { font-weight: bold; }

        /* Main box */
        .main-title { text-align: center; font-weight: bold; font-size: 13px; text-decoration: underline; margin: 2.9px 0 11.6px; }

        .uval { display: inline-block; border-bottom: 1px solid #000; min-width: 130px; text-align: center; font-weight: bold; padding: 0 4px; }

        table.params { width: 100%; border-collapse: collapse; font-size: 10px; }
        table.params td { padding: 2.9px 1.9px; vertical-align: bottom; }
        table.params .lbl { width: 20%; }
        table.params .sub { width: 12%; }
        table.params .val { width: 30%; text-align: center; }
        table.params .val span { display: inline-block; border-bottom: 1px solid #000; min-width: 130px; text-align: center; font-weight: bold; }
        table.params .unit { width: 16%; color: #333; font-size: 9px; }
        table.params .norm { width: 22%; font-size: 9.5px; color: #222; }
        table.params .norm-head { font-weight: bold; font-size: 10px; color: #000; white-space: nowrap; }

        .vial-row { text-align: center; margin-top: 9.7px; font-size: 10px; font-weight: bold; }
        .vial-row .uval { min-width: 70px; }

        /* Times */
        .times { display: table; width: 100%; margin-top: 11.6px; font-size: 10px; }
        .times .tc { display: table-cell; width: 25%; padding: 2.9px 0; }

        .remark-ln { margin-top: 11.6px; font-size: 10px; }
        .remark-ln .uval { min-width: 420px; text-align: left; }

        .sig-table { width: 100%; margin-top: 9.7px; font-size: 10px; border-collapse: collapse; }
        .sig-table td { padding: 2.9px 0; }
        .sig-table .sl { width: 18%; font-weight: bold; }
        .sig-table .sv { width: 34%; }
        .sig-table .sd { width: 24%; }
    </style>
</head>
<body>

    <!-- Header box -->
    <div class="box">
        <div class="header">
            <div class="header-left">
                <img src="{{ public_path('images/logo1.png') }}" alt="Angkor F Hospital" />
                <p class="hosp-name">Angkor-F Hospital</p>
                <p class="hosp-info">#National Road 6A, Salakonseng Village, Sangkat Svay Dangkum, Siem Reap, Cambodia</p>
                <p class="hosp-info">Tel: (855) 31 3 5555 88 | (855) 12 881 307 &nbsp; E-mail: angkorfhospital@gmail.com</p>
                <p class="lab-line">IVF LAB ( Sperm Freezing )</p>
            </div>
            <div class="header-right">
                <div class="hr-row"><span class="c"><span class="lbl">Name</span> &nbsp; {{ $report->patient_name ?? '' }}</span></div>
                <div class="hr-row">
                    <span class="c" style="width:55%;"><span class="lbl">HN.</span> &nbsp; {{ $report->patient_hn ?? '' }}</span>
                    <span class="c"><span class="lbl">SEX</span> &nbsp; MALE</span>
                </div>
                <div class="hr-row">
                    <span class="c" style="width:55%;"><span class="lbl">DOB</span> &nbsp; {{ $report->patient_dob ?? '' }}</span>
                    <span class="c"><span class="lbl">Age</span> &nbsp; {{ $report->patient_age ?? '' }}</span>
                </div>
                <div class="hr-row"><span class="c"><span class="lbl">Date</span> &nbsp; {{ $reportDate }}</span></div>
                <div class="hr-row"><span class="c"><span class="lbl">Doctor</span> &nbsp; {{ $report->doctor_name ?? '' }}</span></div>
            </div>
        </div>
    </div>

    <!-- Main box -->
    <div class="box">
        <p class="main-title">Sperm Freezing</p>

        <table class="params">
            <tr>
                <td class="lbl">Wife 's name</td><td class="sub"></td>
                <td class="val"><span>{{ $report->wife_name ?? '' }}</span></td>
                <td class="unit"></td><td class="norm"></td>
            </tr>
            <tr>
                <td class="lbl">Abstinence day</td><td class="sub"></td>
                <td class="val"><span>{{ $report->abstinence_days !== null ? $report->abstinence_days.' Days' : '' }}</span></td>
                <td class="unit"></td><td class="norm"></td>
            </tr>
            <tr>
                <td class="lbl">Appearance</td><td class="sub"></td>
                <td class="val"><span>{{ $report->appearance ?? '' }}</span></td>
                <td class="unit"></td>
                <td class="norm norm-head">Normal Range</td>
            </tr>
            <tr>
                <td class="lbl">Liquefaction</td><td class="sub"></td>
                <td class="val"><span>{{ $report->liquefaction ?? '' }}</span></td>
                <td class="unit"></td><td class="norm">30 mins</td>
            </tr>
            <tr>
                <td class="lbl">Viscosity</td><td class="sub"></td>
                <td class="val"><span>{{ $report->viscosity ?? '' }}</span></td>
                <td class="unit"></td><td class="norm"></td>
            </tr>
            <tr>
                <td class="lbl">Viability</td><td class="sub"></td>
                <td class="val"><span>{{ $report->viability ?? '' }}</span></td>
                <td class="unit"></td><td class="norm">&gt; 75</td>
            </tr>
            <tr>
                <td class="lbl">Volume</td><td class="sub"></td>
                <td class="val"><span>{{ $report->volume ?? '' }}</span></td>
                <td class="unit">ml.</td><td class="norm">&gt; 2</td>
            </tr>
            <tr>
                <td class="lbl">Count</td><td class="sub"></td>
                <td class="val"><span>{{ $report->count_per_ml ?? '' }}</span></td>
                <td class="unit">million/ml.</td><td class="norm">&gt; 20</td>
            </tr>
            <tr>
                <td class="lbl">Total count</td><td class="sub"></td>
                <td class="val"><span>{{ $report->total_count ?? '' }}</span></td>
                <td class="unit">million</td><td class="norm">&gt; 40</td>
            </tr>
            <tr>
                <td class="lbl">Motile</td><td class="sub"></td>
                <td class="val"><span>{{ $report->motile ?? '' }}</span></td>
                <td class="unit">million/ml.</td><td class="norm"></td>
            </tr>
            <tr>
                <td class="lbl">Total motile</td><td class="sub"></td>
                <td class="val"><span>{{ $report->total_motile ?? '' }}</span></td>
                <td class="unit">million</td><td class="norm"></td>
            </tr>
            <tr>
                <td class="lbl">Motility</td><td class="sub"></td>
                <td class="val"><span>{{ $report->motility ?? '' }}</span></td>
                <td class="unit">%</td><td class="norm">&gt; 50</td>
            </tr>
            <tr>
                <td class="lbl">Motility rate</td><td class="sub">4 rapid</td>
                <td class="val"><span>{{ $report->motility_4_rapid ?? '' }}</span></td>
                <td class="unit">%</td><td class="norm">&gt; 25</td>
            </tr>
            <tr>
                <td class="lbl"></td><td class="sub">3 medium</td>
                <td class="val"><span>{{ $report->motility_3_medium ?? '' }}</span></td>
                <td class="unit">%</td><td class="norm"></td>
            </tr>
            <tr>
                <td class="lbl"></td><td class="sub">2 slow</td>
                <td class="val"><span>{{ $report->motility_2_slow ?? '' }}</span></td>
                <td class="unit">%</td><td class="norm"></td>
            </tr>
            <tr>
                <td class="lbl"></td><td class="sub">1 static</td>
                <td class="val"><span>{{ $report->motility_1_static ?? '' }}</span></td>
                <td class="unit">%</td><td class="norm"></td>
            </tr>
        </table>

        <!-- No. of vial -->
        <div class="vial-row">
            No. of vial &nbsp; <span class="uval">{{ $report->no_of_vial ?? '' }}</span> &nbsp; vials
        </div>

        <!-- Times -->
        <div class="times">
            <span class="tc"><strong>Ejaculation time</strong></span>
            <span class="tc"><span class="uval">{{ $report->ejaculation_time ?? '' }}</span></span>
            <span class="tc"><strong>Examination time</strong></span>
            <span class="tc"><span class="uval">{{ $report->examination_time ?? '' }}</span></span>
        </div>
        <div class="times" style="margin-top:4px;">
            <span class="tc"><strong>Receive time</strong></span>
            <span class="tc"><span class="uval">{{ $report->receive_time ?? '' }}</span></span>
            <span class="tc"><strong>Finish time</strong></span>
            <span class="tc"><span class="uval">{{ $report->finish_time ?? '' }}</span></span>
        </div>

        <!-- Remark -->
        <div class="remark-ln"><strong>Remark&nbsp;&nbsp;:</strong> <span class="uval">{{ $report->remark ?? '' }}</span></div>

        <!-- Signatures -->
        <table class="sig-table">
            <tr>
                <td class="sl">Report by</td>
                <td class="sv">{{ $report->reported_by ?? '' }}</td>
                <td class="sd"><strong>Date</strong> &nbsp; {{ $report->reported_date ?? '' }}</td>
                <td class="sd"><strong>Time :</strong> &nbsp; {{ $report->reported_time ?? '' }}</td>
            </tr>
            <tr>
                <td class="sl">Approve by</td>
                <td class="sv">{{ $report->approved_by ?? '' }}</td>
                <td class="sd"><strong>Date</strong> &nbsp; {{ $report->approved_date ?? '' }}</td>
                <td class="sd"><strong>Time :</strong> &nbsp; {{ $report->approved_time ?? '' }}</td>
            </tr>
        </table>
    </div>

</body>
</html>
