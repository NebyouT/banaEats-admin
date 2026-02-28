<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>{{ $page->title }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: {{ $page->settings['font_family'] ?? 'Inter, sans-serif' }};
            background: {{ $page->settings['background_color'] ?? '#ffffff' }};
            color: {{ $page->settings['text_color'] ?? '#1a1a1a' }};
            -webkit-font-smoothing: antialiased;
            min-height: 100vh;
        }
        img { max-width: 100%; height: auto; }
        
        /* Section styles */
        .pb-section { padding: 16px; }
        .pb-section.hero { padding: 0; position: relative; }
        
        /* Component styles */
        .pb-text { font-size: 14px; line-height: 1.6; }
        .pb-heading { font-weight: 700; margin-bottom: 8px; }
        .pb-heading.h1 { font-size: 28px; }
        .pb-heading.h2 { font-size: 24px; }
        .pb-heading.h3 { font-size: 20px; }
        
        .pb-image { border-radius: 12px; overflow: hidden; }
        .pb-image img { width: 100%; display: block; }
        
        .pb-button {
            display: inline-block;
            padding: 14px 28px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 15px;
            text-decoration: none;
            text-align: center;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .pb-button:active { transform: scale(0.98); }
        
        .pb-spacer { display: block; }
        .pb-divider { border: none; border-top: 1px solid #e0e0e0; margin: 8px 0; }
        
        /* Grid layouts */
        .pb-grid { display: grid; gap: 12px; }
        .pb-grid.cols-2 { grid-template-columns: repeat(2, 1fr); }
        .pb-grid.cols-3 { grid-template-columns: repeat(3, 1fr); }
        
        .pb-carousel { display: flex; gap: 12px; overflow-x: auto; padding-bottom: 8px; scroll-snap-type: x mandatory; -webkit-overflow-scrolling: touch; }
        .pb-carousel::-webkit-scrollbar { display: none; }
        .pb-carousel > * { flex-shrink: 0; scroll-snap-align: start; }
        
        /* Product card */
        .pb-product-card {
            background: #fff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .pb-product-card:active { transform: scale(0.98); }
        .pb-product-card .card-img {
            height: 120px;
            background: #f5f5f5;
            position: relative;
            overflow: hidden;
        }
        .pb-product-card .card-img img { width: 100%; height: 100%; object-fit: cover; }
        .pb-product-card .card-body { padding: 12px; }
        .pb-product-card .card-title { font-size: 14px; font-weight: 600; margin-bottom: 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .pb-product-card .card-restaurant { font-size: 12px; color: #888; margin-bottom: 6px; }
        .pb-product-card .card-footer { display: flex; align-items: center; justify-content: space-between; }
        .pb-product-card .card-price { font-size: 16px; font-weight: 700; color: {{ $page->settings['primary_color'] ?? '#FC6A57' }}; }
        .pb-product-card .card-rating { font-size: 12px; color: #888; }
        .pb-product-card .add-btn {
            width: 32px; height: 32px;
            background: {{ $page->settings['primary_color'] ?? '#FC6A57' }};
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            cursor: pointer;
        }
        
        /* Restaurant card */
        .pb-restaurant-card {
            background: #fff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(0,0,0,0.08);
            cursor: pointer;
            transition: transform 0.2s;
        }
        .pb-restaurant-card:active { transform: scale(0.98); }
        .pb-restaurant-card .card-img { height: 100px; background: #f5f5f5; }
        .pb-restaurant-card .card-img img { width: 100%; height: 100%; object-fit: cover; }
        .pb-restaurant-card .card-body { padding: 12px; }
        .pb-restaurant-card .card-title { font-size: 14px; font-weight: 600; margin-bottom: 4px; }
        .pb-restaurant-card .card-meta { font-size: 12px; color: #888; display: flex; align-items: center; gap: 8px; }
        .pb-restaurant-card .card-meta .rating { color: #FFB800; }
        
        /* Section title */
        .section-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px; }
        .section-title { font-size: 18px; font-weight: 700; }
        .section-link { font-size: 13px; color: {{ $page->settings['primary_color'] ?? '#FC6A57' }}; text-decoration: none; font-weight: 600; }
        
        /* Hero section */
        .pb-hero {
            position: relative;
            min-height: 200px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 40px 20px;
            background-size: cover;
            background-position: center;
        }
        .pb-hero-overlay {
            position: absolute;
            inset: 0;
            background: rgba(0,0,0,0.4);
        }
        .pb-hero-content { position: relative; z-index: 1; color: #fff; }
        .pb-hero-title { font-size: 28px; font-weight: 800; margin-bottom: 8px; }
        .pb-hero-subtitle { font-size: 16px; opacity: 0.9; margin-bottom: 16px; }
        
        /* Countdown */
        .pb-countdown { display: flex; gap: 12px; justify-content: center; }
        .pb-countdown-item { text-align: center; background: rgba(255,255,255,0.1); padding: 12px 16px; border-radius: 12px; min-width: 60px; }
        .pb-countdown-value { font-size: 24px; font-weight: 800; }
        .pb-countdown-label { font-size: 11px; text-transform: uppercase; opacity: 0.7; }
        
        /* Badge */
        .pb-badge {
            display: inline-block;
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }
    </style>
</head>
<body>
    @foreach($page->sections as $section)
        @if($section->is_visible)
            @include('admin-views.page-builder.partials.section', ['section' => $section, 'pageSettings' => $page->settings])
        @endif
    @endforeach

    <script>
        // Handle navigation actions
        document.querySelectorAll('[data-action]').forEach(el => {
            el.addEventListener('click', function() {
                const action = JSON.parse(this.dataset.action);
                handleAction(action);
            });
        });

        function handleAction(action) {
            if (!action || action.type === 'none') return;
            
            // Send message to Flutter WebView
            if (window.flutter_inappwebview) {
                window.flutter_inappwebview.callHandler('onPageAction', action);
            } else if (window.FlutterChannel) {
                window.FlutterChannel.postMessage(JSON.stringify(action));
            } else {
                // Fallback for web preview
                console.log('Action:', action);
                
                switch (action.type) {
                    case 'navigate_product':
                        window.location.href = '/product/' + action.product_id;
                        break;
                    case 'navigate_restaurant':
                        window.location.href = '/restaurant/' + action.restaurant_id;
                        break;
                    case 'open_url':
                        window.open(action.url, '_blank');
                        break;
                }
            }
        }

        // Countdown timer
        document.querySelectorAll('.pb-countdown').forEach(countdown => {
            const targetDate = new Date(countdown.dataset.target);
            
            function updateCountdown() {
                const now = new Date();
                const diff = targetDate - now;
                
                if (diff <= 0) {
                    countdown.innerHTML = '<div class="pb-countdown-item"><span class="pb-countdown-value">Expired</span></div>';
                    return;
                }
                
                const days = Math.floor(diff / (1000 * 60 * 60 * 24));
                const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((diff % (1000 * 60)) / 1000);
                
                countdown.querySelector('.days .pb-countdown-value').textContent = days;
                countdown.querySelector('.hours .pb-countdown-value').textContent = hours;
                countdown.querySelector('.minutes .pb-countdown-value').textContent = minutes;
                countdown.querySelector('.seconds .pb-countdown-value').textContent = seconds;
            }
            
            updateCountdown();
            setInterval(updateCountdown, 1000);
        });
    </script>
</body>
</html>
