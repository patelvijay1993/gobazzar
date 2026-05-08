@extends('layouts.app')
@section('title', 'Edit Post — GoBazzar')

@push('styles')
<style>
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
    <h1>Edit {{ ucfirst($type === 'classified' ? 'Classified Ad' : ($type === 'business' ? 'Business Listing' : $type)) }}</h1>
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
                  <img src="{{ asset('storage/'.$img) }}" style="width:80px;height:64px;object-fit:cover;border-radius:6px;border:1.5px solid var(--border)">
                @endforeach
              </div>
              <div class="form-hint">Upload new photos below to replace all existing photos.</div>
            </div>
          @endif
          <x-image-uploader name="images" :multiple="true" :max="5" label="Photos (up to 5)" hint="JPG, PNG, WEBP · Max 1 MB each · Leave empty to keep current photos" />
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
          @if($record->company_logo)
            <div style="margin-bottom:10px">
              <div class="form-label" style="margin-bottom:6px">Current Logo</div>
              <img src="{{ asset('storage/'.$record->company_logo) }}" style="width:80px;height:64px;object-fit:cover;border-radius:6px;border:1.5px solid var(--border)">
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
              <img src="{{ asset('storage/'.$record->image) }}" style="width:120px;height:80px;object-fit:cover;border-radius:6px;border:1.5px solid var(--border)">
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
  <form method="POST" action="{{ route('post.update', ['type'=>'business','id'=>$record->id]) }}" enctype="multipart/form-data">
    @csrf
    <div class="form-card">
      <div class="form-card-head">🏢 Edit Business Listing</div>
      <div class="form-card-body">
        <div class="form-section">
          <div class="form-section-title">Business Details</div>
          <div class="form-group" style="margin-bottom:14px">
            <label class="form-label">Business Name <span>*</span></label>
            <input type="text" name="name" class="form-input" value="{{ old('name',$record->name) }}" required>
          </div>
          <div class="form-row" style="margin-bottom:14px">
            <div class="form-group">
              <label class="form-label">Category</label>
              <select name="category_id" class="form-input">
                <option value="">Select</option>
                @foreach($categories->get('directory', collect()) as $cat)
                  <option value="{{ $cat->id }}" {{ $record->category_id==$cat->id ? 'selected' : '' }}>{{ $cat->icon }} {{ $cat->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label class="form-label">Phone</label>
              <input type="text" name="phone" class="form-input" value="{{ old('phone',$record->phone) }}">
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">Description</label>
            <textarea name="description" id="ebiz-description" style="display:none">{{ old('description',$record->description) }}</textarea>
            <div id="ebiz-description-editor" class="ql-editor-wrap"></div>
          </div>
        </div>
        <div class="form-section">
          <div class="form-section-title">Location & Contact</div>
          <div class="form-group" style="margin-bottom:14px">
            <label class="form-label">Address</label>
            <input type="text" name="address" class="form-input" value="{{ old('address',$record->address) }}">
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
          <div class="form-row">
            <div class="form-group">
              <label class="form-label">Email</label>
              <input type="email" name="email" class="form-input" value="{{ old('email',$record->email) }}">
            </div>
            <div class="form-group">
              <label class="form-label">Website</label>
              <input type="url" name="website" class="form-input" value="{{ old('website',$record->website) }}">
            </div>
          </div>
        </div>
        <div class="form-section">
          <div class="form-section-title">Photos</div>
          @php $existingBizImages = $record->images ?? ($record->image ? [$record->image] : []); @endphp
          @if(count($existingBizImages))
            <div style="margin-bottom:12px">
              <div class="form-label" style="margin-bottom:6px">Current Photos</div>
              <div style="display:flex;flex-wrap:wrap;gap:8px">
                @foreach($existingBizImages as $img)
                  <img src="{{ asset('storage/'.$img) }}" style="width:80px;height:64px;object-fit:cover;border-radius:6px;border:1.5px solid var(--border)">
                @endforeach
              </div>
              <div class="form-hint">Upload new photos below to replace all existing photos.</div>
            </div>
          @endif
          <x-image-uploader name="images" :multiple="true" :max="5" label="Business Photos (up to 5)" hint="JPG, PNG, WEBP · Max 1 MB each · Leave empty to keep current photos" />
          <div style="margin-top:18px">
            @if($record->logo)
              <div style="margin-bottom:10px">
                <div class="form-label" style="margin-bottom:6px">Current Logo</div>
                <img src="{{ asset('storage/'.$record->logo) }}" style="width:72px;height:72px;object-fit:cover;border-radius:10px;border:1.5px solid var(--border)">
                <div class="form-hint">Upload a new logo below to replace it.</div>
              </div>
            @endif
            <x-image-uploader name="logo" :multiple="false" :max="1" label="Logo" hint="JPG, PNG, WEBP · Max 1 MB · Leave empty to keep current" />
          </div>
        </div>
        <button type="submit" class="btn-save">Save Changes</button>
        <a href="{{ route('account') }}" class="btn-cancel">Cancel</a>
      </div>
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
              <img src="{{ asset('storage/'.$record->photo) }}" style="width:80px;height:80px;object-fit:cover;border-radius:50%;border:1.5px solid var(--border)">
              <div class="form-hint">Upload a new photo below to replace it.</div>
            </div>
          @endif
          <x-image-uploader name="photo" :multiple="false" :max="1" label="Profile Photo" hint="JPG, PNG, WEBP · Max 1 MB · Leave empty to keep current" />
        </div>
        <button type="submit" class="btn-save">Save Changes</button>
        <a href="{{ route('account') }}" class="btn-cancel">Cancel</a>
      </div>
    </div>
  </form>
  @endif
</div>
@push('scripts')
<script>
var _qlToolbar = [
  ['bold','italic','underline'],
  [{'list':'ordered'},{'list':'bullet'}],
  ['clean']
];

function _qlInit(editorId, textareaId) {
  var ta = document.getElementById(textareaId);
  var el = document.getElementById(editorId);
  if (!ta || !el) return;
  var q = new Quill(el, { theme:'snow', modules:{ toolbar: _qlToolbar } });
  if (ta.value) q.clipboard.dangerouslyPasteHTML(ta.value);
  el.closest('form').addEventListener('submit', function() {
    ta.value = q.root.innerHTML;
  });
}

_qlInit('ecl-description-editor',   'ecl-description');
_qlInit('ejob-description-editor',  'ejob-description');
_qlInit('ejob-requirements-editor', 'ejob-requirements');
_qlInit('eev-description-editor',   'eev-description');
_qlInit('ebiz-description-editor',  'ebiz-description');
</script>
@endpush
@endsection
