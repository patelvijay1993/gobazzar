@extends('layouts.app')
@section('title', 'Edit Post — GoBazaar')

@push('styles')
<style>
/* Map legacy theme vars to current blue theme */
.edit-wrap{
  --red:var(--primary);--red-dark:var(--primary-dark);--red-pale:var(--primary-light);
  --border2:var(--border);--surface:#fff;--bg:#f9fafb;--hint:#9ca3af;
  --rl:14px;--r:8px;--amber:#92400e;--amber-bg:#fef9c3;--dark:#1a3a8f;
}
.edit-wrap{max-width:860px;margin:32px auto;padding:0 20px}
.edit-hero{margin-bottom:24px}
.edit-hero h1{font-family:var(--fh);font-size:22px;font-weight:800}
.edit-hero p{color:var(--muted);font-size:13px;margin-top:4px}
.form-card{background:var(--surface);border:1.5px solid var(--border);border-radius:var(--rl);overflow:hidden}
.form-card-head{background:var(--dark);color:#fff;padding:14px 24px;font-family:var(--fh);font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:.8px}
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
.btn-save{background:var(--red);color:#fff;border:none;border-radius:var(--r);padding:12px 32px;font-size:14px;font-weight:700;cursor:pointer;transition:background .15s}
.btn-save:hover{background:var(--red-dark)}
.btn-cancel{background:transparent;border:1.5px solid var(--border2);color:var(--muted);border-radius:var(--r);padding:12px 24px;font-size:13px;cursor:pointer;margin-left:10px}
.status-note{background:var(--amber-bg);border:1px solid #fde68a;border-radius:var(--r);padding:10px 14px;font-size:12.5px;color:var(--amber);margin-bottom:20px}

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
<div class="edit-wrap">
  <div class="breadcrumb" style="margin-bottom:16px">
    <a href="{{ route('home') }}">Home</a><span>›</span>
    <a href="{{ route('account') }}">My Account</a><span>›</span>
    Edit Post
  </div>

  <div class="edit-hero">
    <h1>Edit {{ ['classified'=>'Classified Ad','business'=>'Business Listing','business-post'=>'Business Post','job'=>'Job','event'=>'Event','matrimonial'=>'Matrimonial'][$type] ?? ucfirst($type) }}</h1>
    <p>Changes will be reviewed if the post is currently active.</p>
  </div>

  @if($errors->any())
  <div class="flash flash-error" style="margin-bottom:20px">
    @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
  </div>
  @endif

  <div class="status-note">
    ⚠️ Current status: <strong>{{ ucfirst($record->status) }}</strong>.
    Editing will keep your post pending re-review if it was active.
  </div>

  {{-- ── CLASSIFIED ─────────────────────────────────────────────── --}}
  @if($type === 'classified')
  <form method="POST" action="{{ route('post.update', ['type'=>'classified','id'=>$record->id]) }}" enctype="multipart/form-data">
    @csrf
    <div class="form-card">
      <div class="form-card-head">🏷️ Edit Classified Ad</div>
      <div class="form-card-body">
        <div class="form-section">
          <div class="form-section-title">Basic Info</div>
          <div class="form-group" style="margin-bottom:14px">
            <label class="form-label">Title <span>*</span></label>
            <input type="text" name="title" class="form-input" value="{{ old('title',$record->title) }}" required>
          </div>
          <div class="form-row" style="margin-bottom:14px">
            <div class="form-group">
              <label class="form-label">Category <span>*</span></label>
              <select name="category_id" class="form-input" required>
                @foreach($categories->get('classifieds', collect()) as $cat)
                  <option value="{{ $cat->id }}" {{ $record->category_id==$cat->id ? 'selected' : '' }}>{{ $cat->icon }} {{ $cat->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">Location</label>
              <input type="text" name="location" class="form-input" value="{{ old('location',$record->location) }}">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label class="form-label">Province <span>*</span></label>
              <select name="province" id="ecl-province" class="form-input" required onchange="loadCities('ecl-city',this.value)">
                <option value="">Select province</option>
                @foreach($provinces as $prov)
                  <option value="{{ $prov }}" {{ old('province',$record->province)===$prov ? 'selected' : '' }}>{{ $prov }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">City <span>*</span></label>
              <select name="city" id="ecl-city" class="form-input" required>
                <option value="">Select city</option>
                @foreach($cities as $city)
                  <option value="{{ $city }}" {{ old('city',$record->city)===$city ? 'selected' : '' }}>{{ $city }}</option>
                @endforeach
              </select>
            </div>
          </div>
        </div>
        <div class="form-section">
          <div class="form-section-title">Description & Price</div>
          <div class="form-group" style="margin-bottom:14px">
            <label class="form-label">Description</label>
            <textarea name="description" id="ecl-description" style="display:none">{{ old('description',$record->description) }}</textarea>
            <div id="ecl-description-editor" class="ql-editor-wrap"></div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label class="form-label">Price</label>
              <input type="text" name="price" class="form-input" value="{{ old('price',$record->price) }}">
            </div>
            <div class="form-group">
              <label class="form-label">Price Unit</label>
              <select name="price_unit" class="form-input">
                @foreach(['' => 'One-time', '/mo' => '/month', '/wk' => '/week', '/hr' => '/hour', '/yr' => '/year'] as $v => $l)
                  <option value="{{ $v }}" {{ $record->price_unit==$v ? 'selected' : '' }}>{{ $l }}</option>
                @endforeach
              </select>
            </div>
          </div>
        </div>
        <div class="form-section">
          <div class="form-section-title">Contact & Photos</div>
          <div class="form-row" style="margin-bottom:14px">
            <div class="form-group">
              <label class="form-label">Contact Name</label>
              <input type="text" name="contact_name" class="form-input" value="{{ old('contact_name',$record->contact_name) }}">
            </div>
            <div class="form-group">
              <label class="form-label">Contact Phone</label>
              <input type="text" name="contact_phone" class="form-input" value="{{ old('contact_phone',$record->contact_phone) }}">
            </div>
          </div>
          <div class="form-group" style="margin-bottom:14px">
            <label class="form-label">Contact Email</label>
            <input type="email" name="contact_email" class="form-input" value="{{ old('contact_email',$record->contact_email) }}">
          </div>
          @php $existingImages = $record->images ?? ($record->image ? [$record->image] : []); @endphp
          @if(count($existingImages))
            <div style="margin-bottom:10px">
              <div class="form-label" style="margin-bottom:6px">Current Photos</div>
              <div style="display:flex;flex-wrap:wrap;gap:8px">
                @foreach($existingImages as $img)
                  <img src="{{ str_starts_with($img,'http') ? $img : (\Illuminate\Support\Facades\Storage::disk('public')->exists($img) ? \Illuminate\Support\Facades\Storage::disk('public')->url($img) : \Illuminate\Support\Facades\Storage::disk('s3')->url($img)) }}" style="width:80px;height:64px;object-fit:cover;border-radius:6px;border:1.5px solid var(--border)" onerror="this.src='https://placehold.co/80x64?text=Photo'">
                @endforeach
              </div>
              <div class="form-hint">Upload new photos below to replace all existing photos.</div>
            </div>
          @endif
          <x-image-uploader name="images" :multiple="true" :max="$maxImages" :label="'Photos (up to '.$maxImages.')'" hint="JPG, PNG, WEBP · Max 1 MB each · Leave empty to keep current photos" />
        </div>
        <button type="submit" class="btn-save">Save Changes</button>
        <a href="{{ route('account') }}" class="btn-cancel">Cancel</a>
      </div>
    </div>
  </form>
  @endif

  {{-- ── JOB ─────────────────────────────────────────────────────── --}}
  @if($type === 'job')
  <form method="POST" action="{{ route('post.update', ['type'=>'job','id'=>$record->id]) }}" enctype="multipart/form-data">
    @csrf
    <div class="form-card">
      <div class="form-card-head">💼 Edit Job</div>
      <div class="form-card-body">
        <div class="form-section">
          <div class="form-section-title">Job Details</div>
          <div class="form-group" style="margin-bottom:14px">
            <label class="form-label">Job Title <span>*</span></label>
            <input type="text" name="title" class="form-input" value="{{ old('title',$record->title) }}" required>
          </div>
          <div class="form-row" style="margin-bottom:14px">
            <div class="form-group">
              <label class="form-label">Company <span>*</span></label>
              <input type="text" name="company" class="form-input" value="{{ old('company',$record->company) }}" required>
            </div>
            <div class="form-group">
              <label class="form-label">Category</label>
              <select name="category_id" class="form-input">
                <option value="">Select</option>
                @foreach($categories->get('jobs', collect()) as $cat)
                  <option value="{{ $cat->id }}" {{ $record->category_id==$cat->id ? 'selected' : '' }}>{{ $cat->icon }} {{ $cat->name }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-row-3" style="margin-bottom:14px">
            <div class="form-group">
              <label class="form-label">Job Type <span>*</span></label>
              <select name="job_type" class="form-input" required>
                @foreach(['full-time'=>'Full Time','part-time'=>'Part Time','contract'=>'Contract','freelance'=>'Freelance','internship'=>'Internship'] as $v=>$l)
                  <option value="{{ $v }}" {{ $record->job_type===$v ? 'selected' : '' }}>{{ $l }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">Work Mode <span>*</span></label>
              <select name="work_mode" class="form-input" required>
                @foreach(['onsite'=>'On-site','remote'=>'Remote','hybrid'=>'Hybrid'] as $v=>$l)
                  <option value="{{ $v }}" {{ $record->work_mode===$v ? 'selected' : '' }}>{{ $l }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">Salary</label>
              <input type="text" name="salary" class="form-input" value="{{ old('salary',$record->salary) }}">
            </div>
          </div>
          <div class="form-row" style="margin-bottom:14px">
            <div class="form-group">
              <label class="form-label">Province <span>*</span></label>
              <select name="province" id="ejob-province" class="form-input" required onchange="loadCities('ejob-city',this.value)">
                <option value="">Select province</option>
                @foreach($provinces as $prov)
                  <option value="{{ $prov }}" {{ old('province',$record->province)===$prov ? 'selected' : '' }}>{{ $prov }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">City <span>*</span></label>
              <select name="city" id="ejob-city" class="form-input" required>
                <option value="">Select city</option>
                @foreach($cities as $city)
                  <option value="{{ $city }}" {{ old('city',$record->city)===$city ? 'selected' : '' }}>{{ $city }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label class="form-label">Experience</label>
              <input type="text" name="experience" class="form-input" value="{{ old('experience',$record->experience) }}">
            </div>
          </div>
        </div>
        <div class="form-section">
          <div class="form-section-title">Description</div>
          <div class="form-group" style="margin-bottom:14px">
            <label class="form-label">Job Description</label>
            <textarea name="description" id="ejob-description" style="display:none">{{ old('description',$record->description) }}</textarea>
            <div id="ejob-description-editor" class="ql-editor-wrap"></div>
          </div>
          <div class="form-group">
            <label class="form-label">Requirements</label>
            <textarea name="requirements" id="ejob-requirements" style="display:none">{{ old('requirements',$record->requirements) }}</textarea>
            <div id="ejob-requirements-editor" class="ql-editor-wrap"></div>
          </div>
        </div>
        <div class="form-section">
          <div class="form-section-title">Apply & Logo</div>
          <div class="form-row" style="margin-bottom:14px">
            <div class="form-group">
              <label class="form-label">Apply Email</label>
              <input type="email" name="apply_email" class="form-input" value="{{ old('apply_email',$record->apply_email) }}">
            </div>
            <div class="form-group">
              <label class="form-label">Apply URL</label>
              <input type="url" name="apply_url" class="form-input" value="{{ old('apply_url',$record->apply_url) }}">
            </div>
          </div>
          @if($record->company_logo && strlen($record->company_logo) > 4)
            <div style="margin-bottom:10px">
              <div class="form-label" style="margin-bottom:6px">Current Logo</div>
              @php $cl = $record->company_logo; @endphp
              <img src="{{ str_starts_with($cl,'http') ? $cl : (\Illuminate\Support\Facades\Storage::disk('public')->exists($cl) ? \Illuminate\Support\Facades\Storage::disk('public')->url($cl) : \Illuminate\Support\Facades\Storage::disk('s3')->url($cl)) }}" style="width:80px;height:64px;object-fit:cover;border-radius:6px;border:1.5px solid var(--border)" onerror="this.src='https://placehold.co/80x64?text=Logo'">
              <div class="form-hint">Upload a new logo below to replace it.</div>
            </div>
          @endif
          <x-image-uploader name="company_logo" :multiple="false" :max="1" label="Company Logo" hint="JPG, PNG, WEBP · Max 1 MB · Leave empty to keep current" />
        </div>
        <button type="submit" class="btn-save">Save Changes</button>
        <a href="{{ route('account') }}" class="btn-cancel">Cancel</a>
      </div>
    </div>
  </form>
  @endif

  {{-- ── EVENT ───────────────────────────────────────────────────── --}}
  @if($type === 'event')
  <form method="POST" action="{{ route('post.update', ['type'=>'event','id'=>$record->id]) }}" enctype="multipart/form-data">
    @csrf
    <div class="form-card">
      <div class="form-card-head">🎉 Edit Event</div>
      <div class="form-card-body">
        <div class="form-section">
          <div class="form-section-title">Event Details</div>
          <div class="form-group" style="margin-bottom:14px">
            <label class="form-label">Event Title <span>*</span></label>
            <input type="text" name="title" class="form-input" value="{{ old('title',$record->title) }}" required>
          </div>
          <div class="form-row" style="margin-bottom:14px">
            <div class="form-group">
              <label class="form-label">Start Date <span>*</span></label>
              <input type="datetime-local" name="start_date" class="form-input" value="{{ old('start_date', $record->start_date?->format('Y-m-d\TH:i')) }}" required>
            </div>
            <div class="form-group">
              <label class="form-label">End Date</label>
              <input type="datetime-local" name="end_date" class="form-input" value="{{ old('end_date', $record->end_date?->format('Y-m-d\TH:i')) }}">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label class="form-label">Category</label>
              <select name="category_id" class="form-input">
                <option value="">Select</option>
                @foreach($categories->get('events', collect()) as $cat)
                  <option value="{{ $cat->id }}" {{ $record->category_id==$cat->id ? 'selected' : '' }}>{{ $cat->icon }} {{ $cat->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">Ticket Price</label>
              <input type="text" name="price" class="form-input" value="{{ old('price',$record->price) }}">
            </div>
          </div>
        </div>
        <div class="form-section">
          <div class="form-section-title">Location</div>
          <div class="form-group" style="margin-bottom:14px">
            <label class="form-label">Venue</label>
            <input type="text" name="venue" class="form-input" value="{{ old('venue',$record->venue) }}">
          </div>
          <div class="form-row">
            <div class="form-group">
              <label class="form-label">Province <span>*</span></label>
              <select name="province" id="eev-province" class="form-input" required onchange="loadCities('eev-city',this.value)">
                <option value="">Select province</option>
                @foreach($provinces as $prov)
                  <option value="{{ $prov }}" {{ old('province',$record->province)===$prov ? 'selected' : '' }}>{{ $prov }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">City <span>*</span></label>
              <select name="city" id="eev-city" class="form-input" required>
                <option value="">Select city</option>
                @foreach($cities as $city)
                  <option value="{{ $city }}" {{ old('city',$record->city)===$city ? 'selected' : '' }}>{{ $city }}</option>
                @endforeach
              </select>
            </div>
          </div>
        </div>
        <div class="form-section">
          <div class="form-section-title">Description & Contact</div>
          <div class="form-group" style="margin-bottom:14px">
            <label class="form-label">Description</label>
            <textarea name="description" id="eev-description" style="display:none">{{ old('description',$record->description) }}</textarea>
            <div id="eev-description-editor" class="ql-editor-wrap"></div>
          </div>
          <div class="form-row" style="margin-bottom:14px">
            <div class="form-group">
              <label class="form-label">Organizer</label>
              <input type="text" name="organizer" class="form-input" value="{{ old('organizer',$record->organizer) }}">
            </div>
            <div class="form-group">
              <label class="form-label">Organizer Phone</label>
              <input type="text" name="organizer_phone" class="form-input" value="{{ old('organizer_phone',$record->organizer_phone) }}">
            </div>
          </div>
          <div class="form-row" style="margin-bottom:14px">
            <div class="form-group">
              <label class="form-label">Organizer Email</label>
              <input type="email" name="organizer_email" class="form-input" value="{{ old('organizer_email',$record->organizer_email) }}">
            </div>
            <div class="form-group">
              <label class="form-label">Website</label>
              <input type="url" name="website" class="form-input" value="{{ old('website',$record->website) }}">
            </div>
          </div>
          @if($record->image)
            <div style="margin-bottom:10px">
              <div class="form-label" style="margin-bottom:6px">Current Photo</div>
              @php $ei = $record->image; @endphp
              <img src="{{ str_starts_with($ei,'http') ? $ei : (\Illuminate\Support\Facades\Storage::disk('public')->exists($ei) ? \Illuminate\Support\Facades\Storage::disk('public')->url($ei) : \Illuminate\Support\Facades\Storage::disk('s3')->url($ei)) }}" style="width:120px;height:80px;object-fit:cover;border-radius:6px;border:1.5px solid var(--border)" onerror="this.src='https://placehold.co/120x80?text=Photo'">
              <div class="form-hint">Upload a new photo below to replace it.</div>
            </div>
          @endif
          <x-image-uploader name="image" :multiple="false" :max="1" label="Event Banner / Photo" hint="JPG, PNG, WEBP · Max 1 MB · Leave empty to keep current" />
        </div>
        <button type="submit" class="btn-save">Save Changes</button>
        <a href="{{ route('account') }}" class="btn-cancel">Cancel</a>
      </div>
    </div>
  </form>
  @endif

  {{-- ── BUSINESS ─────────────────────────────────────────────────── --}}
  @if($type === 'business')
  @php
    $days      = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
    $oldHours  = old('hours', $record->hours ?? []);
    $oldSocial = old('social', $record->social ?? []);
    $oldTags   = old('tags_input', is_array($record->tags ?? null) ? implode(', ', $record->tags) : ($record->tags ?? ''));
    $existingBizImages = $record->images ?? ($record->image ? [$record->image] : []);
  @endphp
  <form method="POST" action="{{ route('post.update', ['type'=>'business','id'=>$record->id]) }}" enctype="multipart/form-data" id="ebiz-form">
    @csrf

    {{-- ── STEP 1: Business Identity ── --}}
    <div class="form-card" style="margin-bottom:16px">
      <div class="form-card-head">
        <span style="background:rgba(255,255,255,.2);border-radius:50%;width:26px;height:26px;display:inline-flex;align-items:center;justify-content:center;font-size:13px;font-weight:800;flex-shrink:0">1</span>
        Business Identity
      </div>
      <div class="form-card-body">
        <div class="form-row" style="margin-bottom:14px">
          <div class="form-group" style="grid-column:1/-1">
            <label class="form-label">Business Name <span>*</span></label>
            <input type="text" name="name" id="ebiz-name" class="form-input" value="{{ old('name',$record->name) }}" required>
          </div>
        </div>
        <div class="form-row" style="margin-bottom:14px">
          <div class="form-group">
            <label class="form-label">Category <span>*</span></label>
            <select name="category_id" id="ebiz-category" class="form-input" onchange="loadSubCats('ebiz-subcategory', this.value)" required>
              <option value="">Select category</option>
              @foreach($directoryParents ?? $categories->get('directory', collect()) as $cat)
                <option value="{{ $cat->id }}" {{ old('category_id',$record->category_id)==$cat->id ? 'selected' : '' }}>{{ $cat->icon }} {{ $cat->name }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">Sub-Category</label>
            <select name="subcategory_id" id="ebiz-subcategory" class="form-input">
              <option value="">Select sub-category (optional)</option>
              @if($record->subcategory_id)
                <option value="{{ $record->subcategory_id }}" selected>{{ $record->subcategory->name ?? 'Current sub-category' }}</option>
              @endif
            </select>
          </div>
        </div>

        {{-- ✨ AI Content Generator Panel --}}
        <div id="eai-gen-panel" style="background:linear-gradient(135deg,#f0f4ff,#faf5ff);border:1.5px solid #c7d4f0;border-radius:12px;padding:18px 20px;margin-bottom:18px">
          <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:14px;flex-wrap:wrap;gap:10px">
            <div style="display:flex;align-items:center;gap:10px">
              <div style="background:var(--primary);color:#fff;border-radius:8px;width:34px;height:34px;display:flex;align-items:center;justify-content:center;font-size:18px;flex-shrink:0">✨</div>
              <div>
                <div style="font-size:13px;font-weight:700;color:var(--primary)">AI Content Generator</div>
                <div style="font-size:11px;color:#6b7280">Regenerate description & tags with AI</div>
              </div>
            </div>
            <div style="display:flex;align-items:center;gap:8px">
              <label style="font-size:11.5px;font-weight:600;color:var(--muted);white-space:nowrap">Language:</label>
              <select id="eai-lang" style="border:1.5px solid #c7d4f0;border-radius:6px;padding:5px 10px;font-size:12px;background:#fff;color:var(--text)">
                <option value="en">English</option>
                <option value="gu">ગુજરાતી</option>
                <option value="hi">हिंदी</option>
              </select>
            </div>
          </div>
          <div style="margin-bottom:12px">
            <label style="display:block;font-size:11px;font-weight:700;color:var(--text);text-transform:uppercase;letter-spacing:.4px;margin-bottom:5px">Describe your business <span style="color:var(--primary)">*</span></label>
            <textarea id="eai-keywords" rows="2" style="width:100%;border:1.5px solid #c7d4f0;border-radius:8px;padding:10px 14px;font-size:13px;font-family:var(--fb);resize:vertical;background:#fff;color:var(--text)" placeholder="e.g. We serve authentic Gujarati food — thali, dhokla, snacks. Vegetarian only."></textarea>
          </div>
          <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
            <button type="button" id="eai-gen-btn" onclick="runEAIGenerate()" style="background:var(--primary);color:#fff;border:none;border-radius:8px;padding:10px 22px;font-size:13px;font-weight:700;cursor:pointer;display:flex;align-items:center;gap:8px">
              <span id="eai-btn-icon">✨</span><span id="eai-btn-text">Generate Content</span>
            </button>
            <div id="eai-status" style="font-size:12px;color:var(--muted)"></div>
          </div>
          <div id="eai-preview" style="display:none;margin-top:16px;border-top:1px solid #dde3f5;padding-top:16px">
            <div style="font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.4px;margin-bottom:10px">✨ Generated Content — Review and Apply</div>
            <div style="background:#fff;border:1.5px solid #dde3f5;border-radius:8px;padding:14px 16px;margin-bottom:10px;font-size:13px;line-height:1.7;color:var(--text)" id="eai-desc-preview"></div>
            <div style="margin-bottom:12px">
              <div style="font-size:11px;font-weight:700;color:var(--muted);margin-bottom:6px">SUGGESTED TAGLINE</div>
              <div id="eai-tagline-preview" style="font-size:13px;font-style:italic;color:var(--primary);background:#f0f4ff;padding:8px 12px;border-radius:6px"></div>
            </div>
            <div style="margin-bottom:14px">
              <div style="font-size:11px;font-weight:700;color:var(--muted);margin-bottom:6px">SUGGESTED TAGS</div>
              <div id="eai-tags-preview" style="display:flex;flex-wrap:wrap;gap:6px"></div>
            </div>
            <div style="display:flex;gap:8px;flex-wrap:wrap">
              <button type="button" onclick="applyEAIContent()" style="background:var(--primary);color:#fff;border:none;border-radius:7px;padding:9px 20px;font-size:13px;font-weight:700;cursor:pointer">✅ Apply to Form</button>
              <button type="button" onclick="runEAIGenerate()" style="background:#fff;color:var(--primary);border:1.5px solid var(--primary);border-radius:7px;padding:9px 20px;font-size:13px;font-weight:600;cursor:pointer">🔄 Regenerate</button>
              <button type="button" onclick="document.getElementById('eai-preview').style.display='none'" style="background:#fff;color:var(--muted);border:1.5px solid var(--border);border-radius:7px;padding:9px 16px;font-size:12px;cursor:pointer">Dismiss</button>
            </div>
          </div>
        </div>

        <div class="form-group" style="margin-bottom:14px">
          <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:5px">
            <label class="form-label" style="margin-bottom:0">About Your Business</label>
            <span style="font-size:11px;color:var(--muted)">Use AI above to regenerate ↑</span>
          </div>
          <textarea name="description" id="ebiz-description" style="display:none">{{ old('description',$record->description) }}</textarea>
          <div id="ebiz-description-editor" class="ql-editor-wrap"></div>
        </div>
        <div class="form-group">
          <label class="form-label">Tags / Keywords</label>
          <input type="text" name="tags_input" id="ebiz-tags-input" class="form-input" value="{{ $oldTags }}" placeholder="e.g. vegetarian, Indian food, catering">
          <input type="hidden" name="tags" id="ebiz-tags-hidden" value="{{ $oldTags }}">
          <div class="form-hint">Comma-separated keywords</div>
          <div id="ebiz-tag-pills" style="display:flex;flex-wrap:wrap;gap:6px;margin-top:8px"></div>
        </div>
      </div>
    </div>

    {{-- ── STEP 2: Location & Contact ── --}}
    <div class="form-card" style="margin-bottom:16px">
      <div class="form-card-head">
        <span style="background:rgba(255,255,255,.2);border-radius:50%;width:26px;height:26px;display:inline-flex;align-items:center;justify-content:center;font-size:13px;font-weight:800;flex-shrink:0">2</span>
        Location & Contact
      </div>
      <div class="form-card-body">
        <div class="form-group" style="margin-bottom:14px">
          <label class="form-label">Street Address</label>
          <input type="text" name="address" class="form-input" value="{{ old('address',$record->address) }}" placeholder="123 Main St, Unit 4">
        </div>
        <div class="form-row" style="margin-bottom:14px">
          <div class="form-group">
            <label class="form-label">Province <span>*</span></label>
            <select name="province" id="ebiz-province" class="form-input" required onchange="loadCities('ebiz-city',this.value)">
              <option value="">Select province</option>
              @foreach($provinces as $prov)
                <option value="{{ $prov }}" {{ old('province',$record->province)===$prov ? 'selected' : '' }}>{{ $prov }}</option>
              @endforeach
            </select>
          </div>
          <div class="form-group">
            <label class="form-label">City <span>*</span></label>
            <select name="city" id="ebiz-city" class="form-input" required>
              <option value="">Select city</option>
              @foreach($cities as $city)
                <option value="{{ $city }}" {{ old('city',$record->city)===$city ? 'selected' : '' }}>{{ $city }}</option>
              @endforeach
            </select>
          </div>
        </div>
        <div class="form-row" style="margin-bottom:14px">
          <div class="form-group">
            <label class="form-label">Phone Number</label>
            <input type="text" name="phone" class="form-input" value="{{ old('phone',$record->phone) }}" placeholder="+1 647 xxx xxxx">
          </div>
          <div class="form-group">
            <label class="form-label">Business Email</label>
            <input type="email" name="email" class="form-input" value="{{ old('email',$record->email) }}">
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label">Website</label>
            <input type="url" name="website" class="form-input" value="{{ old('website',$record->website) }}" placeholder="https://yourbusiness.com">
          </div>
          <div class="form-group">
            <label class="form-label">Google Maps Link</label>
            <input type="url" name="map_url" class="form-input" value="{{ old('map_url',$record->map_url) }}" placeholder="https://maps.google.com/…">
          </div>
        </div>
      </div>
    </div>

    {{-- ── STEP 3: Business Hours ── --}}
    <div class="form-card" style="margin-bottom:16px">
      <div class="form-card-head">
        <span style="background:rgba(255,255,255,.2);border-radius:50%;width:26px;height:26px;display:inline-flex;align-items:center;justify-content:center;font-size:13px;font-weight:800;flex-shrink:0">3</span>
        Business Hours
        <span style="margin-left:auto;font-size:11px;font-weight:400;opacity:.7">Optional</span>
      </div>
      <div class="form-card-body">
        <div style="display:grid;gap:10px">
          @foreach($days as $day)
          @php $dkey = strtolower($day); @endphp
          <div style="display:grid;grid-template-columns:110px 1fr 1fr 120px;gap:10px;align-items:center">
            <label style="font-size:13px;font-weight:600;color:var(--text)">{{ $day }}</label>
            <input type="time" name="hours[{{ $dkey }}][open]"  class="form-input" value="{{ $oldHours[$dkey]['open']  ?? '' }}" style="font-size:13px;padding:8px 10px">
            <input type="time" name="hours[{{ $dkey }}][close]" class="form-input" value="{{ $oldHours[$dkey]['close'] ?? '' }}" style="font-size:13px;padding:8px 10px">
            <label style="display:flex;align-items:center;gap:7px;font-size:12px;color:var(--muted);cursor:pointer">
              <input type="checkbox" name="hours[{{ $dkey }}][closed]" value="1" {{ !empty($oldHours[$dkey]['closed']) ? 'checked' : '' }} style="width:16px;height:16px"> Closed
            </label>
          </div>
          @endforeach
        </div>
      </div>
    </div>

    {{-- ── STEP 4: Social Media ── --}}
    <div class="form-card" style="margin-bottom:16px">
      <div class="form-card-head">
        <span style="background:rgba(255,255,255,.2);border-radius:50%;width:26px;height:26px;display:inline-flex;align-items:center;justify-content:center;font-size:13px;font-weight:800;flex-shrink:0">4</span>
        Social Media
        <span style="margin-left:auto;font-size:11px;font-weight:400;opacity:.7">Optional</span>
      </div>
      <div class="form-card-body">
        <div class="form-row" style="margin-bottom:14px">
          <div class="form-group">
            <label class="form-label"><i class="fa-brands fa-facebook" style="color:#1877f2;margin-right:5px"></i>Facebook Page</label>
            <input type="url" name="social[facebook]" class="form-input" value="{{ old('social.facebook', $oldSocial['facebook'] ?? '') }}" placeholder="https://facebook.com/yourbusiness">
          </div>
          <div class="form-group">
            <label class="form-label"><i class="fa-brands fa-instagram" style="color:#e1306c;margin-right:5px"></i>Instagram</label>
            <input type="url" name="social[instagram]" class="form-input" value="{{ old('social.instagram', $oldSocial['instagram'] ?? '') }}" placeholder="https://instagram.com/yourbusiness">
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label"><i class="fa-brands fa-whatsapp" style="color:#25d366;margin-right:5px"></i>WhatsApp Number</label>
            <input type="text" name="social[whatsapp]" class="form-input" value="{{ old('social.whatsapp', $oldSocial['whatsapp'] ?? '') }}" placeholder="+1 647 xxx xxxx">
          </div>
          <div class="form-group">
            <label class="form-label"><i class="fa-brands fa-youtube" style="color:#ff0000;margin-right:5px"></i>YouTube Channel</label>
            <input type="url" name="social[youtube]" class="form-input" value="{{ old('social.youtube', $oldSocial['youtube'] ?? '') }}" placeholder="https://youtube.com/@yourbusiness">
          </div>
        </div>
        <div class="form-row">
          <div class="form-group">
            <label class="form-label"><i class="fa-brands fa-x-twitter" style="margin-right:5px"></i>Twitter / X</label>
            <input type="url" name="social[twitter]" class="form-input" value="{{ old('social.twitter', $oldSocial['twitter'] ?? '') }}" placeholder="https://twitter.com/yourbusiness">
          </div>
          <div class="form-group">
            <label class="form-label"><i class="fa-brands fa-linkedin" style="color:#0077b5;margin-right:5px"></i>LinkedIn</label>
            <input type="url" name="social[linkedin]" class="form-input" value="{{ old('social.linkedin', $oldSocial['linkedin'] ?? '') }}" placeholder="https://linkedin.com/company/yourbusiness">
          </div>
        </div>
      </div>
    </div>

    {{-- ── STEP 5: Photos & Logo ── --}}
    <div class="form-card" style="margin-bottom:20px">
      <div class="form-card-head">
        <span style="background:rgba(255,255,255,.2);border-radius:50%;width:26px;height:26px;display:inline-flex;align-items:center;justify-content:center;font-size:13px;font-weight:800;flex-shrink:0">5</span>
        Photos & Logo
      </div>
      <div class="form-card-body">
        @if(count($existingBizImages))
          <div style="margin-bottom:16px">
            <div class="form-label" style="margin-bottom:6px">Current Photos</div>
            <div id="ebiz-current-photos" style="display:flex;flex-wrap:wrap;gap:10px;margin-bottom:8px">
              @foreach($existingBizImages as $i => $img)
                @php $imgUrl = str_starts_with($img,'http') ? $img : \Illuminate\Support\Facades\Storage::disk('public')->url($img); @endphp
                <div id="ebiz-photo-{{ $i }}" style="position:relative;width:90px;flex-shrink:0">
                  <img src="{{ $imgUrl }}" style="width:90px;height:70px;object-fit:cover;border-radius:6px;border:1.5px solid var(--border);display:block" onerror="this.src='https://placehold.co/90x70?text=Photo'">
                  <button type="button"
                    onclick="removeExistingPhoto({{ $i }},'{{ addslashes($img) }}')"
                    style="position:absolute;top:3px;right:3px;width:20px;height:20px;background:rgba(0,0,0,.65);color:#fff;border:none;border-radius:50%;font-size:11px;cursor:pointer;display:flex;align-items:center;justify-content:center;line-height:1">✕</button>
                </div>
              @endforeach
            </div>
            <div id="ebiz-remove-inputs"></div>
            <div class="form-hint">Click ✕ to remove a photo. Upload new photos below to add more.</div>
          </div>
        @endif
        <div style="margin-bottom:20px">
          <x-image-uploader name="images" :multiple="true" :max="$maxImages" :label="'Add / Replace Photos (up to '.$maxImages.')'" hint="First photo = main banner · Leave empty to keep current photos" />
        </div>
        @if($record->logo)
          <div style="margin-bottom:12px">
            <div class="form-label" style="margin-bottom:6px">Current Logo</div>
            <div style="display:flex;align-items:center;gap:10px">
              <div id="ebiz-logo-wrap" style="position:relative;display:inline-block">
                <img id="ebiz-logo-img" src="{{ str_starts_with($record->logo,'http') ? $record->logo : \Illuminate\Support\Facades\Storage::disk('public')->url($record->logo) }}" style="width:72px;height:72px;object-fit:cover;border-radius:10px;border:1.5px solid var(--border)" onerror="this.src='https://placehold.co/72x72?text=Logo'">
                <button type="button" onclick="removeExistingLogo('{{ addslashes($record->logo) }}')"
                  style="position:absolute;top:3px;right:3px;width:20px;height:20px;background:rgba(0,0,0,.65);color:#fff;border:none;border-radius:50%;font-size:11px;cursor:pointer;display:flex;align-items:center;justify-content:center;line-height:1">✕</button>
              </div>
              <div id="ebiz-logo-remove-input"></div>
            </div>
            <div class="form-hint" style="margin-top:6px">Click ✕ to remove logo, or upload new one below to replace.</div>
          </div>
        @endif
        <x-image-uploader name="logo" :multiple="false" :max="1" label="Business Logo" hint="Square image preferred · Leave empty to keep current" />
      </div>
    </div>

    <div style="display:flex;align-items:center;justify-content:space-between;gap:16px;flex-wrap:wrap">
      <a href="{{ route('account') }}" class="btn-cancel" style="margin-left:0">Cancel</a>
      <button type="button" id="ebiz-submit-btn" onclick="submitEditBizForm()" class="btn-save" style="font-size:15px;padding:14px 40px">
        💾 Save Changes →
      </button>
    </div>
  </form>
  @endif

  {{-- ── MATRIMONIAL ─────────────────────────────────────────────── --}}
  @if($type === 'matrimonial')
  <form method="POST" action="{{ route('post.update', ['type'=>'matrimonial','id'=>$record->id]) }}" enctype="multipart/form-data">
    @csrf
    <div class="form-card">
      <div class="form-card-head">💍 Edit Matrimonial Profile</div>
      <div class="form-card-body">
        <div class="form-section">
          <div class="form-section-title">Profile For</div>
          <div class="form-row">
            <div class="form-group">
              <label class="form-label">Profile For</label>
              <select name="profile_for" class="form-input">
                @foreach(['self'=>'Myself','son'=>'Son','daughter'=>'Daughter','brother'=>'Brother','sister'=>'Sister','friend'=>'Friend'] as $v=>$l)
                  <option value="{{ $v }}" {{ $record->profile_for===$v ? 'selected' : '' }}>{{ $l }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">Gender</label>
              <select name="gender" class="form-input">
                <option value="male" {{ $record->gender==='male' ? 'selected' : '' }}>Male</option>
                <option value="female" {{ $record->gender==='female' ? 'selected' : '' }}>Female</option>
              </select>
            </div>
          </div>
        </div>
        <div class="form-section">
          <div class="form-section-title">Personal Details</div>
          <div class="form-row" style="margin-bottom:14px">
            <div class="form-group">
              <label class="form-label">Full Name <span>*</span></label>
              <input type="text" name="name" class="form-input" value="{{ old('name',$record->name) }}" required>
            </div>
            <div class="form-group">
              <label class="form-label">Age <span>*</span></label>
              <input type="number" name="age" class="form-input" value="{{ old('age',$record->age) }}" required min="18" max="80">
            </div>
          </div>
          <div class="form-row-3" style="margin-bottom:14px">
            <div class="form-group">
              <label class="form-label">Height</label>
              <input type="text" name="height" class="form-input" value="{{ old('height',$record->height) }}">
            </div>
            <div class="form-group">
              <label class="form-label">Marital Status</label>
              <select name="marital_status" class="form-input">
                @foreach(['never_married'=>'Never Married','divorced'=>'Divorced','widowed'=>'Widowed'] as $v=>$l)
                  <option value="{{ $v }}" {{ $record->marital_status===$v ? 'selected' : '' }}>{{ $l }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">Diet</label>
              <select name="diet" class="form-input">
                <option value="">Prefer not to say</option>
                @foreach(['veg'=>'Vegetarian','non-veg'=>'Non-Vegetarian','eggetarian'=>'Eggetarian'] as $v=>$l)
                  <option value="{{ $v }}" {{ $record->diet===$v ? 'selected' : '' }}>{{ $l }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-row-3">
            <div class="form-group">
              <label class="form-label">Religion</label>
              <input type="text" name="religion" class="form-input" value="{{ old('religion',$record->religion) }}">
            </div>
            <div class="form-group">
              <label class="form-label">Caste</label>
              <input type="text" name="caste" class="form-input" value="{{ old('caste',$record->caste) }}">
            </div>
            <div class="form-group">
              <label class="form-label">Mother Tongue</label>
              <input type="text" name="mother_tongue" class="form-input" value="{{ old('mother_tongue',$record->mother_tongue) }}">
            </div>
          </div>
        </div>
        <div class="form-section">
          <div class="form-section-title">Professional & Location</div>
          <div class="form-row-3" style="margin-bottom:14px">
            <div class="form-group">
              <label class="form-label">Education</label>
              <input type="text" name="education" class="form-input" value="{{ old('education',$record->education) }}">
            </div>
            <div class="form-group">
              <label class="form-label">Occupation</label>
              <input type="text" name="occupation" class="form-input" value="{{ old('occupation',$record->occupation) }}">
            </div>
            <div class="form-group">
              <label class="form-label">Income</label>
              <input type="text" name="income" class="form-input" value="{{ old('income',$record->income) }}">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label class="form-label">Province <span>*</span></label>
              <select name="province" id="emat-province" class="form-input" required onchange="loadCities('emat-city',this.value)">
                <option value="">Select province</option>
                @foreach($provinces as $prov)
                  <option value="{{ $prov }}" {{ old('province',$record->province)===$prov ? 'selected' : '' }}>{{ $prov }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">City <span>*</span></label>
              <select name="city" id="emat-city" class="form-input" required>
                <option value="">Select city</option>
                @foreach($cities as $city)
                  <option value="{{ $city }}" {{ old('city',$record->city)===$city ? 'selected' : '' }}>{{ $city }}</option>
                @endforeach
              </select>
            </div>
          </div>
        </div>
        <div class="form-section">
          <div class="form-section-title">About & Preferences</div>
          <div class="form-group" style="margin-bottom:14px">
            <label class="form-label">About Yourself</label>
            <textarea name="about" class="form-input" rows="4">{{ old('about',$record->about) }}</textarea>
          </div>
          <div class="form-group">
            <label class="form-label">Partner Preference</label>
            <textarea name="partner_preference" class="form-input" rows="3">{{ old('partner_preference',$record->partner_preference) }}</textarea>
          </div>
        </div>
        <div class="form-section">
          <div class="form-section-title">Contact & Photo</div>
          <div class="form-row" style="margin-bottom:14px">
            <div class="form-group">
              <label class="form-label">Contact Name</label>
              <input type="text" name="contact_name" class="form-input" value="{{ old('contact_name',$record->contact_name) }}">
            </div>
            <div class="form-group">
              <label class="form-label">Contact Phone</label>
              <input type="text" name="contact_phone" class="form-input" value="{{ old('contact_phone',$record->contact_phone) }}">
            </div>
          </div>
          <div class="form-row" style="margin-bottom:14px">
            <div class="form-group">
              <label class="form-label">Contact Email</label>
              <input type="email" name="contact_email" class="form-input" value="{{ old('contact_email',$record->contact_email) }}">
            </div>
            <div class="form-group" style="display:flex;align-items:center;gap:10px;padding-top:22px">
              <input type="checkbox" name="hide_contact" id="hide_contact" value="1" {{ $record->hide_contact ? 'checked' : '' }} style="width:18px;height:18px;cursor:pointer">
              <label for="hide_contact" style="font-size:13px;color:var(--muted);cursor:pointer">Hide contact from public</label>
            </div>
          </div>
          @if($record->photo)
            <div style="margin-bottom:10px">
              <div class="form-label" style="margin-bottom:6px">Current Photo</div>
              @php $ep = $record->photo; @endphp
              <img src="{{ str_starts_with($ep,'http') ? $ep : (\Illuminate\Support\Facades\Storage::disk('public')->exists($ep) ? \Illuminate\Support\Facades\Storage::disk('public')->url($ep) : \Illuminate\Support\Facades\Storage::disk('s3')->url($ep)) }}" style="width:80px;height:80px;object-fit:cover;border-radius:50%;border:1.5px solid var(--border)" onerror="this.src='https://placehold.co/80x80?text=Photo'">
              <div class="form-hint">Upload a new photo below to replace it.</div>
            </div>
          @endif
          <x-image-uploader name="photo" :multiple="false" :max="1" label="Profile Photo" hint="JPG, PNG, WEBP · Max 1 MB · Leave empty to keep current" />
          <div style="margin-top:16px">
            <x-image-uploader name="photos" :multiple="true" :max="$maxImages" label="Additional Photos (Gallery)" hint="Upload up to {{ $maxImages }} additional photos · Leave empty to keep current gallery" />
          </div>
        </div>
        <button type="submit" class="btn-save">Save Changes</button>
        <a href="{{ route('account') }}" class="btn-cancel">Cancel</a>
      </div>
    </div>
  </form>
  @endif

  {{-- ── BUSINESS POST ───────────────────────────────────────────── --}}
  @if($type === 'business-post')
  <form method="POST" action="{{ route('post.update', ['type'=>'business-post','id'=>$record->id]) }}" enctype="multipart/form-data" id="ebp-form">
    @csrf
    <div class="form-card">
      <div class="form-card-head">📦 Edit Business Post</div>
      <div class="form-card-body">
        <div class="form-section">
          <div class="form-section-title">Business</div>
          <div style="font-size:13px;color:var(--muted)">🏢 {{ $record->business->name ?? '—' }}</div>
        </div>
        <div class="form-section">
          <div class="form-section-title">Post Details</div>
          <div class="form-group" style="margin-bottom:14px">
            <label class="form-label">Title <span>*</span></label>
            <input type="text" name="title" class="form-input" value="{{ old('title',$record->title) }}" required>
          </div>
          <div class="form-row" style="margin-bottom:14px">
            <div class="form-group">
              <label class="form-label">Price</label>
              <input type="text" name="price" class="form-input" value="{{ old('price',$record->price) }}">
            </div>
            <div class="form-group">
              <label class="form-label">Price Unit</label>
              <input type="text" name="price_unit" class="form-input" value="{{ old('price_unit',$record->price_unit) }}">
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">Description</label>
            <textarea name="description" id="ebp-description" style="display:none">{{ old('description',$record->description) }}</textarea>
            <div id="ebp-description-editor" class="ql-editor-wrap"></div>
          </div>
        </div>

        @if($customFields->isNotEmpty())
        <div class="form-section">
          <div class="form-section-title">Additional Details</div>
          @php $cfVals = $record->custom_fields ?? []; @endphp
          @foreach($customFields as $f)
            @php $val = old('cf.'.$f->key, $cfVals[$f->key] ?? ''); @endphp
            <div class="form-group" style="margin-bottom:14px">
              <label class="form-label">{{ $f->label }} @if($f->is_required)<span>*</span>@endif</label>
              @if($f->type === 'textarea')
                <textarea name="cf[{{ $f->key }}]" class="form-input" placeholder="{{ $f->placeholder }}" {{ $f->is_required?'required':'' }}>{{ $val }}</textarea>
              @elseif($f->type === 'number')
                <input type="number" name="cf[{{ $f->key }}]" class="form-input" value="{{ $val }}" placeholder="{{ $f->placeholder }}" {{ $f->is_required?'required':'' }}>
              @elseif($f->type === 'select')
                <select name="cf[{{ $f->key }}]" class="form-input" {{ $f->is_required?'required':'' }}>
                  <option value="">Select…</option>
                  @foreach($f->options ?? [] as $opt)
                    <option value="{{ $opt }}" {{ $val===$opt ? 'selected':'' }}>{{ $opt }}</option>
                  @endforeach
                </select>
              @elseif($f->type === 'checkbox')
                <label style="display:flex;align-items:center;gap:8px;font-size:13px;color:var(--text);cursor:pointer">
                  <input type="checkbox" name="cf[{{ $f->key }}]" value="1" style="width:18px;height:18px" {{ $val==='Yes' ? 'checked':'' }}> Yes
                </label>
              @else
                <input type="text" name="cf[{{ $f->key }}]" class="form-input" value="{{ $val }}" placeholder="{{ $f->placeholder }}" {{ $f->is_required?'required':'' }}>
              @endif
            </div>
          @endforeach
        </div>
        @endif

        <div class="form-section">
          <div class="form-section-title">Photos</div>
          @php
            $existingBpImages = array_values(array_filter(is_array($record->images) ? $record->images : [], fn($v) => !empty($v) && $v !== '0' && $v !== false));
            $resolveUrl = fn($p) => !$p || $p === '0' ? '' : (str_starts_with($p,'http') ? $p : \Illuminate\Support\Facades\Storage::disk(config('filesystems.default','public'))->url($p));
          @endphp
          @if(count($existingBpImages))
            <div style="margin-bottom:12px">
              <div class="form-label" style="margin-bottom:6px">Current Photos</div>
              <div style="display:flex;flex-wrap:wrap;gap:8px">
                @foreach($existingBpImages as $img)
                  @if($img)
                  <img src="{{ $resolveUrl($img) }}" style="width:80px;height:64px;object-fit:cover;border-radius:6px;border:1.5px solid var(--border)" onerror="this.src='https://placehold.co/80x64?text=Photo'">
                  @endif
                @endforeach
              </div>
              <div class="form-hint">Upload new photos below to replace all existing photos.</div>
            </div>
          @endif
          <x-image-uploader name="images" :multiple="true" :max="$maxImages" :label="'Post Photos (up to '.$maxImages.')'" hint="JPG, PNG, WEBP · Max 1 MB each · Leave empty to keep current photos" />
        </div>
        <button type="button" id="ebp-submit-btn" onclick="submitEbpForm()" class="btn-save">Save Changes</button>
        <a href="{{ route('account') }}" class="btn-cancel">Cancel</a>
      </div>
    </div>
  </form>
  @endif
</div>
@push('scripts')
<script>
function loadSubCats(selectId, parentId, selectedId) {
  var sel = document.getElementById(selectId);
  if (!sel) return;
  sel.innerHTML = '<option value="">Loading…</option>';
  if (!parentId) { sel.innerHTML = '<option value="">Select sub-category (optional)</option>'; return; }
  fetch('{{ route("categories.subs") }}?parent=' + encodeURIComponent(parentId))
    .then(function(r){ return r.json(); })
    .then(function(subs){
      sel.innerHTML = '<option value="">Select sub-category (optional)</option>';
      subs.forEach(function(c){
        var o = document.createElement('option');
        o.value = c.id;
        o.textContent = (c.icon ? c.icon + ' ' : '') + c.name;
        if (selectedId && c.id == selectedId) o.selected = true;
        sel.appendChild(o);
      });
    })
    .catch(function(){ sel.innerHTML = '<option value="">Select sub-category (optional)</option>'; });
}

var _qlToolbar = [
  ['bold','italic','underline'],
  [{'list':'ordered'},{'list':'bullet'}],
  ['clean']
];
var _qlToolbarImg = [
  ['bold','italic','underline'],
  [{'list':'ordered'},{'list':'bullet'}],
  ['link','image'],
  ['clean']
];

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

function _qlInitReturn(editorId, textareaId, withImage) {
  var ta = document.getElementById(textareaId);
  var el = document.getElementById(editorId);
  if (!ta || !el) return null;
  var q = new Quill(el, { theme:'snow', modules:{ toolbar: withImage ? _qlToolbarImg : _qlToolbar } });
  if (withImage) q.getModule('toolbar').addHandler('image', _qlImageHandler(q));
  if (ta.value) q.clipboard.dangerouslyPasteHTML(ta.value);
  el.closest('form').addEventListener('submit', function() { ta.value = q.root.innerHTML; });
  return q;
}

function _qlInit(editorId, textareaId, withImage) {
  _qlInitReturn(editorId, textareaId, withImage);
}

_qlInit('ecl-description-editor',   'ecl-description');
_qlInit('ejob-description-editor',  'ejob-description');
_qlInit('ejob-requirements-editor', 'ejob-requirements');
_qlInit('eev-description-editor',   'eev-description');
var _ebizQuill = _qlInitReturn('ebiz-description-editor', 'ebiz-description', true);
var _ebpQuill  = _qlInitReturn('ebp-description-editor',  'ebp-description',  true);

function submitEbpForm() {
  var form = document.getElementById('ebp-form');
  if (!form) return;
  if (_ebpQuill) document.getElementById('ebp-description').value = _ebpQuill.root.innerHTML;
  function _gpf(el) { while(el){ if(el.tagName==='FORM') return el; el=el.parentElement; } return null; }
  var hasError = false;
  Object.keys(window._iuReg||{}).forEach(function(uid){
    var inp = document.getElementById(uid+'_input');
    if(!inp || _gpf(inp)!==form) return;
    if(window._iuReg[uid].files.filter(function(f){return !f.valid;}).length) hasError=true;
  });
  if(hasError){ alert('Please remove oversized images (over 1 MB) before saving.'); return; }
  var btn = document.getElementById('ebp-submit-btn');
  btn.disabled=true; btn.textContent='⏳ Saving…';
  var fd = new FormData();
  new FormData(form).forEach(function(val,key){ if(typeof val==='string') fd.append(key,val); });
  Object.keys(window._iuReg||{}).forEach(function(uid){
    var inp = document.getElementById(uid+'_input');
    if(!inp || _gpf(inp)!==form) return;
    window._iuReg[uid].files.filter(function(e){return e.valid;}).forEach(function(entry){
      fd.append(inp.name, entry.file, entry.file.name);
    });
  });
  fetch(form.action,{
    method:'POST',
    headers:{'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]')?.content||''},
    body:fd
  }).then(function(r){
    if(r.redirected){ window.location.href=r.url; return; }
    return r.text().then(function(html){ document.open();document.write(html);document.close(); });
  }).catch(function(err){
    btn.disabled=false; btn.textContent='Save Changes';
    alert('Save failed. ('+err.message+')');
  });
}

// ── Edit Business form fetch submit (images need manual FormData) ──
function submitEditBizForm() {
  var form = document.getElementById('ebiz-form');
  if (!form) return;
  if (_ebizQuill) {
    document.getElementById('ebiz-description').value = _ebizQuill.root.innerHTML;
  }
  function _gpf(el) { while(el){ if(el.tagName==='FORM') return el; el=el.parentElement; } return null; }
  var hasError = false;
  Object.keys(window._iuReg||{}).forEach(function(uid){
    var inp = document.getElementById(uid+'_input');
    if(!inp || _gpf(inp)!==form) return;
    if(window._iuReg[uid].files.filter(function(f){return !f.valid;}).length) hasError=true;
  });
  if(hasError){ alert('Please remove oversized images (over 1 MB) before saving.'); return; }
  var btn = document.getElementById('ebiz-submit-btn');
  btn.disabled=true; btn.textContent='⏳ Saving…';
  var fd = new FormData();
  new FormData(form).forEach(function(val,key){ if(typeof val==='string') fd.append(key,val); });
  Object.keys(window._iuReg||{}).forEach(function(uid){
    var inp = document.getElementById(uid+'_input');
    if(!inp || _gpf(inp)!==form) return;
    var validFiles = window._iuReg[uid].files.filter(function(e){return e.valid;});
    validFiles.forEach(function(entry){ fd.append(inp.name, entry.file, entry.file.name); });
  });
  fetch(form.action,{
    method:'POST',
    headers:{'X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]')?.content||''},
    body:fd
  }).then(function(r){
    if(r.redirected){ window.location.href=r.url; return; }
    return r.text().then(function(html){ document.open();document.write(html);document.close(); });
  }).catch(function(err){
    btn.disabled=false; btn.textContent='💾 Save Changes →';
    alert('Save failed. Please try again. ('+err.message+')');
  });
}

// ── Edit Business Tag Pills ──
(function(){
  var inp = document.getElementById('ebiz-tags-input');
  var hid = document.getElementById('ebiz-tags-hidden');
  var pills = document.getElementById('ebiz-tag-pills');
  if(!inp) return;
  var _etags = inp.value.split(',').map(function(t){return t.trim();}).filter(Boolean);
  function renderEPills(){
    pills.innerHTML='';
    _etags.forEach(function(t,i){
      var p=document.createElement('span');
      p.style.cssText='display:inline-flex;align-items:center;gap:5px;background:var(--primary-light);color:var(--primary);border:1px solid var(--primary);border-radius:20px;padding:3px 10px;font-size:12px;font-weight:600';
      p.innerHTML=t+' <button type="button" onclick="removeEBizTag('+i+')" style="background:none;border:none;color:var(--primary);cursor:pointer;font-size:14px;line-height:1;padding:0">×</button>';
      pills.appendChild(p);
    });
    hid.value=_etags.join(',');
    inp.value=_etags.join(', ');
  }
  window._etags=_etags;
  window.removeEBizTag=function(i){ _etags.splice(i,1); renderEPills(); };
  inp.addEventListener('blur',function(){
    var fresh=inp.value.split(',').map(function(t){return t.trim();}).filter(Boolean);
    _etags.length=0; fresh.forEach(function(t){_etags.push(t);}); renderEPills();
  });
  renderEPills();
})();

// ── Edit Business AI Generator ──
var _eaiGenResult = null;
function runEAIGenerate(){
  var name    = (document.getElementById('ebiz-name')?.value||'').trim();
  var catSel  = document.getElementById('ebiz-category');
  var catText = catSel?.options[catSel.selectedIndex]?.text?.replace(/^[^\w]+/,'').trim()||'';
  var keywords= (document.getElementById('eai-keywords')?.value||'').trim();
  var lang    = document.getElementById('eai-lang')?.value||'en';
  if(!name){ alert('Please enter Business Name first.'); return; }
  if(!keywords){ alert('Please describe your business in the text area.'); return; }
  var btn=document.getElementById('eai-gen-btn');
  var status=document.getElementById('eai-status');
  btn.disabled=true;
  document.getElementById('eai-btn-icon').textContent='⏳';
  document.getElementById('eai-btn-text').textContent='Generating…';
  status.textContent='Asking AI — usually 5–10 seconds…';
  document.getElementById('eai-preview').style.display='none';
  fetch('{{ route("business.generate-content") }}',{
    method:'POST',
    headers:{'Content-Type':'application/json','X-CSRF-TOKEN':document.querySelector('meta[name="csrf-token"]')?.content||''},
    body:JSON.stringify({business_name:name,category:catText,keywords:keywords,language:lang})
  }).then(function(r){return r.json().then(function(d){return{ok:r.ok,data:d};});})
  .then(function(res){
    btn.disabled=false;
    document.getElementById('eai-btn-icon').textContent='✨';
    document.getElementById('eai-btn-text').textContent='Generate Content';
    if(!res.ok||res.data.error){ status.textContent='⚠️ '+(res.data.error||'Generation failed.'); return; }
    _eaiGenResult=res.data;
    status.textContent='✅ Done! Review below.';
    document.getElementById('eai-desc-preview').innerHTML=res.data.description||'';
    document.getElementById('eai-tagline-preview').textContent=res.data.tagline||'';
    var tp=document.getElementById('eai-tags-preview'); tp.innerHTML='';
    (res.data.tags||[]).forEach(function(t){
      var s=document.createElement('span');
      s.style.cssText='background:#e0e7ff;color:#3730a3;border-radius:20px;padding:3px 10px;font-size:12px;font-weight:600';
      s.textContent=t; tp.appendChild(s);
    });
    document.getElementById('eai-preview').style.display='block';
  }).catch(function(){ btn.disabled=false; status.textContent='⚠️ Network error. Try again.'; });
}
function applyEAIContent(){
  if(!_eaiGenResult) return;
  if(_ebizQuill && _eaiGenResult.description) _ebizQuill.clipboard.dangerouslyPasteHTML(_eaiGenResult.description);
  if(_eaiGenResult.tags && _eaiGenResult.tags.length){
    window._etags.length=0;
    _eaiGenResult.tags.forEach(function(t){ window._etags.push(t); });
    var pills=document.getElementById('ebiz-tag-pills');
    var hid=document.getElementById('ebiz-tags-hidden');
    var inp=document.getElementById('ebiz-tags-input');
    pills.innerHTML='';
    window._etags.forEach(function(t,i){
      var p=document.createElement('span');
      p.style.cssText='display:inline-flex;align-items:center;gap:5px;background:var(--primary-light);color:var(--primary);border:1px solid var(--primary);border-radius:20px;padding:3px 10px;font-size:12px;font-weight:600';
      p.innerHTML=t+' <button type="button" onclick="removeEBizTag('+i+')" style="background:none;border:none;color:var(--primary);cursor:pointer;font-size:14px;line-height:1;padding:0">×</button>';
      pills.appendChild(p);
    });
    hid.value=window._etags.join(',');
    inp.value=window._etags.join(', ');
  }
  document.getElementById('eai-preview').style.display='none';
  document.getElementById('eai-status').textContent='✅ Applied to form!';
}

// load subcategory for existing record
(function(){
  var catId = '{{ $record->category_id }}';
  if(catId) loadSubCats('ebiz-subcategory', catId, '{{ $record->subcategory_id }}');
})();

// ── Remove existing photos ──
function removeExistingPhoto(idx, path) {
  var wrap = document.getElementById('ebiz-photo-' + idx);
  if (wrap) wrap.remove();
  var container = document.getElementById('ebiz-remove-inputs');
  if (!container) return;
  var inp = document.createElement('input');
  inp.type = 'hidden';
  inp.name = 'remove_images[]';
  inp.value = path;
  inp.id = 'ebiz-rm-' + idx;
  container.appendChild(inp);
}

function removeExistingLogo(path) {
  var wrap = document.getElementById('ebiz-logo-wrap');
  if (wrap) wrap.remove();
  var container = document.getElementById('ebiz-logo-remove-input');
  if (!container) return;
  var inp = document.createElement('input');
  inp.type = 'hidden';
  inp.name = 'remove_logo';
  inp.value = '1';
  container.appendChild(inp);
}
</script>
@endpush
@endsection
