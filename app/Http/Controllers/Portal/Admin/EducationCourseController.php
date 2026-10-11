<?php

namespace App\Http\Controllers\Portal\Admin;

use App\Http\Controllers\Controller;
use App\Models\EducationCourse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class EducationCourseController extends Controller
{
    public function index()
    {
        return view('portal.admin.education.index', [
            'courses' => EducationCourse::with('prerequisites')->withCount('enrollments')->orderBy('id')->get(),
        ]);
    }

    public function create()
    {
        return view('portal.admin.education.form', [
            'course' => new EducationCourse(['duration_hours' => 24, 'price' => 0]),
            'courses' => EducationCourse::orderBy('title')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validated($request);
        if ($message = $this->invalidPrerequisiteChain($validated['prerequisites'] ?? [], null)) {
            return back()->withInput()->withErrors(['prerequisites' => $message]);
        }

        $slug = Str::slug($validated['title']) ?: 'opleiding';
        $validated['payout_bank_account_uuid'] = null;
        $course = EducationCourse::create($validated + ['slug' => $slug.'-'.Str::lower(Str::random(6))]);
        $course->prerequisites()->sync($validated['prerequisites'] ?? []);

        return redirect()->route('portal.admin.education.index')->with('success', 'De opleiding is aangemaakt.');
    }

    public function edit(EducationCourse $course)
    {
        return view('portal.admin.education.form', [
            'course' => $course->load('prerequisites'),
            'courses' => EducationCourse::where('id', '!=', $course->id)->orderBy('title')->get(),
        ]);
    }

    public function update(Request $request, EducationCourse $course)
    {
        $validated = $this->validated($request);
        if ($message = $this->invalidPrerequisiteChain($validated['prerequisites'] ?? [], $course->id)) {
            return back()->withInput()->withErrors(['prerequisites' => $message]);
        }

        $validated['payout_bank_account_uuid'] = null;
        $course->fill($validated);
        $course->save();
        $course->prerequisites()->sync($validated['prerequisites'] ?? []);

        return redirect()->route('portal.admin.education.index')->with('success', 'De opleiding is bijgewerkt.');
    }

    public function destroy(EducationCourse $course)
    {
        if ($course->enrollments()->exists() || $course->requiredBy()->exists()) {
            return back()->with('error', [
                'title' => 'Opleiding kan niet worden verwijderd',
                'message' => 'Er zijn inschrijvingen of andere opleidingen die deze opleiding vereisen. Zet haar in plaats daarvan op inactief.',
            ]);
        }

        $course->prerequisites()->detach();
        $course->delete();

        return back()->with('success', 'De opleiding is verwijderd.');
    }

    private function validated(Request $request): array
    {
        $validated = $request->validate([
            'title' => 'required|string|max:120',
            'description' => 'nullable|string|max:2000',
            'price' => 'required|numeric|min:0|max:99999999',
            'duration_hours' => 'required|integer|min:1|max:8760',
            'prerequisites' => 'nullable|array|max:20',
            'prerequisites.*' => 'integer|distinct|exists:education_courses,id',
            'qualifies_for_company' => 'nullable|boolean',
            'is_active' => 'nullable|boolean',
        ], [
            'title.required' => 'Vul een naam voor de opleiding in.',
            'duration_hours.min' => 'Een opleiding moet minimaal één uur duren.',
            'duration_hours.max' => 'Een opleiding kan maximaal één jaar duren.',
        ]);

        $validated['qualifies_for_company'] = $request->boolean('qualifies_for_company');
        $validated['is_active'] = $request->boolean('is_active');

        return $validated;
    }

    private function invalidPrerequisiteChain(array $prerequisiteIds, ?int $courseId): ?string
    {
        if (! $courseId) {
            return null;
        }

        $pending = array_map('intval', $prerequisiteIds);
        $seen = [];
        while ($pending !== []) {
            $id = array_pop($pending);
            if ($id === $courseId) {
                return 'Deze vereisten vormen een cirkel. Een opleiding kan niet direct of indirect zichzelf vereisen.';
            }
            if (isset($seen[$id])) {
                continue;
            }
            $seen[$id] = true;
            $pending = array_merge($pending, EducationCourse::find($id)?->prerequisites()->pluck('education_courses.id')->all() ?? []);
        }

        return null;
    }
}