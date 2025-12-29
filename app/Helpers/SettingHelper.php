<?php

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

if (! function_exists('setting')) {
    function setting(string $key, $default = null)
    {
        return Cache::rememberForever(
            "setting_{$key}",
            fn () => Setting::where('key', $key)->value('value') ?? $default
        );
    }
}
