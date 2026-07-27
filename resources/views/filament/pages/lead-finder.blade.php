<x-filament-panels::page>
<div class="space-y-6">

  {{-- Stats --}}
  <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
    <div class="bg-white dark:bg-gray-900 rounded-xl p-4 ring-1 ring-gray-950/5 dark:ring-white/10 text-center">
      <div class="text-2xl font-black text-primary-600">{{ $this->leadsCount }}</div>
      <div class="text-xs text-gray-500 mt-1">Total Leads</div>
    </div>
    <div class="bg-white dark:bg-gray-900 rounded-xl p-4 ring-1 ring-gray-950/5 dark:ring-white/10 text-center">
      <div class="text-2xl font-black text-orange-500">{{ $this->newLeadsCount }}</div>
      <div class="text-xs text-gray-500 mt-1">New (not contacted)</div>
    </div>
    <div class="bg-white dark:bg-gray-900 rounded-xl p-4 ring-1 ring-gray-950/5 dark:ring-white/10 text-center col-span-2">
      <div class="text-sm font-semibold text-gray-700 dark:text-gray-300">
        <a href="{{ route('filament.admin.resources.leads.index') }}" class="text-primary-600 hover:underline">
          📋 View All Leads →
        </a>
      </div>
      <div class="text-xs text-gray-400 mt-1">Manage, filter, update status</div>
    </div>
  </div>

  {{-- API Key Warning --}}
  @if($api_status === 'no_key')
  <div class="rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 p-4 flex gap-3">
    <span class="text-xl">⚠️</span>
    <div>
      <div class="font-semibold text-red-700 dark:text-red-400">Google Places API Key Missing</div>
      <div class="text-sm text-red-600 dark:text-red-300 mt-1">
        Add <code class="bg-red-100 dark:bg-red-900 px-1 rounded">GOOGLE_PLACES_API_KEY=your_key</code> in .env file.<br>
        Get key from:
        <a href="https://console.cloud.google.com" target="_blank" class="underline">console.cloud.google.com</a>
        → Enable "Places API" → Create credentials → API Key
      </div>
    </div>
  </div>
  @endif

  {{-- SEARCH --}}
  <div class="bg-white dark:bg-gray-900 rounded-xl ring-1 ring-gray-950/5 dark:ring-white/10 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 dark:border-white/10 flex items-center gap-2">
      <span class="text-lg">🗺️</span>
      <h3 class="text-base font-semibold text-gray-950 dark:text-white">Search on Google Maps</h3>
    </div>
    <div class="p-6">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Business Type / Keyword <span class="text-red-500">*</span></label>
          <input wire:model="search_keyword" type="text" placeholder="e.g. Restaurant, Cafe, Doctor, Lawyer"
            class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">City <span class="text-red-500">*</span></label>
          <input wire:model="search_city" type="text" placeholder="e.g. Calgary"
            class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500">
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Province</label>
          <input wire:model="search_province" type="text" placeholder="e.g. AB, ON, BC"
            class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500">
        </div>
      </div>

      <div class="flex items-center gap-4 mb-4">
        <label class="flex items-center gap-2 cursor-pointer">
          <input type="checkbox" wire:model="fetch_details"
            class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
          <span class="text-sm text-gray-600 dark:text-gray-300">
            Fetch phone & website details
            <span class="text-xs text-gray-400">(slower — uses more API quota)</span>
          </span>
        </label>
      </div>

      <button wire:click="search" wire:loading.attr="disabled"
        class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary-600 hover:bg-primary-700 disabled:opacity-60 text-white text-sm font-semibold rounded-lg transition">
        <span wire:loading.remove wire:target="search">🔍 Search Google Maps</span>
        <span wire:loading wire:target="search">⏳ Searching... (may take 10-30s)</span>
      </button>
    </div>
  </div>

  {{-- RESULTS --}}
  @if($results !== null)
    @if(count($results) === 0)
    <div class="bg-yellow-50 dark:bg-yellow-900/20 rounded-xl p-4 text-center text-yellow-700 dark:text-yellow-300 text-sm">
      No results found. Try a different keyword or city.
    </div>
    @else
    <div class="bg-white dark:bg-gray-900 rounded-xl ring-1 ring-gray-950/5 dark:ring-white/10 overflow-hidden">
      <div class="px-6 py-4 border-b border-gray-200 dark:border-white/10 flex items-center justify-between flex-wrap gap-3">
        <h3 class="text-base font-semibold text-gray-950 dark:text-white">
          📍 Found {{ count($results) }} businesses
          <span class="text-sm font-normal text-primary-600 ml-1">({{ count($selected) }} selected)</span>
        </h3>
        <div class="flex gap-2 flex-wrap">
          <button wire:click="selectAll" class="text-xs px-3 py-1.5 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 font-medium">✓ Select All</button>
          <button wire:click="deselectAll" class="text-xs px-3 py-1.5 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 font-medium">✗ Deselect All</button>
          <button wire:click="saveLeads" wire:loading.attr="disabled"
            class="text-xs px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium">
            <span wire:loading.remove wire:target="saveLeads">💾 Save to Leads</span>
            <span wire:loading wire:target="saveLeads">Saving...</span>
          </button>
          <button wire:click="openDirModal" wire:loading.attr="disabled"
            class="text-xs px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg font-medium">
            🏢 Add to Directory ({{ count($selected) }})
          </button>
        </div>
      </div>

      <div class="overflow-x-auto">
        <table class="w-full text-sm">
          <thead class="bg-gray-50 dark:bg-gray-800">
            <tr>
              <th class="px-4 py-3 w-8"></th>
              <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-300">Business Name</th>
              <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-300">Address</th>
              <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-300">Rating</th>
              <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-300">Phone</th>
              <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-300">Website</th>
              <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-300">Maps</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
            @foreach($results as $place)
            <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 {{ in_array($place['google_place_id'], $selected) ? 'bg-primary-50 dark:bg-primary-900/10' : '' }}">
              <td class="px-4 py-3">
                <input type="checkbox" value="{{ $place['google_place_id'] }}" wire:model="selected"
                  class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
              </td>
              <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $place['name'] }}</td>
              <td class="px-4 py-3 text-gray-500 text-xs max-w-xs">{{ $place['address'] ?? '—' }}</td>
              <td class="px-4 py-3">
                @if($place['rating'] ?? null)
                  <span class="text-yellow-500">★</span>
                  <span class="text-gray-700 dark:text-gray-300">{{ $place['rating'] }}</span>
                  <span class="text-gray-400 text-xs">({{ $place['review_count'] ?? 0 }})</span>
                @else
                  <span class="text-gray-300">—</span>
                @endif
              </td>
              <td class="px-4 py-3">
                @if($place['phone'] ?? null)
                  <span class="text-green-600 text-xs">{{ $place['phone'] }}</span>
                @else
                  <span class="text-gray-300 text-xs">—</span>
                @endif
              </td>
              <td class="px-4 py-3">
                @if($place['website'] ?? null)
                  <a href="{{ $place['website'] }}" target="_blank" class="text-primary-600 text-xs hover:underline truncate block max-w-32">
                    {{ parse_url($place['website'], PHP_URL_HOST) ?? $place['website'] }}
                  </a>
                @else
                  <span class="text-gray-300 text-xs">—</span>
                @endif
              </td>
              <td class="px-4 py-3">
                @if($place['google_maps_url'] ?? null)
                  <a href="{{ $place['google_maps_url'] }}" target="_blank"
                    class="text-xs px-2 py-1 bg-red-50 text-red-600 rounded hover:bg-red-100">📍 Maps</a>
                @endif
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>

    {{-- SEND MESSAGE --}}
    <div class="bg-white dark:bg-gray-900 rounded-xl ring-1 ring-gray-950/5 dark:ring-white/10 overflow-hidden">
      <div class="px-6 py-4 border-b border-gray-200 dark:border-white/10">
        <h3 class="text-base font-semibold text-gray-950 dark:text-white">📣 Send Marketing Message</h3>
      </div>
      <div class="p-6 space-y-4">
        <div class="flex gap-4">
          <label class="flex items-center gap-2 cursor-pointer">
            <input type="radio" wire:model="send_type" value="email" class="text-primary-600">
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">✉️ Email</span>
          </label>
          <label class="flex items-center gap-2 cursor-pointer">
            <input type="radio" wire:model="send_type" value="whatsapp" class="text-primary-600">
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">💬 WhatsApp</span>
          </label>
        </div>

        @if($send_type === 'email')
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Subject</label>
          <input wire:model="msg_subject" type="text" placeholder="e.g. List your business FREE on GoBazaar!"
            class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500">
        </div>
        @endif

        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Message</label>
          <textarea wire:model="msg_body" rows="5"
            placeholder="Hello,&#10;&#10;We found your business on Google Maps and would like to invite you to list on GoBazaar — Canada's fastest growing business directory. It's FREE!&#10;&#10;Visit: gobazzarweb.heavendwell.com&#10;&#10;— GoBazaar Team"
            class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 font-mono"></textarea>
        </div>

        <button wire:click="sendMarketing" wire:loading.attr="disabled"
          wire:confirm="Send to {{ count($selected) }} businesses?"
          class="inline-flex items-center gap-2 px-5 py-2.5 bg-green-600 hover:bg-green-700 disabled:opacity-60 text-white text-sm font-semibold rounded-lg transition">
          <span wire:loading.remove wire:target="sendMarketing">
            {{ $send_type === 'email' ? '🚀 Send Emails' : '💬 Generate WhatsApp Links' }} ({{ count($selected) }})
          </span>
          <span wire:loading wire:target="sendMarketing">Sending...</span>
        </button>
      </div>
    </div>
    @endif
  @endif

  {{-- SEND LOG --}}
  @if($send_log !== null)
  <div class="bg-white dark:bg-gray-900 rounded-xl ring-1 ring-gray-950/5 dark:ring-white/10 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 dark:border-white/10">
      <h3 class="text-base font-semibold text-gray-950 dark:text-white">📊 Send Results</h3>
    </div>
    <div class="p-6 space-y-2 max-h-96 overflow-y-auto">
      @foreach($send_log as $log)
      <div class="flex items-center justify-between py-2 px-3 rounded-lg
        {{ $log['status'] === 'sent' ? 'bg-green-50 dark:bg-green-900/20' :
           ($log['status'] === 'whatsapp' ? 'bg-blue-50 dark:bg-blue-900/20' : 'bg-yellow-50 dark:bg-yellow-900/20') }}">
        <div class="flex items-center gap-2 text-sm min-w-0">
          <span>{{ $log['status'] === 'sent' ? '✅' : ($log['status'] === 'whatsapp' ? '💬' : ($log['status'] === 'failed' ? '❌' : '⚠️')) }}</span>
          <span class="font-medium text-gray-800 dark:text-gray-200 truncate">{{ $log['name'] }}</span>
          @if(isset($log['contact']))<span class="text-gray-400 text-xs truncate">{{ $log['contact'] }}</span>@endif
          @if(isset($log['reason']))<span class="text-red-400 text-xs">— {{ $log['reason'] }}</span>@endif
        </div>
        @if($log['status'] === 'whatsapp' && isset($log['link']))
          <a href="{{ $log['link'] }}" target="_blank"
            class="flex-shrink-0 text-xs px-3 py-1 bg-green-500 hover:bg-green-600 text-white rounded-full font-medium ml-2">
            Open ↗
          </a>
        @endif
      </div>
      @endforeach
    </div>
  </div>
  @endif

  {{-- IMPORT LOG --}}
  @if($dir_import_log !== null)
  <div class="bg-white dark:bg-gray-900 rounded-xl ring-1 ring-gray-950/5 dark:ring-white/10 overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 dark:border-white/10 flex items-center gap-2">
      <span class="text-lg">🏢</span>
      <h3 class="text-base font-semibold text-gray-950 dark:text-white">Directory Import Results</h3>
    </div>
    <div class="p-6 space-y-2 max-h-96 overflow-y-auto">
      @foreach($dir_import_log as $log)
      <div class="flex items-center justify-between py-2 px-3 rounded-lg
        {{ $log['status'] === 'added' ? 'bg-green-50 dark:bg-green-900/20' : 'bg-yellow-50 dark:bg-yellow-900/20' }}">
        <div class="flex items-center gap-2 text-sm min-w-0">
          <span>{{ $log['status'] === 'added' ? '✅' : '⚠️' }}</span>
          <span class="font-medium text-gray-800 dark:text-gray-200 truncate">{{ $log['name'] }}</span>
          @if($log['status'] === 'added')
            <span class="text-gray-400 text-xs">· {{ $log['photos'] }} photo(s)</span>
          @else
            <span class="text-yellow-600 text-xs">— {{ $log['reason'] }}</span>
          @endif
        </div>
        @if($log['status'] === 'added')
          <a href="{{ route('directory.show', $log['slug']) }}" target="_blank"
            class="flex-shrink-0 text-xs px-3 py-1 bg-emerald-500 hover:bg-emerald-600 text-white rounded-full font-medium ml-2">
            View ↗
          </a>
        @endif
      </div>
      @endforeach
    </div>
  </div>
  @endif

</div>

{{-- ADD TO DIRECTORY MODAL --}}
@if($show_dir_modal)
<div class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 p-4" wire:click.self="closeDirModal">
  <div class="bg-white dark:bg-gray-900 rounded-2xl shadow-2xl w-full max-w-md overflow-hidden">
    <div class="px-6 py-4 border-b border-gray-200 dark:border-white/10 flex items-center justify-between">
      <h3 class="text-base font-bold text-gray-950 dark:text-white">🏢 Add {{ count($selected) }} Business(es) to Directory</h3>
      <button wire:click="closeDirModal" class="text-gray-400 hover:text-gray-600 text-xl leading-none">✕</button>
    </div>
    <div class="p-6 space-y-5">
      <div class="rounded-lg bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 p-3 text-sm text-blue-700 dark:text-blue-300">
        <strong>What will happen:</strong>
        <ul class="mt-1 ml-4 list-disc space-y-0.5 text-xs">
          <li>Fetch phone, website & photos from Google Places</li>
          <li>Download up to 3 photos → store in your file storage</li>
          <li>Auto-generate description using AI (Groq)</li>
          <li>Create active listing under Admin account</li>
          <li>Skip businesses already in the directory</li>
        </ul>
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">
          Category <span class="text-red-500">*</span>
        </label>
        <select wire:model="dir_category_id"
          class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500">
          <option value="0">— Select a category —</option>
          @foreach($this->directoryCategories as $cat)
            <option value="{{ $cat['id'] }}">{{ $cat['name'] }}</option>
          @endforeach
        </select>
      </div>

      <div class="flex gap-3">
        <button wire:click="closeDirModal" type="button"
          class="flex-1 px-4 py-2.5 rounded-lg border border-gray-300 dark:border-gray-600 text-sm font-medium text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800">
          Cancel
        </button>
        <button wire:click="addToDirectory" wire:loading.attr="disabled" type="button"
          class="flex-1 px-4 py-2.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 disabled:opacity-60 text-white text-sm font-semibold transition">
          <span wire:loading.remove wire:target="addToDirectory">🚀 Import to Directory</span>
          <span wire:loading wire:target="addToDirectory">⏳ Importing... (may take 30s)</span>
        </button>
      </div>
    </div>
  </div>
</div>
@endif

</x-filament-panels::page>
