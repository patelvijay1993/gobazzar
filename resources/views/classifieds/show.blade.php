@extends('layouts.app')
@section('title', $listing->title . ' — ' . ($listing->location ?: $listing->city) . ' | GoBazaar')
@section('description', Str::limit(strip_tags($listing->description ?? $listing->title . ' for sale in ' . ($listing->location ?: 'Canada') . '. Find the best deals on GoBazaar.'), 160))
@section('canonical', route('classifieds.show', $listing))
@section('og_type', 'product')
@section('og_title', $listing->title . ' — ' . ($listing->location ?: $listing->city))
@section('og_description', Str::limit(strip_tags($listing->description ?? $listing->title), 200))
@section('og_image', $listing->image_url ?? asset('images/og-default.jpg'))
@push('schema')
<script type="application/ld+json">
{!! json_encode(array_filter([
  '@context' => 'https://schema.org',
  '@type'    => 'Product',
  'name'     => $listing->title,
  'description' => Str::limit(strip_tags($listing->description ?? ''), 500),
  'url'      => route('classifieds.show', $listing),
  'image'    => $listing->image_url ?: null,
  'offers'   => ($listing->price && (float)$listing->price > 0) ? [
    '@type'        => 'Offer',
    'price'        => (string)$listing->price,
    'priceCurrency'=> 'CAD',
    'availability' => 'https://schema.org/InStock',
    'url'          => route('classifieds.show', $listing),
  ] : null,
  'seller' => [
    '@type' => 'Person',
    'name'  => $listing->user->name ?? 'Seller',
  ],
]), JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES|JSON_PRETTY_PRINT) !!}
</script>
@endpush

@push('styles')
<style>
/* Legacy var bridge */
body{--red:#1a3a8f;--red2:#e74c3c;--red-dark:#122970;--red-pale:#e8edf7;--border2:#e2e0db;--surface:#fff;--bg:#f9fafb;--hint:#9ca3af;--rl:14px;--r:8px;--amber:#92400e;--amber-bg:#fef9c3;--amber-light:#fef9c3;--dark:#1a3a8f;--dark2:#122970;--gold:#e8a020;--blue:#1d4ed8;--blue-bg:#eff6ff;--green:#16a34a;--green-bg:#dcfce7;}
.show-wrap{max-width:1200px;margin:24px auto;padding:0 20px;display:grid;grid-template-columns:1fr 300px;gap:24px;align-items:start}
@media(max-width:768px){
  .show-wrap{grid-template-columns:1fr;padding:0 12px;margin:14px auto;gap:14px}
  .listing-title{font-size:18px}
  .listing-price{font-size:22px}
  .listing-body{padding:16px}
}
@media(max-width:480px){
  .related-grid{grid-template-columns:1fr 1fr}
  .show-wrap{padding:0 10px}
  .listing-title{font-size:16px}
  .listing-price{font-size:20px}
}
.listing-main{background:var(--surface);border:1.5px solid var(--border);border-radius:var(--rl);overflow:hidden}
.listing-img-placeholder{width:100%;height:200px;background:#f3f3f3;font-size:60px;display:flex;align-items:center;justify-content:center}
.listing-body{padding:20px}
.listing-cat{font-size:11px;font-weight:600;color:var(--red);text-transform:uppercase;letter-spacing:.8px;margin-bottom:8px}
.listing-title{font-family:var(--fh);font-size:22px;font-weight:700;line-height:1.3;margin-bottom:10px}
.listing-meta{display:flex;gap:16px;flex-wrap:wrap;font-size:12px;color:var(--muted);margin-bottom:16px}
.listing-price{font-family:var(--fh);font-size:28px;font-weight:800;color:var(--red);margin-bottom:16px}
.listing-desc{font-size:13.5px;line-height:1.7;color:var(--text);border-top:1px solid var(--border);padding-top:16px}
.badge{font-size:10px;font-weight:700;padding:3px 9px;border-radius:20px;margin-right:4px}
.badge-feat{background:var(--gold);color:#fff}
.badge-ver{background:var(--green);color:#fff}
.badge-new{background:var(--blue);color:#fff}
.badge-hot{background:var(--red);color:#fff}
.tag{background:var(--bg);border:1px solid var(--border2);color:var(--muted);font-size:10px;padding:3px 9px;border-radius:20px;margin-right:4px;margin-top:4px;display:inline-block}

.sidebar-card{background:var(--surface);border:1.5px solid var(--border);border-radius:var(--rl);overflow:hidden;margin-bottom:16px}
.sidebar-head{background:var(--dark);color:#fff;padding:10px 14px;font-family:var(--fh);font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:.8px}
.sidebar-body{padding:16px}
.contact-row{display:flex;align-items:center;gap:10px;margin-bottom:11px;font-size:13.5px;color:var(--text)}
.contact-row .icon{width:32px;height:32px;border-radius:8px;background:var(--primary-light);color:var(--primary);display:flex;align-items:center;justify-content:center;font-size:14px;flex-shrink:0}
.contact-btn{display:flex;align-items:center;justify-content:center;gap:8px;width:100%;text-align:center;padding:12px;border-radius:9px;font-size:13.5px;font-weight:700;margin-top:10px;text-decoration:none;transition:all .18s}
.contact-btn-primary{background:var(--primary);color:#fff}
.contact-btn-primary:hover{background:var(--primary-dark)}
.contact-btn-outline{background:#fff;border:1.5px solid var(--primary);color:var(--primary)}
.contact-btn-outline:hover{background:var(--primary-light)}

.related-grid{display:grid;grid-template-columns:1fr 1fr;gap:8px;padding:10px}
.rel-card{border:1px solid var(--border);border-radius:var(--r);overflow:hidden;display:block;color:var(--text);transition:all .15s}
.rel-card:hover{border-color:var(--red2);transform:translateY(-2px)}
.rel-thumb{height:70px;background:#f3f3f3;overflow:hidden;display:flex;align-items:center;justify-content:center;font-size:24px}
.rel-thumb img{width:100%;height:100%;object-fit:cover}
.rel-body{padding:6px 8px}
.rel-title{font-size:11px;line-height:1.3;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden}
.rel-price{font-size:12px;font-weight:700;color:var(--red);font-family:var(--fh)}
</style>
@endpush

@section('content')
<div class="container" style="padding-top:8px">
  <div class="breadcrumb">
    <a href="{{ route('home') }}">Home</a>
    <span>›</span>
    <a href="{{ route('classifieds.index') }}">Classifieds</a>
    <span>›</span>
    <a href="{{ route('classifieds.index', ['category' => $listing->category_id]) }}">{{ $listing->category->name }}</a>
    <span>›</span>
    {{ Str::limit($listing->title, 50) }}
  </div>
</div>

<div class="show-wrap">
  <div class="listing-main">
    @php
        $rawImages = is_array($listing->images) ? $listing->images : [];
        if ($listing->image && $listing->image !== '0') $rawImages[] = $listing->image;
        $allImages = array_values(array_unique(array_filter($rawImages, fn($v) => !empty($v) && $v !== '0' && $v !== false)));
    @endphp
    @if(count($allImages))
      <x-image-slider :images="$allImages" :alt="$listing->title" height="380px" />
    @else
      <div class="listing-img-placeholder">{{ $listing->category->icon ?? '📦' }}</div>
    @endif
    <div class="listing-body">
      <div class="listing-cat">{{ $listing->category->icon }} {{ $listing->category->name }}</div>
      <div style="margin-bottom:8px">
        @foreach($listing->badges ?? [] as $badge)
          <span class="badge badge-{{ $badge }}">{{ strtoupper($badge) }}</span>
        @endforeach
      </div>
      <h1 class="listing-title">
        {{ $listing->title }}
        @if($listing->is_verified)
          <span class="badge badge-ver" style="vertical-align:middle;font-size:11px"><i class="fa-solid fa-circle-check"></i> Verified Seller</span>
        @endif
      </h1>
      <div class="listing-meta">
        <span>📍 {{ $listing->location }}{{ $listing->postal_code ? ' · '.$listing->postal_code : '' }}</span>
        <span>👁 {{ $listing->views }} views</span>
        <span>📅 {{ $listing->created_at->diffForHumans() }}</span>
      </div>
      @if($listing->price)
        <div class="listing-price">{{ $listing->formatted_price }}<span style="font-size:14px;font-weight:400;color:var(--muted);font-family:var(--fb)">{{ $listing->price_unit }}</span></div>
      @endif
      @if($listing->description)
        <div class="listing-desc">{!! clean($listing->description) !!}</div>
      @endif
      @if($listing->custom_fields && count(array_filter((array)$listing->custom_fields)))
        @php
          $categoryFields = $listing->category?->applicableFields() ?? collect();
          $fieldMap = $categoryFields->keyBy('key');
        @endphp
        <div style="margin-top:20px;border-top:1.5px solid var(--border);padding-top:16px">
          <div style="font-size:13px;font-weight:700;color:var(--muted);letter-spacing:.06em;text-transform:uppercase;margin-bottom:12px">Additional Details</div>
          <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(180px,1fr));gap:10px">
            @foreach((array)$listing->custom_fields as $key => $value)
              @if($value !== null && $value !== '')
                @php $field = $fieldMap->get($key); @endphp
                <div style="background:var(--bg-alt,#f8fafc);border:1px solid var(--border);border-radius:8px;padding:10px 14px">
                  <div style="font-size:11px;font-weight:600;color:var(--muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:3px">
                    {{ $field?->label ?? ucwords(str_replace('_',' ',$key)) }}
                  </div>
                  <div style="font-size:14px;font-weight:600;color:var(--text)">
                    @if(is_bool($value) || $value === '1' || $value === '0')
                      {{ $value ? 'Yes' : 'No' }}
                    @else
                      {{ $value }}
                    @endif
                  </div>
                </div>
              @endif
            @endforeach
          </div>
        </div>
      @endif

      @if($listing->tags)
        <div style="margin-top:14px">
          @foreach($listing->tags as $tag)
            <span class="tag">{{ $tag }}</span>
          @endforeach
        </div>
      @endif
    </div>
  </div>

  <div>
    {{-- ── Seller Profile Card ── --}}
    @if($listing->user)
    <div class="sidebar-card" style="margin-bottom:16px">
      <div class="sidebar-head">About the Seller</div>
      <div class="sidebar-body">
        <a href="{{ route('seller.profile', $listing->user) }}" style="text-decoration:none;color:inherit">
          <div style="display:flex;align-items:center;gap:12px;margin-bottom:12px">
            {{-- Avatar --}}
            @if($listing->user->avatar_url)
              <img src="{{ $listing->user->avatar_url }}" alt="{{ $listing->user->name }}"
                   style="width:52px;height:52px;border-radius:50%;object-fit:cover;border:2px solid var(--border);flex-shrink:0">
            @else
              <div style="width:52px;height:52px;border-radius:50%;background:var(--primary);color:#fff;display:flex;align-items:center;justify-content:center;font-size:22px;font-weight:700;flex-shrink:0">
                {{ strtoupper(substr($listing->user->name,0,1)) }}
              </div>
            @endif
            <div>
              <div style="font-weight:700;font-size:15px;color:var(--text)">{{ $listing->user->name }}</div>
              @if($listing->user->is_verified || $listing->is_verified)
                <div style="display:inline-flex;align-items:center;gap:4px;font-size:11px;font-weight:600;color:#15803d;background:#dcfce7;padding:2px 8px;border-radius:20px;margin-top:3px">
                  <i class="fa-solid fa-circle-check" style="font-size:10px"></i> Verified
                </div>
              @endif
            </div>
          </div>
          {{-- Post count --}}
          @php $postCount = \App\Models\Listing::where('user_id',$listing->user_id)->where('status','active')->count(); @endphp
          <div style="font-size:12px;color:var(--muted);margin-bottom:10px">
            <i class="fa-solid fa-list" style="margin-right:4px"></i>
            {{ $postCount }} active {{ Str::plural('listing', $postCount) }}
            &nbsp;·&nbsp;
            <i class="fa-regular fa-calendar" style="margin-right:4px"></i>
            Member since {{ $listing->user->created_at->format('M Y') }}
          </div>
          <div style="display:flex;align-items:center;justify-content:center;gap:6px;width:100%;background:var(--primary-light,#e8f4fd);color:var(--primary);border-radius:8px;padding:8px;font-size:13px;font-weight:600">
            <i class="fa-solid fa-store"></i> View All Listings
          </div>
        </a>
      </div>
    </div>
    @endif

    <div class="sidebar-card">
      <div class="sidebar-head">Contact Seller</div>
      <div class="sidebar-body">
        @if($listing->is_verified)
          <div style="background:#dcfce7;border:1px solid #86efac;border-radius:8px;padding:8px 12px;margin-bottom:12px;font-size:12px;color:#15803d;display:flex;align-items:center;gap:6px">
            <i class="fa-solid fa-circle-check"></i>
            <strong>Verified Seller</strong> — trusted, paid member
          </div>
        @endif
        @if($listing->contact_name)
          <div class="contact-row"><span class="icon"><i class="fa-solid fa-user"></i></span> {{ $listing->contact_name }}</div>
        @endif
        @if($listing->contact_phone && !optional($listing->user)->hide_phone)
          <div class="contact-row"><span class="icon"><i class="fa-solid fa-phone"></i></span> {{ $listing->contact_phone }}</div>
        @endif
        @if($listing->contact_email && !optional($listing->user)->hide_email)
          <div class="contact-row"><span class="icon"><i class="fa-solid fa-envelope"></i></span> {{ Str::limit($listing->contact_email, 24) }}</div>
        @endif
        {{-- Chat with Seller button --}}
        @auth
          @if(Auth::id() !== $listing->user_id)
            <button onclick="gcOpen('{{ route('chat.open.listing', $listing) }}')" class="contact-btn contact-btn-primary" style="background:var(--green);margin-bottom:8px;width:100%;border:none;cursor:pointer">
              <i class="fa-solid fa-comments"></i> Chat with Seller
            </button>
          @endif
        @else
          <a href="{{ route('login') }}" class="contact-btn contact-btn-primary" style="background:var(--green);margin-bottom:8px">
            <i class="fa-solid fa-comments"></i> Chat with Seller
          </a>
        @endauth

        @if($listing->contact_email && !optional($listing->user)->hide_email)
          <a href="mailto:{{ $listing->contact_email }}" class="contact-btn contact-btn-primary"><i class="fa-solid fa-envelope"></i> Send Email</a>
        @endif
        @if($listing->contact_phone && !optional($listing->user)->hide_phone)
          <a href="tel:{{ $listing->contact_phone }}" class="contact-btn contact-btn-outline"><i class="fa-solid fa-phone"></i> Call Now</a>
        @endif
      </div>
    </div>

    <div class="sidebar-card">
      <div class="sidebar-body" style="display:flex;align-items:center;justify-content:space-between;padding:12px 16px">
        <x-favorite-btn type="listing" :model-id="$listing->id" :model-class="\App\Models\Listing::class" />
        @auth
        <button onclick="openReportModal('listing', {{ $listing->id }})" style="background:none;border:none;color:var(--muted);font-size:12px;cursor:pointer;display:inline-flex;align-items:center;gap:5px;padding:6px 10px;border-radius:6px;transition:color .15s" onmouseover="this.style.color='#e74c3c'" onmouseout="this.style.color='var(--muted)'">
          <i class="fa-solid fa-flag"></i> Report this listing
        </button>
        @endauth
      </div>
    </div>

    @if($sellerListings->count())
      <div class="sidebar-card">
        <div class="sidebar-head" style="display:flex;justify-content:space-between;align-items:center">
          <span>More from Seller</span>
          <a href="{{ route('seller.profile', $listing->user) }}" style="font-size:11px;color:var(--primary);font-weight:600;text-decoration:none">View All →</a>
        </div>
        <div class="related-grid">
          @foreach($sellerListings->take(4) as $rel)
            <a href="{{ route('classifieds.show', $rel) }}" class="rel-card">
              <div class="rel-thumb">
                @if($rel->image_url)
                  <img src="{{ $rel->image_url }}" alt="{{ $rel->title }}">
                @else
                  {{ $rel->category->icon ?? '📦' }}
                @endif
              </div>
              <div class="rel-body">
                <div class="rel-title">{{ $rel->title }}</div>
                @if($rel->price)<div class="rel-price">{{ $rel->formatted_price }}</div>@endif
              </div>
            </a>
          @endforeach
        </div>
      </div>
    @endif

    @if($related->count())
      <div class="sidebar-card">
        <div class="sidebar-head">Similar Listings</div>
        <div class="related-grid">
          @foreach($related as $rel)
            <a href="{{ route('classifieds.show', $rel) }}" class="rel-card">
              <div class="rel-thumb">
                @if($rel->image_url)
                  <img src="{{ $rel->image_url }}" alt="{{ $rel->title }}">
                @else
                  {{ $rel->category->icon ?? '📦' }}
                @endif
              </div>
              <div class="rel-body">
                <div class="rel-title">{{ $rel->title }}</div>
                @if($rel->price)<div class="rel-price">{{ $rel->formatted_price }}</div>@endif
              </div>
            </a>
          @endforeach
        </div>
      </div>
    @endif
  </div>
</div>

@endsection
