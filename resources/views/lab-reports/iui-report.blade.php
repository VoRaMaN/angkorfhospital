<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>IUI Report</title>
    <style>
        @page { margin: 10mm 12mm; }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; line-height: 1.5; color: #000; }

        .box { border: 1.2px solid #222; border-radius: 4px; padding: 10px 14px; margin-bottom: 12px; }

        /* Header box */
        .header { display: table; width: 100%; }
        .header-left { display: table-cell; vertical-align: top; width: 48%; }
        .header-left img { width: 60px; float: left; margin-right: 8px; }
        .hosp-name { font-weight: bold; font-size: 14px; padding-top: 4px; }
        .hosp-info { font-size: 7px; color: #333; }
        .lab-line { font-weight: bold; font-size: 11px; margin-top: 18px; clear: both; }
        .header-right { display: table-cell; vertical-align: top; width: 52%; }
        .hr-row { display: table; width: 100%; margin-bottom: 4px; font-size: 10px; }
        .hr-row .c { display: table-cell; }
        .hr-row .lbl { font-weight: bold; }
        .hr-row .v { font-weight: normal; }

        /* Main box */
        .main-title { text-align: center; font-weight: bold; font-size: 13px; text-decoration: underline; margin: 4px 0 14px; }

        .uval { display: inline-block; border-bottom: 1px solid #000; min-width: 130px; text-align: center; font-weight: bold; padding: 0 4px; }
        .uval.short { min-width: 90px; }

        .wife-row { margin-bottom: 12px; font-size: 10px; }

        .cb { display: inline-block; width: 10px; height: 10px; border: 1px solid #000; font-size: 9px; line-height: 10px; text-align: center; vertical-align: middle; margin-right: 5px; }
        .cb-row { display: table; width: 80%; margin: 0 auto 8px; font-size: 10px; }
        .cb-row .c { display: table-cell; width: 50%; }

        .basic-row { display: table; width: 100%; margin-bottom: 6px; font-size: 10px; }
        .basic-row .bl { display: table-cell; width: 24%; }
        .basic-row .bv { display: table-cell; width: 36%; }
        .basic-row .bn { display: table-cell; color: #333; }

        /* Pre / Post preparation grid */
        table.prep { width: 100%; border-collapse: collapse; font-size: 10px; margin-top: 6px; }
        table.prep th { font-weight: bold; font-size: 11px; text-decoration: underline; text-align: center; padding-bottom: 6px; }
        table.prep td { padding: 3px 2px; vertical-align: bottom; }
        table.prep .lbl { width: 17%; }
        table.prep .sub { width: 11%; }
        table.prep .unit { width: 10%; color: #333; }
        table.prep .val { width: 20%; text-align: center; }
        table.prep .val span { display: inline-block; border-bottom: 1px solid #000; min-width: 85px; text-align: center; font-weight: bold; }
        table.prep .norm { width: 9%; font-size: 9px; color: #333; }
        table.prep .gap { width: 8%; }

        /* Times */
        .times { display: table; width: 100%; margin-top: 14px; font-size: 10px; }
        .times .tc { display: table-cell; width: 25%; padding: 3px 0; }

        .remark-ln { margin-top: 12px; font-size: 10px; }
        .remark-ln .uval { min-width: 420px; text-align: left; }

        .sig-table { width: 100%; margin-top: 10px; font-size: 10px; border-collapse: collapse; }
        .sig-table td { padding: 3px 0; }
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
                <p class="lab-line">IVF LAB (IUI Report)</p>
            </div>
            <div class="header-right">
                <div class="hr-row"><span class="c"><span class="lbl">Name</span> &nbsp; <span class="v">{{ $report->patient_name ?? '' }}</span></span></div>
                <div class="hr-row">
                    <span class="c" style="width:55%;"><span class="lbl">HN</span> &nbsp; <span class="v">{{ $report->patient_hn ?? '' }}</span></span>
                    <span class="c" style="width:20%;"><span class="lbl">Sex</span></span>
                    <span class="c"><span class="v">MALE</span></span>
                </div>
                <div class="hr-row">
                    <span class="c" style="width:55%;"><span class="lbl">DOB</span> &nbsp; <span class="v">{{ $report->patient_dob ?? '' }}</span></span>
                    <span class="c" style="width:20%;"><span class="lbl">Age</span> &nbsp; <span class="v">{{ $report->patient_age ?? '' }}</span></span>
                    <span class="c"><span class="lbl">Yrs</span></span>
                </div>
                <div class="hr-row"><span class="c"><span class="lbl">Order date</span> &nbsp; <span class="v">{{ $reportDate }}</span></span></div>
                <div class="hr-row"><span class="c"><span class="lbl">Doctor</span> &nbsp; <span class="v">{{ $report->doctor_name ?? '' }}</span></span></div>
            </div>
        </div>
    </div>

    <!-- Main box -->
    <div class="box">
        <p class="main-title">Sperm preparation for IUI</p>

        <!-- Wife name / HN -->
        <div class="wife-row">
            <strong>Wife 's name</strong> &nbsp; <span class="uval" style="min-width:220px;">{{ $report->wife_name ?? '' }}</span>
            &nbsp;&nbsp; <strong>(H.N)</strong> &nbsp; <span class="uval short">{{ $report->wife_hn ?? '' }}</span>
        </div>

        <!-- Sperm type checkboxes -->
        <div class="cb-row">
            <span class="c"><span class="cb">{!! $report->owner_sperm ? '&#10003;' : '&nbsp;' !!}</span> Owner sperm</span>
            <span class="c"><span class="cb">{!! $report->donor_sperm ? '&#10003;' : '&nbsp;' !!}</span> Donor sperm</span>
        </div>
        <div class="cb-row">
            <span class="c"><span class="cb">{!! $report->fresh_sperm ? '&#10003;' : '&nbsp;' !!}</span> Fresh sperm</span>
            <span class="c"><span class="cb">{!! $report->frozen_sperm ? '&#10003;' : '&nbsp;' !!}</span> Frozen sperm &nbsp; <span class="uval short" style="min-width:50px;">{{ $report->frozen_sperm ? ($report->frozen_vial ?? '') : '' }}</span> vial</span>
        </div>

        <!-- Basic parameters -->
        <div class="basic-row"><span class="bl">Abstinence day</span><span class="bv"><span class="uval">{{ $report->abstinence_days !== null ? $report->abstinence_days.' Days' : '' }}</span></span><span class="bn"></span></div>
        <div class="basic-row"><span class="bl">Appearance</span><span class="bv"><span class="uval">{{ $report->appearance ?? '' }}</span></span><span class="bn"></span></div>
        <div class="basic-row"><span class="bl">Liquefaction</span><span class="bv"><span class="uval">{{ $report->liquefaction ?? '' }}</span></span><span class="bn">30 mins</span></div>
        <div class="basic-row"><span class="bl">Viscosity</span><span class="bv"><span class="uval">{{ $report->viscosity ?? '' }}</span></span><span class="bn"></span></div>

        <!-- Pre / Post preparation -->
        <table class="prep">
            <tr>
                <td class="lbl"></td><td class="sub"></td><td class="unit"></td>
                <th class="val">Pre-preparation</th>
                <td class="norm"></td><td class="gap"></td>
                <th class="val">Post-preparation</th>
            </tr>
            <tr>
                <td class="lbl">Volume</td><td class="sub"></td><td class="unit">(ml.)</td>
                <td class="val"><span>{{ $report->pre_volume ?? '' }}</span></td>
                <td class="norm">&gt; 2</td><td class="gap"></td>
                <td class="val"><span>{{ $report->post_volume ?? '' }}</span></td>
            </tr>
            <tr>
                <td class="lbl">Count</td><td class="sub"></td><td class="unit">(x10&#8310;/ml.)</td>
                <td class="val"><span>{{ $report->pre_count ?? '' }}</span></td>
                <td class="norm">&gt; 20</td><td class="gap"></td>
                <td class="val"><span>{{ $report->post_count ?? '' }}</span></td>
            </tr>
            <tr>
                <td class="lbl">Total count</td><td class="sub"></td><td class="unit">(x10&#8310;)</td>
                <td class="val"><span>{{ $report->pre_total_count ?? '' }}</span></td>
                <td class="norm">&gt; 40</td><td class="gap"></td>
                <td class="val"><span>{{ $report->post_total_count ?? '' }}</span></td>
            </tr>
            <tr>
                <td class="lbl">Motile</td><td class="sub"></td><td class="unit">(x10&#8310;/ml.)</td>
                <td class="val"><span>{{ $report->pre_motile ?? '' }}</span></td>
                <td class="norm"></td><td class="gap"></td>
                <td class="val"><span>{{ $report->post_motile ?? '' }}</span></td>
            </tr>
            <tr>
                <td class="lbl">Total motile</td><td class="sub"></td><td class="unit">(x10&#8310;)</td>
                <td class="val"><span>{{ $report->pre_total_motile ?? '' }}</span></td>
                <td class="norm"></td><td class="gap"></td>
                <td class="val"><span>{{ $report->post_total_motile ?? '' }}</span></td>
            </tr>
            <tr>
                <td class="lbl">Motility</td><td class="sub"></td><td class="unit">(%)</td>
                <td class="val"><span>{{ $report->pre_motility ?? '' }}</span></td>
                <td class="norm">&gt; 50</td><td class="gap"></td>
                <td class="val"><span>{{ $report->post_motility ?? '' }}</span></td>
            </tr>
            <tr>
                <td class="lbl">Motility rate</td><td class="sub">4 rapid</td><td class="unit">(%)</td>
                <td class="val"><span>{{ $report->pre_motility_4_rapid ?? '' }}</span></td>
                <td class="norm">&gt; 25</td><td class="gap"></td>
                <td class="val"><span>{{ $report->post_motility_4_rapid ?? '' }}</span></td>
            </tr>
            <tr>
                <td class="lbl"></td><td class="sub">3 medium</td><td class="unit">(%)</td>
                <td class="val"><span>{{ $report->pre_motility_3_medium ?? '' }}</span></td>
                <td class="norm"></td><td class="gap"></td>
                <td class="val"><span>{{ $report->post_motility_3_medium ?? '' }}</span></td>
            </tr>
            <tr>
                <td class="lbl"></td><td class="sub">2 slow</td><td class="unit">(%)</td>
                <td class="val"><span>{{ $report->pre_motility_2_slow ?? '' }}</span></td>
                <td class="norm"></td><td class="gap"></td>
                <td class="val"><span>{{ $report->post_motility_2_slow ?? '' }}</span></td>
            </tr>
            <tr>
                <td class="lbl"></td><td class="sub">1 static</td><td class="unit">(%)</td>
                <td class="val"><span>{{ $report->pre_motility_1_static ?? '' }}</span></td>
                <td class="norm"></td><td class="gap"></td>
                <td class="val"><span>{{ $report->post_motility_1_static ?? '' }}</span></td>
            </tr>
        </table>

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
                <td class="sl">Reported by</td>
                <td class="sv">{{ $report->reported_by ?? '' }}</td>
                <td class="sd"><strong>Date</strong> &nbsp; {{ $report->reported_date ?? '' }}</td>
                <td class="sd"><strong>Time</strong> &nbsp; {{ $report->reported_time ?? '' }}</td>
            </tr>
            <tr>
                <td class="sl">Approved by</td>
                <td class="sv">{{ $report->approved_by ?? '' }}</td>
                <td class="sd"><strong>Date</strong> &nbsp; {{ $report->approved_date ?? '' }}</td>
                <td class="sd"><strong>Time</strong> &nbsp; {{ $report->approved_time ?? '' }}</td>
            </tr>
        </table>
    </div>

</body>
</html>
