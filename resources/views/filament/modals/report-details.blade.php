@php
    $reportable = $record->reportable;
    $type = class_basename($record->reportable_type);
    $title = $reportable?->title ?? $reportable?->name ?? '—';
    $description = $reportable?->description ?? null;
    $contentUrl = match($type) {
        'Listing'  => route('classifieds.show', $record->reportable_id),
        'Event'    => route('events.show', $record->reportable_id),
        'Job'      => route('jobs.show', $record->reportable_id),
        'Business' => route('directory.show', $record->reportable_id),
        default    => null,
    };
    $reasonLabels = \App\Models\Report::reasons();
@endphp

<div style="font-family: inherit; padding: 4px 0;">

    {{-- Status + Meta row --}}
    <div style="display:flex;gap:10px;flex-wrap:wrap;margin-bottom:20px;align-items:center">
        <span style="padding:4px 12px;border-radius:20px;font-size:12px;font-weight:700;
            background:{{ $record->status === 'pending' ? '#fef3c7' : ($record->status === 'actioned' ? '#dcfce7' : '#f3f4f6') }};
            color:{{ $record->status === 'pending' ? '#92400e' : ($record->status === 'actioned' ? '#166534' : '#6b7280') }}">
            {{ ucfirst($record->status) }}
        </span>
        <span style="font-size:12px;color:#6b7280">Report #{{ $record->id }}</span>
        <span style="font-size:12px;color:#6b7280">{{ $record->created_at->format('M d, Y h:i A') }}</span>
    </div>

    {{-- Reason + Details --}}
    <div style="background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:14px 16px;margin-bottom:16px">
        <div style="font-size:12px;font-weight:700;color:#991b1b;text-transform:uppercase;letter-spacing:.05em;margin-bottom:6px">Reason</div>
        <div style="font-size:15px;font-weight:600;color:#1f2937">{{ $reasonLabels[$record->reason] ?? ucfirst($record->reason) }}</div>
        @if($record->details)
            <div style="margin-top:8px;font-size:13px;color:#374151;line-height:1.6;border-top:1px solid #fecaca;padding-top:8px">
                {{ $record->details }}
            </div>
        @endif
    </div>

    {{-- Reporter info --}}
    <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:14px 16px;margin-bottom:16px">
        <div style="font-size:12px;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;margin-bottom:10px">Reporter</div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:8px">
            <div>
                <div style="font-size:11px;color:#94a3b8;margin-bottom:2px">Name</div>
                <div style="font-size:13px;font-weight:600;color:#1e293b">{{ $record->user?->name ?? 'Guest' }}</div>
            </div>
            <div>
                <div style="font-size:11px;color:#94a3b8;margin-bottom:2px">Email</div>
                <div style="font-size:13px;color:#1e293b">{{ $record->user?->email ?? '—' }}</div>
            </div>
            <div>
                <div style="font-size:11px;color:#94a3b8;margin-bottom:2px">IP Address</div>
                <div style="font-size:13px;color:#1e293b;font-family:monospace">{{ $record->reporter_ip ?? '—' }}</div>
            </div>
            <div>
                <div style="font-size:11px;color:#94a3b8;margin-bottom:2px">Reported At</div>
                <div style="font-size:13px;color:#1e293b">{{ $record->created_at->diffForHumans() }}</div>
            </div>
        </div>
    </div>

    {{-- Reported Content --}}
    <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:14px 16px;">
        <div style="font-size:12px;font-weight:700;color:#475569;text-transform:uppercase;letter-spacing:.05em;margin-bottom:10px">
            Reported Content
            <span style="font-weight:400;text-transform:none;font-size:12px;color:#94a3b8;margin-left:6px">{{ $type }}</span>
        </div>

        @if($reportable)
            <div style="font-size:15px;font-weight:700;color:#1e293b;margin-bottom:8px">{{ $title }}</div>

            @if($description)
                <div style="font-size:13px;color:#475569;line-height:1.6;margin-bottom:12px;max-height:120px;overflow-y:auto;background:#fff;border:1px solid #e2e8f0;border-radius:6px;padding:10px">
                    {!! Str::limit(strip_tags($description), 500) !!}
                </div>
            @endif

            <div style="display:flex;gap:8px;flex-wrap:wrap;font-size:12px">
                @if($reportable->city ?? null)
                    <span style="background:#e0f2fe;color:#0369a1;padding:3px 10px;border-radius:12px">📍 {{ $reportable->city }}</span>
                @endif
                @if(($reportable->status ?? null))
                    <span style="background:#f0fdf4;color:#166534;padding:3px 10px;border-radius:12px">Status: {{ $reportable->status }}</span>
                @endif
                @if($contentUrl)
                    <a href="{{ $contentUrl }}" target="_blank"
                       style="background:#ede9fe;color:#6d28d9;padding:3px 10px;border-radius:12px;text-decoration:none;font-weight:600">
                        🔗 View Live Post ↗
                    </a>
                @endif
            </div>
        @else
            <div style="color:#94a3b8;font-style:italic">Content has been deleted.</div>
        @endif
    </div>

</div>
