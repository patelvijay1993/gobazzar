@extends('layouts.app')

@section('title', 'Post Something — GoBazzar')

@push('styles')
<style>
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
    <div class="type-tab {{ $type==='business' ? 'active' : '' }}" onclick="switchType('business',this)">
      <div class="tab-icon">🏢</div>
      <div class="tab-label">Directory</div>
    </div>
  </div>

  @if($errors->any())
  <div class="flash flash-error" style="margin-bottom:20px">
    <strong>Please fix the following errors:</strong>
    <ul style="margin:6px 0 0 18px">
      @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
    </ul>
  </div>
  @endif

  @php $activePlan = Auth::user()->activePlan(); $postDays = Auth::user()->postDays(); @endphp
  <div style="background:var(--surface);border:1px solid var(--border);border-radius:var(--r);padding:14px 18px;margin-bottom:20px;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap">
    <div style="font-size:13px">
      <span style="color:var(--muted)">Your plan:</span>
      <strong style="color:var(--text);margin:0 6px;text-transform:capitalize">{{ $activePlan }}</strong>
      <span style="color:var(--muted)">·</span>
      <span style="color:var(--muted);margin-left:6px">Posts stay live for
        <strong style="color:var(--text)">{{ $postDays ? $postDays.' days' : 'permanently' }}</strong>
      </span>
    </div>
    @if($activePlan === 'free')
      <a href="{{ route('pricing') }}" style="font-size:12px;font-weight:600;color:var(--red);white-space:nowrap">⬆ Upgrade for longer visibility →</a>
    @endif
  </div>

  {{-- ── CLASSIFIED ─────────────────────────────────────────────── --}}
  <div id="form-classified" class="{{ $type!=='classified' ? 'hidden' : '' }}">
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
                <select name="category_id" class="form-input" required>
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
                <input type="text" name="price" class="form-input" value="{{ old('price') }}" placeholder="$500">
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
            <x-image-uploader name="images" :multiple="true" :max="5" label="Photos (up to 5)" />
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
    <form method="POST" action="{{ route('post.business') }}" enctype="multipart/form-data">
      @csrf
      <div class="form-card">
        <div class="form-card-head">🏢 List Your Business</div>
        <div class="form-card-body">
          <div class="form-section">
            <div class="form-section-title">Business Details</div>
            <div class="form-group" style="margin-bottom:14px">
              <label class="form-label">Business Name <span>*</span></label>
              <input type="text" name="name" class="form-input" value="{{ old('name') }}" required placeholder="e.g. Spice Garden Restaurant">
            </div>
            <div class="form-row" style="margin-bottom:14px">
              <div class="form-group">
                <label class="form-label">Category</label>
                <select name="category_id" class="form-input">
                  <option value="">Select category</option>
                  @foreach($categories->get('directory', collect()) as $cat)
                    <option value="{{ $cat->id }}" {{ old('category_id')==$cat->id ? 'selected' : '' }}>{{ $cat->icon }} {{ $cat->name }}</option>
                  @endforeach
                </select>
              </div>
              <div class="form-group">
                <label class="form-label">Phone</label>
                <input type="text" name="phone" class="form-input" value="{{ old('phone', Auth::user()->phone) }}">
              </div>
            </div>
            <div class="form-group" style="margin-bottom:14px">
              <label class="form-label">Description</label>
              <textarea name="description" id="biz-description" style="display:none">{{ old('description') }}</textarea>
              <div id="biz-description-editor" class="ql-editor-wrap"></div>
            </div>
          </div>

          <div class="form-section">
            <div class="form-section-title">Location & Contact</div>
            <div class="form-group" style="margin-bottom:14px">
              <label class="form-label">Street Address</label>
              <input type="text" name="address" class="form-input" value="{{ old('address') }}" placeholder="123 Main St">
            </div>
            <div class="form-row" style="margin-bottom:14px">
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
            <div class="form-row">
              <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-input" value="{{ old('email', Auth::user()->email) }}">
              </div>
              <div class="form-group">
                <label class="form-label">Website</label>
                <input type="url" name="website" class="form-input" value="{{ old('website') }}" placeholder="https://…">
              </div>
            </div>
          </div>

          <div class="form-section">
            <div class="form-section-title">Photos</div>
            <div style="margin-bottom:16px">
              <x-image-uploader name="images" :multiple="true" :max="5" label="Business Photos (up to 5)" hint="First photo will be the main banner" />
            </div>
            <x-image-uploader name="logo" :multiple="false" :max="1" label="Logo" hint="Square image preferred (e.g. 200×200)" />
          </div>

          <button type="submit" class="btn-submit">Submit Business →</button>
        </div>
      </div>
    </form>
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
  ['classified','job','event','business'].forEach(t => {
    document.getElementById('form-'+t).classList.add('hidden');
  });
  document.querySelectorAll('.type-tab').forEach(t => t.classList.remove('active'));
  document.getElementById('form-'+type).classList.remove('hidden');
  el.classList.add('active');
  history.replaceState(null,'','/post/create?type='+type);
}

// ── Quill rich-text editors ───────────────────────────────────────
var _qlToolbar = [
  ['bold','italic','underline'],
  [{'list':'ordered'},{'list':'bullet'}],
  ['clean']
];

function _qlInit(editorId, textareaId) {
  var ta  = document.getElementById(textareaId);
  var el  = document.getElementById(editorId);
  if (!ta || !el) return;
  var q = new Quill(el, { theme:'snow', modules:{ toolbar: _qlToolbar } });
  if (ta.value) q.clipboard.dangerouslyPasteHTML(ta.value);
  // sync to hidden textarea before form submit
  el.closest('form').addEventListener('submit', function() {
    ta.value = q.root.innerHTML;
  });
}

_qlInit('cl-description-editor',   'cl-description');
_qlInit('job-description-editor',  'job-description');
_qlInit('job-requirements-editor', 'job-requirements');
_qlInit('ev-description-editor',   'ev-description');
_qlInit('biz-description-editor',  'biz-description');
</script>
@endpush
@endsection
