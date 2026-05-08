@extends('layouts.app')

@section('title', 'Matrimonial — GoBazzar')

@push('styles')
<style>
.mat-hero{background:linear-gradient(135deg,#7c3aed 0%,#a855f7 100%);color:#fff;padding:48px 20px;text-align:center}
.mat-hero h1{font-family:var(--fh);font-size:28px;font-weight:800;margin-bottom:8px}
.mat-hero p{color:rgba(255,255,255,.75);font-size:14px}

.mat-layout{max-width:1200px;margin:32px auto;padding:0 20px;display:grid;grid-template-columns:260px 1fr;gap:24px}
.mobile-filter-toggle{display:none;width:100%;background:var(--surface);border:1.5px solid var(--border2);border-radius:var(--r);padding:10px 16px;font-size:13px;font-weight:600;color:var(--text);text-align:left;margin-bottom:12px;cursor:pointer}
@media(max-width:768px){
  .mat-layout{grid-template-columns:1fr;margin:16px auto;padding:0 14px}
  .mat-layout>aside{display:none}
  .mat-layout>aside.open{display:block}
  .mobile-filter-toggle{display:block}
  .profiles-grid{grid-template-columns:repeat(2,1fr)}
  .mat-hero{padding:28px 16px}
  .mat-hero h1{font-size:22px}
}
@media(max-width:480px){
  .profiles-grid{grid-template-columns:1fr}
  .gender-tabs{flex-wrap:wrap}
  .mat-hero h1{font-size:18px}
}

/* Sidebar filters */
.filter-box{background:var(--surface);border:1px solid var(--border);border-radius:var(--rl);padding:20px;position:sticky;top:72px;height:fit-content}
.filter-box h3{font-family:var(--fh);font-size:13px;font-weight:700;margin-bottom:16px;padding-bottom:10px;border-bottom:1px solid var(--border)}
.filter-group{margin-bottom:18px}
.filter-group label{display:block;font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:6px}
.filter-group select,.filter-group input{width:100%;border:1.5px solid var(--border2);border-radius:var(--r);padding:8px 10px;font-size:13px;color:var(--text)}
.filter-group select:focus,.filter-group input:focus{border-color:#a855f7;outline:none}
.age-range{display:grid;grid-template-columns:1fr 1fr;gap:8px}
.btn-filter{width:100%;background:#7c3aed;color:#fff;border:none;border-radius:var(--r);padding:10px;font-size:13px;font-weight:600;cursor:pointer;margin-top:4px}
.btn-filter:hover{background:#6d28d9}
.btn-clear{width:100%;background:transparent;border:1.5px solid var(--border2);color:var(--muted);border-radius:var(--r);padding:8px;font-size:12px;cursor:pointer;margin-top:8px}

/* Gender tabs */
.gender-tabs{display:flex;gap:8px;margin-bottom:20px}
.gender-tab{flex:1;text-align:center;padding:10px;border-radius:var(--r);border:2px solid var(--border2);font-size:13px;font-weight:600;color:var(--muted);cursor:pointer;text-decoration:none;transition:all .15s}
.gender-tab.active-m{border-color:#3b82f6;color:#3b82f6;background:#eff6ff}
.gender-tab.active-f{border-color:#ec4899;color:#ec4899;background:#fdf2f8}
.gender-tab:not(.active-m):not(.active-f):hover{border-color:#a855f7;color:#7c3aed}

/* Profile cards */
.profiles-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(240px,1fr));gap:18px}
.profile-card{background:var(--surface);border:1px solid var(--border);border-radius:var(--rl);overflow:hidden;transition:box-shadow .2s;position:relative}
.profile-card:hover{box-shadow:0 4px 20px rgba(124,58,237,.12)}
.profile-card-photo{height:200px;background:linear-gradient(135deg,#f3e8ff,#fce7f3);overflow:hidden;display:flex;align-items:center;justify-content:center;font-size:64px}
.profile-card-photo img{width:100%;height:100%;object-fit:cover}
.featured-ribbon{position:absolute;top:12px;left:12px;background:#7c3aed;color:#fff;font-size:9px;font-weight:700;padding:3px 8px;border-radius:20px;text-transform:uppercase;letter-spacing:.5px}
.profile-card-body{padding:14px}
.profile-card-body h3{font-family:var(--fh);font-size:15px;font-weight:700;margin-bottom:4px}
.profile-card-body h3 a{color:var(--text)}
.profile-card-body h3 a:hover{color:#7c3aed}
.profile-detail-row{display:flex;flex-wrap:wrap;gap:6px;margin-top:8px}
.p-chip{background:var(--bg);border:1px solid var(--border);border-radius:20px;padding:3px 10px;font-size:11px;color:var(--muted)}
.male-badge{color:#3b82f6;font-size:11px;font-weight:600}
.female-badge{color:#ec4899;font-size:11px;font-weight:600}

.results-bar{display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;font-size:13px;color:var(--muted)}
</style>
@endpush

@section('content')

<div class="mat-hero">
  <h1>💍 Matrimonial</h1>
  <p>Find your life partner within the Indian community in Canada</p>
</div>

<div class="mat-layout">
  <button class="mobile-filter-toggle" onclick="this.nextElementSibling.classList.toggle('open');this.textContent=this.nextElementSibling.classList.contains('open')?'▲ Hide Filters':'▼ Search & Filters'">▼ Search & Filters</button>
  {{-- Sidebar Filters --}}
  <aside>
    <form method="GET" action="{{ route('matrimonial.index') }}">
      <div class="filter-box">
        <h3>Filter Profiles</h3>

        <div class="filter-group">
          <label>Search</label>
          <input type="text" name="search" value="{{ request('search') }}" placeholder="Name, occupation…">
        </div>

        <div class="filter-group">
          <label>Gender</label>
          <select name="gender">
            <option value="">All</option>
            <option value="male" {{ request('gender') === 'male' ? 'selected' : '' }}>Male</option>
            <option value="female" {{ request('gender') === 'female' ? 'selected' : '' }}>Female</option>
          </select>
        </div>

        <div class="filter-group">
          <label>Age Range</label>
          <div class="age-range">
            <input type="number" name="min_age" value="{{ request('min_age') }}" placeholder="Min" min="18" max="80">
            <input type="number" name="max_age" value="{{ request('max_age') }}" placeholder="Max" min="18" max="80">
          </div>
        </div>

        <div class="filter-group">
          <label>Religion</label>
          <select name="religion">
            <option value="">All Religions</option>
            @foreach($religions as $rel)
              <option value="{{ $rel }}" {{ request('religion') === $rel ? 'selected' : '' }}>{{ $rel }}</option>
            @endforeach
          </select>
        </div>

        <div class="filter-group">
          <label>Province</label>
          <select name="province" id="mat-prov-filter" onchange="matLoadCities(this.value)">
            <option value="">All Provinces</option>
            @foreach($provinces as $prov)
              <option value="{{ $prov }}" {{ request('province') === $prov ? 'selected' : '' }}>{{ $prov }}</option>
            @endforeach
          </select>
        </div>

        <div class="filter-group">
          <label>City</label>
          <select name="city" id="mat-city-filter">
            <option value="">All Cities</option>
            @foreach($cities as $city)
              <option value="{{ $city }}" {{ request('city') === $city ? 'selected' : '' }}>{{ $city }}</option>
            @endforeach
          </select>
        </div>

        <button type="submit" class="btn-filter">Search</button>
        <a href="{{ route('matrimonial.index') }}" class="btn-clear">Clear Filters</a>
      </div>
    </form>
  </aside>

  {{-- Main content --}}
  <div>
    <div class="gender-tabs">
      <a href="{{ route('matrimonial.index', array_merge(request()->except('gender','page'), [])) }}"
         class="gender-tab {{ !request('gender') ? 'active-m' : '' }}">All Profiles</a>
      <a href="{{ route('matrimonial.index', array_merge(request()->except('gender','page'), ['gender'=>'male'])) }}"
         class="gender-tab {{ request('gender')==='male' ? 'active-m' : '' }}">👨 Grooms</a>
      <a href="{{ route('matrimonial.index', array_merge(request()->except('gender','page'), ['gender'=>'female'])) }}"
         class="gender-tab {{ request('gender')==='female' ? 'active-f' : '' }}">👩 Brides</a>
    </div>

    <div class="results-bar">
      <span>{{ $profiles->total() }} profiles found</span>
      @if(request()->hasAny(['search','gender','religion','city','min_age','max_age']))
        <a href="{{ route('matrimonial.index') }}" style="color:#7c3aed;font-size:12px">Clear all filters</a>
      @endif
    </div>

    @if($profiles->isEmpty())
      <div style="text-align:center;padding:60px;color:var(--muted)">
        <div style="font-size:48px;margin-bottom:12px">💍</div>
        <p>No profiles found matching your criteria.</p>
        <a href="{{ route('matrimonial.index') }}" style="color:#7c3aed;font-size:13px;margin-top:8px;display:block">View all profiles</a>
      </div>
    @else
      <div class="profiles-grid">
        @foreach($profiles as $p)
        <div class="profile-card">
          @if($p->is_featured)<span class="featured-ribbon">Featured</span>@endif
          <a href="{{ route('matrimonial.show', $p->slug) }}">
            <div class="profile-card-photo">
              @if($p->photo)
                <img src="{{ asset('storage/'.$p->photo) }}" alt="{{ $p->name }}">
              @else
                {{ $p->gender === 'male' ? '👨' : '👩' }}
              @endif
            </div>
          </a>
          <div class="profile-card-body">
            <div class="{{ $p->gender === 'male' ? 'male-badge' : 'female-badge' }}">
              {{ $p->gender === 'male' ? '♂ Male' : '♀ Female' }}
            </div>
            <h3><a href="{{ route('matrimonial.show', $p->slug) }}">{{ $p->name }}</a></h3>
            <div style="font-size:12px;color:var(--muted);margin-top:2px">{{ $p->age }} yrs · {{ $p->city }}@if($p->province), {{ $p->province }}@endif</div>
            <div class="profile-detail-row">
              @if($p->religion)<span class="p-chip">{{ $p->religion }}</span>@endif
              @if($p->education)<span class="p-chip">{{ Str::limit($p->education,20) }}</span>@endif
              @if($p->occupation)<span class="p-chip">{{ Str::limit($p->occupation,20) }}</span>@endif
              @if($p->marital_status !== 'never_married')<span class="p-chip">{{ $p->marital_status_label }}</span>@endif
            </div>
          </div>
        </div>
        @endforeach
      </div>

      <div style="margin-top:28px">
        {{ $profiles->withQueryString()->links() }}
      </div>
    @endif
  </div>
</div>
@endsection

@push('scripts')
<script>
function matLoadCities(province) {
  var sel = document.getElementById('mat-city-filter');
  sel.innerHTML = '<option value="">Loading…</option>';
  fetch('{{ route('locations.cities') }}?province=' + encodeURIComponent(province))
    .then(function(r){ return r.json(); })
    .then(function(data){
      sel.innerHTML = '<option value="">All Cities</option>';
      data.forEach(function(c){
        var o = document.createElement('option');
        o.value = c; o.textContent = c;
        sel.appendChild(o);
      });
    });
}
</script>
@endpush
