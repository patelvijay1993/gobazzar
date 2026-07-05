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
@endphp

<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div class="space-y-2">
        @foreach($days as $key => $label)
            @php
                $day     = $state[$key] ?? [];
                $open    = $day['open']   ?? '';
                $close   = $day['close']  ?? '';
                $closed  = !empty($day['closed']);
                $statePath = $getStatePath();
            @endphp
            <div class="flex items-center gap-3 py-1">
                {{-- Day label --}}
                <span class="w-28 text-sm font-semibold text-gray-700 dark:text-gray-200 shrink-0">{{ $label }}</span>

                {{-- Open time --}}
                <input
                    type="time"
                    wire:model.live="{{ $statePath }}.{{ $key }}.open"
                    value="{{ $open }}"
                    @if($closed) disabled @endif
                    class="block w-36 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-1.5 text-sm text-gray-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500 disabled:opacity-40 disabled:cursor-not-allowed"
                >

                {{-- Separator --}}
                <span class="text-gray-400 text-sm">–</span>

                {{-- Close time --}}
                <input
                    type="time"
                    wire:model.live="{{ $statePath }}.{{ $key }}.close"
                    value="{{ $close }}"
                    @if($closed) disabled @endif
                    class="block w-36 rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-3 py-1.5 text-sm text-gray-900 dark:text-white shadow-sm focus:border-primary-500 focus:ring-1 focus:ring-primary-500 disabled:opacity-40 disabled:cursor-not-allowed"
                >

                {{-- Closed checkbox --}}
                <label class="flex items-center gap-2 cursor-pointer select-none ml-2">
                    <input
                        type="checkbox"
                        wire:model.live="{{ $statePath }}.{{ $key }}.closed"
                        @if($closed) checked @endif
                        class="rounded border-gray-300 dark:border-gray-600 text-primary-600 shadow-sm focus:ring-primary-500"
                    >
                    <span class="text-sm text-gray-600 dark:text-gray-400">Closed</span>
                </label>
            </div>
        @endforeach
    </div>
</x-dynamic-component>
