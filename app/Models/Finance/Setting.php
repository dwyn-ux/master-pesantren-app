<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $table = 'finance_settings';

    protected $fillable = ['key', 'value', 'group', 'label', 'description', 'type'];

    public static function get(string $key, mixed $default = null): mixed
    {
        $value = Cache::remember("finance_setting:{$key}", 3600, function () use ($key) {
            $row = static::where('key', $key)->first();
            return $row ? ['value' => $row->value, 'type' => $row->type] : null;
        });

        if (!$value) return $default;

        return static::cast($value['value'], $value['type']);
    }

    public static function set(string $key, mixed $value, string $type = 'string'): void
    {
        $row = static::firstOrNew(['key' => $key]);
        $row->value = is_array($value) ? json_encode($value) : (string) $value;
        $row->type = $type;
        $row->save();
        Cache::forget("finance_setting:{$key}");
    }

    public static function isEnabled(string $feature): bool
    {
        return (bool) static::get("feature.{$feature}", false);
    }

    protected static function cast(mixed $value, string $type): mixed
    {
        return match ($type) {
            'boolean' => in_array(strtolower((string) $value), ['1', 'true', 'yes', 'on']),
            'integer' => (int) $value,
            'decimal' => (float) $value,
            'json'    => json_decode((string) $value, true),
            default   => $value,
        };
    }

    public function getCastedValueAttribute(): mixed
    {
        return self::cast($this->value, $this->type);
    }
}
