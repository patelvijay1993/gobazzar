<div style="padding:8px 4px">
    @php $total = $poll->options->sum('votes'); @endphp
    <div style="font-size:13px;color:#6b7280;margin-bottom:14px">
        Total votes: <strong style="color:#111">{{ number_format($total) }}</strong>
    </div>
    @foreach($poll->options as $opt)
        @php $pct = $total > 0 ? round(($opt->votes / $total) * 100) : 0; @endphp
        <div style="margin-bottom:12px">
            <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:4px">
                <span style="font-weight:600;color:#111">{{ $opt->label }}</span>
                <span style="color:#6b7280">{{ $opt->votes }} votes · {{ $pct }}%</span>
            </div>
            <div style="background:#f0f0f0;border-radius:6px;height:22px;overflow:hidden">
                <div style="height:100%;width:{{ $pct }}%;background:#1a3a8f;border-radius:6px;transition:width .4s"></div>
            </div>
        </div>
    @endforeach
</div>
