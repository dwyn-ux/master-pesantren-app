<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class AppSetting extends Model
{
    protected $table = 'app_settings';

    protected $fillable = ['key', 'value', 'type', 'group', 'label', 'description'];

    protected const CACHE_PREFIX = 'app_setting:';
    protected const CACHE_TTL    = 3600;

    public static function get(string $key, mixed $default = null): mixed
    {
        $cached = Cache::remember(self::CACHE_PREFIX . $key, self::CACHE_TTL, function () use ($key) {
            $row = static::where('key', $key)->first();
            return $row ? ['value' => $row->value, 'type' => $row->type] : null;
        });

        if (!$cached) {
            return $default;
        }

        return self::cast($cached['value'], $cached['type']);
    }

    public static function set(string $key, mixed $value, string $type = 'string', array $meta = []): void
    {
        $row = static::firstOrNew(['key' => $key]);
        $row->value = is_array($value) || is_object($value)
            ? json_encode($value)
            : (string) $value;
        $row->type = $type;
        if (isset($meta['group']))       $row->group = $meta['group'];
        if (isset($meta['label']))       $row->label = $meta['label'];
        if (isset($meta['description'])) $row->description = $meta['description'];
        $row->save();

        Cache::forget(self::CACHE_PREFIX . $key);
    }

    public static function forget(string $key): void
    {
        static::where('key', $key)->delete();
        Cache::forget(self::CACHE_PREFIX . $key);
    }

    public static function flushCache(): void
    {
        static::all()->each(fn ($row) => Cache::forget(self::CACHE_PREFIX . $row->key));
    }

    protected static function cast(mixed $value, string $type): mixed
    {
        return match ($type) {
            'boolean' => in_array(strtolower((string) $value), ['1', 'true', 'yes', 'on'], true),
            'integer' => (int) $value,
            'decimal' => (float) $value,
            'json'    => json_decode((string) $value, true),
            default   => $value,
        };
    }
}
