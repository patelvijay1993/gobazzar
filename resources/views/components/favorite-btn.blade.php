@auth
  @if(auth()->user()->hasFavorites())
    @php
      $isFav = auth()->user()->userFavorites()
          ->where('favoriteable_type', $modelClass)
          ->where('favoriteable_id', $modelId)
          ->exists();
    @endphp
    <button
      class="fav-btn {{ $isFav ? 'fav-active' : '' }}"
      data-type="{{ $type }}"
      data-id="{{ $modelId }}"
      title="{{ $isFav ? 'Remove from Saved' : 'Save to Favorites' }}"
      style="background:{{ $isFav ? '#fee2e2' : '#f3f4f6' }};border:1.5px solid {{ $isFav ? '#fca5a5' : '#e5e7eb' }};color:{{ $isFav ? '#dc2626' : '#9ca3af' }};border-radius:9px;padding:8px 14px;font-size:13px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:6px;transition:all .15s">
      <i class="fa-{{ $isFav ? 'solid' : 'regular' }} fa-heart"></i>
      <span class="fav-label">{{ $isFav ? 'Saved' : 'Save' }}</span>
    </button>
  @else
    <a href="{{ route('pricing') }}" title="Upgrade to save favorites"
      style="background:#f3f4f6;border:1.5px solid #e5e7eb;color:#9ca3af;border-radius:9px;padding:8px 14px;font-size:13px;font-weight:600;display:inline-flex;align-items:center;gap:6px;text-decoration:none">
      <i class="fa-regular fa-heart"></i> Save
    </a>
  @endif
@else
  <a href="{{ route('login') }}" title="Login to save favorites"
    style="background:#f3f4f6;border:1.5px solid #e5e7eb;color:#9ca3af;border-radius:9px;padding:8px 14px;font-size:13px;font-weight:600;display:inline-flex;align-items:center;gap:6px;text-decoration:none">
    <i class="fa-regular fa-heart"></i> Save
  </a>
@endauth
