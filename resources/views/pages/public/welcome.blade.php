@extends('layouts.app')

@section('title', 'PetHelp - Znajdź Opiekuna dla Zwierząt | Bezpieczna Opieka nad Pupilami')

@section('meta')
<meta name="description" content="Znajdź zweryfikowanego opiekuna dla swojego psa lub kota w Polsce. ✓ Ubezpieczeni opiekunowie ✓ 24/7 wsparcie ✓ Bezpieczne płatności. Sprawdź dostępność w Twoim mieście!">
<meta name="keywords" content="opiekun dla psa, opieka nad kotem, spacer z psem, hotel dla zwierząt, pet sitter Polska">
<meta name="geo.region" content="PL">
<meta name="geo.placename" content="Polska">

<!-- Open Graph -->
<meta property="og:title" content="PetHelp - Znajdź Opiekuna dla Zwierząt">
<meta property="og:description" content="Bezpieczna opieka nad zwierzętami w Polsce. Zweryfikowani opiekunowie, ubezpieczenie OC, 24/7 wsparcie.">
<meta property="og:type" content="website">
<meta property="og:url" content="{{ url('/') }}">
<meta property="og:image" content="https://via.placeholder.com/1200x630/10B981/ffffff?text=PetHelp+-+Opieka+nad+Zwierz%C4%99tami">

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="PetHelp - Znajdź Opiekuna dla Zwierząt">
<meta name="twitter:description" content="Bezpieczna opieka nad zwierzętami w Polsce">
<meta name="twitter:image" content="https://via.placeholder.com/1200x630/10B981/ffffff?text=PetHelp+-+Opieka+nad+Zwierz%C4%99tami">

<!-- Structured Data -->
<script type="application/ld+json">
@php
    echo json_encode([
        "@context" => "https://schema.org",
        "@type" => "WebSite",
        "name" => "PetHelp",
        "description" => "Platforma łącząca właścicieli zwierząt z zweryfikowanymi opiekunami",
        "url" => url('/'),
        "potentialAction" => [
            "@type" => "SearchAction",
            "target" => url('/search') . '?query={search_term_string}',
            "query-input" => "required name=search_term_string",
        ],
    ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
@endphp
</script>


@endsection

@section('content')
<div class="min-h-screen">
    <x-homepage.hero-section />
    <x-homepage.how-it-works />
    <x-homepage.services-section />
    <x-homepage.safety-section />
    <x-homepage.testimonials-section />
    <x-homepage.subscription-plans-teaser />
    <x-homepage.faq-section />
    <x-homepage.final-cta-section />
</div>

@push('scripts')
<script>
// Smooth scrolling for anchor links
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// Intersection Observer for animations
const observerOptions = {
    threshold: 0.1,
    rootMargin: '0px 0px -50px 0px'
};

const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('animate-fadeInUp');
        }
    });
}, observerOptions);

// Observe all sections
document.querySelectorAll('section').forEach(section => {
    observer.observe(section);
});

// Add loading states for CTA buttons
document.querySelectorAll('a[href*="search"], a[href*="register"]').forEach(link => {
    link.addEventListener('click', function() {
        const button = this;
        const originalText = button.innerHTML;
        // Użyj SafeSVGIcons API zamiast innerHTML
        button.innerHTML = '';
        const loadingContainer = document.createElement('div');
        loadingContainer.style.display = 'flex';
        loadingContainer.style.alignItems = 'center';

        if (window.SafeSVGIcons) {
            window.SafeSVGIcons.createLoadingSpinner(loadingContainer, {
                classes: 'animate-spin -ml-1 mr-3 text-current',
                size: { width: 20, height: 20 }
            });
        }

        const textNode = document.createElement('span');
        textNode.textContent = 'Ładowanie...';
        loadingContainer.appendChild(textNode);

        button.appendChild(loadingContainer);

        setTimeout(() => {
            button.innerHTML = originalText;
        }, 3000);
    });
});
</script>

<style>
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fadeInUp {
    animation: fadeInUp 0.6s ease-out forwards;
}

/* Loading animations */
@keyframes pulse {
    0%, 100% {
        opacity: 1;
    }
    50% {
        opacity: 0.5;
    }
}

/* High contrast mode support */
@media (prefers-contrast: high) {
    .bg-gradient-to-r,
    .bg-gradient-to-br {
        background: #000 !important;
        color: #fff !important;
    }
}

/* Reduced motion support */
@media (prefers-reduced-motion: reduce) {
    .animate-pulse,
    .group-hover\:scale-110,
    .hover\:scale-105 {
        animation: none;
        transform: none;
    }
}
</style>
@endpush

@endsection