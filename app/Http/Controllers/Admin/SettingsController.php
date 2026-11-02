<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PortalFeature;
use App\Models\PermitSetting;
use App\Models\CompanySetting;
use App\Models\PlotListingSetting;
use App\Models\SiteContent;
use App\Support\Features;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        return view('portal.admin.settings.index', [
            'features' => PortalFeature::whereIn('key', array_keys(Features::ALL))->get()
                ->sortBy(fn (PortalFeature $feature) => array_search($feature->key, array_keys(Features::ALL))),
            'permitSettings' => PermitSetting::first() ?? PermitSetting::create([]),
            'companySettings' => CompanySetting::first() ?? CompanySetting::create([]),
            'plotListingSettings' => PlotListingSetting::first() ?? PlotListingSetting::create([
                'max_image_size_mb' => PlotListingSetting::DEFAULT_MAX_IMAGE_SIZE_MB,
            ]),
            'siteContent' => SiteContent::current(),
        ]);
    }

    public function updateSiteContent(Request $request)
    {
        $validated = $request->validate([
            'hero_title' => ['required', 'string', 'max:120'],
            'hero_text' => ['required', 'string', 'max:2000'],
            'about_title' => ['required', 'string', 'max:120'],
            'about_text' => ['required', 'string', 'max:5000'],
            'rules_title' => ['required', 'string', 'max:120'],
            'rules_text' => ['required', 'string', 'max:10000'],
        ]);

        SiteContent::current()->update($validated);

        return back()->with('success', 'De inhoud van de website is bijgewerkt.');
    }

    public function updateFeatures(Request $request)
    {
        $features = PortalFeature::whereIn('key', array_keys(Features::ALL))->get();

        foreach ($features as $feature) {
            $feature->update([
                'is_enabled' => $request->has("features.{$feature->key}")
            ]);
        }

        return redirect()->route('portal.admin.settings.index')
            ->with('success', 'Portal functies zijn succesvol bijgewerkt.');
    }

    public function updatePermitSettings(Request $request)
    {
        $validated = $request->validate([
            'payout_bank_account_uuid' => 'required|string'
        ], [
            'payout_bank_account_uuid.required' => 'Selecteer een bankrekening voor vergunningsbetalingen.'
        ]);

        $settings = PermitSetting::first() ?? new PermitSetting();
        $settings->payout_bank_account_uuid = $validated['payout_bank_account_uuid'];
        $settings->save();

        return back()->with('success', 'Vergunningsinstellingen zijn succesvol bijgewerkt.');
    }

    public function updateCompanySettings(Request $request)
    {
        $validated = $request->validate([
            'payout_bank_account_uuid' => 'required|string'
        ], [
            'payout_bank_account_uuid.required' => 'Selecteer een bankrekening voor bedrijfsaanvragen.'
        ]);

        $settings = CompanySetting::first() ?? new CompanySetting();
        $settings->payout_bank_account_uuid = $validated['payout_bank_account_uuid'];
        $settings->save();

        return back()->with('success', 'Bedrijfsinstellingen zijn succesvol bijgewerkt.');
    }

    public function updatePlotListingSettings(Request $request)
    {
        $validated = $request->validate([
            'max_image_size_mb' => 'required|integer|min:1|max:200',
        ], [
            'max_image_size_mb.required' => 'Vul een maximale afbeeldingsgrootte in.',
            'max_image_size_mb.integer' => 'De maximale afbeeldingsgrootte moet een heel getal zijn.',
            'max_image_size_mb.min' => 'De maximale afbeeldingsgrootte moet minimaal 1 MB zijn.',
            'max_image_size_mb.max' => 'De maximale afbeeldingsgrootte mag niet groter dan 200 MB zijn.',
        ]);

        $settings = PlotListingSetting::first() ?? new PlotListingSetting();
        $settings->max_image_size_mb = $validated['max_image_size_mb'];
        $settings->save();

        return back()->with('success', 'De maximale afbeeldingsgrootte voor plots is succesvol bijgewerkt.');
    }
} 