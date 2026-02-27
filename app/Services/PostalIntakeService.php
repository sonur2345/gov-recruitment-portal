<?php

namespace App\Services;

use App\Models\Application;
use App\Models\DemandDraft;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use RuntimeException;

class PostalIntakeService
{
    public function createFromPostal(array $data, ?UploadedFile $envelopeScan, ?UploadedFile $ddScan): Application
    {
        return DB::transaction(function () use ($data, $envelopeScan, $ddScan): Application {
            $duplicate = DemandDraft::query()
                ->where('dd_number', $data['dd_number'])
                ->whereHas('application', function ($query) use ($data): void {
                    $query->where('post_id', $data['post_id'])
                        ->whereHas('user', function ($userQuery) use ($data): void {
                            $userQuery->whereRaw('LOWER(name) = ?', [Str::lower($data['candidate_name'])]);
                        });
                })
                ->exists();

            if ($duplicate) {
                throw new RuntimeException('Duplicate detected: same DD number, candidate name, and post already exist.');
            }

            $user = $this->resolveCandidate($data);

            $nextId = (Application::query()->lockForUpdate()->max('id') ?? 0) + 1;
            $applicationNo = 'APP-' . now()->format('Ymd') . '-' . str_pad((string) $nextId, 6, '0', STR_PAD_LEFT);

            $application = Application::create([
                'application_no' => $applicationNo,
                'user_id' => $user->id,
                'post_id' => $data['post_id'],
                'category' => $data['category'],
                'sub_reservation' => $data['sub_reservation'] ?? null,
                'dob' => $data['dob'],
                'gender' => $data['gender'],
                'father_name' => $data['father_name'] ?? null,
                'mobile' => $data['candidate_mobile'] ?? null,
                'address' => $data['address'] ?? null,
                'status' => 'submitted',
                'source' => 'postal',
                'inward_no' => $data['inward_no'],
                'inward_date' => $data['inward_date'],
                'postal_received_at' => $data['postal_received_at'] ?? null,
            ]);

            if ($envelopeScan) {
                $path = $envelopeScan->store("applications/{$applicationNo}", 'local');
                $application->update([
                    'envelope_scan_path' => $path,
                ]);
            }

            $ddScanPath = null;
            if ($ddScan) {
                $ddScanPath = $ddScan->store("applications/{$applicationNo}", 'local');
            }

            $application->demandDraft()->create([
                'dd_number' => $data['dd_number'],
                'bank_name' => $data['bank_name'],
                'bank_branch' => $data['bank_branch'] ?? null,
                'dd_date' => $data['dd_date'],
                'amount' => $data['amount'],
                'dd_scan_path' => $ddScanPath,
            ]);

            return $application;
        });
    }

    private function resolveCandidate(array $data): User
    {
        $email = $data['candidate_email'] ?? null;
        $mobile = $data['candidate_mobile'] ?? null;

        if ($email) {
            $existing = User::query()->where('email', $email)->first();
            if ($existing) {
                return $existing;
            }
        }

        if ($mobile) {
            $existing = User::query()->where('mobile', $mobile)->first();
            if ($existing) {
                return $existing;
            }
        }

        $generatedEmail = $email ?: 'postal+' . Str::slug($data['candidate_name']) . '-' . Str::random(6) . '@gov.local';

        $user = User::query()->create([
            'name' => $data['candidate_name'],
            'email' => $generatedEmail,
            'mobile' => $mobile,
            'father_name' => $data['father_name'] ?? null,
            'dob' => $data['dob'],
            'gender' => $data['gender'],
            'category' => $data['category'],
            'correspondence_address' => $data['address'] ?? null,
            'permanent_address' => $data['address'] ?? null,
            'password' => Hash::make(Str::random(16)),
            'email_verified_at' => now(),
        ]);

        if (! $user->hasRole('Candidate')) {
            $user->assignRole('Candidate');
        }

        return $user;
    }
}
