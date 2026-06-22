@props(['slot'])

@php
    $enabled = \App\Models\SiteSetting::value('ads_enabled', false);
    $code = \App\Models\SiteSetting::value('ad_' . $slot);
@endphp

@if ($enabled && filled($code))
    <div class="ad-slot ad-{{ $slot }} flex justify-center my-6">{!! $code !!}</div>
@endif
