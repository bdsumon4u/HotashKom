@extends('layouts.yellow.master')

@php
    $bbBrandName = $company->name ?? config('app.name');
    $bbSeoPage = max(1, (int) request()->query('page', 1));

    $bbSeoDescription = $bbSeoPage === 1
        ? $bbBrandName . ' Blog থেকে ইলেকট্রনিকস, কিচেন, হোম, বিউটি ও লাইফস্টাইল পণ্য কেনার গাইড, ব্যবহারবিধি, নিরাপত্তা টিপস এবং সঠিক পণ্য বাছাইয়ের প্রয়োজনীয় তথ্য জানুন।'
        : $bbBrandName . ' Blog-এর পণ্য কেনার গাইড ও ব্যবহারিক টিপসের আরও লেখা পড়ুন। এটি Blog archive-এর Page ' . $bbSeoPage . '; কিচেন, হোম, ইলেকট্রনিকস, বিউটি ও লাইফস্টাইল পণ্য সম্পর্কে প্রয়োজনীয় তথ্য পান।';
@endphp

@section('seo_tags')
    <title>{{ $bbBrandName }} - Blog | পণ্য কেনার গাইড ও ব্যবহারিক টিপস{{ $bbSeoPage > 1 ? ' | Page ' . $bbSeoPage : '' }}</title>
    <meta name="description" content="{{ $bbSeoDescription }}">
@endsection

@section('title', 'Blog | পণ্য কেনার গাইড ও ব্যবহারিক টিপস' . ($bbSeoPage > 1 ? ' | Page ' . $bbSeoPage : ''))

@section('content')
<style>
.bb-blog-page{--bb-ink:#101828;--bb-text:#475467;--bb-muted:#667085;--bb-line:#EAECF0;--bb-card:#FFFFFF;--bb-bg:#F7FAF8;--bb-shadow:0 12px 35px rgba(16,24,40,.08);--bb-shadow-soft:0 6px 18px rgba(16,24,40,.06);color:var(--bb-ink)}
.bb-blog-page *{box-sizing:border-box}.bb-blog-page a{text-decoration:none}.bb-blog-shell{max-width:1240px;margin:0 auto;padding:32px 16px 52px}
.bb-blog-hero{position:relative;overflow:hidden;padding:38px 34px;margin-bottom:28px;border:1px solid #D8EFE4;border-radius:26px;background:radial-gradient(circle at 92% 4%,rgba(29,191,114,.18),transparent 31%),linear-gradient(135deg,#F7FFFB 0%,#FFFFFF 72%);box-shadow:var(--bb-shadow-soft)}
.bb-blog-hero:after{content:"";position:absolute;right:-70px;bottom:-110px;width:260px;height:260px;border:42px solid rgba(29,191,114,.06);border-radius:50%;pointer-events:none}
.bb-blog-eyebrow{display:inline-flex;align-items:center;gap:8px;padding:7px 12px;margin-bottom:14px;border-radius:999px;background:var(--bb-green-soft);color:var(--bb-green-dark);font-size:16px;font-weight:800;letter-spacing:.045em;text-transform:uppercase}
.bb-blog-hero h1{position:relative;z-index:1;max-width:860px;margin:0 0 12px;color:var(--bb-ink);font-size:clamp(31px,4.4vw,48px);line-height:1.12;letter-spacing:-.03em}
.bb-blog-hero p{position:relative;z-index:1;max-width:860px;margin:0;color:var(--bb-text);font-size:15px;line-height:1.9}
.bb-blog-meta-row{position:relative;z-index:1;display:flex;flex-wrap:wrap;gap:10px;margin-top:20px}.bb-blog-meta-pill{display:inline-flex;align-items:center;min-height:38px;padding:0 13px;border:1px solid #DFE8E3;border-radius:999px;background:#fff;color:var(--bb-muted);font-size:13px;font-weight:700}
.bb-blog-section-head{display:flex;align-items:end;justify-content:space-between;gap:20px;margin:0 0 18px}.bb-blog-section-head h2{margin:0;color:var(--bb-ink);font-size:clamp(23px,3vw,30px);line-height:1.25}.bb-blog-section-head p{max-width:520px;margin:0;color:var(--bb-muted);font-size:13px;line-height:1.7;text-align:right}
.bb-blog-featured{display:grid;grid-template-columns:minmax(0,1.16fr) minmax(0,.84fr);overflow:hidden;margin-bottom:24px;border:1px solid var(--bb-line);border-radius:24px;background:var(--bb-card);box-shadow:var(--bb-shadow)}
.bb-blog-featured-media{position:relative;min-height:355px;background:linear-gradient(135deg,#E9FAF2,#F5F7F6)}.bb-blog-featured-media img{width:100%;height:100%;min-height:355px;display:block;object-fit:cover}
.bb-blog-placeholder{width:100%;min-height:100%;display:flex;align-items:center;justify-content:center;padding:30px;background:radial-gradient(circle at top right,rgba(29,191,114,.18),transparent 34%),linear-gradient(135deg,#ECFBF4,#F7FAF8);color:var(--bb-green-dark);font-size:16px;font-weight:800;text-align:center}
.bb-blog-featured-body{display:flex;flex-direction:column;justify-content:center;padding:34px}.bb-blog-badge{display:inline-flex;align-self:flex-start;padding:6px 10px;margin-bottom:13px;border-radius:999px;background:var(--bb-green-soft);color:var(--bb-green-dark);font-size:11px;font-weight:800;letter-spacing:.035em;text-transform:uppercase}
.bb-blog-date{display:block;margin-bottom:10px;color:var(--bb-muted);font-size:12px;font-weight:700}.bb-blog-featured h3{margin:0 0 13px;color:var(--bb-ink);font-size:clamp(24px,3vw,32px);line-height:1.3}.bb-blog-featured h3 a,.bb-blog-card h3 a{color:inherit}.bb-blog-featured h3 a:hover,.bb-blog-card h3 a:hover{color:var(--bb-green-dark)}
.bb-blog-featured-body p{margin:0 0 20px;color:var(--bb-text);font-size:14px;line-height:1.85}.bb-blog-read{display:inline-flex;align-items:center;justify-content:center;align-self:flex-start;min-height:44px;padding:0 16px;border-radius:11px;background:var(--bb-green);color:#082318!important;font-size:13px;font-weight:800;transition:transform .2s ease,box-shadow .2s ease}.bb-blog-read:hover{transform:translateY(-1px);box-shadow:0 8px 18px rgba(29,191,114,.22)}
.bb-blog-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:20px}.bb-blog-card{display:flex;min-width:0;flex-direction:column;overflow:hidden;border:1px solid var(--bb-line);border-radius:20px;background:var(--bb-card);box-shadow:var(--bb-shadow-soft);transition:transform .2s ease,box-shadow .2s ease,border-color .2s ease}.bb-blog-card:hover{transform:translateY(-3px);border-color:#D3EADF;box-shadow:0 14px 32px rgba(16,24,40,.10)}
.bb-blog-card-media{position:relative;aspect-ratio:16/10;overflow:hidden;background:#EEF5F1}.bb-blog-card-media img{width:100%;height:100%;display:block;object-fit:cover;transition:transform .35s ease}.bb-blog-card:hover .bb-blog-card-media img{transform:scale(1.025)}
.bb-blog-card-body{display:flex;flex:1;flex-direction:column;padding:19px}.bb-blog-card h3{margin:0 0 10px;color:var(--bb-ink);font-size:18px;line-height:1.45}.bb-blog-card p{margin:0 0 17px;color:var(--bb-text);font-size:13px;line-height:1.8}.bb-blog-card-link{display:inline-flex;align-items:center;margin-top:auto;color:var(--bb-green-dark)!important;font-size:13px;font-weight:800}
.bb-blog-empty{padding:38px 24px;border:1px solid var(--bb-line);border-radius:20px;background:#fff;color:var(--bb-text);text-align:center}
.bb-blog-pagination{margin-top:30px}.bb-blog-pagination nav{display:flex;justify-content:center}.bb-blog-pagination .pagination{display:flex;flex-wrap:wrap;justify-content:center;gap:7px;margin:0}.bb-blog-pagination .page-item .page-link{min-width:40px;min-height:40px;display:flex;align-items:center;justify-content:center;border:1px solid var(--bb-line);border-radius:10px!important;background:#fff;color:var(--bb-ink);font-weight:700;box-shadow:none}.bb-blog-pagination .page-item.active .page-link{border-color:var(--bb-green);background:var(--bb-green);color:#082318}.bb-blog-pagination .page-item.disabled .page-link{opacity:.5;background:#F8FAF9}
.bb-blog-trust{display:grid;grid-template-columns:minmax(0,1fr) auto;gap:28px;align-items:center;margin-top:34px;padding:26px;border:1px solid #D9ECE2;border-radius:22px;background:linear-gradient(135deg,#F8FFFB,#fff)}.bb-blog-trust h2{margin:0 0 8px;font-size:23px;line-height:1.3}.bb-blog-trust p{max-width:780px;margin:0;color:var(--bb-text);font-size:14px;line-height:1.8}.bb-blog-trust-links{display:flex;flex-wrap:wrap;gap:10px;justify-content:flex-end}.bb-blog-secondary{display:inline-flex;align-items:center;justify-content:center;min-height:42px;padding:0 14px;border:1px solid #D7E9DF;border-radius:10px;background:#fff;color:var(--bb-green-dark)!important;font-size:13px;font-weight:800}
@media(max-width:991px){.bb-blog-featured{grid-template-columns:1fr}.bb-blog-featured-media,.bb-blog-featured-media img{min-height:300px}.bb-blog-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.bb-blog-trust{grid-template-columns:1fr}.bb-blog-trust-links{justify-content:flex-start}}
@media(max-width:767px){.bb-blog-shell{padding-top:22px}.bb-blog-hero{padding:27px 21px;border-radius:21px}.bb-blog-section-head{display:block}.bb-blog-section-head p{margin-top:7px;text-align:left}.bb-blog-featured-body{padding:23px}.bb-blog-grid{grid-template-columns:1fr}}
@media(max-width:480px){.bb-blog-featured-media,.bb-blog-featured-media img{min-height:230px}.bb-blog-trust{padding:20px}}
</style>

@php
    $bbPage = max(1, (int) $blogs->currentPage());

    $bbTitle = function ($blog) use ($bbBrandName) {
        return trim((string) (data_get($blog, 'title') ?: data_get($blog, 'name') ?: ($bbBrandName . ' Buying Guide')));
    };

    $bbUrl = function ($blog) {
        return route('blogs.show', ['blog' => data_get($blog, 'slug')]);
    };

    $bbDateValue = function ($blog) {
        $value = data_get($blog, 'published_at') ?: data_get($blog, 'created_at');
        if (!$value) return null;
        try { return \Illuminate\Support\Carbon::parse($value); } catch (\Throwable $e) { return null; }
    };

    $bbExcerpt = function ($blog) {
        foreach (['excerpt','short_description','description','content','body'] as $field) {
            $value = data_get($blog, $field);
            if (is_string($value) && trim(strip_tags($value)) !== '') {
                return \Illuminate\Support\Str::limit(
                    trim(preg_replace('/\s+/u', ' ', strip_tags($value))),
                    170
                );
            }
        }
        return 'সহজ ভাষায় প্রয়োজনীয় তথ্য, ব্যবহারিক পরামর্শ এবং কেনার আগে গুরুত্বপূর্ণ বিষয়গুলো জেনে নিন।';
    };

    $bbImage = function ($blog) {
        $image = data_get($blog, 'base_image_src') ?: data_get($blog, 'image.src');
        return $image ? asset($image) : null;
    };

    $bbExcerpt = function ($blog) {
        $content = data_get($blog, 'excerpt') ?: data_get($blog, 'content') ?: '';
        return \Illuminate\Support\Str::limit(strip_tags((string) $content), 120);
    };

    $bbFeatured = $bbPage === 1 ? $blogs->getCollection()->first() : null;
    $bbItems = $bbPage === 1 ? $blogs->getCollection()->slice(1) : $blogs->getCollection();
@endphp

<div class="bb-blog-page">
<div class="bb-blog-shell">

<header class="bb-blog-hero">
<div class="bb-blog-eyebrow">{{ $bbBrandName }} Knowledge Hub</div>
<h1>@if($bbPage > 1) {{ $bbBrandName }} Blog &amp; Buying Guides – Page {{ $bbPage }} @else {{ $bbBrandName }} Blog &amp; Buying Guides @endif</h1>
<p>বাংলাদেশে অনলাইন শপিংয়ের আগে পণ্য সম্পর্কে পরিষ্কার ধারণা পেতে {{ $bbBrandName }}-এর buying guide, ব্যবহারিক tips, safety guidance এবং product-care articles পড়ুন। আমাদের লক্ষ্য হলো শুধু পণ্য দেখানো নয়, বরং প্রয়োজন অনুযায়ী সঠিক product বেছে নেওয়ার জন্য সহজ ও কাজে লাগার মতো তথ্য দেওয়া।</p>
<div class="bb-blog-meta-row">
<span class="bb-blog-meta-pill">{{ $blogs->total() }}টি প্রকাশিত গাইড</span>
<span class="bb-blog-meta-pill">Buying Guides</span>
<span class="bb-blog-meta-pill">Practical Tips</span>
<span class="bb-blog-meta-pill">Bangladesh-focused</span>
</div>
</header>

<div class="bb-blog-section-head">
<h2>{{ $bbPage > 1 ? 'More Buying Guides' : 'Latest Buying Guides' }}</h2>
<p>Product features, practical use, maintenance, safety এবং কেনার আগে যাচাই করার গুরুত্বপূর্ণ বিষয়গুলো সহজভাবে জানুন।</p>
</div>

@if($bbFeatured)
@php
$featuredTitle=$bbTitle($bbFeatured);$featuredUrl=$bbUrl($bbFeatured);$featuredImage=$bbImage($bbFeatured);$featuredDate=$bbDateValue($bbFeatured);
@endphp
<article class="bb-blog-featured">
<a class="bb-blog-featured-media" href="{{ $featuredUrl }}" aria-label="{{ $featuredTitle }}">
@if($featuredImage)
<img src="{{ $featuredImage }}" alt="{{ $featuredTitle }}" loading="eager" fetchpriority="high" decoding="async">
@else
<div class="bb-blog-placeholder">{{ $bbBrandName }} Buying Guide</div>
@endif
</a>
<div class="bb-blog-featured-body">
<span class="bb-blog-badge">Featured Guide</span>
@if($featuredDate)<time class="bb-blog-date" datetime="{{ $featuredDate->toDateString() }}">{{ $featuredDate->format('M d, Y') }}</time>@endif
<h3><a href="{{ $featuredUrl }}">{{ $featuredTitle }}</a></h3>
<p>{{ $bbExcerpt($bbFeatured) }}</p>
<a class="bb-blog-read" href="{{ $featuredUrl }}">Read Buying Guide</a>
</div>
</article>
@endif

@if($bbItems->count())
<div class="bb-blog-grid">
@foreach($bbItems as $blog)
@php
$blogTitle=$bbTitle($blog);$blogUrl=$bbUrl($blog);$blogImage=$bbImage($blog);$blogDate=$bbDateValue($blog);
@endphp
<article class="bb-blog-card">
<a class="bb-blog-card-media" href="{{ $blogUrl }}" aria-label="{{ $blogTitle }}">
@if($blogImage)
<img src="{{ $blogImage }}" alt="{{ $blogTitle }}" loading="lazy" decoding="async">
@else
<div class="bb-blog-placeholder">{{ $bbBrandName }} Buying Guide</div>
@endif
</a>
<div class="bb-blog-card-body">
@if($blogDate)<time class="bb-blog-date" datetime="{{ $blogDate->toDateString() }}">{{ $blogDate->format('M d, Y') }}</time>@endif
<h3><a href="{{ $blogUrl }}">{{ $blogTitle }}</a></h3>
<p>{{ $bbExcerpt($blog) }}</p>
<a class="bb-blog-card-link" href="{{ $blogUrl }}">Read Guide →</a>
</div>
</article>
@endforeach
</div>
@elseif(!$bbFeatured)
<div class="bb-blog-empty">এই মুহূর্তে কোনো blog article পাওয়া যায়নি।</div>
@endif

@if($blogs->hasPages())
<div class="bb-blog-pagination" aria-label="Blog pagination">
{{ $blogs->onEachSide(1)->links() }}
</div>
@endif

@if($bbPage === 1)
<section class="bb-blog-trust">
<div>
<h2>{{ $bbBrandName }}-এর Buying Guides কেন পড়বেন?</h2>
<p>আমাদের guides-এ product কেনার আগে কী যাচাই করবেন, কোন feature আপনার প্রয়োজনের জন্য গুরুত্বপূর্ণ, safe use, maintenance এবং বাস্তব shopping decision-এর প্রয়োজনীয় বিষয়গুলো সহজভাবে সাজানো হয়। Product availability, price বা specification সময়ের সঙ্গে পরিবর্তিত হতে পারে, তাই purchase-এর আগে সংশ্লিষ্ট product page-এর বর্তমান তথ্যও যাচাই করুন।</p>
</div>
<div class="bb-blog-trust-links">
<a class="bb-blog-secondary" href="{{ url('/shop') }}">Shop Products</a>
<a class="bb-blog-secondary" href="{{ url('/contact-us') }}">Contact Us</a>
</div>
</section>
@endif

</div>
</div>
@endsection
