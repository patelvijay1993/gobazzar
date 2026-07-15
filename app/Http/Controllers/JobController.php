<?php

namespace App\Http\Controllers;

use App\Models\Advertisement;
use App\Models\Category;
use App\Models\Job;
use App\Models\Location;
use App\Models\ActivityLog;
use App\Models\PageView;
use App\Models\SearchLog;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::where('type', 'jobs')->where('is_active', true)->whereNull('parent_id')
            ->with(['children' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order')])
            ->orderBy('sort_order')->get();

        $jobs = Job::with('category')
            ->live()
            ->when($request->category,  fn ($q) => $q->where('category_id', $request->category))
            ->when($request->search,    fn ($q) => $q->where(fn ($q2) => $q2
                ->where('title',   'like', '%' . addcslashes($request->search, '%_\\') . '%')
                ->orWhere('company', 'like', '%' . addcslashes($request->search, '%_\\') . '%')))
            ->when($request->job_type,  fn ($q) => $q->where('job_type', $request->job_type))
            ->when($request->work_mode, fn ($q) => $q->where('work_mode', $request->work_mode))
            ->when($request->city,      fn ($q) => $q->where('city', $request->city))
            ->when($request->province,  fn ($q) => $q->where('province', $request->province))
            ->orderByDesc('is_featured')
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $provinces = Location::activeProvinces();
        $cities    = Location::activeCities($request->province);
        $ads       = Advertisement::forPosition('sidebar', $request->city, $request->province, 'jobs')
            ->merge(Advertisement::forPosition('inline', $request->city, $request->province, 'jobs'))
            ->unique('id');

        SearchLog::record($request, 'jobs', $jobs->total());
        PageView::recordPage('jobs', $request);

        return view('jobs.index', compact('categories', 'jobs', 'cities', 'provinces', 'ads'));
    }

    public function show(Request $request, Job $job)
    {
        if ($job->status !== 'active') abort(404);
        if ($job->isExpired()) {
            return response(view('errors.expired', [
                'type'      => 'job',
                'title'     => $job->title,
                'expiredAt' => $job->expires_at,
                'browseUrl' => route('jobs.index'),
            ]), 410);
        }
        $job->increment('views');
        PageView::record($job, $request);
        ActivityLog::log($request, 'viewed_job', [
            'subject_type'  => get_class($job),
            'subject_id'    => $job->id,
            'subject_label' => $job->title,
        ]);
        $related = Job::where('category_id', $job->category_id)
            ->where('id', '!=', $job->id)
            ->where('status', 'active')
            ->latest()->limit(4)->get();
        return view('jobs.show', compact('job', 'related'));
    }
}
