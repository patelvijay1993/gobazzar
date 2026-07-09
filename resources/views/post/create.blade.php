@extends('layouts.app')

@section('title', 'Post Something — GoBazaar')

@push('styles')
<style>
/* Map legacy theme vars to current blue theme */
.post-wrap{
  --red:var(--primary);--red-dark:var(--primary-dark);--red-pale:var(--primary-light);
  --border2:var(--border);--surface:#fff;--bg:#f9fafb;--hint:#9ca3af;
  --rl:14px;--r:8px;--amber:#92400e;--amber-bg:#fef9c3;--dark:#1a3a8f;--gold:#e8a020;
}
.post-wrap{max-width:860px;margin:32px auto;padding:0 20px}
@media(max-width:600px){.post-wrap{padding:0 14px;margin:16px auto}.form-card-body{padding:16px}.post-hero h1{font-size:20px}}
.post-hero{text-align:center;margin-bottom:28px}
.post-hero h1{font-family:var(--fh);font-size:24px;font-weight:800;margin-bottom:6px}
.post-hero p{color:var(--muted);font-size:13px}

/* Type selector */
.type-tabs{display:grid;grid-template-columns:repeat(5,1fr);gap:10px;margin-bottom:28px}
@media(max-width:600px){.type-tabs{grid-template-columns:1fr 1fr}}
.type-tab{border:2px solid var(--border2);border-radius:var(--rl);padding:14px 10px;text-align:center;cursor:pointer;transition:all .15s;background:var(--surface)}
.type-tab:hover{border-color:var(--red);background:var(--red-pale)}
.type-tab.active{border-color:var(--red);background:var(--red-pale)}
.type-tab .tab-icon{font-size:26px;margin-bottom:6px}
.type-tab .tab-label{font-size:12px;font-weight:600;color:var(--muted)}
.type-tab.active .tab-label{color:var(--red)}

/* Form card */
.form-card{background:var(--surface);border:1.5px solid var(--border);border-radius:var(--rl);overflow:hidden}
.form-card-head{background:var(--dark);color:#fff;padding:14px 24px;font-family:var(--fh);font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;display:flex;align-items:center;gap:10px}
.form-card-body{padding:28px}

.form-section{margin-bottom:24px;padding-bottom:20px;border-bottom:1px solid var(--border)}
.form-section:last-child{border-bottom:none;margin-bottom:0}
.form-section-title{font-family:var(--fh);font-size:12px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.6px;margin-bottom:14px}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.form-row-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:14px}
@media(max-width:600px){.form-row,.form-row-3{grid-template-columns:1fr}}
.form-group{margin-bottom:0}
.form-label{display:block;font-size:11.5px;font-weight:600;color:var(--text);margin-bottom:5px;text-transform:uppercase;letter-spacing:.4px}
.form-label span{color:var(--red)}
.form-input{width:100%;border:1.5px solid var(--border2);border-radius:var(--r);padding:10px 14px;font-size:13.5px;transition:border .15s;background:var(--surface);color:var(--text)}
.form-input:focus{border-color:var(--red);outline:none}
textarea.form-input{resize:vertical;min-height:100px}
.form-hint{font-size:11px;color:var(--hint);margin-top:4px}
.form-full{grid-column:1/-1}

.btn-submit{background:var(--red);color:#fff;border:none;border-radius:var(--r);padding:12px 32px;font-size:14px;font-weight:700;cursor:pointer;transition:background .15s;display:inline-flex;align-items:center;gap:8px}
.btn-submit:hover{background:var(--red-dark)}

.notice-box{background:var(--amber-bg);border:1px solid #fde68a;border-radius:var(--r);padding:12px 16px;font-size:12.5px;color:var(--amber);margin-bottom:20px;display:flex;gap:10px;align-items:flex-start}

.hidden{display:none}

/* Quill editor */
.ql-editor-wrap{border:1.5px solid var(--border2);border-radius:var(--r);overflow:hidden;background:var(--surface)}
.ql-editor-wrap .ql-toolbar{border:none;border-bottom:1.5px solid var(--border2);background:var(--bg);padding:6px 10px}
.ql-editor-wrap .ql-container{border:none;font-size:13.5px;font-family:var(--fb)}
.ql-editor-wrap .ql-editor{min-height:120px;color:var(--text);padding:10px 14px}
.ql-editor-wrap .ql-editor.ql-blank::before{color:var(--hint);font-style:normal}
.ql-editor-wrap:focus-within{border-color:var(--red)}
</style>
@endpush

@section('content')
<div class="post-wrap">
  @if(in_array($type, ['business', 'business-post']))
  {{-- Business flow: back link instead of tabs --}}
  <div class="post-hero">
    <h1>{{ $type === 'business' ? 'Register Your Business' : 'Add a Business Post' }}</h1>
    <p><a href="{{ route('account') }}#business" onclick="history.back();return false;" style="color:var(--primary);font-weight:600;text-decoration:none"><i class="fa-solid fa-arrow-left" style="font-size:11px;margin-right:5px"></i>Back to My Business</a></p>
  </div>
  @else
  <div class="post-hero">
    <h1>Post Something</h1>
    <p>Choose a category and fill in the details. Your post goes live after admin review.</p>
  </div>

  {{-- Type selector --}}
  <div class="type-tabs">
    <div class="type-tab {{ $type==='classified' ? 'active' : '' }}" onclick="switchType('classified',this)">
      <div class="tab-icon">🏷️</div>
      <div class="tab-label">Classified</div>
    </div>
    <div class="type-tab {{ $type==='job' ? 'active' : '' }}" onclick="switchType('job',this)">
      <div class="tab-icon">💼</div>
      <div class="tab-label">Job</div>
    </div>
    <div class="type-tab {{ $type==='event' ? 'active' : '' }}" onclick="switchType('event',this)">
      <div class="tab-icon">🎉</div>
      <div class="tab-label">Event</div>
    </div>
  </div>
  @endif

  @if($errors->any())
  <div class="flash flash-error" style="margin-bottom:20px">
    <strong>Please fix the following errors:</strong>
    <ul style="margin:6px 0 0 18px">
      @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
    </ul>
  </div>
  @endif

  @php
    $authUser    = Auth::user();
    $activePlan  = $authUser->activePlan();
    $planName    = $authUser->planName();
    $postDays    = $authUser->postDays();
    $maxListings = $authUser->maxListings();
    $usedListings= $authUser->activeListingCount();
  @endphp

  {{-- Plan Status Bar (hidden for business flows) --}}
  @if(!in_array($type, ['business', 'business-post']))
  <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--r);padding:14px 18px;margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap">
    <div style="font-size:13px;display:flex;flex-wrap:wrap;gap:12px;align-items:center">
      <span>
        <span style="color:var(--muted)">Plan:</span>
        <strong style="color:var(--text);margin-left:5px">{{ $planName }}</strong>
        @if($authUser->hasVerifiedBadge())
          <span style="background:#dbeafe;color:#1d4ed8;font-size:10px;font-weight:700;padding:2px 7px;border-radius:10px;margin-left:4px">✓ Verified</span>
        @endif
      </span>
      <span style="color:#d1d5db">|</span>
      <span style="color:var(--muted)">Listings:
        <strong style="color:{{ $usedListings >= $maxListings ? '#dc2626' : 'var(--text)' }}">{{ $usedListings }} / {{ $maxListings === 9999 ? '∞' : $maxListings }}</strong>
      </span>
      <span style="color:#d1d5db">|</span>
      <span style="color:var(--muted)">Visibility:
        <strong style="color:var(--text)">{{ $postDays ? $postDays.' days' : 'Auto-renew ∞' }}</strong>
      </span>
    </div>
    @if($activePlan === 'free')
      <a href="{{ route('pricing') }}" style="font-size:12px;font-weight:600;color:var(--red);white-space:nowrap">⬆ Upgrade Plan →</a>
    @endif
  </div>
  @endif {{-- end plan status bar --}}

  {{-- ── CLASSIFIED ─────────────────────────────────────────────── --}}
  <div id="form-classified" class="{{ $type!=='classified' ? 'hidden' : '' }}">
    @if(!$authUser->canPostListing())
      <div style="background:#fef2f2;border:1.5px solid #fca5a5;border-radius:var(--r);padding:16px 18px;margin-bottom:16px;display:flex;gap:12px;align-items:flex-start">
        <i class="fa-solid fa-circle-xmark" style="color:#dc2626;font-size:18px;flex-shrink:0;margin-top:2px"></i>
        <div>
          <strong style="color:#dc2626;font-size:13px">Listing limit reached ({{ $usedListings }}/{{ $maxListings }})</strong><br>
          <span style="font-size:12px;color:#991b1b">Delete an inactive listing or <a href="{{ route('pricing') }}" style="color:#dc2626;font-weight:700;text-decoration:underline">upgrade your plan</a> to post more.</span>
        </div>
      </div>
    @elseif($usedListings >= $maxListings - 1 && $maxListings < 9999)
      <div class="notice-box" style="margin-bottom:16px">
        <i class="fa-solid fa-triangle-exclamation" style="flex-shrink:0"></i>
        <span>You're using <strong>{{ $usedListings }}/{{ $maxListings }}</strong> active listings on the {{ $planName }} plan. <a href="{{ route('pricing') }}" style="color:inherit;font-weight:700;text-decoration:underline">Upgrade</a> before you run out.</span>
      </div>
    @endif
    <form method="POST" action="{{ route('post.classified') }}" enctype="multipart/form-data">
      @csrf
      <div class="form-card">
        <div class="form-card-head">🏷️ Post a Classified Ad</div>
        <div class="form-card-body">
          <div class="form-section">
            <div class="form-section-title">Basic Info</div>
            <div class="form-group" style="margin-bottom:14px">
              <label class="form-label">Title <span>*</span></label>
              <input type="text" name="title" class="form-input" value="{{ old('title') }}" required placeholder="e.g. 2BHK Apartment for Rent in Brampton">
            </div>
            <div class="form-row">
              <div class="form-group">
                <label class="form-label">Category <span>*</span></label>
                <select name="category_id" class="form-input" required onchange="clLoadFields(this.value)">
                  <option value="">Select category</option>
                  @foreach($categories->get('classifieds', collect()) as $cat)
                    <option value="{{ $cat->id }}" {{ old('category_id')==$cat->id ? 'selected' : '' }}>{{ $cat->icon }} {{ $cat->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="form-group">
                <label class="form-label">Location</label>
                <input type="text" name="location" class="form-input" value="{{ old('location', Auth::user()->city) }}" placeholder="City or area">
              </div>
            </div>
            <div class="form-row" style="margin-top:14px">
              <div class="form-group" style="grid-column:1/-1;margin-bottom:-6px">
                <button type="button" onclick="detectLocation('cl-province','cl-city',this)" style="background:none;border:1px solid var(--primary);color:var(--primary);border-radius:20px;padding:4px 12px;font-size:12px;cursor:pointer;font-weight:600">📍 Use my location</button>
              </div>
              <div class="form-group">
                <label class="form-label">Province <span>*</span></label>
                <select name="province" id="cl-province" class="form-input" required onchange="loadCities('cl-city',this.value)">
                  <option value="">Select province</option>
                  @foreach($provinces as $prov)
                    <option value="{{ $prov }}" {{ old('province', Auth::user()->province) === $prov ? 'selected' : '' }}>{{ $prov }}</option>
                  @endforeach
                </select>
              </div>
              <div class="form-group">
                <label class="form-label">City</label>
                <select name="city" id="cl-city" class="form-input" required>
                  <option value="">Select city</option>
                  @foreach($cities as $city)
                    <option value="{{ $city }}" {{ old('city', Auth::user()->city) === $city ? 'selected' : '' }}>{{ $city }}</option>
                  @endforeach
                </select>
              </div>
            </div>
          </div>

          <div class="form-section">
            <div class="form-section-title">Description & Price</div>
            <div class="form-group" style="margin-bottom:14px">
              <label class="form-label">Description</label>
              <textarea name="description" id="cl-description" style="display:none">{{ old('description') }}</textarea>
              <div id="cl-description-editor" class="ql-editor-wrap"></div>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label class="form-label">Price</label>
                <input type="number" name="price" class="form-input" value="{{ old('price') }}" placeholder="500" min="0" max="99999999" step="any">
              </div>
              <div class="form-group">
                <label class="form-label">Price Unit</label>
                <select name="price_unit" class="form-input">
                  <option value="">One-time</option>
                  <option value="/mo">/month</option>
                  <option value="/wk">/week</option>
                  <option value="/hr">/hour</option>
                  <option value="/yr">/year</option>
                </select>
              </div>
            </div>
          </div>

          {{-- Dynamic custom fields per classified category --}}
          <div class="form-section hidden" id="cl-custom-section">
            <div class="form-section-title" id="cl-custom-title">Additional Details</div>
            <div id="cl-custom-fields"></div>
          </div>

          <div class="form-section">
            <div class="form-section-title">Contact & Photos</div>
            <div class="form-row" style="margin-bottom:14px">
              <div class="form-group">
                <label class="form-label">Contact Name</label>
                <input type="text" name="contact_name" class="form-input" value="{{ old('contact_name', Auth::user()->name) }}">
              </div>
              <div class="form-group">
                <label class="form-label">Contact Phone</label>
                <input type="text" name="contact_phone" class="form-input" value="{{ old('contact_phone', Auth::user()->phone) }}" placeholder="+1 647 xxx xxxx">
              </div>
            </div>
            <div class="form-group" style="margin-bottom:14px">
              <label class="form-label">Contact Email</label>
              <input type="email" name="contact_email" class="form-input" value="{{ old('contact_email', Auth::user()->email) }}">
            </div>
            <x-image-uploader name="images" :multiple="true" :max="$maxImages" :label="'Photos (up to '.$maxImages.')'" :hint="'Your '.$user->planName().' plan allows '.$maxImages.' photos per listing'" />
          </div>

          <button type="submit" class="btn-submit">Submit Ad →</button>
        </div>
      </div>
    </form>
  </div>

  {{-- ── JOB ────────────────────────────────────────────────────── --}}
  <div id="form-job" class="{{ $type!=='job' ? 'hidden' : '' }}">
    <form method="POST" action="{{ route('post.job') }}" enctype="multipart/form-data">
      @csrf
      <div class="form-card">
        <div class="form-card-head">💼 Post a Job</div>
        <div class="form-card-body">
          <div class="form-section">
            <div class="form-section-title">Job Details</div>
            <div class="form-group" style="margin-bottom:14px">
              <label class="form-label">Job Title <span>*</span></label>
              <input type="text" name="title" class="form-input" value="{{ old('title') }}" required placeholder="e.g. Senior Software Engineer">
            </div>
            <div class="form-row" style="margin-bottom:14px">
              <div class="form-group">
                <label class="form-label">Company Name <span>*</span></label>
                <input type="text" name="company" class="form-input" value="{{ old('company') }}" required>
              </div>
              <div class="form-group">
                <label class="form-label">Category</label>
                <select name="category_id" class="form-input">
                  <option value="">Select category</option>
                  @foreach($categories->get('jobs', collect()) as $cat)
                    <option value="{{ $cat->id }}" {{ old('category_id')==$cat->id ? 'selected' : '' }}>{{ $cat->icon }} {{ $cat->name }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="form-row-3" style="margin-bottom:14px">
              <div class="form-group">
                <label class="form-label">Job Type <span>*</span></label>
                <select name="job_type" class="form-input" required>
                  <option value="full-time">Full Time</option>
                  <option value="part-time">Part Time</option>
                  <option value="contract">Contract</option>
                  <option value="freelance">Freelance</option>
                  <option value="internship">Internship</option>
                </select>
              </div>
              <div class="form-group">
                <label class="form-label">Work Mode <span>*</span></label>
                <select name="work_mode" class="form-input" required>
                  <option value="onsite">On-site</option>
                  <option value="remote">Remote</option>
                  <option value="hybrid">Hybrid</option>
                </select>
              </div>
              <div class="form-group">
                <label class="form-label">Salary</label>
                <input type="text" name="salary" class="form-input" value="{{ old('salary') }}" placeholder="$60K–$80K/yr">
              </div>
            </div>
            <div class="form-row">
              <div class="form-group" style="grid-column:1/-1;margin-bottom:-6px">
                <button type="button" onclick="detectLocation('job-province','job-city',this)" style="background:none;border:1px solid var(--primary);color:var(--primary);border-radius:20px;padding:4px 12px;font-size:12px;cursor:pointer;font-weight:600">📍 Use my location</button>
              </div>
              <div class="form-group">
                <label class="form-label">Province <span>*</span></label>
                <select name="province" id="job-province" class="form-input" required onchange="loadCities('job-city',this.value)">
                  <option value="">Select province</option>
                  @foreach($provinces as $prov)
                    <option value="{{ $prov }}" {{ old('province', Auth::user()->province) === $prov ? 'selected' : '' }}>{{ $prov }}</option>
                  @endforeach
                </select>
              </div>
              <div class="form-group">
                <label class="form-label">City <span>*</span></label>
                <select name="city" id="job-city" class="form-input" required>
                  <option value="">Select city</option>
                  @foreach($cities as $city)
                    <option value="{{ $city }}" {{ old('city', Auth::user()->city) === $city ? 'selected' : '' }}>{{ $city }}</option>
                  @endforeach
                </select>
              </div>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label class="form-label">Experience Required</label>
                <input type="text" name="experience" class="form-input" value="{{ old('experience') }}" placeholder="3+ years">
              </div>
            </div>
          </div>

          <div class="form-section">
            <div class="form-section-title">Description</div>
            <div class="form-group" style="margin-bottom:14px">
              <label class="form-label">Job Description</label>
              <textarea name="description" id="job-description" style="display:none">{{ old('description') }}</textarea>
              <div id="job-description-editor" class="ql-editor-wrap"></div>
            </div>
            <div class="form-group">
              <label class="form-label">Requirements</label>
              <textarea name="requirements" id="job-requirements" style="display:none">{{ old('requirements') }}</textarea>
              <div id="job-requirements-editor" class="ql-editor-wrap"></div>
            </div>
          </div>

          <div class="form-section">
            <div class="form-section-title">How to Apply & Logo</div>
            <div class="form-row" style="margin-bottom:14px">
              <div class="form-group">
                <label class="form-label">Apply Email</label>
                <input type="email" name="apply_email" class="form-input" value="{{ old('apply_email', Auth::user()->email) }}">
              </div>
              <div class="form-group">
                <label class="form-label">Apply URL</label>
                <input type="url" name="apply_url" class="form-input" value="{{ old('apply_url') }}" placeholder="https://…">
              </div>
            </div>
            <x-image-uploader name="company_logo" :multiple="false" :max="1" label="Company Logo" hint="Square image preferred (e.g. 200×200)" />
          </div>

          <button type="submit" class="btn-submit">Submit Job →</button>
        </div>
      </div>
    </form>
  </div>

  {{-- ── EVENT ───────────────────────────────────────────────────── --}}
  <div id="form-event" class="{{ $type!=='event' ? 'hidden' : '' }}">
    <form method="POST" action="{{ route('post.event') }}" enctype="multipart/form-data">
      @csrf
      <div class="form-card">
        <div class="form-card-head">🎉 Post an Event</div>
        <div class="form-card-body">
          <div class="form-section">
            <div class="form-section-title">Event Details</div>
            <div class="form-group" style="margin-bottom:14px">
              <label class="form-label">Event Title <span>*</span></label>
              <input type="text" name="title" class="form-input" value="{{ old('title') }}" required placeholder="e.g. Diwali Celebration 2026">
            </div>
            <div class="form-row" style="margin-bottom:14px">
              <div class="form-group">
                <label class="form-label">Start Date & Time <span>*</span></label>
                <input type="datetime-local" name="start_date" class="form-input" value="{{ old('start_date') }}" required>
              </div>
              <div class="form-group">
                <label class="form-label">End Date & Time</label>
                <input type="datetime-local" name="end_date" class="form-input" value="{{ old('end_date') }}">
              </div>
            </div>
            <div class="form-row">
              <div class="form-group">
                <label class="form-label">Category</label>
                <select name="category_id" class="form-input">
                  <option value="">Select category</option>
                  @foreach($categories->get('events', collect()) as $cat)
                    <option value="{{ $cat->id }}" {{ old('category_id')==$cat->id ? 'selected' : '' }}>{{ $cat->icon }} {{ $cat->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="form-group">
                <label class="form-label">Ticket Price</label>
                <input type="text" name="price" class="form-input" value="{{ old('price') }}" placeholder="Free or $25">
              </div>
            </div>
          </div>

          <div class="form-section">
            <div class="form-section-title">Location</div>
            <div class="form-group" style="margin-bottom:14px">
              <label class="form-label">Venue</label>
              <input type="text" name="venue" class="form-input" value="{{ old('venue') }}" placeholder="Hall name or address">
            </div>
            <div class="form-row">
              <div class="form-group" style="grid-column:1/-1;margin-bottom:-6px">
                <button type="button" onclick="detectLocation('ev-province','ev-city',this)" style="background:none;border:1px solid var(--primary);color:var(--primary);border-radius:20px;padding:4px 12px;font-size:12px;cursor:pointer;font-weight:600">📍 Use my location</button>
              </div>
              <div class="form-group">
                <label class="form-label">Province <span>*</span></label>
                <select name="province" id="ev-province" class="form-input" required onchange="loadCities('ev-city',this.value)">
                  <option value="">Select province</option>
                  @foreach($provinces as $prov)
                    <option value="{{ $prov }}" {{ old('province', Auth::user()->province) === $prov ? 'selected' : '' }}>{{ $prov }}</option>
                  @endforeach
                </select>
              </div>
              <div class="form-group">
                <label class="form-label">City <span>*</span></label>
                <select name="city" id="ev-city" class="form-input" required>
                  <option value="">Select city</option>
                  @foreach($cities as $city)
                    <option value="{{ $city }}" {{ old('city', Auth::user()->city) === $city ? 'selected' : '' }}>{{ $city }}</option>
                  @endforeach
                </select>
              </div>
            </div>
          </div>

          <div class="form-section">
            <div class="form-section-title">Description & Contact</div>
            <div class="form-group" style="margin-bottom:14px">
              <label class="form-label">Description</label>
              <textarea name="description" id="ev-description" style="display:none">{{ old('description') }}</textarea>
              <div id="ev-description-editor" class="ql-editor-wrap"></div>
            </div>
            <div class="form-row" style="margin-bottom:14px">
              <div class="form-group">
                <label class="form-label">Organizer Name</label>
                <input type="text" name="organizer" class="form-input" value="{{ old('organizer', Auth::user()->name) }}">
              </div>
              <div class="form-group">
                <label class="form-label">Organizer Phone</label>
                <input type="text" name="organizer_phone" class="form-input" value="{{ old('organizer_phone', Auth::user()->phone) }}">
              </div>
            </div>
            <div class="form-row" style="margin-bottom:14px">
              <div class="form-group">
                <label class="form-label">Organizer Email</label>
                <input type="email" name="organizer_email" class="form-input" value="{{ old('organizer_email', Auth::user()->email) }}">
              </div>
              <div class="form-group">
                <label class="form-label">Event Website</label>
                <input type="url" name="website" class="form-input" value="{{ old('website') }}" placeholder="https://…">
              </div>
            </div>
            <x-image-uploader name="image" :multiple="false" :max="1" label="Event Banner / Photo" hint="Recommended 1200×628 (16:9)" />
          </div>

          <button type="submit" class="btn-submit">Submit Event →</button>
        </div>
      </div>
    </form>
  </div>

  {{-- ── BUSINESS ─────────────────────────────────────────────────── --}}
  <div id="form-business" class="{{ $type!=='business' ? 'hidden' : '' }}">
    @if(!$canBusiness)
      <div class="form-card">
        <div class="form-card-body" style="text-align:center;padding:48px 28px">
          <div style="font-size:52px;margin-bottom:14px">🔒</div>
          <h3 style="font-family:var(--fh);font-size:20px;font-weight:800;margin-bottom:8px;color:var(--text)">Business Listings require Verified or Power Seller</h3>
          <p style="font-size:13.5px;color:var(--muted);max-width:420px;margin:0 auto 24px;line-height:1.6">
            Your Free plan does not include business directory listings. Upgrade to <strong>Verified ($4.99/mo)</strong> to list 1 business, or <strong>Power Seller ($14.99/mo)</strong> for unlimited businesses.
          </p>
          <a href="{{ route('pricing') }}" class="btn-submit" style="text-decoration:none;display:inline-flex">⬆ View Plans & Upgrade</a>
        </div>
      </div>
    @elseif($myBusiness)
      {{-- Already has a business → edit only, no new register --}}
      <div class="form-card">
        <div class="form-card-head">🏢 Your Business</div>
        <div class="form-card-body" style="text-align:center;padding:40px 28px">
          <div style="font-size:46px;margin-bottom:12px">🏢</div>
          <h3 style="font-family:var(--fh);font-size:18px;font-weight:800;margin-bottom:6px;color:var(--text)">{{ $myBusiness->name }}</h3>
          <p style="font-size:13px;color:var(--muted);margin-bottom:22px;line-height:1.6;max-width:420px;margin:0 auto 22px">
            You can register only one business per account. You can edit your existing business or add posts to it.
          </p>
          <div style="display:flex;gap:10px;justify-content:center;flex-wrap:wrap">
            <a href="{{ route('post.edit', ['type'=>'business','id'=>$myBusiness->id]) }}" class="btn-submit" style="text-decoration:none">✏️ Edit Business</a>
            <button type="button" class="btn-submit" style="background:var(--gold)" onclick="switchType('business-post', document.querySelectorAll('.type-tab')[4])">📦 Add a Post →</button>
          </div>
        </div>
      </div>
    @else
    <form method="POST" action="{{ route('post.business') }}" enctype="multipart/form-data" id="biz-form">
      @csrf
      @php
        $days = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
        $oldHours = old('hours', []);
      @endphp

      {{-- ── STEP 1: Basic Info ──────────────────────────────────── --}}
      <div class="form-card" style="margin-bottom:16px">
        <div class="form-card-head">
          <span style="background:rgba(255,255,255,.2);border-radius:50%;width:26px;height:26px;display:inline-flex;align-items:center;justify-content:center;font-size:13px;font-weight:800;flex-shrink:0">1</span>
          Business Identity
        </div>
        <div class="form-card-body">
          <div class="form-row" style="margin-bottom:14px">
            <div class="form-group" style="grid-column:1/-1">
              <label class="form-label">Business Name <span>*</span></label>
              <input type="text" name="name" id="biz-name" class="form-input" value="{{ old('name') }}" required placeholder="e.g. Spice Garden Restaurant">
              <div class="form-hint">This is how customers will find you on GoBazaar</div>
            </div>
          </div>
          <div class="form-row" style="margin-bottom:14px">
            <div class="form-group">
              <label class="form-label">Category <span>*</span></label>
              <select name="category_id" id="biz-category" class="form-input" onchange="loadSubCats('biz-subcategory', this.value)" required>
                <option value="">Select category</option>
                @foreach($directoryParents as $cat)
                  <option value="{{ $cat->id }}" {{ old('category_id')==$cat->id ? 'selected' : '' }}>{{ $cat->icon }} {{ $cat->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">Sub-Category</label>
              <select name="subcategory_id" id="biz-subcategory" class="form-input">
                <option value="">Select sub-category (optional)</option>
              </select>
            </div>
          </div>

          {{-- ✨ AI Content Generator Panel --}}
          <div id="ai-gen-panel" style="background:linear-gradient(135deg,#f0f4ff,#faf5ff);border:1.5px solid #c7d4f0;border-radius:12px;padding:18px 20px;margin-bottom:18px">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;flex-wrap:wrap;gap:10px">
              <div style="display:flex;align-items:center;gap:10px">
                <div style="background:var(--primary);color:#fff;border-radius:8px;width:34px;height:34px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0">✨</div>
                <div>
                  <div style="font-size:13px;font-weight:700;color:var(--primary)">AI Content Generator</div>
                  <div style="font-size:11px;color:#6b7280">Describe your business in a few words — AI will write the full description & tags</div>
                </div>
              </div>
              <div style="display:flex;align-items:center;gap:8px">
                <label style="font-size:11.5px;font-weight:600;color:var(--muted);white-space:nowrap">Language:</label>
                <select id="ai-lang" style="border:1.5px solid #c7d4f0;border-radius:6px;padding:5px 10px;font-size:12px;background:#fff;color:var(--text)">
                  <option value="en">English</option>
                  <option value="gu">ગુજરાતી</option>
                  <option value="hi">हिंदी</option>
                </select>
              </div>
            </div>

            <div style="margin-bottom:12px">
              <label style="display:block;font-size:11px;font-weight:700;color:var(--text);text-transform:uppercase;letter-spacing:.4px;margin-bottom:5px">
                What does your business do? <span style="color:var(--primary)">*</span>
              </label>
              <textarea id="ai-keywords" rows="3" style="width:100%;border:1.5px solid #c7d4f0;border-radius:8px;padding:10px 14px;font-size:13px;font-family:var(--fb);resize:vertical;background:#fff;color:var(--text)" placeholder="e.g. We serve authentic Gujarati food — thali, dhokla, snacks. Vegetarian only. Family-run since 2015. Catering available for events. Located in Brampton near Mandir."></textarea>
            </div>

            <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
              <button type="button" id="ai-gen-btn" onclick="runAIGenerate()" style="background:var(--primary);color:#fff;border:none;border-radius:8px;padding:10px 22px;font-size:13px;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:8px;transition:background .15s">
                <span id="ai-btn-icon">✨</span>
                <span id="ai-btn-text">Generate Content</span>
              </button>
              <div id="ai-status" style="font-size:12px;color:var(--muted)"></div>
            </div>

            {{-- Preview area --}}
            <div id="ai-preview" style="display:none;margin-top:16px;border-top:1px solid #dde3f5;padding-top:16px">
              <div style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.4px;margin-bottom:10px">✨ Generated Content — Review and Apply</div>

              <div style="background:#fff;border:1.5px solid #dde3f5;border-radius:8px;padding:14px 16px;margin-bottom:10px;font-size:13px;line-height:1.7;color:var(--text)" id="ai-desc-preview"></div>

              <div style="margin-bottom:12px">
                <div style="font-size:11px;font-weight:700;color:var(--muted);margin-bottom:6px">SUGGESTED TAGLINE</div>
                <div id="ai-tagline-preview" style="font-size:13px;font-style:italic;color:var(--primary);background:#f0f4ff;padding:8px 12px;border-radius:6px"></div>
              </div>

              <div style="margin-bottom:14px">
                <div style="font-size:11px;font-weight:700;color:var(--muted);margin-bottom:6px">SUGGESTED TAGS</div>
                <div id="ai-tags-preview" style="display:flex;flex-wrap:wrap;gap:6px"></div>
              </div>

              <div style="display:flex;gap:8px;flex-wrap:wrap">
                <button type="button" onclick="applyAIContent()" style="background:var(--primary);color:#fff;border:none;border-radius:7px;padding:9px 20px;font-size:13px;font-weight:700;cursor:pointer">✅ Apply to Form</button>
                <button type="button" onclick="runAIGenerate()" style="background:#fff;color:var(--primary);border:1.5px solid var(--primary);border-radius:7px;padding:9px 20px;font-size:13px;font-weight:600;cursor:pointer">🔄 Regenerate</button>
                <button type="button" onclick="document.getElementById('ai-preview').style.display='none'" style="background:#fff;color:var(--muted);border:1.5px solid var(--border);border-radius:7px;padding:9px 16px;font-size:12px;cursor:pointer">Dismiss</button>
              </div>
            </div>
          </div>

          <div class="form-group" style="margin-bottom:14px">
            <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:5px">
              <label class="form-label" style="margin-bottom:0">About Your Business</label>
              <span style="font-size:11px;color:var(--muted)">Use AI above to auto-fill ↑</span>
            </div>
            <textarea name="description" id="biz-description" style="display:none">{{ old('description') }}</textarea>
            <div id="biz-description-editor" class="ql-editor-wrap"></div>
            <div class="form-hint">Describe your services, specialties, and what makes you unique. Minimum 50 words recommended.</div>
          </div>
          <div class="form-group">
            <label class="form-label">Tags / Keywords</label>
            <input type="text" name="tags_input" id="biz-tags-input" class="form-input" value="{{ old('tags_input', is_array(old('tags')) ? implode(', ', old('tags')) : '') }}" placeholder="e.g. vegetarian, Indian food, catering, halal, delivery">
            <input type="hidden" name="tags" id="biz-tags-hidden" value="{{ old('tags', is_array(old('tags')) ? implode(',', old('tags')) : '') }}">
            <div class="form-hint">Comma-separated keywords — helps customers find you in search</div>
            <div id="biz-tag-pills" style="display:flex;flex-wrap:wrap;gap:6px;margin-top:8px"></div>
          </div>
        </div>
      </div>

      {{-- ── STEP 2: Location & Contact ──────────────────────────── --}}
      <div class="form-card" style="margin-bottom:16px">
        <div class="form-card-head">
          <span style="background:rgba(255,255,255,.2);border-radius:50%;width:26px;height:26px;display:inline-flex;align-items:center;justify-content:center;font-size:13px;font-weight:800;flex-shrink:0">2</span>
          Location & Contact
        </div>
        <div class="form-card-body">
          <div class="form-group" style="margin-bottom:14px">
            <label class="form-label">Street Address</label>
            <input type="text" name="address" class="form-input" value="{{ old('address') }}" placeholder="123 Main St, Unit 4">
          </div>
          <div class="form-row" style="margin-bottom:14px">
            <div class="form-group" style="grid-column:1/-1;margin-bottom:-6px">
              <button type="button" onclick="detectLocation('biz-province','biz-city',this)" style="background:none;border:1px solid var(--primary);color:var(--primary);border-radius:20px;padding:4px 12px;font-size:12px;cursor:pointer;font-weight:600">📍 Use my location</button>
            </div>
            <div class="form-group">
              <label class="form-label">Province <span>*</span></label>
              <select name="province" id="biz-province" class="form-input" required onchange="loadCities('biz-city',this.value)">
                <option value="">Select province</option>
                @foreach($provinces as $prov)
                  <option value="{{ $prov }}" {{ old('province', Auth::user()->province) === $prov ? 'selected' : '' }}>{{ $prov }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">City <span>*</span></label>
              <select name="city" id="biz-city" class="form-input" required>
                <option value="">Select city</option>
                @foreach($cities as $city)
                  <option value="{{ $city }}" {{ old('city', Auth::user()->city) === $city ? 'selected' : '' }}>{{ $city }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-row" style="margin-bottom:14px">
            <div class="form-group">
              <label class="form-label">Phone Number</label>
              <input type="text" name="phone" class="form-input" value="{{ old('phone', Auth::user()->phone) }}" placeholder="+1 647 xxx xxxx">
            </div>
            <div class="form-group">
              <label class="form-label">Business Email</label>
              <input type="email" name="email" class="form-input" value="{{ old('email', Auth::user()->email) }}">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label class="form-label">Website</label>
              <input type="url" name="website" class="form-input" value="{{ old('website') }}" placeholder="https://yourbusiness.com">
            </div>
            <div class="form-group">
              <label class="form-label">Google Maps Link</label>
              <input type="url" name="map_url" class="form-input" value="{{ old('map_url') }}" placeholder="https://maps.google.com/…">
            </div>
          </div>
        </div>
      </div>

      {{-- ── STEP 3: Business Hours ──────────────────────────────── --}}
      <div class="form-card" style="margin-bottom:16px">
        <div class="form-card-head">
          <span style="background:rgba(255,255,255,.2);border-radius:50%;width:26px;height:26px;display:inline-flex;align-items:center;justify-content:center;font-size:13px;font-weight:800;flex-shrink:0">3</span>
          Business Hours
          <span style="margin-left:auto;font-size:11px;font-weight:400;opacity:.7">Optional — leave blank if hours vary</span>
        </div>
        <div class="form-card-body">
          <div style="display:grid;gap:10px">
            @foreach($days as $day)
            @php $dkey = strtolower($day); @endphp
            <div style="display:grid;grid-template-columns:110px 1fr 1fr 120px;gap:10px;align-items:center">
              <label style="font-size:13px;font-weight:600;color:var(--text)">{{ $day }}</label>
              <input type="time" name="hours[{{ $dkey }}][open]" class="form-input" value="{{ $oldHours[$dkey]['open'] ?? '' }}" placeholder="09:00" style="font-size:13px;padding:8px 10px">
              <input type="time" name="hours[{{ $dkey }}][close]" class="form-input" value="{{ $oldHours[$dkey]['close'] ?? '' }}" placeholder="18:00" style="font-size:13px;padding:8px 10px">
              <label style="display:flex;align-items:center;gap:7px;font-size:12px;color:var(--muted);cursor:pointer">
                <input type="checkbox" name="hours[{{ $dkey }}][closed]" value="1" {{ !empty($oldHours[$dkey]['closed']) ? 'checked' : '' }} style="width:16px;height:16px"> Closed
              </label>
            </div>
            @endforeach
          </div>
          <div class="form-hint" style="margin-top:12px">Enter opening and closing times for each day. Check "Closed" for days you're not open.</div>
        </div>
      </div>

      {{-- ── STEP 4: Social Media ────────────────────────────────── --}}
      <div class="form-card" style="margin-bottom:16px">
        <div class="form-card-head">
          <span style="background:rgba(255,255,255,.2);border-radius:50%;width:26px;height:26px;display:inline-flex;align-items:center;justify-content:center;font-size:13px;font-weight:800;flex-shrink:0">4</span>
          Social Media
          <span style="margin-left:auto;font-size:11px;font-weight:400;opacity:.7">Optional</span>
        </div>
        <div class="form-card-body">
          <div class="form-row" style="margin-bottom:14px">
            <div class="form-group">
              <label class="form-label">
                <i class="fa-brands fa-facebook" style="color:#1877f2;margin-right:5px"></i>Facebook Page
              </label>
              <input type="url" name="social[facebook]" class="form-input" value="{{ old('social.facebook') }}" placeholder="https://facebook.com/yourbusiness">
            </div>
            <div class="form-group">
              <label class="form-label">
                <i class="fa-brands fa-instagram" style="color:#e1306c;margin-right:5px"></i>Instagram
              </label>
              <input type="url" name="social[instagram]" class="form-input" value="{{ old('social.instagram') }}" placeholder="https://instagram.com/yourbusiness">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label class="form-label">
                <i class="fa-brands fa-whatsapp" style="color:#25d366;margin-right:5px"></i>WhatsApp Number
              </label>
              <input type="text" name="social[whatsapp]" class="form-input" value="{{ old('social.whatsapp') }}" placeholder="+1 647 xxx xxxx">
            </div>
            <div class="form-group">
              <label class="form-label">
                <i class="fa-brands fa-youtube" style="color:#ff0000;margin-right:5px"></i>YouTube Channel
              </label>
              <input type="url" name="social[youtube]" class="form-input" value="{{ old('social.youtube') }}" placeholder="https://youtube.com/@yourbusiness">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label class="form-label">
                <i class="fa-brands fa-x-twitter" style="margin-right:5px"></i>Twitter / X
              </label>
              <input type="url" name="social[twitter]" class="form-input" value="{{ old('social.twitter') }}" placeholder="https://twitter.com/yourbusiness">
            </div>
            <div class="form-group">
              <label class="form-label">
                <i class="fa-brands fa-linkedin" style="color:#0077b5;margin-right:5px"></i>LinkedIn
              </label>
              <input type="url" name="social[linkedin]" class="form-input" value="{{ old('social.linkedin') }}" placeholder="https://linkedin.com/company/yourbusiness">
            </div>
          </div>
        </div>
      </div>

      {{-- ── STEP 5: Photos & Logo ───────────────────────────────── --}}
      <div class="form-card" style="margin-bottom:20px">
        <div class="form-card-head">
          <span style="background:rgba(255,255,255,.2);border-radius:50%;width:26px;height:26px;display:inline-flex;align-items:center;justify-content:center;font-size:13px;font-weight:800;flex-shrink:0">5</span>
          Photos & Logo
        </div>
        <div class="form-card-body">
          <div style="background:var(--primary-light);border-radius:var(--r);padding:12px 16px;margin-bottom:20px;font-size:12.5px;color:var(--primary);display:flex;gap:10px;align-items:flex-start">
            <i class="fa-solid fa-circle-info" style="flex-shrink:0;margin-top:2px"></i>
            <span>The <strong>first photo</strong> you upload becomes your main banner on the directory listing. Use a high-quality landscape image (1200×628 recommended).</span>
          </div>
          <div style="margin-bottom:20px">
            <x-image-uploader name="images" :multiple="true" :max="$maxImages" :label="'Business Photos (up to '.$maxImages.')'" :hint="'First photo = main banner · '.$user->planName().' plan allows '.$maxImages.' photos'" />
          </div>
          <x-image-uploader name="logo" :multiple="false" :max="1" label="Business Logo" hint="Square image preferred (200×200 minimum). Shows in search results and listings." />
        </div>
      </div>

      <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap">
        <div style="font-size:12px;color:var(--muted)">
          <i class="fa-solid fa-shield-halved" style="color:var(--primary);margin-right:5px"></i>
          Your listing goes live after a quick admin review (usually within 24 hours).
        </div>
        <button type="button" id="biz-submit-btn" onclick="submitBizForm()" class="btn-submit" style="font-size:15px;padding:14px 40px">
          🏢 Submit Business Listing →
        </button>
      </div>
    </form>
    @endif
  </div>

  {{-- ── BUSINESS POST (product/service under a registered business) ── --}}
  <div id="form-business-post" class="{{ $type!=='business-post' ? 'hidden' : '' }}">
    @if(!$canBusiness)
      <div class="form-card">
        <div class="form-card-body" style="text-align:center;padding:48px 28px">
          <div style="font-size:52px;margin-bottom:14px">🔒</div>
          <h3 style="font-family:var(--fh);font-size:20px;font-weight:800;margin-bottom:8px;color:var(--text)">Business Posts require Verified or Power Seller</h3>
          <p style="font-size:13.5px;color:var(--muted);max-width:420px;margin:0 auto 24px;line-height:1.6">
            Upgrade to <strong>Verified ($4.99/mo)</strong> or <strong>Power Seller ($14.99/mo)</strong> to list your business and add products/service posts.
          </p>
          <a href="{{ route('pricing') }}" class="btn-submit" style="text-decoration:none;display:inline-flex">⬆ View Plans & Upgrade</a>
        </div>
      </div>
    @elseif($myBusinesses->isEmpty())
      {{-- Has plan, but no business yet → prompt to register first --}}
      <div class="form-card">
        <div class="form-card-head">📦 Post in Your Business</div>
        <div class="form-card-body" style="text-align:center;padding:48px 28px">
          <div style="font-size:48px;margin-bottom:14px">🏢</div>
          <h3 style="font-family:var(--fh);font-size:18px;font-weight:800;margin-bottom:8px;color:var(--text)">Register a business first</h3>
          <p style="font-size:13px;color:var(--muted);margin-bottom:22px;line-height:1.6;max-width:420px;margin-left:auto;margin-right:auto">
            To add products or service posts, you need a registered business. It only takes a minute — register your business, then come back here to post.
          </p>
          <button type="button" class="btn-submit" onclick="switchType('business', document.querySelectorAll('.type-tab')[3])">
            🏢 Register Your Business →
          </button>
        </div>
      </div>
    @else
      @php $bizForPost = $myBusinesses->first(); @endphp
      <form method="POST" action="{{ route('post.business-post') }}" enctype="multipart/form-data">
        @csrf
        <input type="hidden" name="business_id" value="{{ $bizForPost->id }}">
        <div class="form-card">
          <div class="form-card-head">📦 Add a Post to "{{ $bizForPost->name }}"</div>
          <div class="form-card-body">

            <div class="form-section">
              <div class="form-section-title">Category</div>
              <div class="form-row" style="margin-bottom:0">
                <div class="form-group">
                  <label class="form-label">Category <span>*</span></label>
                  <select name="category_id" id="bp-category" class="form-input" required onchange="bpOnCategoryChange()">
                    <option value="">Select category</option>
                    @foreach($directoryParents as $cat)
                      <option value="{{ $cat->id }}" {{ old('category_id')==$cat->id ? 'selected' : '' }}>{{ $cat->icon }} {{ $cat->name }}</option>
                    @endforeach
                  </select>
                </div>
                <div class="form-group">
                  <label class="form-label">Sub-Category</label>
                  <select name="subcategory_id" id="bp-subcategory" class="form-input" onchange="bpLoadFields()">
                    <option value="">Select sub-category (optional)</option>
                  </select>
                </div>
              </div>
            </div>

            <div class="form-section">
              <div class="form-section-title">Post Details</div>
              <div class="form-group" style="margin-bottom:14px">
                <label class="form-label">Title <span>*</span></label>
                <input type="text" name="title" class="form-input" value="{{ old('title') }}" required placeholder="e.g. Veg Thali — Lunch Special">
              </div>
              <div class="form-row" style="margin-bottom:14px">
                <div class="form-group">
                  <label class="form-label">Price</label>
                  <input type="text" name="price" class="form-input" value="{{ old('price') }}" placeholder="e.g. $12.99">
                </div>
                <div class="form-group">
                  <label class="form-label">Price Unit</label>
                  <input type="text" name="price_unit" class="form-input" value="{{ old('price_unit') }}" placeholder="e.g. /plate, /hr">
                </div>
              </div>
              <div class="form-group">
                <label class="form-label">Description</label>
                <textarea name="description" id="bp-description" style="display:none">{{ old('description') }}</textarea>
                <div id="bp-description-editor" class="ql-editor-wrap"></div>
              </div>
            </div>

            {{-- Dynamic custom fields per category --}}
            <div class="form-section hidden" id="bp-custom-section">
              <div class="form-section-title" id="bp-custom-title">Additional Details</div>
              <div id="bp-custom-fields"></div>
            </div>

            <div class="form-section">
              <div class="form-section-title">Photos</div>
              <x-image-uploader name="images" :multiple="true" :max="$maxImages" :label="'Post Photos (up to '.$maxImages.')'" :hint="'First photo will be the main image · '.$user->planName().' plan: '.$maxImages.' photos'" />
            </div>

            <button type="submit" class="btn-submit">Publish Post →</button>
          </div>
        </div>
      </form>
    @endif
  </div>

  {{-- MATRIMONIAL REMOVED --}}
  <div id="form-matrimonial" class="hidden" style="display:none">
    <form method="POST" action="{{ route('post.matrimonial') }}" enctype="multipart/form-data">
      @csrf
      <div class="form-card">
        <div class="form-card-head">💍 Create Matrimonial Profile</div>
        <div class="form-card-body">
          <div class="form-section">
            <div class="form-section-title">Profile For</div>
            <div class="form-row">
              <div class="form-group">
                <label class="form-label">Profile For <span>*</span></label>
                <select name="profile_for" class="form-input" required>
                  <option value="self">Myself</option>
                  <option value="son">Son</option>
                  <option value="daughter">Daughter</option>
                  <option value="brother">Brother</option>
                  <option value="sister">Sister</option>
                  <option value="friend">Friend</option>
                </select>
              </div>
              <div class="form-group">
                <label class="form-label">Gender <span>*</span></label>
                <select name="gender" class="form-input" required>
                  <option value="male">Male</option>
                  <option value="female">Female</option>
                </select>
              </div>
            </div>
          </div>

          <div class="form-section">
            <div class="form-section-title">Personal Details</div>
            <div class="form-row" style="margin-bottom:14px">
              <div class="form-group">
                <label class="form-label">Full Name <span>*</span></label>
                <input type="text" name="name" class="form-input" value="{{ old('name', Auth::user()->name) }}" required>
              </div>
              <div class="form-group">
                <label class="form-label">Age <span>*</span></label>
                <input type="number" name="age" class="form-input" value="{{ old('age') }}" required min="18" max="80">
              </div>
            </div>
            <div class="form-row-3" style="margin-bottom:14px">
              <div class="form-group">
                <label class="form-label">Height</label>
                <input type="text" name="height" class="form-input" value="{{ old('height') }}" placeholder="5'7&quot;">
              </div>
              <div class="form-group">
                <label class="form-label">Marital Status</label>
                <select name="marital_status" class="form-input">
                  <option value="never_married">Never Married</option>
                  <option value="divorced">Divorced</option>
                  <option value="widowed">Widowed</option>
                </select>
              </div>
              <div class="form-group">
                <label class="form-label">Diet</label>
                <select name="diet" class="form-input">
                  <option value="">Prefer not to say</option>
                  <option value="veg">Vegetarian</option>
                  <option value="non-veg">Non-Vegetarian</option>
                  <option value="eggetarian">Eggetarian</option>
                </select>
              </div>
            </div>
            <div class="form-row-3">
              <div class="form-group">
                <label class="form-label">Religion</label>
                <input type="text" name="religion" class="form-input" value="{{ old('religion') }}" placeholder="Hindu, Muslim…">
              </div>
              <div class="form-group">
                <label class="form-label">Caste</label>
                <input type="text" name="caste" class="form-input" value="{{ old('caste') }}" placeholder="Optional">
              </div>
              <div class="form-group">
                <label class="form-label">Mother Tongue</label>
                <input type="text" name="mother_tongue" class="form-input" value="{{ old('mother_tongue') }}" placeholder="Hindi, Gujarati…">
              </div>
            </div>
          </div>

          <div class="form-section">
            <div class="form-section-title">Professional</div>
            <div class="form-row-3">
              <div class="form-group">
                <label class="form-label">Education</label>
                <input type="text" name="education" class="form-input" value="{{ old('education') }}" placeholder="B.Tech, MBA…">
              </div>
              <div class="form-group">
                <label class="form-label">Occupation</label>
                <input type="text" name="occupation" class="form-input" value="{{ old('occupation') }}" placeholder="Software Engineer…">
              </div>
              <div class="form-group">
                <label class="form-label">Annual Income</label>
                <input type="text" name="income" class="form-input" value="{{ old('income') }}" placeholder="$60K–$80K">
              </div>
            </div>
          </div>

          <div class="form-section">
            <div class="form-section-title">Location</div>
            <div class="form-row">
              <div class="form-group" style="grid-column:1/-1;margin-bottom:-6px">
                <button type="button" onclick="detectLocation('mat-province','mat-city',this)" style="background:none;border:1px solid var(--primary);color:var(--primary);border-radius:20px;padding:4px 12px;font-size:12px;cursor:pointer;font-weight:600">📍 Use my location</button>
              </div>
              <div class="form-group">
                <label class="form-label">Province <span>*</span></label>
                <select name="province" id="mat-province" class="form-input" required onchange="loadCities('mat-city',this.value)">
                  <option value="">Select province</option>
                  @foreach($provinces as $prov)
                    <option value="{{ $prov }}" {{ old('province', Auth::user()->province) === $prov ? 'selected' : '' }}>{{ $prov }}</option>
                  @endforeach
                </select>
              </div>
              <div class="form-group">
                <label class="form-label">City <span>*</span></label>
                <select name="city" id="mat-city" class="form-input" required>
                  <option value="">Select city</option>
                  @foreach($cities as $city)
                    <option value="{{ $city }}" {{ old('city', Auth::user()->city) === $city ? 'selected' : '' }}>{{ $city }}</option>
                  @endforeach
                </select>
              </div>
            </div>
          </div>

          <div class="form-section">
            <div class="form-section-title">About & Preferences</div>
            <div class="form-group" style="margin-bottom:14px">
              <label class="form-label">About Yourself</label>
              <textarea name="about" class="form-input" rows="4" placeholder="Write a short bio…">{{ old('about') }}</textarea>
            </div>
            <div class="form-group">
              <label class="form-label">Partner Preference</label>
              <textarea name="partner_preference" class="form-input" rows="3" placeholder="What are you looking for in a partner?">{{ old('partner_preference') }}</textarea>
            </div>
          </div>

          <div class="form-section">
            <div class="form-section-title">Contact & Photo</div>
            <div class="form-row" style="margin-bottom:14px">
              <div class="form-group">
                <label class="form-label">Contact Name</label>
                <input type="text" name="contact_name" class="form-input" value="{{ old('contact_name', Auth::user()->name) }}">
              </div>
              <div class="form-group">
                <label class="form-label">Contact Phone</label>
                <input type="text" name="contact_phone" class="form-input" value="{{ old('contact_phone', Auth::user()->phone) }}">
              </div>
            </div>
            <div class="form-row" style="margin-bottom:14px">
              <div class="form-group">
                <label class="form-label">Contact Email</label>
                <input type="email" name="contact_email" class="form-input" value="{{ old('contact_email', Auth::user()->email) }}">
              </div>
              <div class="form-group" style="display:flex;align-items:center;gap:10px;padding-top:22px">
                <input type="checkbox" name="hide_contact" id="hide_contact" value="1" {{ old('hide_contact') ? 'checked' : '' }} style="width:18px;height:18px;cursor:pointer">
                <label for="hide_contact" style="font-size:13px;color:var(--muted);cursor:pointer">Hide contact from public</label>
              </div>
            </div>
            <x-image-uploader name="photo" :multiple="false" :max="1" label="Profile Photo" hint="Square photo preferred (e.g. 400×400)" />
            <div style="margin-top:16px">
              <x-image-uploader name="photos" :multiple="true" :max="$maxImages" label="Additional Photos (Gallery)" hint="Upload up to {{ $maxImages }} additional photos to showcase your profile" />
            </div>
          </div>

          <button type="submit" class="btn-submit">Submit Profile →</button>
        </div>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
function switchType(type, el) {
  ['classified','job','event','business','business-post'].forEach(t => {
    var f = document.getElementById('form-'+t);
    if (f) f.classList.add('hidden');
  });
  document.querySelectorAll('.type-tab').forEach(t => t.classList.remove('active'));
  var target = document.getElementById('form-'+type);
  if (target) target.classList.remove('hidden');
  if (el) el.classList.add('active');
  history.replaceState(null,'','/post/create?type='+type);
}

// ── Business-post: category change → load subs + fields ──────────
function bpOnCategoryChange() {
  loadSubCats('bp-subcategory', document.getElementById('bp-category').value);
  bpLoadFields();
}

// Load custom fields for the chosen sub-category (or category)
function bpLoadFields() {
  var sub = document.getElementById('bp-subcategory').value;
  var cat = document.getElementById('bp-category').value;
  var id  = sub || cat;
  var section = document.getElementById('bp-custom-section');
  var wrap    = document.getElementById('bp-custom-fields');
  if (!id) { section.classList.add('hidden'); wrap.innerHTML = ''; return; }

  fetch('/categories/' + id + '/fields')
    .then(r => r.json())
    .then(fields => {
      if (!fields.length) { section.classList.add('hidden'); wrap.innerHTML = ''; return; }
      wrap.innerHTML = fields.map(bpFieldHtml).join('');
      section.classList.remove('hidden');
    })
    .catch(() => { section.classList.add('hidden'); wrap.innerHTML = ''; });
}

function bpFieldHtml(f) {
  var req = f.required ? ' <span>*</span>' : '';
  var reqAttr = f.required ? ' required' : '';
  var name = 'cf[' + f.key + ']';
  var inner = '';
  if (f.type === 'textarea') {
    inner = '<textarea class="form-input" name="' + name + '" placeholder="' + (f.placeholder||'') + '"' + reqAttr + '></textarea>';
  } else if (f.type === 'number') {
    inner = '<input type="number" class="form-input" name="' + name + '" placeholder="' + (f.placeholder||'') + '"' + reqAttr + '>';
  } else if (f.type === 'select') {
    var opts = '<option value="">Select…</option>' + (f.options||[]).map(function(o){ return '<option value="'+o+'">'+o+'</option>'; }).join('');
    inner = '<select class="form-input" name="' + name + '"' + reqAttr + '>' + opts + '</select>';
  } else if (f.type === 'checkbox') {
    inner = '<label style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--text);cursor:pointer"><input type="checkbox" value="1" name="' + name + '" style="width:18px;height:18px">Yes</label>';
    return '<div class="form-group" style="margin-bottom:14px"><label class="form-label">' + f.label + req + '</label>' + inner + '</div>';
  } else {
    inner = '<input type="text" class="form-input" name="' + name + '" placeholder="' + (f.placeholder||'') + '"' + reqAttr + '>';
  }
  return '<div class="form-group" style="margin-bottom:14px"><label class="form-label">' + f.label + req + '</label>' + inner + '</div>';
}

// ── Classified: category change → load custom fields ─────────────
function clLoadFields(catId) {
  var section = document.getElementById('cl-custom-section');
  var wrap    = document.getElementById('cl-custom-fields');
  if (!catId) { section.classList.add('hidden'); wrap.innerHTML = ''; return; }

  fetch('/categories/' + catId + '/fields')
    .then(r => r.json())
    .then(fields => {
      if (!fields.length) { section.classList.add('hidden'); wrap.innerHTML = ''; return; }
      wrap.innerHTML = fields.map(bpFieldHtml).join('');
      section.classList.remove('hidden');
    })
    .catch(() => { section.classList.add('hidden'); wrap.innerHTML = ''; });
}

// Init on page load if old() category selected
(function() {
  var sel = document.querySelector('#form-classified select[name="category_id"]');
  if (sel && sel.value) clLoadFields(sel.value);
})();

// ── Sub-category cascade (parent → children) ──────────────────────
function loadSubCats(selectId, parentId) {
  var sel = document.getElementById(selectId);
  if (!sel) return;
  sel.innerHTML = '<option value="">Loading…</option>';
  if (!parentId) { sel.innerHTML = '<option value="">Select sub-category (optional)</option>'; return; }
  fetch('{{ route("categories.subs") }}?parent=' + encodeURIComponent(parentId))
    .then(r => r.json())
    .then(subs => {
      sel.innerHTML = '<option value="">Select sub-category (optional)</option>';
      subs.forEach(function(c) {
        var o = document.createElement('option');
        o.value = c.id;
        o.textContent = (c.icon ? c.icon + ' ' : '') + c.name;
        sel.appendChild(o);
      });
    })
    .catch(() => { sel.innerHTML = '<option value="">Select sub-category (optional)</option>'; });
}

// ── Quill rich-text editors ───────────────────────────────────────
var _qlToolbar = [
  ['bold','italic','underline'],
  [{'list':'ordered'},{'list':'bullet'}],
  ['clean']
];
// Toolbar WITH image support (business description)
var _qlToolbarImg = [
  ['bold','italic','underline'],
  [{'list':'ordered'},{'list':'bullet'}],
  ['link','image'],
  ['clean']
];

// Upload selected image to server, insert its URL into the editor
function _qlImageHandler(quill) {
  return function() {
    var input = document.createElement('input');
    input.setAttribute('type', 'file');
    input.setAttribute('accept', 'image/*');
    input.click();
    input.onchange = function() {
      var file = input.files[0];
      if (!file) return;
      var fd = new FormData();
      fd.append('image', file);
      var range = quill.getSelection(true);
      quill.insertText(range.index, 'Uploading image…', { italic: true });
      fetch('{{ route("post.editor-image") }}', {
        method: 'POST',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Requested-With': 'XMLHttpRequest' },
        body: fd,
      })
      .then(r => r.json())
      .then(data => {
        quill.deleteText(range.index, 'Uploading image…'.length);
        if (data.url) {
          quill.insertEmbed(range.index, 'image', data.url);
          quill.setSelection(range.index + 1);
        }
      })
      .catch(() => {
        quill.deleteText(range.index, 'Uploading image…'.length);
        alert('Image upload failed. Please try again.');
      });
    };
  };
}

function _qlInit(editorId, textareaId, withImage) {
  _qlInitReturn(editorId, textareaId, withImage);
}

function _qlInitReturn(editorId, textareaId, withImage) {
  var ta  = document.getElementById(textareaId);
  var el  = document.getElementById(editorId);
  if (!ta || !el) return null;
  var modules = { toolbar: withImage ? _qlToolbarImg : _qlToolbar };
  var q = new Quill(el, { theme:'snow', modules: modules });
  if (withImage) {
    q.getModule('toolbar').addHandler('image', _qlImageHandler(q));
  }
  if (ta.value) q.clipboard.dangerouslyPasteHTML(ta.value);
  el.closest('form').addEventListener('submit', function() {
    ta.value = q.root.innerHTML;
  });
  return q;
}

_qlInit('cl-description-editor',   'cl-description');
_qlInit('job-description-editor',  'job-description');
_qlInit('job-requirements-editor', 'job-requirements');
_qlInit('ev-description-editor',   'ev-description');
_bizQuill = _qlInitReturn('biz-description-editor', 'biz-description', true);
_qlInit('bp-description-editor',   'bp-description', true);

// ── Business form submit — builds FormData manually so images are included ──
function submitBizForm() {
  var form = document.getElementById('biz-form');
  if (!form) return;

  // Validate required Quill fields — sync to hidden textareas first
  // (already handled by Quill submit listener, but we need to trigger it manually)
  form.querySelectorAll('textarea[style*="display:none"]').forEach(function(ta) {
    // already synced by Quill's submit listener on the form — skip
  });

  // Trigger Quill sync by dispatching a 'submit' event on form
  // BUT we need the raw FormData AFTER sync, so we manually sync the biz description
  if (_bizQuill) {
    document.getElementById('biz-description').value = _bizQuill.root.innerHTML;
  }

  // Check for oversize images
  function _getParentForm(el) {
    while (el) { if (el.tagName === 'FORM') return el; el = el.parentElement; }
    return null;
  }
  var hasError = false;
  Object.keys(window._iuReg || {}).forEach(function(uid) {
    var inp = document.getElementById(uid + '_input');
    if (!inp || _getParentForm(inp) !== form) return;
    var oversize = window._iuReg[uid].files.filter(function(f){ return !f.valid; });
    if (oversize.length) hasError = true;
  });
  if (hasError) {
    alert('Please remove oversized images (over 1 MB) before submitting.');
    return;
  }

  var btn = document.getElementById('biz-submit-btn');
  btn.disabled = true;
  btn.textContent = '⏳ Submitting…';

  // Inject files from custom image uploaders into hidden <input type="file"> elements
  // so the native form submit carries them to the server correctly.
  var injected = [];
  Object.keys(window._iuReg || {}).forEach(function(uid) {
    var inp = document.getElementById(uid + '_input');
    if (!inp || _getParentForm(inp) !== form) return;
    var cfg = window._iuReg[uid];
    var validFiles = cfg.files.filter(function(e){ return e.valid; });
    if (!validFiles.length) return;

    var dt = new DataTransfer();
    validFiles.forEach(function(e){ dt.items.add(e.file); });
    inp.files = dt.files;
    injected.push(inp);
  });

  form.submit();
}

// ── AI Business Content Generator ───────────────────────────────
var _aiGenResult = null;
var _bizQuill    = null; // filled in when Quill inits

function runAIGenerate() {
  var name     = (document.getElementById('biz-name')?.value || '').trim();
  var catSel   = document.getElementById('biz-category');
  var catText  = catSel?.options[catSel.selectedIndex]?.text?.replace(/^[^\w]+/, '').trim() || '';
  var keywords = (document.getElementById('ai-keywords')?.value || '').trim();
  var lang     = document.getElementById('ai-lang')?.value || 'en';

  if (!name) { alert('Please enter your Business Name first (Step 1).'); return; }
  if (!keywords) { alert('Please describe your business briefly in the text area above.'); return; }

  var btn     = document.getElementById('ai-gen-btn');
  var icon    = document.getElementById('ai-btn-icon');
  var txt     = document.getElementById('ai-btn-text');
  var status  = document.getElementById('ai-status');
  var preview = document.getElementById('ai-preview');

  btn.disabled = true;
  icon.textContent = '⏳';
  txt.textContent  = 'Generating…';
  status.textContent = 'Asking AI — usually takes 5–10 seconds…';
  preview.style.display = 'none';

  var csrf = document.querySelector('meta[name="csrf-token"]')?.content || '';

  fetch('{{ route("business.generate-content") }}', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf },
    body: JSON.stringify({ business_name: name, category: catText, keywords: keywords, language: lang })
  })
  .then(function(r) { return r.json().then(function(d) { return { ok: r.ok, data: d }; }); })
  .then(function(res) {
    btn.disabled = false;
    icon.textContent = '✨';
    txt.textContent  = 'Generate Content';

    if (!res.ok || res.data.error) {
      status.textContent = '⚠️ ' + (res.data.error || 'Generation failed.');
      return;
    }

    _aiGenResult = res.data;
    status.textContent = '✅ Done! Review below.';

    // Show description preview
    document.getElementById('ai-desc-preview').innerHTML = res.data.description || '';
    document.getElementById('ai-tagline-preview').textContent = res.data.tagline || '';

    // Show tag pills in preview
    var tagWrap = document.getElementById('ai-tags-preview');
    tagWrap.innerHTML = '';
    (res.data.tags || []).forEach(function(tag) {
      var span = document.createElement('span');
      span.style.cssText = 'background:#f0f4ff;color:var(--primary);border:1px solid #c7d4f0;border-radius:20px;padding:3px 10px;font-size:12px;font-weight:600;cursor:pointer';
      span.title = 'Click to toggle';
      span.textContent = tag;
      span.dataset.selected = '1';
      span.addEventListener('click', function() {
        var sel = span.dataset.selected === '1';
        span.dataset.selected = sel ? '0' : '1';
        span.style.opacity = sel ? '0.4' : '1';
      });
      tagWrap.appendChild(span);
    });

    preview.style.display = 'block';
    preview.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
  })
  .catch(function(err) {
    btn.disabled = false;
    icon.textContent = '✨';
    txt.textContent  = 'Generate Content';
    status.textContent = '⚠️ Network error. Please try again.';
  });
}

function applyAIContent() {
  if (!_aiGenResult) return;

  // Apply description to Quill editor
  if (_bizQuill) {
    _bizQuill.clipboard.dangerouslyPasteHTML(_aiGenResult.description || '');
    document.getElementById('biz-description').value = _aiGenResult.description || '';
  }

  // Apply selected tags
  var tagSpans = document.querySelectorAll('#ai-tags-preview span');
  var selected = [];
  tagSpans.forEach(function(s) {
    if (s.dataset.selected !== '0') selected.push(s.textContent.trim());
  });

  var tagInput = document.getElementById('biz-tags-input');
  if (tagInput) {
    var existing = tagInput.value.split(',').map(function(t){ return t.trim(); }).filter(Boolean);
    var merged   = [...new Set([...existing, ...selected])];
    tagInput.value = merged.join(', ');
    // Trigger pill render
    tagInput.dispatchEvent(new Event('blur'));
  }

  document.getElementById('ai-preview').style.display = 'none';
  document.getElementById('ai-status').textContent = '✅ Content applied to form!';

  // Scroll to description
  document.getElementById('biz-description-editor').scrollIntoView({ behavior: 'smooth', block: 'center' });
}

// ── Business tag pills ──────────────────────────────────────────
(function() {
  var input  = document.getElementById('biz-tags-input');
  var hidden = document.getElementById('biz-tags-hidden');
  var pills  = document.getElementById('biz-tag-pills');
  if (!input) return;

  function renderPills(tags) {
    pills.innerHTML = '';
    tags.forEach(function(tag, i) {
      if (!tag) return;
      var pill = document.createElement('span');
      pill.style.cssText = 'display:inline-flex;align-items:center;gap:5px;background:var(--primary-light);color:var(--primary);border:1px solid #c7d4f0;border-radius:20px;padding:3px 10px;font-size:12px;font-weight:600';
      pill.innerHTML = tag + ' <button type="button" onclick="removeBizTag('+i+')" style="background:none;border:none;cursor:pointer;font-size:14px;color:var(--primary);line-height:1;padding:0">×</button>';
      pills.appendChild(pill);
    });
    hidden.value = tags.filter(Boolean).join(',');
  }

  window.removeBizTag = function(i) {
    var tags = hidden.value.split(',').filter(Boolean);
    tags.splice(i, 1);
    input.value = tags.join(', ');
    renderPills(tags);
  };

  function syncTags() {
    var tags = input.value.split(',').map(function(t){ return t.trim(); }).filter(Boolean);
    renderPills(tags);
  }

  input.addEventListener('blur', syncTags);
  input.addEventListener('keydown', function(e) {
    if (e.key === 'Enter' || e.key === ',') { e.preventDefault(); syncTags(); }
  });

  // Init from old() value
  if (input.value) syncTags();

  // Sync on business hours: auto-uncheck open/close when "Closed" is checked
  document.querySelectorAll('#biz-form input[type=checkbox]').forEach(function(cb) {
    cb.addEventListener('change', function() {
      var row = cb.closest('div');
      var inputs = row.querySelectorAll('input[type=time]');
      inputs.forEach(function(t) { t.disabled = cb.checked; if(cb.checked) t.value=''; });
    });
  });
})();
</script>
@endpush
@endsection
