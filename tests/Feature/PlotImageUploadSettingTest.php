<?php

use App\Models\PlotListingSetting;

it('stores a custom maximum plot image size in MB', function () {
    $tenant = $this->createTenant('alpha');

    $tenant->run(function () {
        $setting = PlotListingSetting::first() ?? new PlotListingSetting();
        $setting->max_image_size_mb = 25;
        $setting->save();

        expect($setting->max_image_size_mb)->toBe(25)
            ->and(PlotListingSetting::current()->max_image_size_mb)->toBe(25)
            ->and(PlotListingSetting::current()->max_image_kb())->toBe(25600);
    });
});
