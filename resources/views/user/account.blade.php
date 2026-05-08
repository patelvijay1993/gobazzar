@extends('layouts.app')
@section('title', 'My Account')

@push('styles')
<style>
.acct-wrap{max-width:1100px;margin:24px auto;padding:0 20px;display:grid;grid-template-columns:240px 1fr;gap:20px;align-items:start}
@media(max-width:768px){.acct-wrap{grid-template-columns:1fr;padding:0 14px}.form-row{grid-template-columns:1fr}.acct-sidebar{position:static}}
.acct-sidebar{background:var(--surface);border:1.5px solid var(--border);border-radius:var(--rl);overflow:hidden;position:sticky;top:72px}
.acct-profile{padding:24px;text-align:center;border-bottom:1px solid var(--border)}
.acct-avatar{width:72px;height:72px;border-radius:50%;object-fit:cover;border:3px solid var(--border2);margin:0 auto 12px;display:block}
.acct-avatar-placeholder{width:72px;height:72px;border-radius:50%;background:var(--red);color:#fff;display:flex;align-items:center;justify-content:center;font-size:28px;font-family:var(--fh);font-weight:800;margin:0 auto 12px}
.acct-name{font-family:var(--fh);font-size:15px;font-weight:700}
.acct-email{font-size:11.5px;color:var(--muted);margin-top:2px}
.acct-menu{padding:8px 0}
.acct-mi{display:flex;align-items:center;gap:10px;padding:10px 18px;font-size:13px;cursor:pointer;transition:background .12s;color:var(--text);border-left:3px solid transparent}
.acct-mi:hover{background:var(--red-pale);color:var(--red)}
.acct-mi.active{background:var(--red-pale);color:var(--red);border-left-color:var(--red);font-weight:600}

.post-btn{display:block;margin:12px 16px;background:var(--red);color:#fff;border-radius:var(--r);padding:10px 14px;font-size:13px;font-weight:700;text-align:center;transition:background .15s}
.post-btn:hover{background:var(--red-dark);color:#fff}

.acct-main{display:flex;flex-direction:column;gap:16px}
.panel{background:var(--surface);border:1.5px solid var(--border);border-radius:var(--rl);overflow:hidden;display:none}
.panel.active{display:block}
.panel-head{background:var(--dark);color:#fff;padding:12px 20px;font-family:var(--fh);font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:.8px;display:flex;align-items:center;justify-content:space-between}
.panel-body{padding:24px}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:14px}
.form-group{margin-bottom:16px}
.form-label{display:block;font-size:12px;font-weight:600;color:var(--text);margin-bottom:5px;text-transform:uppercase;letter-spacing:.5px}
.form-input{width:100%;border:1.5px solid var(--border2);border-radius:var(--r);padding:10px 14px;font-size:13.5px;transition:border .15s;background:var(--surface)}
.form-input:focus{border-color:var(--red);outline:none}
.btn-save{padding:10px 24px;background:var(--red);color:#fff;border-radius:var(--r);font-size:13px;font-weight:600;cursor:pointer;border:none;transition:background .15s}
.btn-save:hover{background:var(--red-dark)}
.flash{padding:10px 16px;border-radius:var(--r);margin-bottom:16px;font-size:13px}
.flash-success{background:var(--green-bg);color:var(--green);border:1px solid #bbf7d0}
.flash-error{background:var(--red-pale);color:var(--red);border:1px solid #fecaca}

.sub-row{display:flex;gap:12px;padding:12px 0;border-bottom:1px solid var(--border);align-items:center}
.sub-row:last-child{border-bottom:none}
.sub-thumb{width:50px;height:44px;border-radius:8px;overflow:hidden;background:var(--bg);display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0}
.sub-thumb img{width:100%;height:100%;object-fit:cover}
.sub-title{font-size:13px;font-weight:500;line-height:1.3}
.sub-meta{font-size:11px;color:var(--muted);margin-top:2px}
.status-badge{font-size:9.5px;font-weight:700;padding:2px 8px;border-radius:20px}
.status-active{background:var(--green-bg);color:var(--green)}
.status-pending{background:var(--amber-bg);color:var(--amber)}
.status-rejected,.status-inactive{background:var(--red-pale);color:var(--red)}
.status-draft{background:#f1f5f9;color:#64748b}
.empty-state{text-align:center;padding:30px;color:var(--muted);font-size:13px}
.row-actions{display:flex;gap:6px;flex-shrink:0}
.btn-edit{font-size:11px;padding:4px 10px;border-radius:6px;border:1.5px solid var(--border2);color:var(--muted);cursor:pointer;text-decoration:none;transition:all .15s}
.btn-edit:hover{border-color:var(--blue);color:var(--blue)}
.btn-del{font-size:11px;padding:4px 10px;border-radius:6px;border:1.5px solid var(--border2);color:var(--muted);cursor:pointer;background:none;transition:all .15s}
.btn-del:hover{border-color:var(--red);color:var(--red)}

/* Sub-tabs inside panels */
.sub-tabs{display:flex;gap:0;border-bottom:2px solid var(--border);margin-bottom:20px;overflow-x:auto}
.sub-tab{padding:8px 16px;font-size:12.5px;font-weight:600;color:var(--muted);cursor:pointer;border-bottom:2px solid transparent;margin-bottom:-2px;white-space:nowrap}
.sub-tab.active{color:var(--red);border-bottom-color:var(--red)}
.sub-panel{display:none}.sub-panel.active{display:block}
</style>
@endpush

@section('content')
<div class="acct-wrap">
  <aside class="acct-sidebar">
    <div class="acct-profile">
      @if($user->avatar)
        <img src="{{ asset('storage/'.$user->avatar) }}" alt="{{ $user->name }}" class="acct-avatar">
      @else
        <div class="acct-avatar-placeholder">{{ strtoupper(substr($user->name,0,1)) }}</div>
      @endif
      <div class="acct-name">{{ $user->name }}</div>
      <div class="acct-email">{{ $user->email }}</div>
      @if($user->city)<div style="font-size:11px;color:var(--hint);margin-top:4px">📍 {{ $user->city }}</div>@endif
    </div>

    <a href="{{ route('post.create') }}" class="post-btn">+ Post Something</a>

    <nav class="acct-menu">
      <a href="#" class="acct-mi active" onclick="showPanel('submissions',this)">📋 My Submissions</a>
      <a href="#" class="acct-mi" onclick="showPanel('profile',this)">👤 Edit Profile</a>
      <a href="#" class="acct-mi" onclick="showPanel('password',this)">🔒 Change Password</a>
      <a href="{{ route('home') }}" class="acct-mi">🏠 Browse Site</a>
      <a href="#" onclick="event.preventDefault();document.getElementById('logout-form').submit()" class="acct-mi" style="color:var(--red)">🚪 Logout</a>
    </nav>
    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display:none">@csrf</form>
  </aside>

  <div class="acct-main">
    @if(session('success'))
      <div class="flash flash-success">{{ session('success') }}</div>
    @endif

    {{-- MY SUBMISSIONS --}}
    <div class="panel active" id="panel-submissions">
      <div class="panel-head">
        <span>My Submissions</span>
        <a href="{{ route('post.create') }}" style="background:var(--red);color:#fff;font-size:11px;padding:4px 12px;border-radius:6px;font-weight:600">+ New Post</a>
      </div>
      <div class="panel-body">
        @php
          $totalCount = $listings->count() + $jobs->count() + $events->count() + $businesses->count() + $matrimonials->count();
        @endphp

        @if($totalCount === 0)
          <div class="empty-state">
            <div style="font-size:40px;margin-bottom:10px">📭</div>
            <p>You haven't posted anything yet.</p>
            <a href="{{ route('post.create') }}" style="color:var(--red);font-weight:600;margin-top:8px;display:inline-block">Post something now →</a>
          </div>
        @else
          <div class="sub-tabs">
            @if($listings->isNotEmpty())<div class="sub-tab active" onclick="showSubTab('classifieds',this)">🏷️ Classifieds ({{ $listings->count() }})</div>@endif
            @if($jobs->isNotEmpty())<div class="sub-tab {{ $listings->isEmpty() ? 'active' : '' }}" onclick="showSubTab('jobs',this)">💼 Jobs ({{ $jobs->count() }})</div>@endif
            @if($events->isNotEmpty())<div class="sub-tab {{ $listings->isEmpty() && $jobs->isEmpty() ? 'active' : '' }}" onclick="showSubTab('events',this)">🎉 Events ({{ $events->count() }})</div>@endif
            @if($businesses->isNotEmpty())<div class="sub-tab {{ $listings->isEmpty() && $jobs->isEmpty() && $events->isEmpty() ? 'active' : '' }}" onclick="showSubTab('biz',this)">🏢 Directory ({{ $businesses->count() }})</div>@endif
            @if($matrimonials->isNotEmpty())<div class="sub-tab {{ $listings->isEmpty() && $jobs->isEmpty() && $events->isEmpty() && $businesses->isEmpty() ? 'active' : '' }}" onclick="showSubTab('mat',this)">💍 Matrimonial ({{ $matrimonials->count() }})</div>@endif
          </div>

          {{-- Classifieds --}}
          @if($listings->isNotEmpty())
          <div class="sub-panel active" id="sub-classifieds">
            @foreach($listings as $item)
            <div class="sub-row">
              <div class="sub-thumb">
                @if($item->image)<img src="{{ asset('storage/'.$item->image) }}" alt="">
                @else {{ $item->category->icon ?? '🏷️' }} @endif
              </div>
              <div style="flex:1;min-width:0">
                <div class="sub-title">{{ $item->title }}</div>
                <div class="sub-meta">{{ $item->category->name ?? '' }} · {{ $item->location }} · {{ $item->created_at->format('d M Y') }}</div>
              </div>
              <div style="display:flex;flex-direction:column;align-items:flex-end;gap:6px;flex-shrink:0">
                <span class="status-badge status-{{ $item->status }}">{{ ucfirst($item->status) }}</span>
                <div class="row-actions">
                  <a href="{{ route('post.edit', ['type'=>'classified','id'=>$item->id]) }}" class="btn-edit">Edit</a>
                  <form method="POST" action="{{ route('post.destroy', ['type'=>'classified','id'=>$item->id]) }}" onsubmit="return confirm('Delete this post?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-del">Delete</button>
                  </form>
                </div>
              </div>
            </div>
            @endforeach
          </div>
          @endif

          {{-- Jobs --}}
          @if($jobs->isNotEmpty())
          <div class="sub-panel {{ $listings->isEmpty() ? 'active' : '' }}" id="sub-jobs">
            @foreach($jobs as $item)
            <div class="sub-row">
              <div class="sub-thumb">
                @if($item->company_logo)<img src="{{ asset('storage/'.$item->company_logo) }}" alt="">
                @else 💼 @endif
              </div>
              <div style="flex:1;min-width:0">
                <div class="sub-title">{{ $item->title }}</div>
                <div class="sub-meta">{{ $item->company }} · {{ $item->city }} · {{ $item->created_at->format('d M Y') }}</div>
              </div>
              <div style="display:flex;flex-direction:column;align-items:flex-end;gap:6px;flex-shrink:0">
                <span class="status-badge status-{{ $item->status }}">{{ ucfirst($item->status) }}</span>
                <div class="row-actions">
                  <a href="{{ route('post.edit', ['type'=>'job','id'=>$item->id]) }}" class="btn-edit">Edit</a>
                  <form method="POST" action="{{ route('post.destroy', ['type'=>'job','id'=>$item->id]) }}" onsubmit="return confirm('Delete this post?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-del">Delete</button>
                  </form>
                </div>
              </div>
            </div>
            @endforeach
          </div>
          @endif

          {{-- Events --}}
          @if($events->isNotEmpty())
          <div class="sub-panel {{ $listings->isEmpty() && $jobs->isEmpty() ? 'active' : '' }}" id="sub-events">
            @foreach($events as $item)
            <div class="sub-row">
              <div class="sub-thumb">
                @if($item->image)<img src="{{ asset('storage/'.$item->image) }}" alt="">
                @else 🎉 @endif
              </div>
              <div style="flex:1;min-width:0">
                <div class="sub-title">{{ $item->title }}</div>
                <div class="sub-meta">{{ $item->city }} · {{ $item->start_date?->format('d M Y') }} · {{ $item->created_at->format('d M Y') }}</div>
              </div>
              <div style="display:flex;flex-direction:column;align-items:flex-end;gap:6px;flex-shrink:0">
                <span class="status-badge status-{{ $item->status }}">{{ ucfirst($item->status) }}</span>
                <div class="row-actions">
                  <a href="{{ route('post.edit', ['type'=>'event','id'=>$item->id]) }}" class="btn-edit">Edit</a>
                  <form method="POST" action="{{ route('post.destroy', ['type'=>'event','id'=>$item->id]) }}" onsubmit="return confirm('Delete this post?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-del">Delete</button>
                  </form>
                </div>
              </div>
            </div>
            @endforeach
          </div>
          @endif

          {{-- Businesses --}}
          @if($businesses->isNotEmpty())
          <div class="sub-panel {{ $listings->isEmpty() && $jobs->isEmpty() && $events->isEmpty() ? 'active' : '' }}" id="sub-biz">
            @foreach($businesses as $item)
            <div class="sub-row">
              <div class="sub-thumb">
                @if($item->logo)<img src="{{ asset('storage/'.$item->logo) }}" alt="">
                @elseif($item->image)<img src="{{ asset('storage/'.$item->image) }}" alt="">
                @else 🏢 @endif
              </div>
              <div style="flex:1;min-width:0">
                <div class="sub-title">{{ $item->name }}</div>
                <div class="sub-meta">{{ $item->category->name ?? '' }} · {{ $item->city }} · {{ $item->created_at->format('d M Y') }}</div>
              </div>
              <div style="display:flex;flex-direction:column;align-items:flex-end;gap:6px;flex-shrink:0">
                <span class="status-badge status-{{ $item->status }}">{{ ucfirst($item->status) }}</span>
                <div class="row-actions">
                  <a href="{{ route('post.edit', ['type'=>'business','id'=>$item->id]) }}" class="btn-edit">Edit</a>
                  <form method="POST" action="{{ route('post.destroy', ['type'=>'business','id'=>$item->id]) }}" onsubmit="return confirm('Delete this post?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-del">Delete</button>
                  </form>
                </div>
              </div>
            </div>
            @endforeach
          </div>
          @endif

          {{-- Matrimonial --}}
          @if($matrimonials->isNotEmpty())
          <div class="sub-panel {{ $listings->isEmpty() && $jobs->isEmpty() && $events->isEmpty() && $businesses->isEmpty() ? 'active' : '' }}" id="sub-mat">
            @foreach($matrimonials as $item)
            <div class="sub-row">
              <div class="sub-thumb">
                @if($item->photo)<img src="{{ asset('storage/'.$item->photo) }}" alt="">
                @else {{ $item->gender === 'male' ? '👨' : '👩' }} @endif
              </div>
              <div style="flex:1;min-width:0">
                <div class="sub-title">{{ $item->name }}</div>
                <div class="sub-meta">{{ $item->age }} yrs · {{ $item->city }} · {{ $item->created_at->format('d M Y') }}</div>
              </div>
              <div style="display:flex;flex-direction:column;align-items:flex-end;gap:6px;flex-shrink:0">
                <span class="status-badge status-{{ $item->status }}">{{ ucfirst($item->status) }}</span>
                <div class="row-actions">
                  <a href="{{ route('post.edit', ['type'=>'matrimonial','id'=>$item->id]) }}" class="btn-edit">Edit</a>
                  <form method="POST" action="{{ route('post.destroy', ['type'=>'matrimonial','id'=>$item->id]) }}" onsubmit="return confirm('Delete this post?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn-del">Delete</button>
                  </form>
                </div>
              </div>
            </div>
            @endforeach
          </div>
          @endif
        @endif
      </div>
    </div>

    {{-- EDIT PROFILE --}}
    <div class="panel" id="panel-profile">
      <div class="panel-head"><span>Edit Profile</span></div>
      <div class="panel-body">
        @if($errors->has('name') || $errors->has('phone'))
          <div class="flash flash-error">Please fix the errors below.</div>
        @endif
        <form method="POST" action="{{ route('account.profile') }}" enctype="multipart/form-data">
          @csrf @method('PATCH')
          <div class="form-row">
            <div class="form-group">
              <label class="form-label">Full Name</label>
              <input type="text" name="name" value="{{ old('name', $user->name) }}" class="form-input" required>
            </div>
            <div class="form-group">
              <label class="form-label">Phone</label>
              <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="form-input" placeholder="+1 647 xxx xxxx">
            </div>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label class="form-label">City</label>
              <input type="text" name="city" value="{{ old('city', $user->city) }}" class="form-input" placeholder="Toronto">
            </div>
            <div class="form-group">
              <label class="form-label">Province</label>
              <input type="text" name="province" value="{{ old('province', $user->province) }}" class="form-input" placeholder="ON">
            </div>
          </div>
          <div class="form-group">
            <label class="form-label">Bio</label>
            <textarea name="bio" class="form-input" rows="3" placeholder="Tell the community about yourself...">{{ old('bio', $user->bio) }}</textarea>
          </div>
          <div class="form-group">
            <label class="form-label">Profile Photo</label>
            @if($user->avatar)
              <div style="margin-bottom:8px"><img src="{{ asset('storage/'.$user->avatar) }}" style="width:56px;height:56px;border-radius:50%;object-fit:cover;border:2px solid var(--border2)"></div>
            @endif
            <input type="file" name="avatar" class="form-input" accept="image/*">
          </div>
          <button type="submit" class="btn-save">Save Profile</button>
        </form>
      </div>
    </div>

    {{-- CHANGE PASSWORD --}}
    <div class="panel" id="panel-password">
      <div class="panel-head"><span>Change Password</span></div>
      <div class="panel-body">
        @error('current_password')<div class="flash flash-error">{{ $message }}</div>@enderror
        <form method="POST" action="{{ route('account.password') }}">
          @csrf @method('PATCH')
          <div class="form-group">
            <label class="form-label">Current Password</label>
            <input type="password" name="current_password" class="form-input" required>
          </div>
          <div class="form-row">
            <div class="form-group">
              <label class="form-label">New Password</label>
              <input type="password" name="password" class="form-input" placeholder="Min 8 characters" required>
            </div>
            <div class="form-group">
              <label class="form-label">Confirm Password</label>
              <input type="password" name="password_confirmation" class="form-input" required>
            </div>
          </div>
          <button type="submit" class="btn-save">Update Password</button>
        </form>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
function showPanel(name, el) {
  event.preventDefault();
  document.querySelectorAll('.panel').forEach(p => p.classList.remove('active'));
  document.querySelectorAll('.acct-mi').forEach(m => m.classList.remove('active'));
  document.getElementById('panel-' + name).classList.add('active');
  el.classList.add('active');
}
function showSubTab(name, el) {
  document.querySelectorAll('.sub-panel').forEach(p => p.classList.remove('active'));
  document.querySelectorAll('.sub-tab').forEach(t => t.classList.remove('active'));
  document.getElementById('sub-' + name).classList.add('active');
  el.classList.add('active');
}
</script>
@endpush
@endsection
