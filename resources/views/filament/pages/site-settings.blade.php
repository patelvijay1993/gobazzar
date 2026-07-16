<x-filament-panels::page>
    <form wire:submit="save">
        {{ $this->form }}

        <div class="mt-6">
            <x-filament::button type="submit" size="lg">
                Save Settings
            </x-filament::button>
        </div>
    </form>

    {{-- Standalone OG Image upload — outside Livewire so file upload works properly --}}
    <div style="margin-top:32px;background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:24px">
        <h3 style="font-size:15px;font-weight:700;color:#111827;margin-bottom:4px">🖼️ OG Image Upload</h3>
        <p style="font-size:13px;color:#6b7280;margin-bottom:16px">Recommended 1200×630px · JPG/PNG/WebP · Max 2MB · Saved to local server storage</p>

        @if(session('og_success'))
            <div style="background:#f0fdf4;border:1px solid #bbf7d0;border-radius:8px;padding:10px 14px;font-size:13px;color:#15803d;margin-bottom:14px;font-weight:600">
                ✅ {{ session('og_success') }}
            </div>
        @endif

        @php $currentOg = \App\Models\Setting::get('seo_og_image', ''); @endphp
        @if($currentOg)
            <div style="margin-bottom:14px">
                <p style="font-size:12px;color:#6b7280;margin-bottom:6px">Current image:</p>
                <img src="{{ $currentOg }}" style="max-width:400px;height:auto;border-radius:8px;border:1px solid #e5e7eb"
                     onerror="this.style.display='none';this.nextSibling.style.display='block'">
                <p style="display:none;font-size:12px;color:#dc2626">⚠️ Image failed to load: {{ $currentOg }}</p>
            </div>
        @endif

        <form method="POST" action="{{ route('admin.og-image.upload') }}" enctype="multipart/form-data">
            @csrf
            <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap">
                <input type="file" name="og_image" accept="image/jpeg,image/png,image/webp"
                    style="flex:1;min-width:200px;padding:10px;border:1.5px dashed #d1d5db;border-radius:8px;font-size:13px;cursor:pointer;background:#f9fafb">
                <button type="submit"
                    style="background:#1a3a8f;color:#fff;border:none;border-radius:8px;padding:10px 22px;font-size:13px;font-weight:700;cursor:pointer;white-space:nowrap">
                    Upload & Save
                </button>
            </div>
            @error('og_image')
                <p style="margin-top:8px;font-size:12px;color:#dc2626">{{ $message }}</p>
            @enderror
        </form>
    </div>
</x-filament-panels::page>
