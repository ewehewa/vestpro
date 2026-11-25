@props([
    'size' => 40,      // icon size in px; the wordmark scales with it
    'text' => true,    // false = icon only
    'href' => null,
    'tone' => 'dark',  // 'dark' for dark backgrounds, 'light' for white/light backgrounds
])

@once
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@600;800&display=swap" rel="stylesheet">
<style>
    .brand-logo {
        --logo-green: #34E834;
        --logo-gold: #E8D27C;
        display: inline-flex;
        align-items: center;
        gap: calc(var(--logo-size) * 0.22);
        text-decoration: none;
        line-height: 1;
    }

    /* Deeper shades keep the logo readable on white */
    .brand-logo--light {
        --logo-green: #1FAF1F;
        --logo-gold: #A8841F;
    }

    .brand-logo:hover {
        text-decoration: none;
    }

    .brand-logo__mark {
        width: var(--logo-size);
        height: var(--logo-size);
        flex-shrink: 0;
        overflow: visible;
    }

    .brand-logo__bar { fill: var(--logo-green); }
    .brand-logo__tick { fill: none; stroke: #000; stroke-opacity: 0.45; stroke-width: 0.9; stroke-linecap: round; stroke-linejoin: round; }
    .brand-logo__swoosh { fill: var(--logo-gold); }
    .brand-logo__arrow { fill: var(--logo-gold); }

    .brand-logo__text {
        display: inline-flex;
        flex-direction: column;
        justify-content: center;
        gap: calc(var(--logo-size) * 0.1);
        font-family: 'Montserrat', 'Segoe UI', Arial, sans-serif;
        white-space: nowrap;
    }

    .brand-logo__name {
        font-size: calc(var(--logo-size) * 0.5);
        font-weight: 800;
        letter-spacing: 0.01em;
        line-height: 0.9;
        color: var(--logo-green);
    }

    .brand-logo__tag {
        font-size: calc(var(--logo-size) * 0.245);
        font-weight: 600;
        letter-spacing: 0.22em;
        line-height: 1;
        color: var(--logo-gold);
    }
</style>
@endonce

@php($tag = $href ? 'a' : 'span')

<{{ $tag }} @if ($href) href="{{ $href }}" aria-label="Strategic Invest Pro home" @else role="img" aria-label="Strategic Invest Pro" @endif
    {{ $attributes->merge(['class' => 'brand-logo' . ($tone === 'light' ? ' brand-logo--light' : ''), 'style' => "--logo-size: {$size}px;"]) }}>
    <svg class="brand-logo__mark" viewBox="0 0 48 48" aria-hidden="true">
        {{-- four rising bars, each with a small up-arrow --}}
        <rect class="brand-logo__bar" x="8" y="26" width="6" height="14" rx="0.8" />
        <rect class="brand-logo__bar" x="16" y="20" width="6" height="20" rx="0.8" />
        <rect class="brand-logo__bar" x="24" y="14" width="6" height="26" rx="0.8" />
        <rect class="brand-logo__bar" x="32" y="8" width="6" height="32" rx="0.8" />
        <path class="brand-logo__tick" d="M11 37V29.5M9.6 31L11 29.5L12.4 31" />
        <path class="brand-logo__tick" d="M19 37V23.5M17.6 25L19 23.5L20.4 25" />
        <path class="brand-logo__tick" d="M27 37V17.5M25.6 19L27 17.5L28.4 19" />
        <path class="brand-logo__tick" d="M35 37V11.5M33.6 13L35 11.5L36.4 13" />
        {{-- gold swoosh sweeping under the bars and up to the right --}}
        <path class="brand-logo__swoosh" d="M2.5 39.5C15 47.5 33 44.5 42.2 31L39.8 29C31.5 40.5 15.5 44 2.5 39.5Z" />
        <path class="brand-logo__arrow" d="M45 25.5L44.6 32.6L38.6 28.2Z" />
    </svg>
    @if ($text)
        <span class="brand-logo__text">
            <span class="brand-logo__name">STRATEGIC</span>
            <span class="brand-logo__tag">INVEST PRO</span>
        </span>
    @endif
</{{ $tag }}>
