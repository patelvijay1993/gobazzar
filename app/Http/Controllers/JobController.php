<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Job;
use App\Models\Location;
use Illuminate\Http\Request;

class JobController extends Controller
{
    public function index(Request $request)
    {
        $categories = Category::where('type', 'jobs')->where('is_active', true)->orderBy('sort_order')->get();

        $jobs = Job::with('category')
            ->where('status', 'active')
            ->where(fn ($q) => $q->whereNull('expires_at')->orWhere('expires_at', '>', now()))
            ->when($request->category,  fn ($q) => $q->where('category_id', $request->category))
            ->when($request->search,    fn ($q) => $q->where(fn ($q2) =>
                $q2->where('title', 'like', '%' . $request->search . '%')
                   ->orWhere('company', 'like', '%' . $request->search . '%')))
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

        return view('jobs.index', compact('categories', 'jobs', 'cities', 'provinces'));
    }

    public function show(Job $job)
    {
        abort_if($job->status !== 'active', 404);
        $job->increment('views');
        $related = Job::where('category_id', $job->category_id)
            ->where('id', '!=', $job->id)
            ->where('status', 'active')
            ->latest()->limit(4)->get();
        return view('jobs.show', compact('job', 'related'));
    }
}
