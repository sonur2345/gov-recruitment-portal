<?php

namespace App\Http\Controllers\Candidate;

use App\Http\Controllers\Controller;
use App\Models\Application;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdmitCardController extends Controller
{
    public function download(Request $request, Application $application): Response
    {
        abort_unless($application->user_id === $request->user()->id, 403);

        $validated = $request->validate([
            'exam_date' => ['required', 'date'],
            'venue' => ['required', 'string', 'max:255'],
        ]);

        $application->loadMissing(['user', 'post']);

        $qrCodeUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=160x160&data='
            . rawurlencode($application->application_no);

        $pdf = Pdf::setOption(['isRemoteEnabled' => true])
            ->loadView('candidate.admit-card-pdf', [
                'candidateName' => $application->user->name,
                'postName' => $application->post->name,
                'examDate' => $validated['exam_date'],
                'venue' => $validated['venue'],
                'applicationNo' => $application->application_no,
                'qrCodeUrl' => $qrCodeUrl,
            ]);

        $filename = 'admit-card-' . $application->application_no . '.pdf';

        return $pdf->download($filename);
    }
}
