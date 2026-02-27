<?php

namespace App\Services;

use App\Models\Post;
use App\Enums\ApplicationStatus;
use App\Models\Application;
use App\Models\Shortlist;
use Illuminate\Support\Facades\DB;

class ShortlistService
{
    public function generate(Post $post): int
    {
        return DB::transaction(function () use ($post): int {
            $limit = $this->resolveShortlistLimit((int) $post->total_vacancies);
            if ($limit === 0) {
                return 0;
            }

            $existingApplicationIds = Shortlist::query()
                ->where('post_id', $post->id)
                ->lockForUpdate()
                ->pluck('application_id')
                ->all();

            $applications = Application::query()
                ->where('post_id', $post->id)
                ->where('status', ApplicationStatus::Eligible->value)
                ->when($existingApplicationIds !== [], function ($query) use ($existingApplicationIds) {
                    $query->whereNotIn('id', $existingApplicationIds);
                })
                ->orderByDesc('education_percentage')
                ->orderBy('id')
                ->limit($limit)
                ->lockForUpdate()
                ->get(['id']);

            if ($applications->isEmpty()) {
                return 0;
            }

            $applicationIds = $applications->pluck('id')->all();

            Application::query()
                ->whereIn('id', $applicationIds)
                ->update([
                    'status' => ApplicationStatus::Shortlisted->value,
                    'updated_at' => now(),
                ]);

            $currentMaxRank = (int) (Shortlist::query()
                ->where('post_id', $post->id)
                ->lockForUpdate()
                ->max('rank') ?? 0);

            $now = now();
            $rows = [];
            foreach ($applicationIds as $index => $applicationId) {
                $rows[] = [
                    'post_id' => $post->id,
                    'application_id' => $applicationId,
                    'rank' => $currentMaxRank + $index + 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }

            // Prevent duplicate shortlist entries for the same application.
            Shortlist::query()->upsert($rows, ['application_id'], ['post_id', 'rank', 'updated_at']);

            return count($applicationIds);
        });
    }

    private function resolveShortlistLimit(int $vacancies): int
    {
        if ($vacancies >= 1 && $vacancies <= 10) {
            return $vacancies * 10;
        }

        if ($vacancies >= 11 && $vacancies <= 50) {
            return $vacancies * 5;
        }

        if ($vacancies > 50) {
            return $vacancies * 3;
        }

        return 0;
    }
}
