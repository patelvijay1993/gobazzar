@props(['route', 'provinces', 'cities'])
@php
  $currentProvince = request('province', '');
  $currentCity     = request('city', '');
  $uid = 'lf_' . Str::random(5);
@endphp

<div class="filter-box" style="padding:16px">
  <div class="filter-box-head" style="margin-bottom:12px">📍 Location</div>

  <div style="margin-bottom:10px">
    <label style="font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;display:block;margin-bottom:5px">Province</label>
    <select id="{{ $uid }}_prov"
            style="width:100%;border:1.5px solid var(--border2);border-radius:var(--r);padding:8px 10px;font-size:13px;color:var(--text);background:var(--surface)"
            onchange="lfLoadCities('{{ $uid }}','{{ $route }}', this.value)">
      <option value="">All Provinces</option>
      @foreach($provinces as $prov)
        <option value="{{ $prov }}" {{ $currentProvince === $prov ? 'selected' : '' }}>{{ $prov }}</option>
      @endforeach
    </select>
  </div>

  <div>
    <label style="font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;display:block;margin-bottom:5px">City</label>
    <select id="{{ $uid }}_city"
            style="width:100%;border:1.5px solid var(--border2);border-radius:var(--r);padding:8px 10px;font-size:13px;color:var(--text);background:var(--surface)"
            onchange="lfGo('{{ $route }}', document.getElementById('{{ $uid }}_prov').value, this.value)">
      <option value="">All Cities</option>
      @foreach($cities as $city)
        <option value="{{ $city }}" {{ $currentCity === $city ? 'selected' : '' }}>{{ $city }}</option>
      @endforeach
    </select>
  </div>

  @if($currentProvince || $currentCity)
    <a href="{{ route($route, request()->except(['province','city','page'])) }}"
       style="display:block;margin-top:10px;font-size:12px;color:var(--red);text-align:center">✕ Clear location</a>
  @endif
</div>

<script>
function lfLoadCities(uid, routeName, province) {
  var citySel = document.getElementById(uid + '_city');
  citySel.innerHTML = '<option value="">Loading…</option>';
  fetch('{{ route('locations.cities') }}?province=' + encodeURIComponent(province))
    .then(r => r.json())
    .then(cities => {
      citySel.innerHTML = '<option value="">All Cities</option>';
      cities.forEach(c => {
        var o = document.createElement('option');
        o.value = c; o.textContent = c;
        citySel.appendChild(o);
      });
      // Navigate immediately with new province, no city
      lfGo(routeName, province, '');
    });
}

function lfGo(routeName, province, city) {
  var url = new URL(window.location.href);
  var params = new URLSearchParams(url.search);
  params.delete('page');
  if (province) params.set('province', province); else params.delete('province');
  if (city)     params.set('city', city);         else params.delete('city');
  window.location.href = url.pathname + '?' + params.toString();
}
</script>
