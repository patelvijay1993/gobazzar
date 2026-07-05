@if ($paginator->hasPages())
<nav aria-label="Pagination" style="margin-top:20px">
    <ul style="display:flex;align-items:center;gap:4px;list-style:none;padding:0;margin:0;flex-wrap:wrap">
        {{-- Previous --}}
        @if ($paginator->onFirstPage())
            <li><span style="display:inline-flex;align-items:center;justify-content:center;min-width:34px;height:34px;padding:0 10px;border-radius:6px;border:1.5px solid var(--border,#e5e7eb);font-size:13px;color:#bbb;cursor:default;background:#fafafa">&lsaquo;</span></li>
        @else
            <li><a href="{{ $paginator->previousPageUrl() }}" rel="prev" style="display:inline-flex;align-items:center;justify-content:center;min-width:34px;height:34px;padding:0 10px;border-radius:6px;border:1.5px solid var(--border,#e5e7eb);font-size:13px;color:var(--text,#1f2937);text-decoration:none;background:#fff;transition:all .15s" onmouseover="this.style.borderColor='var(--primary,#1a3a8f)';this.style.color='var(--primary,#1a3a8f)'" onmouseout="this.style.borderColor='var(--border,#e5e7eb)';this.style.color='var(--text,#1f2937)'">&lsaquo;</a></li>
        @endif

        {{-- Pages --}}
        @foreach ($elements as $element)
            @if (is_string($element))
                <li><span style="display:inline-flex;align-items:center;justify-content:center;min-width:34px;height:34px;padding:0 10px;font-size:13px;color:#9ca3af">{{ $element }}</span></li>
            @endif
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li aria-current="page"><span style="display:inline-flex;align-items:center;justify-content:center;min-width:34px;height:34px;padding:0 10px;border-radius:6px;border:1.5px solid var(--primary,#1a3a8f);font-size:13px;font-weight:700;color:#fff;background:var(--primary,#1a3a8f)">{{ $page }}</span></li>
                    @else
                        <li><a href="{{ $url }}" style="display:inline-flex;align-items:center;justify-content:center;min-width:34px;height:34px;padding:0 10px;border-radius:6px;border:1.5px solid var(--border,#e5e7eb);font-size:13px;color:var(--text,#1f2937);text-decoration:none;background:#fff;transition:all .15s" onmouseover="this.style.borderColor='var(--primary,#1a3a8f)';this.style.color='var(--primary,#1a3a8f)'" onmouseout="this.style.borderColor='var(--border,#e5e7eb)';this.style.color='var(--text,#1f2937)'">{{ $page }}</a></li>
                    @endif
                @endforeach
            @endif
        @endforeach

        {{-- Next --}}
        @if ($paginator->hasMorePages())
            <li><a href="{{ $paginator->nextPageUrl() }}" rel="next" style="display:inline-flex;align-items:center;justify-content:center;min-width:34px;height:34px;padding:0 10px;border-radius:6px;border:1.5px solid var(--border,#e5e7eb);font-size:13px;color:var(--text,#1f2937);text-decoration:none;background:#fff;transition:all .15s" onmouseover="this.style.borderColor='var(--primary,#1a3a8f)';this.style.color='var(--primary,#1a3a8f)'" onmouseout="this.style.borderColor='var(--border,#e5e7eb)';this.style.color='var(--text,#1f2937)'">&rsaquo;</a></li>
        @else
            <li><span style="display:inline-flex;align-items:center;justify-content:center;min-width:34px;height:34px;padding:0 10px;border-radius:6px;border:1.5px solid var(--border,#e5e7eb);font-size:13px;color:#bbb;cursor:default;background:#fafafa">&rsaquo;</span></li>
        @endif
    </ul>
</nav>
@endif
