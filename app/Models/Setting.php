<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];

    public static function get(string $key, mixed $default = null): mixed
    {
        $value = static::where('key', $key)->value('value');

        return $value ?? $default;
    }

    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value === null ? null : (string) $value]);
    }

    /** Store an array as JSON. */
    public static function setJson(string $key, array $value): void
    {
        static::set($key, json_encode($value));
    }

    public static function getJson(string $key, array $default = []): array
    {
        $decoded = json_decode((string) static::get($key, ''), true);

        return is_array($decoded) ? $decoded : $default;
    }
}
