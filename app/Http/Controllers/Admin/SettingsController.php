<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PortalFeature;
use App\Models\PermitSetting;
use App\Models\CompanySetting;
use App\Models\PlotListingSetting;
use App\Models\SiteContent;
use App\Models\SiteRule;
use App\Models\SiteRuleCategory;
use App\Support\Features;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

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
            'siteRuleCategories' => SiteRuleCategory::with('rules')->orderBy('sort_order')->orderBy('id')->get(),
        ]);
    }

    public function updateSiteContent(Request $request)
    {
        $validated = $request->validate([
            'hero_title' => ['required', 'string', 'max:120'],
            'hero_text' => ['required', 'string', 'max:2000'],
            'about_title' => ['required', 'string', 'max:120'],
            'about_text' => ['required', 'string', 'max:5000'],
        ]);

        SiteContent::current()->update($validated);

        return back()->with('success', 'De inhoud van de website is bijgewerkt.');
    }

    public function updateSiteImages(Request $request)
    {
        $request->validate([
            'hero_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'unique_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'steps_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'remove_hero_image' => ['nullable', 'boolean'],
            'remove_unique_image' => ['nullable', 'boolean'],
            'remove_steps_image' => ['nullable', 'boolean'],
        ]);

        $content = SiteContent::current();
        $publicDisk = Storage::disk('public');
        $replacedPaths = [];

        foreach ([
            'hero_image' => 'hero_image_path',
            'unique_image' => 'unique_image_path',
            'steps_image' => 'steps_image_path',
        ] as $input => $column) {
            $remove = $request->boolean('remove_'.$input);
            if (! $request->hasFile($input) && ! $remove) {
                continue;
            }

            $replacedPaths[] = $content->{$column};
            $content->{$column} = null;

            if ($request->hasFile($input)) {
                $path = $publicDisk->putFile('site', $request->file($input));
                if (! $path) {
                    throw new RuntimeException("Het opslaan van de afbeelding {$input} is mislukt.");
                }
                $content->{$column} = $path;
            }
        }

        $content->save();

        foreach (array_filter($replacedPaths) as $path) {
            if ($publicDisk->exists($path) && (! $publicDisk->delete($path) || $publicDisk->exists($path))) {
                throw new RuntimeException("Het verwijderen van de oude website-afbeelding {$path} is mislukt.");
            }
        }

        return back()->with('success', 'De afbeeldingen van de website zijn bijgewerkt.');
    }

    public function storeSiteRuleCategory(Request $request)
    {
        $validated = $request->validate(['name' => ['required', 'string', 'max:100']]);
        $validated['sort_order'] = (int) SiteRuleCategory::max('sort_order') + 1;
        SiteRuleCategory::create($validated);

        return back()->with('success', 'De regelcategorie is toegevoegd.');
    }

    public function destroySiteRuleCategory(SiteRuleCategory $category)
    {
        $category->delete();

        return back()->with('success', 'De regelcategorie en bijbehorende regels zijn verwijderd.');
    }

    public function storeSiteRule(Request $request, SiteRuleCategory $category)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'body' => ['required', 'string', 'max:5000'],
        ]);
        $validated['sort_order'] = (int) $category->rules()->max('sort_order') + 1;
        $category->rules()->create($validated);

        return back()->with('success', 'De regel is toegevoegd.');
    }

    public function updateSiteRule(Request $request, SiteRule $siteRule)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:160'],
            'body' => ['required', 'string', 'max:5000'],
        ]);
        $siteRule->update($validated);

        return back()->with('success', 'De regel is bijgewerkt.');
    }

    public function destroySiteRule(SiteRule $siteRule)
    {
        $siteRule->delete();

        return back()->with('success', 'De regel is verwijderd.');
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