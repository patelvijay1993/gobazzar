<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $primaryKey = 'key';
    protected $keyType    = 'string';
    public $incrementing  = false;

    protected $fillable = ['key', 'value'];

    public static function get(string $key, mixed $default = null): mixed
    {
        return Cache::remember("setting_{$key}", 300, function () use ($key, $default) {
            $row = static::find($key);
            return $row ? $row->value : $default;
        });
    }

    public static function set(string $key, mixed $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget("setting_{$key}");
    }

    public static function bool(string $key, bool $default = false): bool
    {
        return filter_var(static::get($key, $default ? '1' : '0'), FILTER_VALIDATE_BOOLEAN);
    }
}
