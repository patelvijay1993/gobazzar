<div class="space-y-4 p-4">
    <div>
        <span class="text-sm font-semibold text-gray-500 dark:text-gray-400">Flag Message</span>
        <p class="mt-1 text-sm text-gray-800 dark:text-gray-200">{{ $record->flag_message }}</p>
    </div>

    @if($record->description)
    <div>
        <span class="text-sm font-semibold text-gray-500 dark:text-gray-400">Description Snippet</span>
        <p class="mt-1 text-sm text-gray-800 dark:text-gray-200">{{ $record->description }}</p>
    </div>
    @endif

    @if($record->raw_data)
    <div>
        <span class="text-sm font-semibold text-gray-500 dark:text-gray-400">Raw Submitted Data</span>
        <pre class="mt-1 rounded-lg bg-gray-100 dark:bg-gray-800 p-3 text-xs text-gray-700 dark:text-gray-300 overflow-auto max-h-64">{{ json_encode($record->raw_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
    </div>
    @endif

    <div class="grid grid-cols-2 gap-3 text-sm">
        <div>
            <span class="font-semibold text-gray-500 dark:text-gray-400">Post Type</span>
            <p class="text-gray-800 dark:text-gray-200">{{ $record->post_type }}</p>
        </div>
        <div>
            <span class="font-semibold text-gray-500 dark:text-gray-400">Flag Reason</span>
            <p class="text-gray-800 dark:text-gray-200">{{ $record->flag_reason }}</p>
        </div>
        <div>
            <span class="font-semibold text-gray-500 dark:text-gray-400">Flag Field</span>
            <p class="text-gray-800 dark:text-gray-200">{{ $record->flag_field ?? '—' }}</p>
        </div>
        <div>
            <span class="font-semibold text-gray-500 dark:text-gray-400">IP Address</span>
            <p class="text-gray-800 dark:text-gray-200">{{ $record->ip ?? '—' }}</p>
        </div>
        <div>
            <span class="font-semibold text-gray-500 dark:text-gray-400">User</span>
            <p class="text-gray-800 dark:text-gray-200">{{ $record->user?->name ?? 'Guest' }}</p>
        </div>
        <div>
            <span class="font-semibold text-gray-500 dark:text-gray-400">Attempted At</span>
            <p class="text-gray-800 dark:text-gray-200">{{ $record->created_at->format('M d, Y H:i') }}</p>
        </div>
    </div>
</div>
