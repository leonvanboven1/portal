{{-- The admin-chosen accent colour, overriding the default --accent-* variables from app.css. --}}
@php($branding = \App\Models\BrandingSetting::current())
@if ($branding->hasCustomAccent() || $branding->sidebar_color)
    <style>
        :root {
            @if ($branding->hasCustomAccent())
                @foreach ($branding->palette() as $shade => $channels)
                    --accent-{{ $shade }}: {{ $channels }};
                @endforeach
            @endif
            @if ($branding->sidebar_color)
                --omt-rail: {{ $branding->sidebar_color }};
                --omt-rail-soft: color-mix(in srgb, {{ $branding->sidebar_color }} 82%, white 18%);
            @endif
        }
    </style>
@endif
@if ($branding->logo_path)
    <link rel="icon" href="{{ $branding->logoUrl() }}">
@endif
