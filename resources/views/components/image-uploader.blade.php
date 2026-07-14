@props([
    'name'     => 'images',
    'multiple' => true,
    'max'      => 5,
    'min'      => 0,
    'label'    => 'Photos',
    'hint'     => null,
])
@php
  $uid      = 'iu' . Str::random(7);
  $maxBytes = 1024 * 1024;
  $accept   = 'image/jpeg,image/png,image/webp,image/gif';
@endphp

<div class="img-uploader" id="{{ $uid }}_wrap">
  <label class="form-label">{{ $label }}</label>
  @if($hint)<div style="font-size:11px;color:var(--hint);margin-bottom:4px">{{ $hint }}</div>@endif

  <div class="img-dropzone" id="{{ $uid }}_zone"
       onclick="document.getElementById('{{ $uid }}_input').click()"
       ondragover="event.preventDefault();this.classList.add('drag-over')"
       ondragleave="this.classList.remove('drag-over')"
       ondrop="_iu_drop(event,'{{ $uid }}')">
    <div class="img-dropzone-inner">
      <div class="img-dropzone-icon">🖼️</div>
      <div class="img-dropzone-text">
        <strong>Click to upload</strong> or drag & drop<br>
        <span>JPG, PNG, WEBP · Auto-compressed to WebP{{ $multiple ? ' · Up to '.$max.' photos' : '' }}{{ $min > 0 ? ' · Min '.$min.' required' : '' }}</span>
      </div>
    </div>
  </div>

  <input type="file"
         id="{{ $uid }}_input"
         name="{{ $name }}{{ $multiple ? '[]' : '' }}"
         accept="{{ $accept }}"
         @if($multiple) multiple @endif
         style="display:none"
         onchange="_iu_handle(this,'{{ $uid }}')">

  <div class="img-preview-grid" id="{{ $uid }}_grid"></div>
  <div id="{{ $uid }}_errors"></div>
</div>

{{-- Shared CSS — only emit once per page --}}
@once
<style>
.img-uploader{margin-bottom:0}
.img-dropzone{border:2px dashed var(--border2);border-radius:var(--r);background:var(--bg);cursor:pointer;transition:all .15s;margin-top:6px}
.img-dropzone:hover,.img-dropzone.drag-over{border-color:var(--red);background:var(--red-pale)}
.img-dropzone-inner{padding:22px 20px;display:flex;align-items:center;gap:16px}
.img-dropzone-icon{font-size:30px;flex-shrink:0}
.img-dropzone-text{font-size:13px;color:var(--muted);line-height:1.5}
.img-dropzone-text strong{color:var(--text)}
.img-dropzone-text span{font-size:11px}
.img-preview-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(110px,1fr));gap:10px;margin-top:10px}
.img-preview-item{position:relative;border:1.5px solid var(--border);border-radius:var(--r);overflow:hidden;background:#f3f3f3}
.img-preview-item img{width:100%;height:96px;object-fit:cover;display:block}
.img-preview-item.has-error{border-color:#E74C3C}
.img-preview-meta{padding:5px 7px;font-size:10px;color:var(--muted);line-height:1.45;background:var(--surface)}
.img-preview-meta .ratio{font-weight:700;color:var(--text)}
.img-preview-meta .sz{color:var(--hint)}
.img-preview-meta .sz.oversize{color:#C0392B;font-weight:600}
.img-rm{position:absolute;top:4px;right:4px;width:22px;height:22px;background:rgba(0,0,0,.6);color:#fff;border-radius:50%;font-size:12px;display:flex;align-items:center;justify-content:center;cursor:pointer;border:none;line-height:1;padding:0;transition:background .15s}
.img-rm:hover{background:#C0392B}
.img-err-msg{font-size:11.5px;color:#C0392B;background:#FEF2F1;border:1px solid #fecaca;border-radius:var(--r);padding:6px 10px;margin-top:6px}
@media(max-width:480px){.img-dropzone-inner{flex-direction:column;text-align:center;gap:8px;padding:16px}}
</style>
@endonce

{{-- Shared JS engine — only emit once per page --}}
@once
<script>
window._iuReg = {};   // uid → { files:[{file,blob,url,w,h,valid,converting}], isMulti, maxFiles, minFiles, maxBytes }

var _IU_MAX_W    = 1600;  // max dimension before downscale
var _IU_QUALITY  = 0.82;  // WebP quality

// Convert a raw File → compressed WebP Blob via Canvas, then call cb(blob)
function _iu_compress(file, cb) {
  var img = new Image();
  var objUrl = URL.createObjectURL(file);
  img.onload = function() {
    var w = img.naturalWidth, h = img.naturalHeight;
    if (w > _IU_MAX_W) { h = Math.round(h * _IU_MAX_W / w); w = _IU_MAX_W; }
    var canvas = document.createElement('canvas');
    canvas.width = w; canvas.height = h;
    canvas.getContext('2d').drawImage(img, 0, 0, w, h);
    URL.revokeObjectURL(objUrl);
    canvas.toBlob(function(blob) { cb(blob, w, h); }, 'image/webp', _IU_QUALITY);
  };
  img.onerror = function() { URL.revokeObjectURL(objUrl); cb(null, 0, 0); };
  img.src = objUrl;
}

function _iu_handle(input, uid) {
  _iu_process(Array.from(input.files), uid);
  input.value = '';
}

function _iu_drop(e, uid) {
  e.preventDefault();
  document.getElementById(uid + '_zone').classList.remove('drag-over');
  _iu_process(
    Array.from(e.dataTransfer.files).filter(function(f){ return f.type.startsWith('image/'); }),
    uid
  );
}

function _iu_process(incoming, uid) {
  var cfg    = window._iuReg[uid];
  if (!cfg) return;
  var errDiv = document.getElementById(uid + '_errors');
  errDiv.innerHTML = '';

  var toAdd = cfg.isMulti ? incoming : incoming.slice(0, 1);

  if (!cfg.isMulti) {
    cfg.files.forEach(function(e){ if (e.url) URL.revokeObjectURL(e.url); });
    cfg.files = [];
  } else if (cfg.files.length + toAdd.length > cfg.maxFiles) {
    toAdd = toAdd.slice(0, cfg.maxFiles - cfg.files.length);
    _iu_err(errDiv, 'Maximum ' + cfg.maxFiles + ' photos allowed. Extra files ignored.');
  }

  toAdd.forEach(function(file) {
    var entry = { file: file, blob: null, url: null, w: 0, h: 0, valid: false, converting: true };
    cfg.files.push(entry);
    var idx = cfg.files.length - 1;

    // Show placeholder immediately
    _iu_render(uid);

    _iu_compress(file, function(blob, w, h) {
      if (!blob) {
        // compression failed — fall back to original
        blob = file; w = 0; h = 0;
      }
      entry.blob       = blob;
      entry.w          = w;
      entry.h          = h;
      entry.valid      = blob.size <= cfg.maxBytes;
      entry.converting = false;
      entry.url        = URL.createObjectURL(blob);
      _iu_render(uid);
      _iu_sync(uid);
    });
  });
}

function _iu_render(uid) {
  var cfg  = window._iuReg[uid];
  var grid = document.getElementById(uid + '_grid');
  grid.innerHTML = '';

  cfg.files.forEach(function(entry, idx) {
    var item = document.createElement('div');
    item.className = 'img-preview-item' + (!entry.converting && !entry.valid ? ' has-error' : '');

    if (entry.converting) {
      // Spinner placeholder while compressing
      var spin = document.createElement('div');
      spin.style.cssText = 'height:96px;display:flex;align-items:center;justify-content:center;font-size:11px;color:var(--hint)';
      spin.textContent = 'Compressing…';
      item.appendChild(spin);
      grid.appendChild(item);
      return;
    }

    var img = document.createElement('img');
    img.src = entry.url;

    var rm = document.createElement('button');
    rm.type = 'button';
    rm.className = 'img-rm';
    rm.innerHTML = '✕';
    rm.setAttribute('data-uid', uid);
    rm.setAttribute('data-idx', idx);
    rm.onclick = function() {
      var u = this.getAttribute('data-uid');
      var i = parseInt(this.getAttribute('data-idx'), 10);
      _iu_remove(u, i);
    };

    var meta = document.createElement('div');
    meta.className = 'img-preview-meta';

    var sizeKB = Math.round(entry.blob.size / 1024);
    var sizeMB = (entry.blob.size / (1024 * 1024)).toFixed(2);
    var dimStr = entry.w && entry.h ? entry.w + '×' + entry.h : 'WebP';
    meta.innerHTML = '<span class="ratio">' + dimStr + '</span><br>'
      + '<span class="sz' + (entry.valid ? '' : ' oversize') + '">'
      + (entry.valid ? sizeKB + ' KB · WebP' : '⚠ ' + sizeMB + ' MB — over 1 MB')
      + '</span>';

    item.appendChild(img);
    item.appendChild(rm);
    item.appendChild(meta);
    grid.appendChild(item);
  });
}

function _iu_remove(uid, idx) {
  var cfg = window._iuReg[uid];
  if (!cfg || !cfg.files[idx]) return;
  var e = cfg.files[idx];
  if (e.url) URL.revokeObjectURL(e.url);
  cfg.files.splice(idx, 1);
  _iu_render(uid);
  _iu_sync(uid);
}

function _iu_sync(uid) {
  var cfg    = window._iuReg[uid];
  var errDiv = document.getElementById(uid + '_errors');
  errDiv.innerHTML = '';

  var oversize = cfg.files.filter(function(e){ return !e.converting && !e.valid; });
  if (oversize.length > 0) {
    _iu_err(errDiv, oversize.length + ' photo(s) exceed 1 MB even after compression and cannot be uploaded.');
  }
}

// On form submit: inject compressed WebP blobs into the form via DataTransfer
function _iu_injectFiles(form) {
  Object.keys(window._iuReg).forEach(function(uid) {
    var cfg   = window._iuReg[uid];
    var input = document.getElementById(uid + '_input');
    if (!input || !input.closest('form') || input.closest('form') !== form) return;

    var validFiles = cfg.files.filter(function(e){ return !e.converting && e.valid; });

    try {
      var dt = new DataTransfer();
      validFiles.forEach(function(entry, i) {
        var ext  = entry.file.name.replace(/\.[^.]+$/, '');
        var name = ext + '.webp';
        var webpFile = new File([entry.blob], name, { type: 'image/webp' });
        dt.items.add(webpFile);
      });
      input.files = dt.files;
      input.style.display = 'none';
    } catch(ex) {
      var inputName = input.name;
      input.parentNode.removeChild(input);
      validFiles.forEach(function(entry) {
        var fresh = document.createElement('input');
        fresh.type  = 'file';
        fresh.name  = inputName;
        fresh.style.display = 'none';
        try {
          var ext  = entry.file.name.replace(/\.[^.]+$/, '');
          var dt2  = new DataTransfer();
          dt2.items.add(new File([entry.blob], ext + '.webp', { type: 'image/webp' }));
          fresh.files = dt2.files;
        } catch(e2) {}
        form.appendChild(fresh);
      });
    }
  });
}

function _iu_err(container, msg) {
  var d = document.createElement('div');
  d.className = 'img-err-msg';
  d.textContent = msg;
  container.appendChild(d);
}

function _iu_ratio(w, h) {
  function gcd(a, b){ return b === 0 ? a : gcd(b, a % b); }
  var g = gcd(w, h);
  return (w / g) + ':' + (h / g);
}
</script>
@endonce

{{-- Per-instance registry init + form submit hook --}}
<script>
window._iuReg['{{ $uid }}'] = {
  files:    [],
  isMulti:  {{ $multiple ? 'true' : 'false' }},
  maxFiles: {{ $max }},
  minFiles: {{ $min }},
  maxBytes: {{ $maxBytes }}
};

// Hook the parent form once — use a flag so multiple uploaders on same form don't double-bind
(function(){
  var input = document.getElementById('{{ $uid }}_input');
  var form  = input ? input.closest('form') : null;
  if (!form || form._iuHooked) return;
  form._iuHooked = true;
  form.addEventListener('submit', function(e) {
    var hasError = false;
    Object.keys(window._iuReg).forEach(function(u) {
      var inp = document.getElementById(u + '_input');
      if (!inp || !inp.closest || inp.closest('form') !== form) return;
      var cfg = window._iuReg[u];

      // Still compressing — block submit
      var converting = cfg.files.filter(function(f){ return f.converting; });
      if (converting.length > 0) {
        var errDiv = document.getElementById(u + '_errors');
        if (errDiv) _iu_err(errDiv, 'Photos are still being compressed. Please wait a moment.');
        hasError = true;
        return;
      }

      var validFiles = cfg.files.filter(function(f){ return f.valid; });

      // Oversize check (after compression)
      var oversize = cfg.files.filter(function(f){ return !f.valid; });
      if (oversize.length > 0) { hasError = true; }

      // Minimum images check
      if (cfg.minFiles > 0 && validFiles.length < cfg.minFiles) {
        var errDiv = document.getElementById(u + '_errors');
        if (errDiv) _iu_err(errDiv, 'Please upload at least ' + cfg.minFiles + ' photo' + (cfg.minFiles > 1 ? 's' : '') + '.');
        var zone = document.getElementById(u + '_zone');
        if (zone) { zone.style.borderColor = '#E74C3C'; setTimeout(function(){ zone.style.borderColor = ''; }, 3000); }
        hasError = true;
      }
    });
    if (hasError) { e.preventDefault(); return; }
    _iu_injectFiles(form);
  });
})();
</script>
