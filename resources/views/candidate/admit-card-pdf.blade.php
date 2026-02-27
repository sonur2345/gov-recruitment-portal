<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admit Card</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            color: #111827;
            font-size: 14px;
            margin: 24px;
        }
        .header {
            text-align: center;
            margin-bottom: 18px;
        }
        .title {
            font-size: 22px;
            font-weight: bold;
            margin: 0;
        }
        .subtitle {
            margin-top: 6px;
            color: #4b5563;
        }
        .card {
            border: 1px solid #d1d5db;
            border-radius: 8px;
            padding: 16px;
        }
        .row {
            margin-bottom: 10px;
        }
        .label {
            display: inline-block;
            width: 165px;
            color: #374151;
            font-weight: 600;
        }
        .value {
            color: #111827;
        }
        .qr-wrap {
            margin-top: 18px;
            text-align: right;
        }
        .qr-wrap img {
            width: 140px;
            height: 140px;
            border: 1px solid #e5e7eb;
            padding: 4px;
        }
        .note {
            margin-top: 12px;
            color: #6b7280;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <p class="title">Admit Card</p>
        <p class="subtitle">Application No: {{ $applicationNo }}</p>
    </div>

    <div class="card">
        <div class="row">
            <span class="label">Candidate Name:</span>
            <span class="value">{{ $candidateName }}</span>
        </div>
        <div class="row">
            <span class="label">Post Name:</span>
            <span class="value">{{ $postName }}</span>
        </div>
        <div class="row">
            <span class="label">Exam Date:</span>
            <span class="value">{{ \Illuminate\Support\Carbon::parse($examDate)->format('d M Y') }}</span>
        </div>
        <div class="row">
            <span class="label">Venue:</span>
            <span class="value">{{ $venue }}</span>
        </div>

        <div class="qr-wrap">
            <img src="{{ $qrCodeUrl }}" alt="QR Code">
            <div class="note">QR contains application number</div>
        </div>
    </div>
</body>
</html>
