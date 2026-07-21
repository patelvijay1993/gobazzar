@extends('layouts.app')
@section('title', "GoBazaar — Canada's #1 Community Marketplace")
@section('description', "Canada's #1 Community Marketplace — Classifieds, Yellow Pages, Events, Jobs, Blog and more for your community in Canada.")

@push('schema')
@verbatim
<script type="application/ld+json">
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "Organization",
      "@id": "https://gobazaar.ca/#organization",
      "name": "GoBazaar",
      "url": "https://gobazaar.ca",
      "logo": {
        "@type": "ImageObject",
        "url": "https://gobazaar.ca/images/logo.png"
      },
      "sameAs": []
    },
    {
      "@type": "WebSite",
      "@id": "https://gobazaar.ca/#website",
      "url": "https://gobazaar.ca",
      "name": "GoBazaar",
      "description": "Canada's #1 Community Marketplace — Classifieds, Jobs, Events, Businesses and more.",
      "publisher": { "@id": "https://gobazaar.ca/#organization" },
      "potentialAction": {
        "@type": "SearchAction",
        "target": {
          "@type": "EntryPoint",
          "urlTemplate": "https://gobazaar.ca/classifieds?search={search_term_string}"
        },
        "query-input": "required name=search_term_string"
      }
    }
  ]
}
</script>
@endverbatim
@endpush

@push('styles')
<style>
/* ══ HERO ══════════════════════════════════════════════════════════ */
.hero{background:var(--primary);position:relative;overflow:hidden}
.hero::before{content:'';position:absolute;inset:0;background:url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23ffffff' fill-opacity='0.04'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/svg%3E");pointer-events:none;z-index:1}
/* City/province landmark background */
.hero.hero-bg{background-size:cover;background-position:center}
.hero.hero-bg::before{display:none}
.hero-overlay{position:absolute;inset:0;background:linear-gradient(120deg,rgba(18,41,112,.92) 0%,rgba(26,58,143,.82) 55%,rgba(26,58,143,.55) 100%);z-index:0}
.hero.hero-bg .hero-inner{position:relative;z-index:2}
.hero-inner{max-width:1280px;margin:0 auto;padding:40px 24px;display:flex;gap:28px;align-items:center;position:relative;z-index:2}
.hero-left{flex:1;min-width:0}
.hero-eyebrow{display:inline-flex;align-items:center;gap:6px;background:rgba(255,255,255,.12);color:rgba(255,255,255,.85);font-size:11px;font-weight:600;padding:5px 13px;border-radius:20px;margin-bottom:14px;letter-spacing:.6px;text-transform:uppercase;border:1px solid rgba(255,255,255,.15)}
.hero-title{font-family:var(--fh);font-size:38px;font-weight:800;color:#fff;line-height:1.15;margin-bottom:10px}
.hero-title span{color:var(--accent)}
.hero-sub{font-size:14px;color:rgba(255,255,255,.7);margin-bottom:22px;line-height:1.6}

/* Search */
.hero-search{display:flex;background:#fff;border-radius:10px;overflow:hidden;box-shadow:0 8px 30px rgba(0,0,0,.25);height:54px}
.hs-icon{display:flex;align-items:center;padding:0 8px 0 16px;flex-shrink:0}
.hs-icon i{font-size:16px;color:#9ca3af}
.hs-input{flex:1;border:none;font-size:14px;color:#111;font-family:var(--fb);min-width:0;background:transparent}
.hs-input:focus{outline:none}
.hs-div{width:1px;height:28px;background:#e5e7eb;align-self:center;flex-shrink:0}
.hs-sel{display:flex;align-items:center;gap:5px;padding:0 14px;flex-shrink:0;white-space:nowrap}
.hs-sel i{font-size:12px;color:var(--primary)}
.hs-sel select{border:none;background:none;font-size:13px;color:#555;font-family:var(--fb);appearance:none;cursor:pointer}
.hs-sel select:focus{outline:none}
.hs-btn{background:var(--primary);color:#fff;border:none;padding:0 22px;font-size:14px;font-weight:700;display:flex;align-items:center;gap:7px;height:100%;cursor:pointer;transition:background .2s;white-space:nowrap;flex-shrink:0}
.hs-btn:hover{background:var(--primary-dark)}

.popular-tags{display:flex;align-items:center;gap:7px;margin-top:14px;flex-wrap:wrap}
.popular-label{font-size:12px;color:rgba(255,255,255,.5);white-space:nowrap}
.ptag{background:rgba(255,255,255,.12);color:rgba(255,255,255,.9);font-size:11.5px;padding:4px 12px;border-radius:20px;cursor:pointer;border:1px solid rgba(255,255,255,.15);transition:background .15s}
.ptag:hover{background:rgba(255,255,255,.22)}

.hero-stats{display:flex;gap:28px;margin-top:24px;padding-top:20px;border-top:1px solid rgba(255,255,255,.12);flex-wrap:wrap}
.hero-stat-num{font-family:var(--fh);font-size:22px;font-weight:800;color:#fff}
.hero-stat-lbl{font-size:11px;color:rgba(255,255,255,.5);margin-top:1px}

/* Hero right cards */
.hero-right{width:270px;flex-shrink:0;display:flex;flex-direction:column;gap:12px}
.hcard{background:#fff;border-radius:var(--radius);padding:16px}
.hcard-icon-row{display:flex;align-items:center;gap:10px;margin-bottom:10px}
.hcard-icon{width:38px;height:38px;border-radius:10px;background:var(--primary-light);display:flex;align-items:center;justify-content:center;flex-shrink:0}
.hcard-icon i{font-size:17px;color:var(--primary)}
.hcard-title{font-size:14px;font-weight:700;color:#111;line-height:1.2}
.hcard-sub{font-size:11.5px;color:#888;margin-top:1px}
.hcard-btn{width:100%;background:var(--primary);color:#fff;border:none;padding:10px;border-radius:8px;font-size:13px;font-weight:700;display:flex;align-items:center;justify-content:center;gap:6px;text-decoration:none;transition:background .2s}
.hcard-btn:hover{background:var(--primary-dark)}
.hcard-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:8px}
.hcard-mini{display:flex;flex-direction:column;align-items:center;gap:4px;text-decoration:none}
.hcard-mini-icon{width:38px;height:38px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:17px;margin:0 auto}
.hcard-mini-lbl{font-size:10px;font-weight:600;color:#333;text-align:center}
.hero-trust{background:rgba(0,0,0,.2);border:1px solid rgba(255,255,255,.1);border-radius:var(--radius);padding:13px 15px}
.trust-title{font-size:12px;font-weight:700;color:#fff;margin-bottom:9px;display:flex;align-items:center;gap:7px}
.trust-title i{color:var(--accent)}
.trust-item{display:flex;align-items:center;gap:7px;font-size:12px;color:rgba(255,255,255,.75);margin-bottom:6px}
.trust-item:last-child{margin-bottom:0}
.trust-item i{color:#4cd964;font-size:11px}

/* ══ PAGE BODY ══════════════════════════════════════════════════════ */
.home-body{max-width:1280px;margin:0 auto;padding:22px 24px;display:grid;grid-template-columns:1fr 270px;gap:22px;align-items:start}
.home-main{min-width:0}
.home-sidebar{display:flex;flex-direction:column;gap:14px}

/* ══ SECTION HEAD ══════════════════════════════════════════════════ */
.sh{display:flex;align-items:center;justify-content:space-between;margin-bottom:14px}
.sh-title{font-family:var(--fh);font-size:16px;font-weight:800;color:var(--text);display:flex;align-items:center;gap:8px}
.sh-title i{color:var(--primary);font-size:15px}
.sh-link{font-size:12px;color:var(--primary);font-weight:600;text-decoration:none;background:var(--primary-light);padding:4px 12px;border-radius:20px;display:flex;align-items:center;gap:4px;transition:background .15s}
.sh-link:hover{background:#d0d9f0}
.sec-div{height:1px;background:var(--border);margin:4px 0 22px}

/* ══ QUICK CAT TABS ════════════════════════════════════════════════ */
.cat-tabs{display:grid;grid-template-columns:repeat(7,1fr);gap:8px;margin-bottom:22px}
.cat-tab{background:#fff;border:1.5px solid var(--border);border-radius:12px;padding:12px 6px;text-align:center;text-decoration:none;color:var(--text);transition:all .15s;display:block}
.cat-tab:hover{border-color:var(--primary);transform:translateY(-2px);box-shadow:0 4px 12px rgba(26,58,143,.1)}
.ct-icon{width:44px;height:44px;border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 7px;font-size:20px}
.ct-name{font-size:11px;font-weight:700;color:var(--text)}
.ct-count{font-size:10px;color:var(--muted);margin-top:1px}

/* ══ CLASSIFIEDS GRID ══════════════════════════════════════════════ */
.cl-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:12px;margin-bottom:22px}
.cl-card{background:#fff;border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;display:block;text-decoration:none;color:var(--text);transition:all .18s}
.cl-card:hover{border-color:var(--primary);transform:translateY(-2px);box-shadow:0 6px 18px rgba(26,58,143,.11)}
.cl-img{height:130px;position:relative;overflow:hidden;background:#f5f0ec;display:flex;align-items:center;justify-content:center;font-size:40px}
.cl-img img{width:100%;height:100%;object-fit:cover;display:block}
.cl-feat{position:absolute;top:7px;left:7px;background:var(--primary);color:#fff;font-size:9px;font-weight:700;padding:2px 8px;border-radius:4px;text-transform:uppercase}
.cl-fav{position:absolute;top:7px;right:7px;background:rgba(255,255,255,.9);width:26px;height:26px;border-radius:50%;display:flex;align-items:center;justify-content:center}
.cl-fav i{font-size:12px;color:#bbb}
.cl-body{padding:10px 12px}
.cl-price{font-family:var(--fh);font-size:16px;font-weight:800;color:var(--primary);margin-bottom:2px}
.cl-price small{font-size:11px;font-weight:400;color:var(--muted)}
.cl-title{font-size:12.5px;font-weight:500;color:var(--text);white-space:nowrap;overflow:hidden;text-overflow:ellipsis;margin-bottom:3px}
.cl-cat{font-size:10px;color:var(--muted);margin-bottom:8px}
.cl-foot{display:flex;justify-content:space-between;font-size:10.5px;color:var(--muted);border-top:1px solid var(--border);padding-top:7px}
.cl-foot i{margin-right:2px;font-size:10px}

/* ══ EVENTS GRID ══════════════════════════════════════════════════ */
.ev-grid{display:grid;grid-template-columns:1fr 1fr;gap:10px;margin-bottom:22px}
.ev-card{background:#fff;border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;display:flex;text-decoration:none;color:var(--text);transition:all .15s}
.ev-card:hover{border-color:var(--primary);transform:translateY(-1px)}
.ev-date{min-width:56px;display:flex;flex-direction:column;align-items:center;justify-content:center;padding:10px 6px;flex-shrink:0}
.ev-day{font-family:var(--fh);font-size:24px;font-weight:800;color:#fff;line-height:1}
.ev-mon{font-size:10px;color:rgba(255,255,255,.75);text-transform:uppercase;letter-spacing:.5px}
.ev-body{padding:10px 13px;flex:1;min-width:0}
.ev-title{font-size:13px;font-weight:600;color:var(--text);margin-bottom:4px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.ev-meta{font-size:11px;color:var(--muted);display:flex;align-items:center;gap:4px;margin-bottom:5px}
.ev-meta i{font-size:11px}
.ev-badge{display:inline-block;font-size:10px;font-weight:600;padding:2px 9px;border-radius:20px}
.badge-free{background:#dcfce7;color:#15803d}
.badge-paid{background:#fef9c3;color:#92400e}

/* ══ JOBS ══════════════════════════════════════════════════════════ */
.jobs-list{display:flex;flex-direction:column;gap:9px;margin-bottom:22px}
.job-card{background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:13px 15px;display:flex;gap:13px;align-items:flex-start;text-decoration:none;color:var(--text);transition:all .15s}
.job-card:hover{border-color:var(--primary);box-shadow:0 3px 12px rgba(26,58,143,.08)}
.job-logo{width:44px;height:44px;border-radius:10px;background:#f0ede8;display:flex;align-items:center;justify-content:center;font-size:20px;flex-shrink:0;border:1px solid var(--border);overflow:hidden}
.job-logo img{width:100%;height:100%;object-fit:cover;border-radius:9px}
.job-info{flex:1;min-width:0}
.job-title{font-size:13px;font-weight:700;color:var(--text);margin-bottom:2px}
.job-co{font-size:11.5px;color:var(--muted);margin-bottom:6px}
.job-tags{display:flex;gap:5px;flex-wrap:wrap}
.job-tag{font-size:10px;background:#f0ede8;color:#555;padding:2px 9px;border-radius:20px;border:1px solid var(--border)}
.job-sal{font-family:var(--fh);font-size:13.5px;font-weight:800;color:var(--green);white-space:nowrap;flex-shrink:0}

/* ══ NEWS ══════════════════════════════════════════════════════════ */
.news-wrap{background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:4px 16px;margin-bottom:22px}
.news-item{padding:11px 0;border-bottom:1px solid var(--border)}
.news-item:last-child{border-bottom:none}
.news-item:hover .news-title{color:var(--primary)}
.news-title{font-size:13px;font-weight:600;color:var(--text);margin-bottom:3px;transition:color .15s;cursor:pointer}
.news-meta{font-size:11px;color:var(--muted);display:flex;align-items:center;gap:7px}
.ntag{font-size:10px;font-weight:600;padding:1px 8px;border-radius:20px}
.ntag-hot{background:#fee2e2;color:#b91c1c}
.ntag-comm{background:var(--primary-light);color:var(--primary)}
.ntag-new{background:#dcfce7;color:#15803d}

/* ══ BIZ GRID ══════════════════════════════════════════════════════ */
.biz-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:11px;margin-bottom:22px}
.biz-card{background:#fff;border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;display:block;text-decoration:none;color:var(--text);transition:all .15s}
.biz-card:hover{border-color:var(--primary);transform:translateY(-2px)}
.biz-img{width:100%;height:80px;background:#f5f0ec;display:flex;align-items:center;justify-content:center;font-size:32px;overflow:hidden}
.biz-img img{width:100%;height:100%;object-fit:cover;display:block}
.biz-body{padding:9px 10px 12px}
.biz-name{font-size:12px;font-weight:700;color:var(--text);margin-bottom:2px}
.biz-cat{font-size:10px;color:var(--muted);margin-bottom:6px}
.biz-stars{display:flex;align-items:center;justify-content:center;gap:2px;font-size:11px;color:var(--accent)}

/* ══ ALL LISTINGS ══════════════════════════════════════════════════ */
.al-list{display:flex;flex-direction:column;gap:9px;margin-bottom:22px}
.al-item{background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:11px;display:flex;gap:11px;text-decoration:none;color:var(--text);transition:border-color .15s}
.al-item:hover{border-color:var(--primary)}
.al-thumb{width:68px;height:62px;border-radius:8px;background:#f5f0ec;flex-shrink:0;overflow:hidden;border:1px solid var(--border)}
.al-thumb img{width:100%;height:100%;object-fit:cover;display:block}
.al-info{flex:1;min-width:0}
.al-title{font-size:12.5px;font-weight:600;color:var(--text);margin-bottom:3px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.al-price{font-family:var(--fh);font-size:14.5px;font-weight:800;color:var(--primary);margin-bottom:4px}
.al-price small{font-size:11px;font-weight:400;color:var(--muted)}
.al-meta{display:flex;gap:12px;font-size:11px;color:var(--muted)}
.al-meta i{font-size:11px;margin-right:2px}

/* ══ SIDEBAR ═══════════════════════════════════════════════════════ */
.sb-card{background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:15px}
.sb-head{font-family:var(--fh);font-size:14px;font-weight:700;color:var(--text);margin-bottom:12px;display:flex;align-items:center;gap:7px}
.sb-head i{color:var(--primary)}

.post-btn{width:100%;background:var(--primary);color:#fff;border:none;padding:11px;border-radius:8px;font-size:13px;font-weight:700;display:flex;align-items:center;justify-content:center;gap:6px;margin-bottom:8px;text-decoration:none;transition:background .2s}
.post-btn:hover{background:var(--primary-dark)}
.free-note{background:#dcfce7;border:1px solid #bbf7d0;padding:8px;border-radius:7px;font-size:11px;color:#15803d;text-align:center;font-weight:500}

.stat-grid{display:grid;grid-template-columns:1fr 1fr;gap:7px}
.stat-box{background:#f5f7fb;border-radius:8px;padding:11px;text-align:center;border:1px solid #e8edf7}
.stat-num{font-family:var(--fh);font-size:19px;font-weight:800;color:var(--primary)}
.stat-lbl{font-size:10px;color:var(--muted);margin-top:1px}

.poll-q{font-size:12.5px;color:var(--text);margin-bottom:12px;font-weight:500;line-height:1.4}
.poll-row{display:flex;align-items:center;gap:8px;margin-bottom:7px}
.poll-lbl{font-size:12px;color:var(--text);min-width:28px;font-weight:600}
.poll-bar-bg{flex:1;background:#f0ede8;border-radius:4px;height:20px;overflow:hidden;border:1px solid var(--border)}
.poll-bar{height:100%;border-radius:4px;display:flex;align-items:center;padding-left:8px;font-size:10px;color:#fff;font-weight:600}
.poll-btn{width:100%;background:var(--primary);color:#fff;border:none;padding:9px;border-radius:8px;font-size:12.5px;font-weight:700;margin-top:8px;cursor:pointer;transition:background .2s}
.poll-btn:hover{background:var(--primary-dark)}

.ql{display:flex;align-items:center;gap:9px;padding:8px 0;border-bottom:1px solid var(--border);text-decoration:none;color:var(--text);font-size:12.5px;font-weight:500;transition:color .15s,padding-left .15s}
.ql:last-child{border-bottom:none}
.ql:hover{color:var(--primary);padding-left:4px}
.ql-ico{width:28px;height:28px;border-radius:7px;display:flex;align-items:center;justify-content:center;flex-shrink:0;font-size:13px}

.featured-ad{border:1px solid var(--border);border-radius:var(--radius);overflow:hidden;margin-bottom:9px;text-decoration:none;display:block;transition:border-color .15s}
.featured-ad:last-child{margin-bottom:0}
.featured-ad:hover{border-color:var(--primary)}
.fa-img{height:88px;overflow:hidden;background:#f5f0ec}
.fa-img img{width:100%;height:100%;object-fit:cover;display:block}
.fa-body{padding:10px 12px}
.fa-name{font-size:12px;font-weight:700;color:var(--text);margin-bottom:1px}
.fa-sub{font-size:10px;color:var(--muted);margin-bottom:7px}
.fa-btn{width:100%;padding:6px;border-radius:6px;font-size:11px;font-weight:700;border:none;cursor:pointer}

.la-list{display:flex;flex-direction:column}
.la-item{display:flex;gap:9px;padding:9px 0;border-bottom:1px solid var(--border);text-decoration:none;color:var(--text);align-items:flex-start;transition:padding-left .15s}
.la-item:last-child{border-bottom:none}
.la-item:hover{padding-left:4px}
.la-thumb{width:50px;height:46px;border-radius:7px;overflow:hidden;background:#f5f0ec;flex-shrink:0;border:1px solid var(--border)}
.la-thumb img{width:100%;height:100%;object-fit:cover;display:block}
.la-info{flex:1;min-width:0}
.la-name{font-size:11px;font-weight:600;color:var(--text);margin-bottom:2px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.la-price{font-family:var(--fh);font-size:12.5px;font-weight:800;color:var(--primary)}
.la-price small{font-size:10px;font-weight:400;color:var(--muted)}
.la-loc{font-size:10px;color:var(--muted)}
.la-side{flex-shrink:0;display:flex;flex-direction:column;align-items:flex-end;gap:4px}
.la-time{font-size:10px;color:var(--muted)}
.la-fav i{font-size:14px;color:#ddd;cursor:pointer}
.la-fav i:hover{color:var(--primary)}

/* Mobile sidebar ads — hidden on desktop, shown in main on mobile */
.mobile-sidebar-ads{display:none}
@media(max-width:600px){
  .mobile-sidebar-ads{display:block;margin:14px 0}
  .mobile-sidebar-ads .ad-slot--sidebar img{height:180px;border-radius:10px}
}

/* ══ RESPONSIVE ═════════════════════════════════════════════════════ */
@media(max-width:1024px){
  .hero-right{width:240px}
  .cl-grid{grid-template-columns:repeat(2,1fr)}
  .biz-grid{grid-template-columns:repeat(2,1fr)}
  .cat-tabs{grid-template-columns:repeat(4,1fr)}
}
@media(max-width:900px){
  .hero-inner{flex-direction:column;padding:26px 18px 30px}
  .hero-right{display:none}
  .hero-title{font-size:27px}
  .hero-sub{font-size:13px;margin-bottom:16px}
  .hero-stats{gap:16px;margin-top:18px}
  .hero-search{height:48px}
  .hs-sel{display:none}
  .hs-div{display:none}
  .home-body{grid-template-columns:1fr;padding:14px 16px}
  .home-sidebar{display:none}
  .cat-tabs{grid-template-columns:repeat(4,1fr);gap:7px}
  .cl-grid{grid-template-columns:repeat(2,1fr);gap:10px}
  .ev-grid{grid-template-columns:1fr}
  .biz-grid{grid-template-columns:repeat(2,1fr)}
}
@media(max-width:520px){
  /* Hero — compact app style */
  .hero{padding:0}
  .hero-inner{padding:16px 14px 20px;gap:14px;flex-direction:column}
  .hero-right{display:none}
  .hero-eyebrow{font-size:10px;padding:4px 10px;margin-bottom:10px}
  .hero-title{font-size:22px;margin-bottom:8px}
  .hero-sub{font-size:12.5px;margin-bottom:14px}
  .hero-stats{gap:14px;margin-top:16px;padding-top:14px}
  .hero-stat-num{font-size:18px}
  .hero-stat-lbl{font-size:10px}

  /* Search bar - pill style */
  .hero-search{height:48px;border-radius:12px}
  .hs-input{font-size:13px;padding-left:4px}
  .hs-sel{display:none}
  .hs-div{display:none}
  .hs-btn{padding:0 16px;font-size:13px;border-radius:0 12px 12px 0}
  .popular-tags{gap:5px;margin-top:10px}
  .ptag{font-size:11px;padding:3px 10px}

  /* Category icons — 4-col horizontal scroll */
  .cat-tabs{grid-template-columns:repeat(4,1fr);gap:7px;margin-bottom:16px}
  .cat-tab{padding:10px 4px;border-radius:14px}
  .ct-icon{width:40px;height:40px;font-size:19px;margin-bottom:5px}
  .ct-name{font-size:10.5px;font-weight:700}
  .ct-count{display:none}

  /* Cards - 2 column */
  .cl-grid{grid-template-columns:1fr 1fr;gap:10px;margin-bottom:16px}
  .cl-img{height:110px}
  .cl-body{padding:8px 10px}
  .cl-price{font-size:14px}
  .cl-title{font-size:11.5px}

  .biz-grid{grid-template-columns:1fr 1fr;gap:8px}
  .biz-img{height:70px}
  .biz-name{font-size:11px}

  /* Jobs - full width card */
  .job-sal{display:none}
  .job-card{padding:10px 12px;gap:10px}
  .job-logo{width:38px;height:38px}
  .job-title{font-size:12px}
  .job-co{font-size:10.5px}

  /* Events - single column */
  .ev-grid{grid-template-columns:1fr;gap:8px}

  /* Section heads */
  .sh-title{font-size:15px}
  .sh-link{font-size:11px;padding:3px 10px}

  /* Home body full width */
  .home-body{grid-template-columns:1fr;padding:12px 12px;gap:0}
  .home-sidebar{display:none}
  .sec-div{margin:2px 0 16px}

  /* News */
  .news-title{font-size:12.5px}
}
</style>
@endpush

@section('content')

@php
$heroLocLabel = request('city') ?: request('province');
@endphp

{{-- ═══ HERO ═══ --}}
<div class="hero @if($heroBg) hero-bg @endif" @if($heroBg) style="background-image:url('{{ $heroBg }}')" @endif>
  @if($heroBg)<div class="hero-overlay"></div>@endif
  <div class="hero-inner">
    <div class="hero-left">
      <div class="hero-eyebrow"><i class="fa-solid fa-star"></i> Canada's #1 Community Marketplace</div>
      @if($heroLocLabel)
        <h1 class="hero-title">Canada's Community<br><span>Marketplace in {{ $heroLocLabel }}</span></h1>
        <p class="hero-sub">Classifieds · Yellow Pages · Events · Jobs · Blog — everything your community in {{ $heroLocLabel }} needs, in one place.</p>
      @else
        <h1 class="hero-title">Canada's #1 Community<br><span>Marketplace</span></h1>
        <p class="hero-sub">Classifieds · Yellow Pages · Events · Jobs · Blog — everything your community needs, in one place.</p>
      @endif

      <div class="hero-search">
        <div class="hs-icon"><i class="fa-solid fa-magnifying-glass"></i></div>
        <input class="hs-input" type="text" id="hero-q" placeholder="What are you looking for?" value="{{ request('search') }}">
        <div class="hs-div"></div>
        <div class="hs-sel">
          <i class="fa-solid fa-map"></i>
          <select id="hero-prov" onchange="heroLoadCities(this.value)">
            <option value="">All Provinces</option>
            @foreach($provinces as $prov)
              <option value="{{ $prov }}">{{ $prov }}</option>
            @endforeach
          </select>
        </div>
        <div class="hs-div"></div>
        <div class="hs-sel">
          <i class="fa-solid fa-location-dot"></i>
          <select id="hero-city">
            <option value="">All Cities</option>
          </select>
        </div>
        <button class="hs-btn" onclick="heroSubmit()"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
      </div>
      <div style="margin-top:8px">
        <button type="button" id="hero-loc-btn" onclick="heroDetectLocation(this)" style="background:none;border:1px solid rgba(255,255,255,.5);color:#fff;border-radius:20px;padding:4px 13px;font-size:12px;cursor:pointer;font-weight:600;display:inline-flex;align-items:center;gap:5px">
          <i class="fa-solid fa-location-crosshairs"></i> Use my location
        </button>
      </div>

      <div class="popular-tags">
        <span class="popular-label">Popular:</span>
        @foreach(['iPhone','Tiffin Service','House for Rent','IT Jobs','Driving Lessons'] as $tag)
          <span class="ptag" onclick="document.getElementById('hero-q').value='{{ $tag }}';heroSubmit()">{{ $tag }}</span>
        @endforeach
      </div>

      <div class="hero-stats">
        <div><div class="hero-stat-num">{{ number_format($stats['businesses']) }}+</div><div class="hero-stat-lbl">Businesses</div></div>
        <div><div class="hero-stat-num">{{ number_format($stats['listings']) }}+</div><div class="hero-stat-lbl">Free Ads</div></div>
        <div><div class="hero-stat-num">{{ number_format($stats['events']) }}+</div><div class="hero-stat-lbl">Events</div></div>
        <div><div class="hero-stat-num">{{ number_format(\App\Models\User::count()) }}+</div><div class="hero-stat-lbl">Members</div></div>
      </div>
    </div>

    <div class="hero-right">
      <div class="hcard">
        <div class="hcard-icon-row">
          <div class="hcard-icon"><i class="fa-solid fa-bullhorn"></i></div>
          <div>
            <div class="hcard-title">Post Your Ad</div>
            <div class="hcard-sub">Reach thousands of local buyers & sellers</div>
          </div>
        </div>
        @auth
          <a href="{{ route('post.create') }}" class="hcard-btn"><i class="fa-solid fa-plus"></i> Post Your Ad</a>
        @else
          <a href="{{ route('register') }}" class="hcard-btn"><i class="fa-solid fa-plus"></i> Post Your Ad</a>
        @endauth
      </div>

      <div class="hcard" style="padding:13px 15px">
        <div class="hcard-grid">
          @foreach([
            [route('classifieds.index', ['categories' => $housingCategories]),'🏠','#fef6e4','Housing'],
            [route('jobs.index'),'💼','#e8f5e9','Jobs'],
            [route('classifieds.index', ['category' => $autosCategoryId]),'🚗','#fff3e0','Autos'],
            [route('directory.index', ['category' => $diningCategoryId]),'🍛','#fce4ec','Dining'],
            [route('events.index'),'🎉','#e8eaf6','Events'],
            [route('blog.index'),'📰','#e0f7fa','Blog'],
          ] as [$url,$icon,$bg,$lbl])
          <a href="{{ $url }}" class="hcard-mini">
            <div class="hcard-mini-icon" style="background:{{ $bg }}">{{ $icon }}</div>
            <div class="hcard-mini-lbl">{{ $lbl }}</div>
          </a>
          @endforeach
        </div>
      </div>

      <div class="hero-trust">
        <div class="trust-title"><i class="fa-solid fa-shield-halved"></i> Buy & Sell with Confidence</div>
        <div class="trust-item"><i class="fa-solid fa-circle-check"></i> Verified users</div>
        <div class="trust-item"><i class="fa-solid fa-circle-check"></i> Safe & secure platform</div>
        <div class="trust-item"><i class="fa-solid fa-circle-check"></i> Indian community driven</div>
      </div>
    </div>
  </div>
</div>

{{-- ═══ HOME BANNER AD ═══ --}}
@if($ads->where('position','home-banner')->isNotEmpty())
<div style="max-width:1280px;margin:14px auto 0;padding:0 24px">
  <x-ad-slot position="home-banner" :ads="$ads" />
</div>
@endif

{{-- ═══ BODY ═══ --}}
<div class="home-body">
<div class="home-main">

  {{-- QUICK CATEGORY TABS --}}
  <div class="cat-tabs">
    <a href="{{ route('classifieds.index') }}" class="cat-tab">
      <div class="ct-icon" style="background:#fef6e4"><i class="fa-solid fa-tag" style="color:#7a5200"></i></div>
      <div class="ct-name">Classifieds</div><div class="ct-count">{{ number_format($stats['listings']) }}+ Ads</div>
    </a>
    <a href="{{ route('jobs.index') }}" class="cat-tab">
      <div class="ct-icon" style="background:#e8f5e9"><i class="fa-solid fa-briefcase" style="color:#2e7d32"></i></div>
      <div class="ct-name">Jobs</div><div class="ct-count">{{ number_format($stats['jobs']??0) }}+ Jobs</div>
    </a>
    <a href="{{ route('classifieds.index', ['categories' => $housingCategories]) }}" class="cat-tab">
      <div class="ct-icon" style="background:#e8edf7"><i class="fa-solid fa-building" style="color:var(--primary)"></i></div>
      <div class="ct-name">Rentals</div><div class="ct-count">Rooms & Apts</div>
    </a>
    <a href="{{ route('events.index') }}" class="cat-tab">
      <div class="ct-icon" style="background:#e8eaf6"><i class="fa-solid fa-calendar-days" style="color:#3949ab"></i></div>
      <div class="ct-name">Events</div><div class="ct-count">{{ number_format($stats['events']) }}+ Events</div>
    </a>
    <a href="{{ route('directory.index') }}" class="cat-tab">
      <div class="ct-icon" style="background:#e0f7fa"><i class="fa-solid fa-building-columns" style="color:#00838f"></i></div>
      <div class="ct-name">Directory</div><div class="ct-count">{{ number_format($stats['businesses']) }}+ Biz</div>
    </a>
    <a href="{{ route('blog.index') }}" class="cat-tab">
      <div class="ct-icon" style="background:#fff3e0"><i class="fa-solid fa-newspaper" style="color:#e65100"></i></div>
      <div class="ct-name">Blog</div><div class="ct-count">Community</div>
    </a>
    <a href="{{ route('classifieds.index') }}" class="cat-tab">
      <div class="ct-icon" style="background:#f1efe8"><i class="fa-solid fa-ellipsis" style="color:#666"></i></div>
      <div class="ct-name">More</div><div class="ct-count">All Categories</div>
    </a>
  </div>

  {{-- FEATURED CLASSIFIEDS --}}
  <div class="sh">
    <div class="sh-title"><i class="fa-solid fa-star"></i> Featured Classifieds</div>
    <a href="{{ route('classifieds.index', array_filter(['province' => request('province'), 'city' => request('city')])) }}" class="sh-link">View All <i class="fa-solid fa-arrow-right" style="font-size:10px"></i></a>
  </div>
  @if($latestListings->isEmpty())
    <div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:30px;text-align:center;color:var(--muted);font-size:13px;margin-bottom:22px">
      <div style="font-size:32px;margin-bottom:8px">📭</div>
      No Classifieds Found.
      <a href="{{ route('post.create') }}" style="color:var(--primary);font-weight:600;text-decoration:none;display:block;margin-top:6px">Post the first one →</a>
    </div>
  @else
  <div class="cl-grid">
    @foreach($latestListings->take(4) as $i => $listing)
      <a href="{{ route('classifieds.show', $listing->slug) }}" class="cl-card">
        <div class="cl-img">
          @if($listing->is_featured)<div class="cl-feat">Featured</div>@endif
          <div class="cl-fav"><i class="fa-regular fa-heart"></i></div>
          @if($listing->image_url)<img src="{{ $listing->image_url }}" alt="{{ $listing->title }}">
          @else<span style="font-size:38px">{{ $listing->category->icon ?? '📦' }}</span>@endif
        </div>
        <div class="cl-body">
          @if($listing->price)<div class="cl-price">{{ $listing->formatted_price }}<small>{{ $listing->price_unit }}</small></div>@endif
          <div class="cl-title">{{ $listing->title }}</div>
          <div style="display:flex;align-items:center;gap:5px;margin-bottom:4px;flex-wrap:wrap">
            <div class="cl-cat" style="margin-bottom:0">{{ $listing->category->name ?? 'Classifieds' }}</div>
            @if($listing->is_verified)
              <span style="display:inline-flex;align-items:center;gap:3px;font-size:10px;font-weight:700;background:#dcfce7;color:#15803d;padding:2px 7px;border-radius:20px;white-space:nowrap"><i class="fa-solid fa-circle-check" style="font-size:9px"></i> Verified</span>
            @endif
          </div>
          <div class="cl-foot"><span><i class="fa-solid fa-location-dot"></i>{{ $listing->location }}</span><span>{{ $listing->created_at->diffForHumans() }}</span></div>
        </div>
      </a>
    @endforeach
  </div>
  @endif

  <div class="sec-div"></div>

  {{-- INLINE AD --}}
  <x-ad-slot position="inline" :ads="$ads" />

  {{-- COMMUNITY EVENTS --}}
  @php $evColors=['#1a3a8f','#e8a020','#c0392b','#2e7d32']; @endphp
  <div class="sh">
    <div class="sh-title"><i class="fa-solid fa-calendar-days"></i> Community Events</div>
    <a href="{{ route('events.index', array_filter(['province' => request('province'), 'city' => request('city')])) }}" class="sh-link">View All <i class="fa-solid fa-arrow-right" style="font-size:10px"></i></a>
  </div>
  @if($upcomingEvents->isEmpty())
    <div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:30px;text-align:center;color:var(--muted);font-size:13px;margin-bottom:22px">
      <div style="font-size:32px;margin-bottom:8px">📅</div>
      No Events Found.
      <a href="{{ route('post.create') }}" style="color:var(--primary);font-weight:600;text-decoration:none;display:block;margin-top:6px">Post an event →</a>
    </div>
  @else
  <div class="ev-grid">
    @foreach($upcomingEvents->take(4) as $i => $event)
    <a href="{{ route('events.show', $event->slug) }}" class="ev-card">
      <div class="ev-date" style="background:{{ $evColors[$i%4] }}">
        <div class="ev-day">{{ $event->start_date->format('j') }}</div>
        <div class="ev-mon">{{ $event->start_date->format('M') }}</div>
      </div>
      <div class="ev-body">
        <div class="ev-title">{{ $event->title }}</div>
        <div class="ev-meta"><i class="fa-solid fa-location-dot"></i> {{ $event->city }}@if($event->venue) · {{ $event->venue }}@endif</div>
        @if($event->price)
          @php $isFree = strtolower($event->price)==='free'||$event->price==='0'; @endphp
          <span class="ev-badge {{ $isFree?'badge-free':'badge-paid' }}">{{ $isFree?'Free':$event->formatted_price }}</span>
        @endif
      </div>
    </a>
    @endforeach
  </div>
  @endif

  <div class="sec-div"></div>

  {{-- COMMUNITY NEWS --}}
  <div class="sh">
    <div class="sh-title"><i class="fa-solid fa-newspaper"></i> Community News</div>
    <a href="{{ route('blog.index') }}" class="sh-link">View All <i class="fa-solid fa-arrow-right" style="font-size:10px"></i></a>
  </div>
  <div class="news-wrap">
    @forelse($blogPosts as $post)
      @php
        $isFirst  = $loop->first;
        $isLast   = $loop->last;
        $tagClass = $post->is_featured ? 'ntag-hot' : ($isFirst ? 'ntag-new' : 'ntag-comm');
        $tagLabel = $post->is_featured ? 'Featured' : ($isFirst ? 'New' : 'Community');
        $meta     = trim($post->category ?? '');
      @endphp
      <a href="{{ route('blog.show', $post->slug) }}" class="news-item" style="display:block;text-decoration:none;{{ $isLast ? 'border-bottom:none' : '' }}">
        <div class="news-title">{{ $post->title }}</div>
        <div class="news-meta">
          <span class="ntag {{ $tagClass }}">{{ $tagLabel }}</span>
          @if($meta) {{ $meta }} · @endif
          {{ $post->published_at ? $post->published_at->diffForHumans() : $post->created_at->diffForHumans() }}
        </div>
      </a>
    @empty
      <div class="news-item" style="border-bottom:none;color:var(--muted);font-size:13px;text-align:center;padding:20px 0">
        <i class="fa-solid fa-newspaper" style="font-size:28px;display:block;margin-bottom:8px;color:#ccc"></i>
        No Community News Found.<br>
        <a href="{{ route('blog.index') }}" style="color:var(--primary);font-weight:600;margin-top:6px;display:inline-block">Visit the blog →</a>
      </div>
    @endforelse
  </div>

  {{-- JOBS --}}
  <div class="sh">
    <div class="sh-title"><i class="fa-solid fa-briefcase"></i> Jobs & IT Training</div>
    <a href="{{ route('jobs.index') }}" class="sh-link">View All <i class="fa-solid fa-arrow-right" style="font-size:10px"></i></a>
  </div>
  @if($latestJobs->isEmpty())
    <div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:30px;text-align:center;color:var(--muted);font-size:13px;margin-bottom:22px">
      <div style="font-size:32px;margin-bottom:8px">💼</div>
      No Jobs Found.
      <a href="{{ route('post.create') }}" style="color:var(--primary);font-weight:600;text-decoration:none;display:block;margin-top:6px">Post a job →</a>
    </div>
  @else
  <div class="jobs-list">
    @foreach($latestJobs as $i=>$job)
      <a href="{{ route('jobs.show', $job->slug) }}" class="job-card">
        <div class="job-logo" style="padding:0;overflow:hidden">
          @if($job->company_logo_url ?? false)
            <img src="{{ $job->company_logo_url }}" alt="{{ $job->company }}" style="width:100%;height:100%;object-fit:cover;border-radius:9px">
          @else
            <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;font-size:28px;background:#f0f4ff;border-radius:9px">💼</div>
          @endif
        </div>
        <div class="job-info">
          <div class="job-title">{{ $job->title }}@if($job->company) — {{ $job->company }}@endif</div>
          <div class="job-co">{{ $job->job_type_label }}@if($job->city) · {{ $job->city }}@endif</div>
          <div class="job-tags">@if($job->category)<span class="job-tag">{{ $job->category->name }}</span>@endif<span class="job-tag">{{ $job->job_type_label }}</span></div>
        </div>
        @if($job->salary)<div class="job-sal">{{ $job->formatted_salary }}</div>@endif
      </a>
    @endforeach
  </div>
  @endif

  <div class="sec-div"></div>

  {{-- INLINE AD --}}
  <x-ad-slot position="inline" :ads="$ads" />

  {{-- BUSINESS DIRECTORY --}}
  <div class="sh">
    <div class="sh-title"><i class="fa-solid fa-building-columns"></i> Business Directory</div>
    <a href="{{ route('directory.index') }}" class="sh-link">{{ $stats['businesses']>0?number_format($stats['businesses']).'+ Listings':'View All' }} <i class="fa-solid fa-arrow-right" style="font-size:10px"></i></a>
  </div>
  @if($latestBusinesses->isEmpty())
    <div style="background:#fff;border:1px solid var(--border);border-radius:var(--radius);padding:30px;text-align:center;color:var(--muted);font-size:13px;margin-bottom:22px">
      <div style="font-size:32px;margin-bottom:8px">🏢</div>
      No Businesses Found.
      <a href="{{ route('post.create') }}" style="color:var(--primary);font-weight:600;text-decoration:none;display:block;margin-top:6px">List your business →</a>
    </div>
  @else
  <div class="biz-grid">
    @foreach($latestBusinesses->take(4) as $i=>$biz)
      <a href="{{ route('directory.show', $biz->slug) }}" class="biz-card">
        <div class="biz-img">
          @if($biz->image_url)<img src="{{ $biz->image_url }}" alt="{{ $biz->name }}">
          @else<span style="font-size:40px">{{ $biz->category->icon ?? '🏢' }}</span>@endif
        </div>
        <div class="biz-body">
          <div class="biz-name">{{ Str::limit($biz->name,22) }}</div>
          <div class="biz-cat">{{ $biz->category->name??'Business' }}@if($biz->city) · {{ $biz->city }}@endif</div>
          @if($biz->rating>0)<div class="biz-stars"><i class="fa-solid fa-star"></i> {{ number_format($biz->rating,1) }}</div>@endif
        </div>
      </a>
    @endforeach
  </div>
  @endif

  {{-- ALL LISTINGS — always show (location filtered) --}}
  @if($allListings->isNotEmpty())
  <div class="sh">
    <div class="sh-title"><i class="fa-solid fa-list"></i> All Listings</div>
    <a href="{{ route('classifieds.index', array_filter(['province' => request('province'), 'city' => request('city')])) }}" class="sh-link">View All <i class="fa-solid fa-arrow-right" style="font-size:10px"></i></a>
  </div>
  <div class="al-list">
    @foreach($allListings as $listing)
    <a href="{{ route('classifieds.show', $listing->slug) }}" class="al-item">
      <div class="al-thumb">
        @if($listing->image_url)<img src="{{ $listing->image_url }}" alt="{{ $listing->title }}">
        @else<span style="font-size:32px">{{ $listing->category->icon ?? '📦' }}</span>@endif
      </div>
      <div class="al-info">
        <div class="al-title">{{ $listing->title }}@if($listing->is_verified)<span style="display:inline-flex;align-items:center;gap:2px;font-size:9px;font-weight:700;background:#dcfce7;color:#15803d;padding:1px 6px;border-radius:20px;margin-left:5px;vertical-align:middle"><i class="fa-solid fa-circle-check" style="font-size:8px"></i> Verified</span>@endif</div>
        @if($listing->price)<div class="al-price">{{ $listing->formatted_price }}<small>{{ $listing->price_unit }}</small></div>@endif
        <div class="al-meta"><span>{{ $listing->category->name ?? '' }}</span><span><i class="fa-solid fa-location-dot"></i>{{ $listing->location }}</span><span><i class="fa-regular fa-clock"></i>{{ $listing->created_at->diffForHumans() }}</span></div>
      </div>
      <div style="color:#ddd;align-self:flex-start;margin-top:2px"><i class="fa-regular fa-heart" style="font-size:16px"></i></div>
    </a>
    @endforeach
  </div>
  @endif

  {{-- MOBILE ONLY: Sidebar Ads --}}
  @if($ads->where('position','sidebar')->isNotEmpty())
  <div class="mobile-sidebar-ads">
    <x-ad-slot position="sidebar" :ads="$ads" />
  </div>
  @endif

</div>{{-- /home-main --}}

{{-- ═══ SIDEBAR ═══ --}}
<div class="home-sidebar">

  {{-- POST AD --}}
  <div class="sb-card">
    <div class="sb-head"><i class="fa-solid fa-bullhorn"></i> Post Your Ad</div>
    @auth
      <a href="{{ route('post.create') }}" class="post-btn"><i class="fa-solid fa-plus"></i> Post Your Ad</a>
    @else
      <a href="{{ route('register') }}" class="post-btn"><i class="fa-solid fa-plus"></i> Post Your Ad</a>
    @endauth
    <div class="free-note"><i class="fa-solid fa-circle-check"></i> 100% free · No hidden fees · Instant</div>
  </div>

  {{-- PAID SIDEBAR ADS --}}
  @if($ads->where('position','sidebar')->isNotEmpty())
  <div class="sb-card" style="padding:10px">
    <x-ad-slot position="sidebar" :ads="$ads" />
  </div>
  @endif

  {{-- STATS --}}
  <div class="sb-card">
    <div class="sb-head"><i class="fa-solid fa-chart-bar"></i> Site Stats</div>
    <div class="stat-grid">
      <div class="stat-box"><div class="stat-num">{{ number_format($stats['businesses']) }}+</div><div class="stat-lbl">Businesses</div></div>
      <div class="stat-box"><div class="stat-num">{{ number_format($stats['listings']) }}+</div><div class="stat-lbl">Free Ads</div></div>
      <div class="stat-box"><div class="stat-num">{{ number_format($stats['events']) }}+</div><div class="stat-lbl">Events</div></div>
      <div class="stat-box"><div class="stat-num">{{ number_format(\App\Models\User::count()) }}+</div><div class="stat-lbl">Members</div></div>
    </div>
  </div>

  {{-- NEWSLETTER --}}
  <div class="sb-card" style="background:linear-gradient(135deg,#1a3a8f,#122970);border-color:transparent">
    <div class="sb-head" style="color:#fff"><i class="fa-solid fa-envelope" style="color:var(--accent)"></i> Newsletter</div>
    <input type="email" id="sub-email" placeholder="Your email address" style="width:100%;border:none;border-radius:7px;padding:9px 12px;font-size:12.5px;margin-bottom:8px;font-family:var(--fb)">
    <button id="sub-btn" style="width:100%;background:var(--accent);color:#fff;border:none;padding:9px;border-radius:7px;font-size:12.5px;font-weight:700;cursor:pointer">Subscribe Free</button>
  </div>

  {{-- QUICK LINKS --}}
  <div class="sb-card">
    <div class="sb-head"><i class="fa-solid fa-link"></i> Quick Links</div>
    <a href="{{ route('classifieds.index', ['category' => $roommatesCatId]) }}" class="ql"><div class="ql-ico" style="background:#fce4ec"><i class="fa-solid fa-person-shelter" style="color:#c2185b"></i></div> Find Roommate</a>
    <a href="{{ route('classifieds.index', ['category' => $autosCategoryId]) }}" class="ql"><div class="ql-ico" style="background:#fff3e0"><i class="fa-solid fa-car" style="color:#e65100"></i></div> Buy / Sell Cars</a>
    <a href="{{ route('directory.index', ['category' => $travelAgentCatId]) }}" class="ql"><div class="ql-ico" style="background:#e8f5e9"><i class="fa-solid fa-plane" style="color:#2e7d32"></i></div> Travel Agents</a>
    <a href="{{ route('blog.index') }}" class="ql"><div class="ql-ico" style="background:#e8edf7"><i class="fa-solid fa-users" style="color:var(--primary)"></i></div> Community Blog</a>
    <a href="{{ route('feed') }}" class="ql"><div class="ql-ico" style="background:#e0f7fa"><i class="fa-solid fa-rss" style="color:#00838f"></i></div> Community Feed</a>
    <a href="{{ route('jobs.index') }}" class="ql"><div class="ql-ico" style="background:#fef6e4"><i class="fa-solid fa-briefcase" style="color:#7a5200"></i></div> Find a Job</a>
  </div>

  {{-- MI POLL --}}
  @if($poll)
  <div class="sb-card" id="poll-widget" data-poll-id="{{ $poll->id }}" data-vote-url="{{ route('poll.vote', $poll->id) }}">
    <div class="sb-head"><i class="fa-solid fa-chart-simple"></i> GoBazaar Poll</div>
    <div class="poll-q">{{ $poll->question }}</div>

    {{-- Voting view (radio choices) --}}
    <div id="poll-choices">
      @foreach($poll->options as $opt)
      <label class="poll-choice" style="display:flex;align-items:center;gap:8px;padding:8px 10px;border:1.5px solid var(--border);border-radius:8px;margin-bottom:7px;cursor:pointer;font-size:13px;transition:border-color .15s" onmouseover="this.style.borderColor='var(--primary)'" onmouseout="if(!this.querySelector('input').checked)this.style.borderColor='var(--border)'">
        <input type="radio" name="poll-option" value="{{ $opt->id }}" style="accent-color:var(--primary);cursor:pointer">
        <span>{{ $opt->label }}</span>
      </label>
      @endforeach
      <button class="poll-btn" id="poll-vote-btn" onclick="submitPollVote()">Vote</button>
    </div>

    {{-- Results view (hidden until voted) --}}
    <div id="poll-results" style="display:none">
      @foreach($poll->options as $opt)
      <div class="poll-row" data-opt="{{ $opt->id }}">
        <span class="poll-lbl">{{ Str::limit($opt->label, 10) }}</span>
        <div class="poll-bar-bg"><div class="poll-bar poll-bar-fill" style="width:{{ $opt->percentage }}%;background:{{ $loop->first ? 'var(--primary)' : '#9ca3af' }}">{{ $opt->percentage }}%</div></div>
      </div>
      @endforeach
      <div style="font-size:11px;color:var(--muted);text-align:center;margin-top:6px"><span id="poll-total">{{ $poll->total_votes }}</span> votes · <span style="color:#16a34a;font-weight:600">✓ You voted</span></div>
    </div>
  </div>
  @endif


  {{-- LATEST ADS --}}
  <div class="sb-card">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:11px">
      <div class="sb-head" style="margin-bottom:0"><i class="fa-solid fa-clock"></i> Latest Ads</div>
      <a href="{{ route('classifieds.index') }}" style="font-size:11px;color:var(--primary);font-weight:600;text-decoration:none">View all</a>
    </div>
    @php
    @endphp
    <div class="la-list">
      @if($latestListings->isNotEmpty())
        @foreach($latestListings->take(4) as $i=>$listing)
        <a href="{{ route('classifieds.show', $listing->slug) }}" class="la-item">
          <div class="la-thumb">@if($listing->image_url)<img src="{{ $listing->image_url }}" alt="{{ $listing->title }}">@else<span style="font-size:26px">{{ $listing->category->icon ?? '📦' }}</span>@endif</div>
          <div class="la-info"><div class="la-name">{{ Str::limit($listing->title,28) }}</div>@if($listing->price)<div class="la-price">{{ $listing->formatted_price }}<small>{{ $listing->price_unit }}</small></div>@endif<div class="la-loc"><i class="fa-solid fa-location-dot" style="font-size:10px"></i> {{ $listing->location }}</div></div>
          <div class="la-side"><div class="la-fav"><i class="fa-regular fa-heart"></i></div><div class="la-time">{{ $listing->created_at->diffForHumans(null,true) }}</div></div>
        </a>
        @endforeach
      @else
        <div style="text-align:center;padding:20px;color:var(--muted);font-size:12px">
          <div style="font-size:28px;margin-bottom:6px">📭</div>
          No listings yet.<br>
          <a href="{{ route('post.create') }}" style="color:var(--primary);font-weight:600;text-decoration:none">Post the first one →</a>
        </div>
      @endif
    </div>
  </div>

  {{-- ADVERTISE --}}
  <div class="sb-card" style="background:#122970;border-color:#2a4fa8;text-align:center">
    <div style="font-size:13px;font-weight:700;color:#fff;margin-bottom:5px">Advertise with Us</div>
    <div style="font-size:11px;color:rgba(255,255,255,.7);margin-bottom:11px;line-height:1.5">Reach thousands of Indian-Canadians. Get listed by brand & category.</div>
    <button onclick="document.getElementById('advertise-modal').classList.add('open')"
            style="display:block;width:100%;background:var(--accent);color:#fff;padding:9px;border-radius:7px;font-size:12px;font-weight:700;border:none;cursor:pointer">
      Click Here for Info
    </button>
  </div>

</div>{{-- /home-sidebar --}}
</div>{{-- /home-body --}}

@endsection

@push('modals')
{{-- ADVERTISE MODAL --}}
<div id="advertise-modal" style="display:none;position:fixed;top:0;left:0;right:0;bottom:0;z-index:9999;background:rgba(0,0,0,.55);align-items:center;justify-content:center;padding:16px"
     onclick="if(event.target===this)this.classList.remove('open')">
  <div class="adv-modal-box">
    <div class="adv-modal-head">
      <h3>📢 Advertise with GoBazaar</h3>
      <button class="adv-modal-close" onclick="document.getElementById('advertise-modal').classList.remove('open')">✕</button>
    </div>
    <div class="adv-modal-body">

      {{-- Success state --}}
      <div id="adv-success" style="display:none;text-align:center;padding:30px 20px">
        <div style="font-size:52px;margin-bottom:12px">🎉</div>
        <div style="font-family:var(--fh);font-size:18px;font-weight:800;color:var(--text);margin-bottom:8px">Enquiry Submitted!</div>
        <div style="font-size:13px;color:var(--muted);line-height:1.6;margin-bottom:20px">Thank you! Our team will contact you within 24 hours with ad placement options and pricing.</div>
        <button type="button" onclick="document.getElementById('advertise-modal').classList.remove('open')"
                style="background:var(--primary);color:#fff;border:none;border-radius:8px;padding:10px 28px;font-size:13px;font-weight:700;cursor:pointer;font-family:var(--fb)">
          Close
        </button>
      </div>

      {{-- Form --}}
      <div id="adv-form-wrap">
      <p style="font-size:13px;color:var(--muted);margin-bottom:16px;line-height:1.6">
        Fill in your details and we'll contact you within 24 hours with ad placement options and pricing.
      </p>

      <form id="adv-form" action="{{ route('advertise.store') }}" method="POST">
        @csrf
        <div class="adv-grid">
          <div class="adv-field">
            <label>Your Name *</label>
            <input type="text" name="name" value="{{ auth()->user()->name ?? old('name') }}" required placeholder="Full name">
          </div>
          <div class="adv-field">
            <label>Email *</label>
            <input type="email" name="email" value="{{ auth()->user()->email ?? old('email') }}" required placeholder="your@email.com">
          </div>
        </div>
        <div class="adv-grid">
          <div class="adv-field">
            <label>Phone / WhatsApp</label>
            <input type="text" name="phone" placeholder="+1 (416) 555-0123">
          </div>
          <div class="adv-field">
            <label>Business Name</label>
            <input type="text" name="business_name" placeholder="Your business">
          </div>
        </div>
        <div class="adv-grid">
          <div class="adv-field">
            <label>Ad Position Interested In</label>
            <select name="ad_position">
              <option value="">-- Select --</option>
              <option value="home-banner">Home Top Banner</option>
              <option value="sidebar">Sidebar</option>
              <option value="inline">Inline (Between Listings)</option>
              <option value="all">All Positions</option>
            </select>
          </div>
          <div class="adv-field">
            <label>Monthly Budget</label>
            <select name="budget">
              <option value="">-- Select --</option>
              <option value="under-100">Under $100</option>
              <option value="100-300">$100 – $300</option>
              <option value="300-500">$300 – $500</option>
              <option value="500+">$500+</option>
            </select>
          </div>
        </div>
        <div class="adv-field">
          <label>Website (optional)</label>
          <input type="url" name="website" placeholder="https://yourbusiness.com">
        </div>
        <div class="adv-field">
          <label>Message (optional)</label>
          <textarea name="message" placeholder="Tell us about your business or what you'd like to promote…"></textarea>
        </div>
        <button type="submit" class="adv-submit" id="adv-submit-btn">
          <i class="fa-solid fa-paper-plane"></i> Send Enquiry
        </button>
      </form>
      </div>{{-- /adv-form-wrap --}}
    </div>
  </div>
</div>
<style>
#advertise-modal.open{display:flex !important}
.adv-modal-box{background:#fff;border-radius:16px;width:100%;max-width:520px;max-height:90vh;overflow-y:auto;box-shadow:0 20px 60px rgba(0,0,0,.25);animation:advSlide .2s ease}
@keyframes advSlide{from{transform:translateY(-20px);opacity:0}to{transform:translateY(0);opacity:1}}
.adv-modal-head{display:flex;align-items:center;justify-content:space-between;padding:18px 20px;border-bottom:1px solid #f0f0f0}
.adv-modal-head h3{font-family:var(--fh);font-size:17px;font-weight:800;color:var(--text)}
.adv-modal-close{width:32px;height:32px;border-radius:50%;background:#f5f5f5;border:none;cursor:pointer;font-size:16px;display:flex;align-items:center;justify-content:center;color:#555}
.adv-modal-body{padding:20px}
.adv-field{margin-bottom:14px}
.adv-field label{display:block;font-size:11px;font-weight:700;color:var(--muted);text-transform:uppercase;letter-spacing:.5px;margin-bottom:5px}
.adv-field input,.adv-field select,.adv-field textarea{width:100%;border:1.5px solid var(--border);border-radius:8px;padding:9px 12px;font-size:13px;font-family:var(--fb);color:var(--text);transition:border .15s}
.adv-field input:focus,.adv-field select:focus,.adv-field textarea:focus{outline:none;border-color:var(--primary)}
.adv-field textarea{resize:vertical;min-height:70px}
.adv-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px}
.adv-submit{width:100%;background:var(--primary);color:#fff;border:none;border-radius:8px;padding:12px;font-size:13.5px;font-weight:700;cursor:pointer;font-family:var(--fb);margin-top:4px;transition:background .2s}
.adv-submit:hover{background:var(--primary-dark)}
</style>
@endpush

@push('scripts')
<script>
function heroLoadCities(province, selectCity) {
  var sel = document.getElementById('hero-city');
  if (!sel) return;
  if (!province) { sel.innerHTML = '<option value="">All Cities</option>'; return; }
  sel.innerHTML = '<option value="">Loading…</option>';
  fetch('{{ route("locations.cities") }}?province=' + encodeURIComponent(province))
    .then(r => r.json()).then(cities => {
      sel.innerHTML = '<option value="">All Cities</option>';
      cities.forEach(c => {
        var o = document.createElement('option');
        o.value = c; o.textContent = c;
        if (selectCity && c === selectCity) o.selected = true;
        sel.appendChild(o);
      });
    });
}

function heroSubmit() {
  var q    = document.getElementById('hero-q')?.value.trim() || '';
  var prov = document.getElementById('hero-prov')?.value || '';
  var city = document.getElementById('hero-city')?.value || '';
  var params = new URLSearchParams();
  if (prov) params.set('province', prov);
  if (city) params.set('city', city);
  if (q) { params.set('search', q); window.location.href = '{{ route("classifieds.index") }}?' + params; }
  else { window.location.href = '{{ route("home") }}' + (params.toString() ? '?' + params : ''); }
}
document.getElementById('hero-q')?.addEventListener('keydown', e => { if (e.key === 'Enter') heroSubmit(); });

function heroDetectLocation(btn) {
  if (!navigator.geolocation) { alert('Geolocation not supported by your browser.'); return; }
  btn.disabled = true;
  btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Detecting…';

  navigator.geolocation.getCurrentPosition(function(pos) {
    fetch('https://nominatim.openstreetmap.org/reverse?lat=' + pos.coords.latitude + '&lon=' + pos.coords.longitude + '&format=json', {
      headers: { 'Accept-Language': 'en' }
    })
    .then(r => r.json())
    .then(function(data) {
      btn.disabled = false;
      btn.innerHTML = '<i class="fa-solid fa-location-crosshairs"></i> Use my location';

      var addr = data.address || {};
      var detectedProvince = addr.state || addr.region || addr.county || '';
      var detectedCity     = addr.city || addr.town || addr.village || addr.suburb || '';

      var provSel = document.getElementById('hero-prov');
      var matched = '';
      Array.from(provSel.options).forEach(function(opt) {
        if (!opt.value) return;
        var ov = opt.value.toLowerCase(), dv = detectedProvince.toLowerCase();
        if (dv.includes(ov) || ov.includes(dv)) matched = opt.value;
      });

      if (matched) {
        provSel.value = matched;
        localStorage.setItem('gobazaar_province', matched);
        fetch('{{ route("locations.cities") }}?province=' + encodeURIComponent(matched))
          .then(r => r.json())
          .then(function(cities) {
            var citySel = document.getElementById('hero-city');
            citySel.innerHTML = '<option value="">All Cities</option>';
            var cityMatched = '';
            cities.forEach(function(c) {
              var o = document.createElement('option');
              o.value = c; o.textContent = c;
              citySel.appendChild(o);
              var cv = c.toLowerCase(), dv = detectedCity.toLowerCase();
              if (!cityMatched && (dv.includes(cv) || cv.includes(dv))) cityMatched = c;
            });
            if (cityMatched) {
              citySel.value = cityMatched;
              localStorage.setItem('gobazaar_city', cityMatched);
            }
          });
      } else {
        alert('Could not match your location to a known province. Please select manually.');
      }
    })
    .catch(function() {
      btn.disabled = false;
      btn.innerHTML = '<i class="fa-solid fa-location-crosshairs"></i> Use my location';
      alert('Could not fetch location. Please select manually.');
    });
  }, function() {
    btn.disabled = false;
    btn.innerHTML = '<i class="fa-solid fa-location-crosshairs"></i> Use my location';
    alert('Location access denied. Please allow location and try again.');
  });
}

// Sync hero selects from URL params (priority) or localStorage or auto-detect
(function() {
  var urlParams = new URLSearchParams(window.location.search);
  var urlProv = urlParams.get('province') || '';
  var urlCity = urlParams.get('city') || '';
  var prov = urlProv || localStorage.getItem('gobazaar_province') || '';
  var city = urlCity || localStorage.getItem('gobazaar_city') || '';
  var provSel = document.getElementById('hero-prov');

  if (prov) {
    // Already known — restore from URL/localStorage
    if (provSel) provSel.value = prov;
    heroLoadCities(prov, city);
  } else if (navigator.geolocation) {
    // First visit — auto-detect silently
    var btn = document.getElementById('hero-loc-btn');
    if (btn) { btn.disabled = true; btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Detecting…'; }
    navigator.geolocation.getCurrentPosition(function(pos) {
      fetch('https://nominatim.openstreetmap.org/reverse?lat=' + pos.coords.latitude + '&lon=' + pos.coords.longitude + '&format=json', {
        headers: { 'Accept-Language': 'en' }
      })
      .then(function(r) { return r.json(); })
      .then(function(data) {
        if (btn) { btn.disabled = false; btn.innerHTML = '<i class="fa-solid fa-location-crosshairs"></i> Use my location'; }
        var addr = data.address || {};
        var detectedProvince = addr.state || addr.region || addr.county || '';
        var detectedCity     = addr.city || addr.town || addr.village || addr.suburb || '';
        var matched = '';
        if (provSel) {
          Array.from(provSel.options).forEach(function(opt) {
            if (!opt.value) return;
            var ov = opt.value.toLowerCase(), dv = detectedProvince.toLowerCase();
            if (dv.includes(ov) || ov.includes(dv)) matched = opt.value;
          });
        }
        if (matched) {
          provSel.value = matched;
          localStorage.setItem('gobazaar_province', matched);
          fetch('{{ route("locations.cities") }}?province=' + encodeURIComponent(matched))
            .then(function(r) { return r.json(); })
            .then(function(cities) {
              var citySel = document.getElementById('hero-city');
              citySel.innerHTML = '<option value="">All Cities</option>';
              var cityMatched = '';
              cities.forEach(function(c) {
                var o = document.createElement('option');
                o.value = c; o.textContent = c;
                citySel.appendChild(o);
                var cv = c.toLowerCase(), dv = detectedCity.toLowerCase();
                if (!cityMatched && (dv.includes(cv) || cv.includes(dv))) cityMatched = c;
              });
              if (cityMatched) { citySel.value = cityMatched; localStorage.setItem('gobazaar_city', cityMatched); }
            });
        }
      })
      .catch(function() {
        if (btn) { btn.disabled = false; btn.innerHTML = '<i class="fa-solid fa-location-crosshairs"></i> Use my location'; }
      });
    }, function() {
      // User denied — silently ignore, show button normally
      if (btn) { btn.disabled = false; btn.innerHTML = '<i class="fa-solid fa-location-crosshairs"></i> Use my location'; }
    }, { timeout: 8000 });
  }
})();
// ── POLL VOTING ──────────────────────────────────────────────────
function getPollToken() {
  var t = localStorage.getItem('gobazaar_voter_token');
  if (!t) { t = 'v_' + Math.random().toString(36).slice(2) + Date.now().toString(36); localStorage.setItem('gobazaar_voter_token', t); }
  return t;
}

function showPollResults(data) {
  document.getElementById('poll-choices').style.display = 'none';
  var resultsBox = document.getElementById('poll-results');
  resultsBox.style.display = 'block';
  if (data && data.options) {
    data.options.forEach(function(o, i) {
      var row = resultsBox.querySelector('[data-opt="' + o.id + '"]');
      if (row) {
        var bar = row.querySelector('.poll-bar-fill');
        bar.style.width = o.pct + '%';
        bar.textContent = o.pct + '%';
      }
    });
    document.getElementById('poll-total').textContent = data.total;
  }
}

function submitPollVote() {
  var widget = document.getElementById('poll-widget');
  if (!widget) return;
  var checked = widget.querySelector('input[name="poll-option"]:checked');
  if (!checked) {
    var btn = document.getElementById('poll-vote-btn');
    btn.textContent = 'Please select an option';
    setTimeout(function(){ btn.textContent = 'Vote'; }, 1500);
    return;
  }
  var btn = document.getElementById('poll-vote-btn');
  btn.textContent = 'Submitting…'; btn.disabled = true;

  fetch(widget.dataset.voteUrl, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
    body: JSON.stringify({ option_id: checked.value, token: getPollToken() })
  })
  .then(r => r.json())
  .then(data => { showPollResults(data); markPollVoted(widget.dataset.pollId); })
  .catch(() => { btn.textContent = 'Try again'; btn.disabled = false; });
}

function markPollVoted(pollId) {
  var voted = JSON.parse(localStorage.getItem('gobazaar_voted_polls') || '[]');
  if (!voted.includes(pollId)) { voted.push(pollId); localStorage.setItem('gobazaar_voted_polls', JSON.stringify(voted)); }
}

// On load: if already voted this poll, show results immediately
(function() {
  var widget = document.getElementById('poll-widget');
  if (!widget) return;
  var voted = JSON.parse(localStorage.getItem('gobazaar_voted_polls') || '[]');
  if (voted.includes(widget.dataset.pollId)) {
    showPollResults(null); // results already rendered server-side with current %
  }
})();
document.getElementById('sub-btn')?.addEventListener('click', function(){
  var inp = document.getElementById('sub-email');
  if (inp?.value.includes('@')) { this.textContent = '✅ Subscribed!'; this.disabled = true; inp.disabled = true; }
  else if (inp) { inp.style.outline = '2px solid red'; inp.placeholder = 'Enter a valid email'; }
});
// Advertise form AJAX submit
document.getElementById('adv-form')?.addEventListener('submit', function(e) {
  e.preventDefault();
  var btn = document.getElementById('adv-submit-btn');
  btn.disabled = true;
  btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Sending…';

  fetch(this.action, {
    method: 'POST',
    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'X-Requested-With': 'XMLHttpRequest' },
    body: new FormData(this),
  })
  .then(r => r.json())
  .then(data => {
    if (data.success) {
      document.getElementById('adv-form-wrap').style.display = 'none';
      document.getElementById('adv-success').style.display = 'block';
    } else {
      btn.disabled = false;
      btn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Send Enquiry';
    }
  })
  .catch(() => {
    btn.disabled = false;
    btn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> Send Enquiry';
  });
});
</script>
@endpush
