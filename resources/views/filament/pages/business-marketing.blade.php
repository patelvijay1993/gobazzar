<x-filament-panels::page>
<div class="space-y-6">

  {{-- FILTERS --}}
  <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
    <div class="fi-section-header px-6 py-4 border-b border-gray-200 dark:border-white/10">
      <h3 class="text-base font-semibold text-gray-950 dark:text-white">🔍 Filter Businesses</h3>
    </div>
    <div class="p-6">
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">

        {{-- Province --}}
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Province</label>
          <select wire:model.live="filter_province"
            class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500">
            <option value="">— All Provinces —</option>
            @foreach($this->provinces as $prov)
              <option value="{{ $prov }}">{{ $prov }}</option>
            @endforeach
          </select>
        </div>

        {{-- City --}}
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">City</label>
          <select wire:model="filter_city"
            class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500">
            <option value="">— All Cities —</option>
            @foreach($this->cities as $city)
              <option value="{{ $city }}">{{ $city }}</option>
            @endforeach
          </select>
        </div>

        {{-- Category (searchable) --}}
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Category</label>
          <input wire:model="filter_category" list="cat-list" type="text" placeholder="Type to search..."
            class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500">
          <datalist id="cat-list">
            @foreach($this->categories as $id => $name)
              <option value="{{ $id }}">{{ $name }}</option>
            @endforeach
          </datalist>
          {{-- Show selected category name --}}
          @if($filter_category && isset($this->categories[$filter_category]))
            <div class="mt-1 text-xs text-primary-600">Selected: {{ $this->categories[$filter_category] }}
              <button wire:click="$set('filter_category','')" class="ml-1 text-gray-400 hover:text-red-500">✕</button>
            </div>
          @endif
        </div>

        {{-- Search button --}}
        <div class="flex items-end">
          <button wire:click="search" wire:loading.attr="disabled"
            class="w-full inline-flex items-center justify-center gap-2 px-5 py-2 bg-primary-600 hover:bg-primary-700 disabled:opacity-60 text-white text-sm font-semibold rounded-lg transition">
            <span wire:loading.remove wire:target="search">🔍 Search</span>
            <span wire:loading wire:target="search">Searching...</span>
          </button>
        </div>
      </div>
    </div>
  </div>

  @if($businesses !== null)
  {{-- RESULTS TABLE --}}
  <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
    <div class="fi-section-header px-6 py-4 border-b border-gray-200 dark:border-white/10 flex items-center justify-between flex-wrap gap-3">
      <h3 class="text-base font-semibold text-gray-950 dark:text-white">
        📋 {{ count($businesses) }} Businesses Found
        <span class="text-sm font-normal text-primary-600 ml-1">({{ count($selected) }} selected)</span>
      </h3>
      <div class="flex gap-2">
        <button wire:click="selectAll" class="text-xs px-3 py-1.5 bg-green-100 text-green-700 rounded-lg hover:bg-green-200 font-medium">✓ Select All</button>
        <button wire:click="deselectAll" class="text-xs px-3 py-1.5 bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 font-medium">✗ Deselect All</button>
      </div>
    </div>

    @if(count($businesses) === 0)
      <div class="p-8 text-center text-gray-400 text-sm">No active businesses found with these filters.</div>
    @else
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700">
          <tr>
            <th class="px-4 py-3 text-left w-8">
              <input type="checkbox"
                @if(count($selected) === count($businesses)) checked @endif
                wire:click="{{ count($selected) === count($businesses) ? 'deselectAll' : 'selectAll' }}"
                class="rounded border-gray-300 text-primary-600">
            </th>
            <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-300">Business</th>
            <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-300">Category</th>
            <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-300">City</th>
            <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-300">Contact</th>
            <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-300 w-40">Send Via</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
          @foreach($businesses as $biz)
          @php $isSelected = in_array($biz['id'], $selected); @endphp
          <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 {{ $isSelected ? 'bg-primary-50/40 dark:bg-primary-900/10' : '' }}">

            {{-- Checkbox --}}
            <td class="px-4 py-3">
              <input type="checkbox" value="{{ $biz['id'] }}" wire:model="selected"
                class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
            </td>

            {{-- Name --}}
            <td class="px-4 py-3 font-semibold text-gray-900 dark:text-white">{{ $biz['name'] }}</td>

            {{-- Category --}}
            <td class="px-4 py-3">
              <span class="text-xs bg-blue-50 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300 px-2 py-0.5 rounded-full">{{ $biz['category'] }}</span>
            </td>

            {{-- City --}}
            <td class="px-4 py-3 text-gray-500 text-xs">{{ $biz['city'] }}{{ $biz['province'] ? ', '.$biz['province'] : '' }}</td>

            {{-- Contact info --}}
            <td class="px-4 py-3">
              <div class="space-y-0.5">
                @if($biz['email'])
                  <div class="text-xs text-green-600"><i class="fa-solid fa-envelope mr-1"></i>{{ Str::limit($biz['email'], 22) }}</div>
                @else
                  <div class="text-xs text-gray-300">✗ No email</div>
                @endif
                @if($biz['phone'])
                  <div class="text-xs text-green-600"><i class="fa-solid fa-phone mr-1"></i>{{ $biz['phone'] }}</div>
                @else
                  <div class="text-xs text-gray-300">✗ No phone</div>
                @endif
              </div>
            </td>

            {{-- Per-row send type selector --}}
            <td class="px-4 py-3">
              <div class="flex gap-1 flex-wrap">
                <button type="button"
                  wire:click="$set('send_types.{{ $biz['id'] }}', 'email')"
                  @if(empty($biz['email'])) disabled @endif
                  class="text-xs px-2.5 py-1 rounded-full font-medium border transition
                    {{ ($send_types[$biz['id']] ?? '') === 'email'
                        ? 'bg-primary-600 text-white border-primary-600'
                        : 'bg-white text-gray-500 border-gray-300 hover:border-primary-400 dark:bg-gray-800 dark:text-gray-400' }}
                    {{ empty($biz['email']) ? 'opacity-30 cursor-not-allowed' : 'cursor-pointer' }}">
                  ✉️ Email
                </button>
                <button type="button"
                  wire:click="$set('send_types.{{ $biz['id'] }}', 'whatsapp')"
                  @if(empty($biz['phone'])) disabled @endif
                  class="text-xs px-2.5 py-1 rounded-full font-medium border transition
                    {{ ($send_types[$biz['id']] ?? '') === 'whatsapp'
                        ? 'bg-green-600 text-white border-green-600'
                        : 'bg-white text-gray-500 border-gray-300 hover:border-green-400 dark:bg-gray-800 dark:text-gray-400' }}
                    {{ empty($biz['phone']) ? 'opacity-30 cursor-not-allowed' : 'cursor-pointer' }}">
                  💬 WA
                </button>
                <button type="button"
                  wire:click="$set('send_types.{{ $biz['id'] }}', 'none')"
                  class="text-xs px-2.5 py-1 rounded-full font-medium border transition
                    {{ ($send_types[$biz['id']] ?? '') === 'none'
                        ? 'bg-gray-400 text-white border-gray-400'
                        : 'bg-white text-gray-400 border-gray-200 hover:border-gray-400 dark:bg-gray-800' }}">
                  ✕
                </button>
              </div>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    @endif
  </div>

  {{-- COMPOSE --}}
  @if(count($businesses) > 0)
  <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
    <div class="fi-section-header px-6 py-4 border-b border-gray-200 dark:border-white/10">
      <h3 class="text-base font-semibold text-gray-950 dark:text-white">✍️ Compose Message</h3>
      <p class="text-xs text-gray-400 mt-0.5">
        Will send Email or WhatsApp based on per-row selection above.
        Email count: <strong class="text-primary-600">{{ collect($send_types)->filter(fn($t) => $t === 'email')->count() }}</strong> |
        WhatsApp count: <strong class="text-green-600">{{ collect($send_types)->filter(fn($t) => $t === 'whatsapp')->count() }}</strong>
      </p>
    </div>
    <div class="p-6 space-y-4">
      <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
          Email Subject <span class="text-gray-400 font-normal">(only for email sends)</span>
        </label>
        <input wire:model="subject" type="text" placeholder="e.g. Grow your business with GoBazaar!"
          class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500">
      </div>

      <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Message</label>
        <textarea wire:model="message" rows="6"
          placeholder="Dear [Business Name],&#10;&#10;We'd like to invite you to list your business on GoBazaar — Canada's growing marketplace.&#10;&#10;It's FREE to list! Visit gobazzarweb.heavendwell.com&#10;&#10;— GoBazaar Team"
          class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 font-mono"></textarea>
        <p class="text-xs text-gray-400 mt-1">WhatsApp messages are sent with business name prepended automatically.</p>
      </div>

      <div class="flex items-center gap-4 pt-1">
        <button wire:click="send" wire:loading.attr="disabled"
          wire:confirm="Send to {{ count($selected) }} selected businesses?"
          class="inline-flex items-center gap-2 px-6 py-2.5 bg-primary-600 hover:bg-primary-700 disabled:opacity-50 text-white text-sm font-bold rounded-lg transition">
          <span wire:loading.remove wire:target="send">🚀 Send Now ({{ count($selected) }})</span>
          <span wire:loading wire:target="send">Sending...</span>
        </button>
        <div class="text-sm text-gray-500">
          <span class="text-primary-600 font-semibold">{{ count($selected) }}</span> selected ·
          <span class="text-green-600 font-semibold">{{ collect($send_types)->filter(fn($t,$id) => $t==='email' && in_array($id,$selected))->count() }}</span> via email ·
          <span class="text-green-500 font-semibold">{{ collect($send_types)->filter(fn($t,$id) => $t==='whatsapp' && in_array($id,$selected))->count() }}</span> via WhatsApp
        </div>
      </div>
    </div>
  </div>
  @endif
  @endif

  {{-- SEND LOG --}}
  @if($send_log !== null)
  <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
    <div class="fi-section-header px-6 py-4 border-b border-gray-200 dark:border-white/10 flex items-center justify-between">
      <h3 class="text-base font-semibold text-gray-950 dark:text-white">📊 Send Results</h3>
      <div class="flex gap-3 text-xs">
        <span class="px-2.5 py-1 bg-green-100 text-green-700 rounded-full font-semibold">
          ✉️ {{ count(array_filter($send_log, fn($l) => $l['status'] === 'sent')) }} sent
        </span>
        <span class="px-2.5 py-1 bg-blue-100 text-blue-700 rounded-full font-semibold">
          💬 {{ count(array_filter($send_log, fn($l) => $l['status'] === 'whatsapp')) }} WhatsApp
        </span>
        <span class="px-2.5 py-1 bg-yellow-100 text-yellow-700 rounded-full font-semibold">
          ⚠️ {{ count(array_filter($send_log, fn($l) => in_array($l['status'], ['skipped','failed']))) }} skipped
        </span>
      </div>
    </div>
    <div class="p-4 space-y-2 max-h-80 overflow-y-auto">
      @foreach($send_log as $log)
      <div class="flex items-center justify-between py-2 px-3 rounded-lg text-sm
        {{ $log['status'] === 'sent' ? 'bg-green-50 dark:bg-green-900/20' :
           ($log['status'] === 'whatsapp' ? 'bg-blue-50 dark:bg-blue-900/20' : 'bg-yellow-50 dark:bg-yellow-900/20') }}">
        <div class="flex items-center gap-2 min-w-0">
          <span>{{ $log['status'] === 'sent' ? '✅' : ($log['status'] === 'whatsapp' ? '💬' : ($log['status'] === 'failed' ? '❌' : '⚠️')) }}</span>
          <span class="font-semibold text-gray-800 dark:text-gray-200 truncate">{{ $log['name'] }}</span>
          @if(isset($log['contact']))<span class="text-gray-400 text-xs truncate">{{ $log['contact'] }}</span>@endif
          @if(isset($log['reason']))<span class="text-red-400 text-xs">— {{ $log['reason'] }}</span>@endif
        </div>
        @if($log['status'] === 'whatsapp' && isset($log['link']))
          <a href="{{ $log['link'] }}" target="_blank"
            class="flex-shrink-0 ml-2 text-xs px-3 py-1 bg-green-500 hover:bg-green-600 text-white rounded-full font-semibold">
            Open WhatsApp ↗
          </a>
        @endif
      </div>
      @endforeach
    </div>
  </div>
  @endif

</div>
</x-filament-panels::page>
