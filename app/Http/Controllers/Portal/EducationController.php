<?php

namespace App\Http\Controllers\Portal;

use App\Http\Controllers\Controller;
use App\Models\EducationCourse;
use App\Models\EducationEnrollment;
use App\Services\EducationService;
use App\Services\Plugin\BankingService;
use Illuminate\Http\Request;

class EducationController extends Controller
{
    public function __construct(private EducationService $education, private BankingService $banking)
    {
    }

    public function index()
    {
        return view('portal.education.index', [
            'roadmap' => $this->education->roadmap(auth()->user()),
            'companyRequirementCount' => $this->education->companyRequirementCount(),
            'bankAccounts' => collect(auth()->user()->bank_accounts),
        ]);
    }

    public function enroll(Request $request, EducationCourse $course)
    {
        abort_unless($course->is_active, 404);

        $item = $this->education->roadmap(auth()->user())->firstWhere('course.id', $course->id);
        if (! $item || $item['state'] === 'locked') {
            return back()->with('error', [
                'title' => 'Opleiding nog niet beschikbaar',
                'message' => 'Rond eerst de vereiste opleidingen af.',
            ]);
        }

        if (in_array($item['state'], ['completed', 'in_progress'], true)) {
            return back()->with('error', [
                'title' => 'Je volgt deze opleiding al',
                'message' => 'Je kunt een opleiding maar één keer volgen.',
            ]);
        }

        $validated = $request->validate([
            'payer_bank_account_uuid' => [$course->price > 0 ? 'required' : 'nullable', 'string'],
        ], [
            'payer_bank_account_uuid.required' => 'Selecteer een bankrekening om de opleiding te betalen.',
        ]);

        $payerUuid = $course->price > 0 ? ($validated['payer_bank_account_uuid'] ?? null) : null;
        if ($course->price > 0) {
            $account = collect(auth()->user()->bank_accounts)->firstWhere('uuid', $payerUuid);
            if (! $account) {
                return back()->withInput()->withErrors(['payer_bank_account_uuid' => 'Kies een van je eigen bankrekeningen.']);
            }
            if ($account['balance'] < $course->price) {
                return back()->withInput()->withErrors(['payer_bank_account_uuid' => 'Op deze bankrekening staat niet genoeg geld.']);
            }
            if (! $course->payout_bank_account_uuid) {
                return back()->with('error', [
                    'title' => 'Opleiding tijdelijk niet beschikbaar',
                    'message' => 'Voor deze opleiding is nog geen betaalrekening ingesteld.',
                ]);
            }

            if (! $this->banking->withdraw($payerUuid, (float) $course->price)) {
                return back()->with('error', ['title' => 'Betaling mislukt', 'message' => 'Het bedrag kon niet van je rekening worden afgeschreven.']);
            }

            if (! $this->banking->deposit($course->payout_bank_account_uuid, (float) $course->price)) {
                $this->banking->deposit($payerUuid, (float) $course->price);

                return back()->with('error', ['title' => 'Betaling mislukt', 'message' => 'De opleidingskosten konden niet worden verwerkt. Er is geprobeerd het bedrag terug te storten.']);
            }
        }

        EducationEnrollment::create([
            'user_id' => auth()->id(),
            'education_course_id' => $course->id,
            'payer_bank_account_uuid' => $payerUuid,
            'payout_bank_account_uuid' => $course->payout_bank_account_uuid,
            'amount_paid' => $course->price,
            'started_at' => now(),
            'completes_at' => now()->addHours($course->duration_hours),
            'status' => 'in_progress',
        ]);

        return back()->with('success', [
            'title' => 'Opleiding gestart',
            'message' => 'Je opleiding is gestart. Je voortgang staat op de routekaart.',
        ]);
    }
}