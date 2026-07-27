@props([
    'name'     => 'images',
    'multiple' => true,
    'max'      => 5,
    'min'      => 0,
    'label'    => 'Photos',
    'hint'     => null,
    'crop'     => false,
    'aspect'   => null,   // e.g. '16/9', '1/1', '4/3', or null for free crop
])
@php
  $uid      = 'iu' . Str::random(7);
  $maxBytes = 1024 * 1024;
  $accept   = 'image/jpeg,image/png,image/webp,image/gif';

  // Convert aspect string to JS number
  if ($aspect === null) {
      $aspectJs = 'NaN';
  } else {
      $parts = explode('/', $aspect);
      $aspectJs = count($parts) === 2 ? ((int)$parts[0] / (int)$parts[1]) : (float)$aspect;
  }
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
        <span>JPG, PNG, WEBP · Auto-compressed to WebP{{ $multiple ? ' · Up to '.$max.' photos' : '' }}{{ $min > 0 ? ' · Min '.$min.' required' : '' }}{{ $crop ? ' · Crop available' : '' }}</span>
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
<link rel="stylesheet" href="/css/cropper.min.css">
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

/* Crop modal */
#_iu_crop_modal{display:none;position:fixed;inset:0;z-index:9999;background:rgba(0,0,0,.7);align-items:center;justify-content:center}
#_iu_crop_modal.open{display:flex}
#_iu_crop_box{background:#fff;border-radius:12px;overflow:hidden;max-width:92vw;max-height:90vh;display:flex;flex-direction:column;box-shadow:0 20px 60px rgba(0,0,0,.4);width:560px}
#_iu_crop_header{padding:12px 16px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid #eee;flex-shrink:0}
#_iu_crop_header h3{font-family:var(--fh,sans-serif);font-size:15px;font-weight:700;margin:0}
#_iu_crop_close{background:none;border:none;font-size:20px;cursor:pointer;color:#666;line-height:1;padding:0 4px}
#_iu_crop_img_wrap{flex:1;overflow:hidden;background:#111;max-height:60vh;display:flex;align-items:center;justify-content:center}
#_iu_crop_img_wrap img{max-width:100%;max-height:60vh;display:block}
#_iu_crop_footer{padding:12px 16px;display:flex;align-items:center;justify-content:space-between;gap:10px;border-top:1px solid #eee;flex-shrink:0;flex-wrap:wrap}
#_iu_crop_hint{font-size:11.5px;color:#666;flex:1}
#_iu_crop_actions{display:flex;gap:8px}
.iu-crop-btn{padding:8px 18px;border-radius:8px;font-size:13px;font-weight:600;cursor:pointer;border:none;transition:background .15s}
#_iu_crop_skip{background:#f3f3f3;color:#444}
#_iu_crop_skip:hover{background:#e5e5e5}
#_iu_crop_use{background:var(--red,#1a3a8f);color:#fff}
#_iu_crop_use:hover{background:var(--red-dark,#122970)}
</style>
@endonce

{{-- Shared JS engine — only emit once per page --}}
@once
<script src="/js/cropper.min.js"></script>
<script>
window._iuReg = {};   // uid → { files:[{file,blob,url,w,h,valid,converting}], isMulti, maxFiles, minFiles, maxBytes, crop, aspect }

var _IU_MAX_W    = 1600;  // max dimension before downscale
var _IU_QUALITY  = 0.82;  // WebP quality

// ---------- Crop modal (singleton) ----------
var _iuCropper     = null;
var _iuCropResolve = null;

// Lazy-build the modal DOM once
function _iu_ensureModal() {
  if (document.getElementById('_iu_crop_modal')) return;
  var modal = document.createElement('div');
  modal.id = '_iu_crop_modal';
  modal.innerHTML = [
    '<div id="_iu_crop_box">',
      '<div id="_iu_crop_header">',
        '<h3>Crop Image</h3>',
        '<button id="_iu_crop_close" type="button" title="Close">✕</button>',
      '</div>',
      '<div id="_iu_crop_img_wrap"><img id="_iu_crop_target" src="" alt=""></div>',
      '<div id="_iu_crop_footer">',
        '<span id="_iu_crop_hint">Drag to reposition · Pinch or scroll to zoom</span>',
        '<div id="_iu_crop_actions">',
          '<button class="iu-crop-btn" id="_iu_crop_skip" type="button">Skip Crop</button>',
          '<button class="iu-crop-btn" id="_iu_crop_use"  type="button">✓ Crop & Use</button>',
        '</div>',
      '</div>',
    '</div>'
  ].join('');
  document.body.appendChild(modal);

  document.getElementById('_iu_crop_close').onclick = function() { _iuCropResolve && _iuCropResolve(null); };
  document.getElementById('_iu_crop_skip').onclick  = function() { _iuCropResolve && _iuCropResolve(null); };
  document.getElementById('_iu_crop_use').onclick   = function() {
    if (!_iuCropper) { _iuCropResolve && _iuCropResolve(null); return; }
    _iuCropper.getCroppedCanvas({ maxWidth: _IU_MAX_W * 2, imageSmoothingQuality: 'high' })
      .toBlob(function(blob) { _iuCropResolve && _iuCropResolve(blob); }, 'image/webp', _IU_QUALITY);
  };
  modal.addEventListener('click', function(e) {
    if (e.target === modal) { _iuCropResolve && _iuCropResolve(null); }
  });
}

// Open the crop modal for a file; returns Promise<Blob|null>
// null means "skip / use original"
function _iu_openCrop(file, aspectRatio) {
  _iu_ensureModal();
  var modal  = document.getElementById('_iu_crop_modal');
  var target = document.getElementById('_iu_crop_target');

  return new Promise(function(resolve) {
    _iuCropResolve = function(result) {
      modal.classList.remove('open');
      if (_iuCropper) { _iuCropper.destroy(); _iuCropper = null; }
      URL.revokeObjectURL(target.src);
      target.src = '';
      _iuCropResolve = null;
      resolve(result);
    };

    target.onload = function() {
      var opts = { viewMode: 1, autoCropArea: 1, movable: true, zoomable: true, rotatable: false };
      if (!isNaN(aspectRatio)) opts.aspectRatio = aspectRatio;
      _iuCropper = new Cropper(target, opts);
    };
    target.src = URL.createObjectURL(file);
    modal.classList.add('open');
  });
}

// Convert a raw File or Blob → compressed WebP Blob via Canvas, then call cb(blob, w, h)
function _iu_compress(source, cb) {
  var img    = new Image();
  var objUrl = URL.createObjectURL(source);
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

  // Process files one at a time (important when crop modal is involved)
  _iu_processQueue(toAdd, 0, cfg, uid);
}

function _iu_processQueue(files, idx, cfg, uid) {
  if (idx >= files.length) return;
  var file = files[idx];

  if (cfg.crop) {
    // Show crop modal first, then compress the cropped result
    _iu_openCrop(file, cfg.aspect).then(function(croppedBlob) {
      var source = croppedBlob || file;  // null = skip crop, use original
      _iu_addAndCompress(source, file, cfg, uid);
      _iu_processQueue(files, idx + 1, cfg, uid);
    });
  } else {
    _iu_addAndCompress(file, file, cfg, uid);
    _iu_processQueue(files, idx + 1, cfg, uid);
  }
}

function _iu_addAndCompress(source, originalFile, cfg, uid) {
  var entry = { file: originalFile, blob: null, url: null, w: 0, h: 0, valid: false, converting: true };
  cfg.files.push(entry);

  // Show placeholder immediately
  _iu_render(uid);

  _iu_compress(source, function(blob, w, h) {
    if (!blob) { blob = source; w = 0; h = 0; }
    entry.blob       = blob;
    entry.w          = w;
    entry.h          = h;
    entry.valid      = blob.size <= cfg.maxBytes;
    entry.converting = false;
    entry.url        = URL.createObjectURL(blob);
    _iu_render(uid);
    _iu_sync(uid);
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
      validFiles.forEach(function(entry) {
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
</script>
@endonce

{{-- Per-instance registry init + form submit hook --}}
<script>
window._iuReg['{{ $uid }}'] = {
  files:    [],
  isMulti:  {{ $multiple ? 'true' : 'false' }},
  maxFiles: {{ $max }},
  minFiles: {{ $min }},
  maxBytes: {{ $maxBytes }},
  crop:     {{ $crop ? 'true' : 'false' }},
  aspect:   {{ $aspectJs }}
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
