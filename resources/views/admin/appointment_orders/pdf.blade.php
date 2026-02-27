<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Appointment Order</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            color: #111827;
            margin: 36px;
            font-size: 13px;
            line-height: 1.55;
        }
        .header {
            text-align: center;
            margin-bottom: 16px;
        }
        .header h1 {
            font-size: 20px;
            margin: 0;
            letter-spacing: 0.3px;
        }
        .subline {
            margin-top: 6px;
            color: #4b5563;
            font-size: 12px;
        }
        .meta {
            border: 1px solid #d1d5db;
            padding: 10px 12px;
            margin-bottom: 16px;
        }
        .meta-row {
            margin-bottom: 4px;
        }
        .meta-row:last-child {
            margin-bottom: 0;
        }
        .label {
            font-weight: bold;
            width: 180px;
            display: inline-block;
        }
        .body {
            margin-top: 10px;
        }
        .sign-wrap {
            margin-top: 44px;
            text-align: right;
        }
        .sign-box {
            display: inline-block;
            min-width: 220px;
            text-align: center;
        }
        .sign-line {
            border-top: 1px solid #111827;
            margin-bottom: 6px;
        }
        .small {
            color: #6b7280;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>Appointment Order</h1>
        <div class="subline">Government Recruitment Department</div>
    </div>

    <div class="meta">
        <div class="meta-row"><span class="label">Order Number:</span> {{ $orderNumber }}</div>
        <div class="meta-row"><span class="label">Reference Number:</span> {{ $referenceNumber }}</div>
        <div class="meta-row"><span class="label">Issue Date:</span> {{ \Illuminate\Support\Carbon::parse($issueDate)->format('d M Y') }}</div>
    </div>

    <div class="body">
        <p>
            This is to inform that <strong>{{ $candidateName }}</strong>, S/o or D/o
            <strong>{{ $fatherName }}</strong>, has been appointed to the post of
            <strong>{{ $postName }}</strong> under category <strong>{{ $category }}</strong>.
        </p>

        <p>
            The candidate's merit rank is <strong>{{ $meritRank }}</strong>. The candidate is instructed
            to join duty on or before <strong>{{ \Illuminate\Support\Carbon::parse($joiningDeadline)->format('d M Y') }}</strong>,
            failing which the appointment may be treated as cancelled as per department rules.
        </p>

        <p>
            <strong>Office Address:</strong><br>
            {{ $officeAddress }}
        </p>
    </div>

    <div class="sign-wrap">
        <div class="sign-box">
            <div class="sign-line"></div>
            <div>{{ $signatureName }}</div>
            <div class="small">Official Signature</div>
        </div>
    </div>
</body>
</html>
