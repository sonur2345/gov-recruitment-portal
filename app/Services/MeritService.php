<?php

namespace App\Services;

use App\Models\Post;
use App\Models\Application;
use App\Models\MeritGeneration;
use Illuminate\Support\Facades\DB;
use App\Enums\ApplicationStatus;
use RuntimeException;

class MeritService
{
    public function generate(Post $post): int
    {
        return DB::transaction(function () use ($post): int {
            $alreadyGenerated = MeritGeneration::query()
                ->where('post_id', $post->id)
                ->lockForUpdate()
                ->exists();

            if ($alreadyGenerated) {
                throw new RuntimeException('Merit already generated for this post.');
            }

            $applications = Application::query()
                ->where('post_id', $post->id)
                ->where('status', ApplicationStatus::Qualified->value)
                ->lockForUpdate()
                ->get();

            $vacancies = (int) $post->total_vacancies;
            $weightEducation = (float) ($post->weight_education ?? 1);
            $weightSkill = (float) ($post->weight_skill ?? 1);
            $weightExperience = (float) ($post->weight_experience ?? 1);

            $sorted = $applications->sort(function ($a, $b) use ($weightEducation, $weightSkill, $weightExperience) {
                $aTotal = (float) ($a->education_percentage ?? 0) * $weightEducation
                    + (float) ($a->skill_marks ?? 0) * $weightSkill
                    + (float) ($a->experience_marks ?? 0) * $weightExperience;
                $bTotal = (float) ($b->education_percentage ?? 0) * $weightEducation
                    + (float) ($b->skill_marks ?? 0) * $weightSkill
                    + (float) ($b->experience_marks ?? 0) * $weightExperience;

                if ((int) round(($bTotal - $aTotal) * 100) === 0) {
                    // Older DOB first.
                    return strcmp((string) $a->dob, (string) $b->dob);
                }

                return $bTotal <=> $aTotal;
            })->values();

            $rank = 1;
            $selectedCount = 0;
            foreach ($sorted as $i => $application) {
                $total = round(
                    (float) ($application->education_percentage ?? 0) * $weightEducation
                    + (float) ($application->skill_marks ?? 0) * $weightSkill
                    + (float) ($application->experience_marks ?? 0) * $weightExperience,
                    2
                );

                $status = $i < $vacancies
                    ? ApplicationStatus::Selected->value
                    : ApplicationStatus::Waiting->value;

                if ($status === ApplicationStatus::Selected->value) {
                    $selectedCount++;
                }

                $application->update([
                    'total_marks' => $total,
                    'rank' => $rank++,
                    'status' => $status,
                ]);
            }

            MeritGeneration::create([
                'post_id' => $post->id,
                'vacancies' => $vacancies,
                'qualified_count' => $sorted->count(),
                'selected_count' => $selectedCount,
                'waiting_valid_until' => now()->addYear()->toDateString(),
            ]);

            return $sorted->count();
        });
    }

    // Backward compatibility for existing callers.
    public function generateMerit(Post $post): int
    {
        return $this->generate($post);
    }
}
