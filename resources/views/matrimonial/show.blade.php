@extends('layouts.app')

@section('title', $profile->name.' — Matrimonial — GoBazzar')

@push('styles')
<style>
.mat-show-wrap{max-width:1100px;margin:32px auto;padding:0 20px;display:grid;grid-template-columns:1fr 320px;gap:28px}
@media(max-width:768px){.mat-show-wrap{grid-template-columns:1fr;padding:0 14px}.profile-name-row h1{font-size:18px}}
@media(max-width:480px){.related-grid{grid-template-columns:1fr}}

.profile-main{background:var(--surface);border:1px solid var(--border);border-radius:var(--rl);overflow:hidden}
.profile-banner{height:220px;background:linear-gradient(135deg,#7c3aed,#a855f7);display:flex;align-items:center;justify-content:center;position:relative}
.profile-avatar{width:130px;height:130px;border-radius:50%;border:4px solid #fff;object-fit:cover;box-shadow:0 4px 20px rgba(0,0,0,.2)}
.profile-avatar-placeholder{width:130px;height:130px;border-radius:50%;border:4px solid rgba(255,255,255,.5);background:rgba(255,255,255,.15);display:grid;place-items:center;font-size:56px}
.profile-body{padding:28px}
.profile-name-row{display:flex;align-items:center;gap:12px;margin-bottom:4px}
.profile-name-row h1{font-family:var(--fh);font-size:22px;font-weight:800}
.gender-badge-m{background:#eff6ff;color:#3b82f6;font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px}
.gender-badge-f{background:#fdf2f8;color:#ec4899;font-size:11px;font-weight:700;padding:3px 10px;border-radius:20px}
.featured-tag{background:#7c3aed;color:#fff;font-size:10px;font-weight:700;padding:3px 10px;border-radius:20px}

.info-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin:24px 0;padding:20px;background:var(--bg);border-radius:var(--r)}
@media(max-width:600px){.info-grid{grid-template-columns:1fr 1fr}}
.info-item label{display:block;font-size:10px;font-weight:700;color:var(--hint);text-transform:uppercase;letter-spacing:.5px;margin-bottom:3px}
.info-item span{font-size:13px;font-weight:600;color:var(--text)}

.section-block{margin-bottom:24px}
.section-block h3{font-family:var(--fh);font-size:14px;font-weight:700;margin-bottom:10px;padding-bottom:8px;border-bottom:1px solid var(--border)}
.section-block p{font-size:14px;color:var(--muted);line-height:1.7}

/* Sidebar */
.sidebar-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--rl);padding:20px;margin-bottom:16px}
.sidebar-card h4{font-family:var(--fh);font-size:13px;font-weight:700;margin-bottom:14px;padding-bottom:8px;border-bottom:1px solid var(--border)}
.contact-row{display:flex;align-items:center;gap:10px;padding:10px 0;border-bottom:1px solid var(--border);font-size:13px}
.contact-row:last-child{border-bottom:none}
.contact-icon{width:32px;height:32px;border-radius:8px;background:var(--bg);display:grid;place-items:center;font-size:16px;flex-shrink:0}
.btn-interest{display:block;width:100%;text-align:center;background:#7c3aed;color:#fff;border-radius:var(--r);padding:11px;font-size:13px;font-weight:600;margin-bottom:8px}
.btn-interest:hover{background:#6d28d9;color:#fff}

/* Related */
.related-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:24px}
.related-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--rl);overflow:hidden;display:flex;gap:12px;padding:12px;align-items:center}
.related-avatar{width:48px;height:48px;border-radius:50%;object-fit:cover;flex-shrink:0;background:linear-gradient(135deg,#f3e8ff,#fce7f3);display:grid;place-items:center;font-size:22px}
.related-avatar img{width:100%;height:100%;object-fit:cover;border-radius:50%}
</style>
@endpush

@section('content')
<div class="mat-show-wrap">
  {{-- Profile Main --}}
  <div>
    <div class="breadcrumb" style="margin-bottom:16px">
      <a href="{{ route('home') }}">Home</a>
      <span>›</span>
      <a href="{{ route('matrimonial.index') }}">Matrimonial</a>
      <span>›</span>
      {{ $profile->name }}
    </div>

    <div class="profile-main">
      <div class="profile-banner">
        @if($profile->photo)
          <img src="{{ asset('storage/'.$profile->photo) }}" alt="{{ $profile->name }}" class="profile-avatar">
        @else
          <div class="profile-avatar-placeholder">{{ $profile->gender === 'male' ? '👨' : '👩' }}</div>
        @endif
      </div>

      <div class="profile-body">
        <div class="profile-name-row">
          <h1>{{ $profile->name }}</h1>
          <span class="{{ $profile->gender === 'male' ? 'gender-badge-m' : 'gender-badge-f' }}">
            {{ $profile->gender === 'male' ? '♂ Male' : '♀ Female' }}
          </span>
          @if($profile->is_featured)<span class="featured-tag">⭐ Featured</span>@endif
        </div>
        <div style="font-size:13px;color:var(--muted)">Profile for: <strong>{{ ucfirst($profile->profile_for) }}</strong> · {{ $profile->views }} views</div>

        <div class="info-grid">
          <div class="info-item"><label>Age</label><span>{{ $profile->age }} years</span></div>
          @if($profile->height)<div class="info-item"><label>Height</label><span>{{ $profile->height }}</span></div>@endif
          <div class="info-item"><label>Marital Status</label><span>{{ $profile->marital_status_label }}</span></div>
          @if($profile->religion)<div class="info-item"><label>Religion</label><span>{{ $profile->religion }}</span></div>@endif
          @if($profile->caste)<div class="info-item"><label>Caste</label><span>{{ $profile->caste }}</span></div>@endif
          @if($profile->mother_tongue)<div class="info-item"><label>Mother Tongue</label><span>{{ $profile->mother_tongue }}</span></div>@endif
          @if($profile->diet)<div class="info-item"><label>Diet</label><span>{{ ucfirst($profile->diet) }}</span></div>@endif
          @if($profile->education)<div class="info-item"><label>Education</label><span>{{ $profile->education }}</span></div>@endif
          @if($profile->occupation)<div class="info-item"><label>Occupation</label><span>{{ $profile->occupation }}</span></div>@endif
          @if($profile->income)<div class="info-item"><label>Annual Income</label><span>{{ $profile->income }}</span></div>@endif
          <div class="info-item"><label>Location</label><span>{{ $profile->city }}@if($profile->province), {{ $profile->province }}@endif, {{ $profile->country }}</span></div>
        </div>

        @if($profile->about)
        <div class="section-block">
          <h3>About</h3>
          <p>{{ $profile->about }}</p>
        </div>
        @endif

        @if($profile->partner_preference)
        <div class="section-block">
          <h3>Partner Preference</h3>
          <p>{{ $profile->partner_preference }}</p>
        </div>
        @endif
      </div>
    </div>

    {{-- Related profiles --}}
    @if($related->isNotEmpty())
    <div style="margin-top:28px">
      <h3 style="font-family:var(--fh);font-size:16px;font-weight:700;margin-bottom:14px">Similar Profiles</h3>
      <div class="related-grid">
        @foreach($related as $r)
        <a href="{{ route('matrimonial.show', $r->slug) }}" class="related-card" style="text-decoration:none;color:inherit">
          <div class="related-avatar">
            @if($r->photo)
              <img src="{{ asset('storage/'.$r->photo) }}" alt="{{ $r->name }}">
            @else
              {{ $r->gender === 'male' ? '👨' : '👩' }}
            @endif
          </div>
          <div>
            <div style="font-weight:600;font-size:13px">{{ $r->name }}</div>
            <div style="font-size:11px;color:var(--muted)">{{ $r->age }} yrs · {{ $r->city }}</div>
            @if($r->religion)<div style="font-size:11px;color:var(--hint)">{{ $r->religion }}</div>@endif
          </div>
        </a>
        @endforeach
      </div>
    </div>
    @endif
  </div>

  {{-- Sidebar --}}
  <aside>
    <div class="sidebar-card">
      <h4>Contact Details</h4>
      @if($profile->hide_contact)
        <div style="text-align:center;padding:20px;color:var(--muted);font-size:13px">
          <div style="font-size:28px;margin-bottom:8px">🔒</div>
          <p>Contact details are hidden.<br>Please login to send interest.</p>
        </div>
      @else
        @if($profile->contact_name)
        <div class="contact-row">
          <div class="contact-icon">👤</div>
          <div>
            <div style="font-size:10px;color:var(--hint)">Contact Person</div>
            <div style="font-weight:600">{{ $profile->contact_name }}</div>
          </div>
        </div>
        @endif
        @if($profile->contact_phone)
        <div class="contact-row">
          <div class="contact-icon">📞</div>
          <div>
            <div style="font-size:10px;color:var(--hint)">Phone</div>
            <a href="tel:{{ $profile->contact_phone }}" style="font-weight:600;color:var(--text)">{{ $profile->contact_phone }}</a>
          </div>
        </div>
        @endif
        @if($profile->contact_email)
        <div class="contact-row">
          <div class="contact-icon">✉️</div>
          <div>
            <div style="font-size:10px;color:var(--hint)">Email</div>
            <a href="mailto:{{ $profile->contact_email }}" style="font-weight:600;color:var(--red);word-break:break-all">{{ $profile->contact_email }}</a>
          </div>
        </div>
        @endif
        @if(!$profile->contact_name && !$profile->contact_phone && !$profile->contact_email)
          <p style="font-size:13px;color:var(--muted)">No contact details provided.</p>
        @endif
      @endif
    </div>

    <div class="sidebar-card">
      <h4>Quick Info</h4>
      <div style="font-size:13px;color:var(--muted);line-height:2">
        <div>👤 <strong>Age:</strong> {{ $profile->age }} yrs</div>
        @if($profile->height)<div>📏 <strong>Height:</strong> {{ $profile->height }}</div>@endif
        <div>📍 <strong>City:</strong> {{ $profile->city }}</div>
        @if($profile->religion)<div>🛕 <strong>Religion:</strong> {{ $profile->religion }}</div>@endif
        @if($profile->mother_tongue)<div>🗣 <strong>Language:</strong> {{ $profile->mother_tongue }}</div>@endif
      </div>
    </div>

    <a href="{{ route('matrimonial.index') }}" class="btn btn-ghost" style="display:block;text-align:center;width:100%">← Back to Profiles</a>
  </aside>
</div>
@endsection
