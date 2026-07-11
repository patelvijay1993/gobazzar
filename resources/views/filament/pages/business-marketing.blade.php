<x-filament-panels::page>
<div class="space-y-6">

  {{-- FILTERS --}}
  <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
    <div class="fi-section-header px-6 py-4 border-b border-gray-200 dark:border-white/10">
      <h3 class="fi-section-header-heading text-base font-semibold text-gray-950 dark:text-white">
        🔍 Filter Businesses
      </h3>
    </div>
    <div class="p-6">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">

        {{-- City --}}
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">City</label>
          <input wire:model="filter_city" list="city-list" type="text" placeholder="e.g. Calgary"
            class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 focus:border-primary-500">
          <datalist id="city-list">
            @foreach($this->cities as $city)
              <option value="{{ $city }}">
            @endforeach
          </datalist>
        </div>

        {{-- Category --}}
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Category</label>
          <select wire:model="filter_category"
            class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500">
            <option value="">— All Categories —</option>
            @foreach($this->categories as $id => $name)
              <option value="{{ $id }}">{{ $name }}</option>
            @endforeach
          </select>
        </div>

        {{-- Type --}}
        <div>
          <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Send Via</label>
          <select wire:model="filter_type"
            class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500">
            <option value="email">✉️ Email</option>
            <option value="whatsapp">💬 WhatsApp</option>
          </select>
        </div>
      </div>

      <button wire:click="search" wire:loading.attr="disabled"
        class="inline-flex items-center gap-2 px-5 py-2 bg-primary-600 hover:bg-primary-700 text-white text-sm font-semibold rounded-lg transition">
        <span wire:loading.remove wire:target="search">🔍 Search Businesses</span>
        <span wire:loading wire:target="search">Searching...</span>
      </button>
    </div>
  </div>

  @if($businesses !== null)
  {{-- RESULTS --}}
  <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
    <div class="fi-section-header px-6 py-4 border-b border-gray-200 dark:border-white/10 flex items-center justify-between">
      <h3 class="text-base font-semibold text-gray-950 dark:text-white">
        📋 Found {{ count($businesses) }} Businesses
        @if(count($selected) > 0)
          <span class="ml-2 text-sm font-normal text-primary-600">({{ count($selected) }} selected)</span>
        @endif
      </h3>
      <div class="flex gap-2">
        <button wire:click="selectAll" class="text-xs px-3 py-1 bg-green-100 text-green-700 rounded-md hover:bg-green-200 font-medium">Select All</button>
        <button wire:click="deselectAll" class="text-xs px-3 py-1 bg-gray-100 text-gray-600 rounded-md hover:bg-gray-200 font-medium">Deselect All</button>
      </div>
    </div>

    @if(count($businesses) === 0)
      <div class="p-6 text-center text-gray-400 text-sm">No active businesses found with these filters.</div>
    @else
    <div class="overflow-x-auto">
      <table class="w-full text-sm">
        <thead class="bg-gray-50 dark:bg-gray-800">
          <tr>
            <th class="px-4 py-3 text-left w-8"></th>
            <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-300">Business</th>
            <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-300">Category</th>
            <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-300">City</th>
            <th class="px-4 py-3 text-left font-medium text-gray-600 dark:text-gray-300">
              {{ $filter_type === 'email' ? 'Email' : 'Phone' }}
            </th>
          </tr>
        </thead>
        <tbody class="divide-y divide-gray-100 dark:divide-gray-800">
          @foreach($businesses as $biz)
          <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 {{ in_array($biz['id'], $selected) ? 'bg-primary-50 dark:bg-primary-900/10' : '' }}">
            <td class="px-4 py-3">
              <input type="checkbox" value="{{ $biz['id'] }}" wire:model="selected"
                class="rounded border-gray-300 text-primary-600 focus:ring-primary-500">
            </td>
            <td class="px-4 py-3 font-medium text-gray-900 dark:text-white">{{ $biz['name'] }}</td>
            <td class="px-4 py-3 text-gray-500">{{ $biz['category'] }}</td>
            <td class="px-4 py-3 text-gray-500">{{ $biz['city'] }}</td>
            <td class="px-4 py-3">
              @if($filter_type === 'email')
                @if($biz['email'])
                  <span class="text-green-600 text-xs">✓ {{ $biz['email'] }}</span>
                @else
                  <span class="text-red-400 text-xs">✗ No email</span>
                @endif
              @else
                @if($biz['phone'])
                  <span class="text-green-600 text-xs">✓ {{ $biz['phone'] }}</span>
                @else
                  <span class="text-red-400 text-xs">✗ No phone</span>
                @endif
              @endif
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
    @endif
  </div>

  {{-- COMPOSE MESSAGE --}}
  @if(count($businesses) > 0)
  <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
    <div class="fi-section-header px-6 py-4 border-b border-gray-200 dark:border-white/10">
      <h3 class="text-base font-semibold text-gray-950 dark:text-white">
        {{ $filter_type === 'email' ? '✉️ Compose Email' : '💬 Compose WhatsApp Message' }}
      </h3>
    </div>
    <div class="p-6 space-y-4">

      @if($filter_type === 'email')
      <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Subject</label>
        <input wire:model="subject" type="text" placeholder="e.g. Grow Your Business with GoBazaar!"
          class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500">
      </div>
      @endif

      <div>
        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
          Message
          <span class="text-gray-400 font-normal ml-1">(Use {business_name} for personalization)</span>
        </label>
        <textarea wire:model="message" rows="6" placeholder="Dear {business_name},&#10;&#10;We'd like to invite you to feature your business on GoBazaar...&#10;&#10;Best regards,&#10;GoBazaar Team"
          class="w-full rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-800 text-gray-900 dark:text-white px-3 py-2 text-sm focus:ring-2 focus:ring-primary-500 font-mono"></textarea>
      </div>

      <div class="flex items-center gap-3 pt-2">
        <button wire:click="send" wire:loading.attr="disabled" wire:confirm="Send to {{ count($selected) }} businesses?"
          class="inline-flex items-center gap-2 px-6 py-2.5 bg-primary-600 hover:bg-primary-700 disabled:opacity-50 text-white text-sm font-semibold rounded-lg transition">
          <span wire:loading.remove wire:target="send">
            {{ $filter_type === 'email' ? '🚀 Send Emails' : '💬 Generate WhatsApp Links' }}
            ({{ count($selected) }})
          </span>
          <span wire:loading wire:target="send">Sending...</span>
        </button>
        <span class="text-xs text-gray-400">
          {{ count($selected) }} of {{ count($businesses) }} selected
        </span>
      </div>
    </div>
  </div>
  @endif
  @endif

  {{-- SEND LOG --}}
  @if($send_log !== null)
  <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10">
    <div class="fi-section-header px-6 py-4 border-b border-gray-200 dark:border-white/10">
      <h3 class="text-base font-semibold text-gray-950 dark:text-white">📊 Send Results</h3>
    </div>
    <div class="p-6">
      <div class="flex gap-4 mb-4 text-sm flex-wrap">
        <span class="px-3 py-1 bg-green-100 text-green-700 rounded-full font-medium">
          ✓ Sent: {{ count(array_filter($send_log, fn($l) => $l['status'] === 'sent')) }}
        </span>
        <span class="px-3 py-1 bg-blue-100 text-blue-700 rounded-full font-medium">
          💬 WhatsApp: {{ count(array_filter($send_log, fn($l) => $l['status'] === 'whatsapp')) }}
        </span>
        <span class="px-3 py-1 bg-yellow-100 text-yellow-700 rounded-full font-medium">
          ⚠ Skipped: {{ count(array_filter($send_log, fn($l) => in_array($l['status'], ['skipped', 'failed']))) }}
        </span>
      </div>

      <div class="space-y-2 max-h-80 overflow-y-auto">
        @foreach($send_log as $log)
        <div class="flex items-center justify-between py-2 px-3 rounded-lg
          {{ $log['status'] === 'sent' ? 'bg-green-50 dark:bg-green-900/20' :
             ($log['status'] === 'whatsapp' ? 'bg-blue-50 dark:bg-blue-900/20' : 'bg-yellow-50 dark:bg-yellow-900/20') }}">
          <div class="flex items-center gap-2 text-sm">
            <span>
              @if($log['status'] === 'sent') ✅
              @elseif($log['status'] === 'whatsapp') 💬
              @elseif($log['status'] === 'failed') ❌
              @else ⚠️
              @endif
            </span>
            <span class="font-medium text-gray-800 dark:text-gray-200">{{ $log['name'] }}</span>
            @if(isset($log['contact']))
              <span class="text-gray-400 text-xs">{{ $log['contact'] }}</span>
            @endif
            @if(isset($log['reason']))
              <span class="text-red-400 text-xs">— {{ $log['reason'] }}</span>
            @endif
          </div>
          @if($log['status'] === 'whatsapp' && isset($log['link']))
            <a href="{{ $log['link'] }}" target="_blank"
              class="text-xs px-3 py-1 bg-green-500 hover:bg-green-600 text-white rounded-full font-medium">
              Open WhatsApp ↗
            </a>
          @endif
        </div>
        @endforeach
      </div>
    </div>
  </div>
  @endif

</div>
</x-filament-panels::page>
