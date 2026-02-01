@php
$metaDescription = Str::length($slot->toHtml()) != 0 ? $slot : $description;
@endphp

@push('meta')
{{-- Standard SEO --}}
<meta name="description" content="{{ $metaDescription }}">
<link rel="canonical" href="{{ $url }}">

{{-- Open Graph --}}
<meta property="og:title" content="{{ $title }}">
<meta property="og:site_name" content="{{ $siteName }}">
<meta property="og:url" content="{{ $url }}">
<meta property="og:image" content="{{ url('/images/og-image-wide.jpg') }}">
<meta property="og:description" content="{{ $metaDescription }}">
<meta property="og:type" content="{{ $type }}">

{{-- Twitter Card --}}
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="{{ $title }}">
<meta name="twitter:description" content="{{ $metaDescription }}">
<meta name="twitter:image" content="{{ url('/images/og-image-wide.jpg') }}">
@endpush
