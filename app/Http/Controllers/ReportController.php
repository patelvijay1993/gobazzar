<?php

namespace App\Http\Controllers;

use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'reportable_type' => 'required|in:listing,event,business,job,blog_post',
            'reportable_id'   => 'required|integer',
            'reason'          => 'required|in:pornography,harmful,misleading,spam,fake,other',
            'details'         => 'nullable|string|max:500',
        ]);

        $modelMap = [
            'listing'   => \App\Models\Listing::class,
            'event'     => \App\Models\Event::class,
            'business'  => \App\Models\Business::class,
            'job'       => \App\Models\Job::class,
            'blog_post' => \App\Models\BlogPost::class,
        ];

        $modelClass = $modelMap[$request->reportable_type];
        $model = $modelClass::findOrFail($request->reportable_id);

        // Prevent duplicate report from same user/IP within 24h
        $existing = Report::where('reportable_type', $modelClass)
            ->where('reportable_id', $request->reportable_id)
            ->where(function ($q) use ($request) {
                $q->where('user_id', auth()->id())
                  ->orWhere('reporter_ip', $request->ip());
            })
            ->where('created_at', '>=', now()->subDay())
            ->exists();

        if ($existing) {
            return response()->json(['message' => 'You have already reported this content.'], 422);
        }

        Report::create([
            'user_id'         => auth()->id(),
            'reportable_type' => $modelClass,
            'reportable_id'   => $model->id,
            'reason'          => $request->reason,
            'details'         => $request->details,
            'reporter_ip'     => $request->ip(),
        ]);

        // Auto-flag content if reports threshold reached (3 reports)
        $reportCount = Report::where('reportable_type', $modelClass)
            ->where('reportable_id', $model->id)
            ->where('status', 'pending')
            ->count();

        if ($reportCount >= 3 && method_exists($model, 'update')) {
            if (isset($model->status)) {
                $model->update(['status' => 'flagged']);
            }
        }

        return response()->json(['message' => 'Thank you for your report. Our team will review it shortly.']);
    }
}
