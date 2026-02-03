@php
$metaDescription = Str::length($slot->toHtml()) != 0 ? $slot : $description;
$ogImage = $image ?? url('/images/og-image-wide.jpg');
@endphp

@push('meta')
{{-- Standard SEO --}}
<meta name="description" content="{{ $metaDescription }}">
<link rel="canonical" href="{{ $url }}">

{{-- Open Graph --}}
<meta property="og:title" content="{{ $title }}">
<meta property="og:site_name" content="{{ $siteName }}">
<meta property="og:url" content="{{ $url }}">
<meta property="og:image" content="{{ $ogImage }}">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">
<meta property="og:description" content="{{ $metaDescription }}">
<meta property="og:type" content="{{ $type }}">

{{-- Twitter Card --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:site" content="@wordabordle">
<meta name="twitter:title" content="{{ $title }}">
<meta name="twitter:description" content="{{ $metaDescription }}">
<meta name="twitter:image" content="{{ $ogImage }}">
@endpush
