<?php
// app/Models/Setting.php

namespace App\Models;

use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
        'type',
        'group',
        'description',
        'is_public',
    ];

    protected $casts = [
        'is_public' => 'boolean',
    ];

    // Cache settings untuk performa
    protected static function booted()
    {
        static::saved(function () {
            Cache::forget('settings');
        });

        static::deleted(function () {
            Cache::forget('settings');
        });
    }

    // Helper method untuk get setting dengan cache
    public static function getValue(string $key, $default = null)
    {
        $settings = Cache::rememberForever('settings', function () {
            return static::all()->pluck('value', 'key');
        });

        $value = $settings->get($key, $default);

        // Convert berdasarkan type
        $setting = static::where('key', $key)->first();

        if ($setting) {
            return match ($setting->type) {
                'integer' => (int) $value,
                'boolean' => in_array($value, ['true', '1', 1, true], true),
                'decimal' => (float) $value,
                'json' => json_decode($value, true),
                default => $value,
            };
        }

        return $value;
    }

    // Helper method untuk set setting
    public static function setValue(string $key, $value): void
    {
        $setting = static::firstOrCreate(['key' => $key]);

        // Convert value ke string based on type
        if (is_bool($value)) {
            $value = $value ? 'true' : 'false';
        } elseif (is_array($value)) {
            $value = json_encode($value);
        }

        $setting->update(['value' => $value]);
    }
}
