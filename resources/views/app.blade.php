<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title inertia>{{ config('app.name', 'Veneno Auto Care Center') }}</title>
        
        <!-- Canonical URL -->
        <link rel="canonical" href="{{ url()->current() }}">

        <!-- Favicons & Mobile Touch Icons -->
        <link rel="icon" type="image/png" sizes="32x32" href="/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="/favicon-16x16.png">
        <link rel="apple-touch-icon" sizes="180x180" href="/apple-touch-icon.png">
        <link rel="shortcut icon" href="/favicon.ico">

        <!-- Search Engine Optimization (SEO) Meta Tags -->
        <meta name="robots" content="index, follow, max-image-preview:large, max-snippet:-1, max-video-preview:-1">
        <meta name="author" content="Veneno Auto Care Center">
        <meta name="publisher" content="Veneno Auto Care Center">
        <meta name="keywords" content="3M PPF Abu Dhabi, Paint Protection Film UAE, GYEON Ceramic Coating Abu Dhabi, Car Detailing UAE, 3M Window Tinting, Vinyl Wrap Dubai, Paintless Dent Repair PDR, Veneno Auto Care, مركز فينينو للعناية بالسيارات, حماية سيارات أبوظبي, نانو سيراميك">

        <!-- Geographic Location Tags (Local SEO UAE) -->
        <meta name="geo.region" content="AE-AZ">
        <meta name="geo.placename" content="Abu Dhabi">
        <meta name="geo.position" content="24.4539;54.3773">
        <meta name="ICBM" content="24.4539, 54.3773">

        <!-- Mobile & Browser Display -->
        <meta name="theme-color" content="#09090b">
        <meta name="apple-mobile-web-app-capable" content="yes">
        <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
        <meta name="apple-mobile-web-app-title" content="Veneno Auto Care Center">

        <!-- Primary Meta Tags & Open Graph (Social Preview / WhatsApp / Facebook / LinkedIn) -->
        <meta name="title" content="Veneno Auto Care Center — The Ultimate Art of Care in UAE">
        <meta name="description" content="UAE's Largest Multi-Brand Auto Care Center. Certified 3M Paint Protection Film (PPF), GYEON Nano Ceramic Coatings, and master detailing in Abu Dhabi, UAE.">

        <meta property="og:type" content="website">
        <meta property="og:url" content="{{ url()->current() }}">
        <meta property="og:title" content="Veneno Auto Care Center — The Ultimate Art of Care in UAE">
        <meta property="og:description" content="UAE's Largest Multi-Brand Auto Care Center. Certified 3M Paint Protection Film (PPF), GYEON Nano Ceramic Coatings, and master detailing in Abu Dhabi, UAE.">
        <meta property="og:image" content="{{ asset('images/main-branch.webp') }}">
        <meta property="og:image:secure_url" content="{{ asset('images/main-branch.webp') }}">
        <meta property="og:image:type" content="image/webp">
        <meta property="og:image:width" content="1200">
        <meta property="og:image:height" content="630">
        <meta property="og:image:alt" content="Veneno Auto Care Center — Main Branch Abu Dhabi">
        <meta property="og:site_name" content="Veneno Auto Care Center">
        <meta property="og:locale" content="en_US">
        <meta property="og:locale:alternate" content="ar_AE">

        <!-- Twitter / X Card -->
        <meta name="twitter:card" content="summary_large_image">
        <meta name="twitter:url" content="{{ url()->current() }}">
        <meta name="twitter:title" content="Veneno Auto Care Center — The Ultimate Art of Care in UAE">
        <meta name="twitter:description" content="UAE's Largest Multi-Brand Auto Care Center. Certified 3M Paint Protection Film (PPF), GYEON Nano Ceramic Coatings, and master detailing in Abu Dhabi, UAE.">
        <meta name="twitter:image" content="{{ asset('images/main-branch.webp') }}">
        <meta name="twitter:image:alt" content="Veneno Auto Care Center — Main Branch Abu Dhabi">

        <!-- Schema.org Structured Data (JSON-LD Local Business & AutoRepair) -->
        <script type="application/ld+json">
        {!! json_encode([
            '@context' => 'https://schema.org',
            '@graph' => [
                [
                    '@type' => 'AutoRepair',
                    '@id' => url('/') . '/#organization',
                    'name' => 'Veneno Auto Care Center',
                    'alternateName' => 'مركز فينينو للعناية بالسيارات',
                    'url' => url('/'),
                    'logo' => [
                        '@type' => 'ImageObject',
                        'url' => asset('images/logo.png'),
                    ],
                    'image' => asset('images/main-branch.webp'),
                    'description' => "UAE's Largest Multi-Brand Auto Care Center. Certified 3M Paint Protection Film (PPF), GYEON Nano Ceramic Coatings, and master detailing in Abu Dhabi, UAE.",
                    'telephone' => '+97126344403',
                    'email' => 'info@veneno.ae',
                    'priceRange' => '$$$',
                    'address' => [
                        '@type' => 'PostalAddress',
                        'addressLocality' => 'Abu Dhabi',
                        'addressRegion' => 'Abu Dhabi',
                        'addressCountry' => 'AE',
                    ],
                    'geo' => [
                        '@type' => 'GeoCoordinates',
                        'latitude' => 24.4539,
                        'longitude' => 54.3773,
                    ],
                    'openingHoursSpecification' => [
                        [
                            '@type' => 'OpeningHoursSpecification',
                            'dayOfWeek' => ['Saturday', 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday'],
                            'opens' => '08:30',
                            'closes' => '21:30',
                        ],
                    ],
                    'sameAs' => [
                        'https://wa.me/97126344403',
                        'https://www.instagram.com/veneno_ae',
                        'https://www.tiktok.com/@veneno_ae',
                    ],
                    'hasOfferCatalog' => [
                        '@type' => 'OfferCatalog',
                        'name' => 'Veneno Auto Care Services',
                        'itemListElement' => [
                            [
                                '@type' => 'Offer',
                                'itemOffered' => [
                                    '@type' => 'Service',
                                    'name' => '3M Paint Protection Film (PPF)',
                                    'description' => 'Official 3M Self-Healing 8mil Optical Armor with 10-Year Warranty',
                                ],
                            ],
                            [
                                '@type' => 'Offer',
                                'itemOffered' => [
                                    '@type' => 'Service',
                                    'name' => 'GYEON Diamond Ceramic Coating',
                                    'description' => '9H+ Multi-Layer GYEON Graphene Hydrophobic Shield',
                                ],
                            ],
                            [
                                '@type' => 'Offer',
                                'itemOffered' => [
                                    '@type' => 'Service',
                                    'name' => 'Detailing & Paint Correction',
                                    'description' => 'Concourse Swirl & Hologram Leveling with GYEON',
                                ],
                            ],
                            [
                                '@type' => 'Offer',
                                'itemOffered' => [
                                    '@type' => 'Service',
                                    'name' => '3M Ceramic Window Tinting',
                                    'description' => 'Up to 98% Infrared Heat & 99% UV Rejection by 3M',
                                ],
                            ],
                            [
                                '@type' => 'Offer',
                                'itemOffered' => [
                                    '@type' => 'Service',
                                    'name' => 'Vehicle Wrapping & Color Change',
                                    'description' => 'Premium 3M Cast Vinyl in Gloss, Satin & Matte Finishes',
                                ],
                            ],
                            [
                                '@type' => 'Offer',
                                'itemOffered' => [
                                    '@type' => 'Service',
                                    'name' => 'Paintless Dent Repair (PDR)',
                                    'description' => 'Factory-Finish Restoration with Zero Repainting',
                                ],
                            ],
                            [
                                '@type' => 'Offer',
                                'itemOffered' => [
                                    '@type' => 'Service',
                                    'name' => 'Rims Protection & Wheel Repair',
                                    'description' => 'Curb Rash CNC Machining & High-Temp Ceramic Armor',
                                ],
                            ],
                            [
                                '@type' => 'Offer',
                                'itemOffered' => [
                                    '@type' => 'Service',
                                    'name' => 'Paint & Body Restoration',
                                    'description' => 'Concourse Panel Spraying & Scratch Elimination',
                                ],
                            ],
                            [
                                '@type' => 'Offer',
                                'itemOffered' => [
                                    '@type' => 'Service',
                                    'name' => 'Signature Decontamination Car Wash',
                                    'description' => 'Two-Bucket Scratch-Free Wash with Iron Decontamination',
                                ],
                            ],
                        ],
                    ],
                ],
            ],
        ], JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) !!}
        </script>

        <!-- Fonts: Primary English (Outfit), Primary Arabic (Cairo), Luxury Script (Alex Brush, Great Vibes) -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Alex+Brush&family=Cairo:wght@400;500;600;700;800;900&family=Great+Vibes&family=Outfit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @routes
        @vite(['resources/js/app.js'])
        @inertiaHead
    </head>
    <body class="font-sans antialiased bg-zinc-950 text-zinc-100 selection:bg-red-600 selection:text-white min-h-screen">
        @inertia
    </body>
</html>
