<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Http\Request;

class SearchLog extends Model
{
    public $timestamps = false;

    protected $fillable = ['keyword', 'section', 'province', 'city', 'results_count', 'user_id', 'ip', 'searched_at'];

    protected $casts = ['searched_at' => 'datetime'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function record(Request $request, string $section, int $resultsCount): void
    {
        $keyword = trim($request->input('search', ''));
        if ($keyword === '') return;

        $user = $request->user();
        if ($user && $user->role === 'admin') return;

        static::create([
            'keyword'       => substr($keyword, 0, 200),
            'section'       => $section,
            'province'      => $request->input('province'),
            'city'          => $request->input('city'),
            'results_count' => $resultsCount,
            'user_id'       => $user?->id,
            'ip'            => $request->ip(),
            'searched_at'   => now(),
        ]);

        ActivityLog::log($request, 'searched', [
            'meta' => [
                'keyword'  => substr($keyword, 0, 200),
                'section'  => $section,
                'results'  => $resultsCount,
                'province' => $request->input('province'),
                'city'     => $request->input('city'),
            ],
        ]);
    }
}
