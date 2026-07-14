@php
    $days = [
        'monday'    => 'Monday',
        'tuesday'   => 'Tuesday',
        'wednesday' => 'Wednesday',
        'thursday'  => 'Thursday',
        'friday'    => 'Friday',
        'saturday'  => 'Saturday',
        'sunday'    => 'Sunday',
    ];
    $state = $getState() ?? [];
    if (is_string($state)) {
        $state = json_decode($state, true) ?? [];
    }
    $statePath = $getStatePath();

    // Build initial state for Alpine
    $initial = [];
    foreach ($days as $key => $label) {
        $day = $state[$key] ?? [];
        $initial[$key] = [
            'open'   => $day['open']   ?? '',
            'close'  => $day['close']  ?? '',
            'closed' => !empty($day['closed']),
        ];
    }
@endphp

<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div
        x-data="businessHours({{ json_encode($initial) }}, '{{ $statePath }}')"
        x-init="init()"
        class="space-y-2"
    >
        @foreach($days as $key => $label)
        <div class="flex items-center gap-3 py-1">
            <span class="w-28 text-sm font-semibold text-gray-700 dark:text-gray-200 shrink-0">{{ $label }}</span>

            <input
                type="time"
                x-model="days.{{ $key }}.open"
                x-bind:disabled="days.{{ $key }}.closed"
                @change="sync()"
                class="block w-36 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-1.5 text-sm text-gray-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500 disabled:opacity-40 disabled:cursor-not-allowed"
            >

            <span class="text-gray-400 text-sm">–</span>

            <input
                type="time"
                x-model="days.{{ $key }}.close"
                x-bind:disabled="days.{{ $key }}.closed"
                @change="sync()"
                class="block w-36 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-1.5 text-sm text-gray-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500 disabled:opacity-40 disabled:cursor-not-allowed"
            >

            <label class="flex items-center gap-2 cursor-pointer select-none ml-2">
                <input
                    type="checkbox"
                    x-model="days.{{ $key }}.closed"
                    @change="sync()"
                    class="rounded border-gray-300 dark:border-gray-600 text-primary-600 shadow-sm focus:ring-primary-500"
                >
                <span class="text-sm text-gray-600 dark:text-gray-400">Closed</span>
            </label>
        </div>
        @endforeach

        {{-- Hidden inputs Livewire me data sync karne ke liye --}}
        @foreach($days as $key => $label)
            <input type="hidden" name="{{ $statePath }}[{{ $key }}][open]"   x-bind:value="days.{{ $key }}.open">
            <input type="hidden" name="{{ $statePath }}[{{ $key }}][close]"  x-bind:value="days.{{ $key }}.close">
            <input type="hidden" name="{{ $statePath }}[{{ $key }}][closed]" x-bind:value="days.{{ $key }}.closed ? '1' : '0'">
        @endforeach
    </div>
</x-dynamic-component>

<script>
function businessHours(initial, statePath) {
    return {
        days: initial,
        init() {
            // Sync to Livewire on load
            this.$nextTick(() => this.sync());
        },
        sync() {
            // Push each day into Livewire component state
            Object.entries(this.days).forEach(([day, val]) => {
                const base = statePath + '.' + day;
                if (typeof $wire !== 'undefined') {
                    $wire.set(base + '.open',   val.open   ?? '');
                    $wire.set(base + '.close',  val.close  ?? '');
                    $wire.set(base + '.closed', val.closed ?? false);
                }
            });
        }
    }
}
</script>
