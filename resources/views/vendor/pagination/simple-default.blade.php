@if ($paginator->hasPages())
<nav aria-label="Pagination" style="margin-top:20px;display:flex;gap:8px">
    @if ($paginator->onFirstPage())
        <span style="display:inline-flex;align-items:center;padding:6px 14px;border-radius:6px;border:1.5px solid var(--border,#e5e7eb);font-size:13px;color:#bbb;cursor:default;background:#fafafa">&lsaquo; @lang('pagination.previous')</span>
    @else
        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" style="display:inline-flex;align-items:center;padding:6px 14px;border-radius:6px;border:1.5px solid var(--border,#e5e7eb);font-size:13px;color:var(--text,#1f2937);text-decoration:none;background:#fff">&lsaquo; @lang('pagination.previous')</a>
    @endif
    @if ($paginator->hasMorePages())
        <a href="{{ $paginator->nextPageUrl() }}" rel="next" style="display:inline-flex;align-items:center;padding:6px 14px;border-radius:6px;border:1.5px solid var(--border,#e5e7eb);font-size:13px;color:var(--text,#1f2937);text-decoration:none;background:#fff">@lang('pagination.next') &rsaquo;</a>
    @else
        <span style="display:inline-flex;align-items:center;padding:6px 14px;border-radius:6px;border:1.5px solid var(--border,#e5e7eb);font-size:13px;color:#bbb;cursor:default;background:#fafafa">@lang('pagination.next') &rsaquo;</span>
    @endif
</nav>
@endif
