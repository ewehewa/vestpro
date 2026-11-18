@props([
    'size' => 40,      // icon size in px; the wordmark scales with it
    'text' => true,    // false = icon only
    'href' => null,
])

@once
<style>
    .brand-logo {
        display: inline-flex;
        align-items: center;
        gap: calc(var(--logo-size) * 0.25);
        text-decoration: none;
        line-height: 1;
        color: inherit;
    }

    .brand-logo:hover {
        color: inherit;
    }

    .brand-logo__mark {
        width: var(--logo-size);
        height: var(--logo-size);
        flex-shrink: 0;
    }

    .brand-logo__text {
        display: inline-flex;
        align-items: baseline;
        gap: 0.3em;
        font-size: calc(var(--logo-size) * 0.5);
        font-weight: 700;
        letter-spacing: -0.01em;
        white-space: nowrap;
    }

    .brand-logo__pro {
        color: #34E834;
    }
</style>
@endonce

@php($tag = $href ? 'a' : 'span')

<{{ $tag }} @if ($href) href="{{ $href }}" aria-label="Strategic Investment Pro home" @else role="img" aria-label="Strategic Investment Pro" @endif
    {{ $attributes->merge(['class' => 'brand-logo', 'style' => "--logo-size: {$size}px;"]) }}>
    <svg class="brand-logo__mark" viewBox="0 0 40 40" aria-hidden="true">
        <rect width="40" height="40" rx="10" fill="#34E834" />
        <path d="M9 29l8-8 5 4 10-12" fill="none" stroke="#000" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" />
        <path d="M24.5 13H32v7.5" fill="none" stroke="#000" stroke-width="3.5" stroke-linecap="round" stroke-linejoin="round" />
    </svg>
    @if ($text)
        <span class="brand-logo__text">
            <span class="brand-logo__name">Strategic Investment</span>
            <span class="brand-logo__pro">Pro</span>
        </span>
    @endif
</{{ $tag }}>
