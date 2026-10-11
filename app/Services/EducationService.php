<?php

namespace App\Services;

use App\Models\EducationCourse;
use App\Models\EducationEnrollment;
use App\Models\User;
use App\Support\Features;
use Illuminate\Support\Collection;

class EducationService
{
    /** Complete courses whose configured study time has passed. */
    public function completeDue(User $user): void
    {
        EducationEnrollment::where('user_id', $user->id)
            ->where('status', 'in_progress')
            ->where('completes_at', '<=', now())
            ->update(['status' => 'completed', 'completed_at' => now(), 'updated_at' => now()]);
    }

    /** @return Collection<int, array{course: EducationCourse, enrollment: ?EducationEnrollment, missing: Collection, state: string}> */
    public function roadmap(User $user): Collection
    {
        $this->completeDue($user);

        $courses = EducationCourse::active()->with('prerequisites:id,title')->orderBy('id')->get();
        $enrollments = EducationEnrollment::where('user_id', $user->id)->get()->keyBy('education_course_id');
        $completedIds = $enrollments->where('status', 'completed')->keys();

        return $courses->map(function (EducationCourse $course) use ($enrollments, $completedIds) {
            $enrollment = $enrollments->get($course->id);
            $missing = $course->prerequisites->reject(fn (EducationCourse $prerequisite) => $completedIds->contains($prerequisite->id))->values();
            $state = match (true) {
                $enrollment?->status === 'completed' => 'completed',
                $enrollment?->status === 'in_progress' => 'in_progress',
                $missing->isNotEmpty() => 'locked',
                default => 'available',
            };

            return compact('course', 'enrollment', 'missing', 'state');
        });
    }

    /** Whether a player can submit a company request under the configured education gate. */
    public function canCreateCompany(User $user): bool
    {
        if (! Features::enabled('education')) {
            return true;
        }

        $this->completeDue($user);

        $requiredCourses = EducationCourse::where('qualifies_for_company', true)->pluck('id');
        if ($requiredCourses->isEmpty()) {
            return false;
        }

        return EducationEnrollment::where('user_id', $user->id)
            ->whereIn('education_course_id', $requiredCourses)
            ->where('status', 'completed')
            ->exists();
    }

    public function companyRequirementCount(): int
    {
        return EducationCourse::active()->where('qualifies_for_company', true)->count();
    }
}