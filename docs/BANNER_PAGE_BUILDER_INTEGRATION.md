# Banner & Page Builder Integration API Documentation

## Overview

Banners can now link to **Page Builder pages** (new system) in addition to the old custom pages. This allows you to create rich, interactive promotional pages with advanced layouts, animations, and dynamic content.

---

## Key Changes

### 1. Banner Model Updates

The `custom_page_banners` table now includes:
- `page_type` (string): Either `'custom'` (old system) or `'builder'` (new page builder)
- `builder_page_id` (integer, nullable): Foreign key to `builder_pages` table

### 2. Backward Compatibility

- Old banners with `page_type = 'custom'` continue to work as before
- New banners with `page_type = 'builder'` link to page builder pages
- The API automatically returns the correct page data based on `page_type`

---

## API Endpoints

### 1. List All Banners

**Endpoint:** `GET /api/v1/custom-page-banners`

**Query Parameters:**
- `type` (optional): Filter by banner type (`'square'` or `'horizontal'`)

**Response:**
```json
{
  "banners": [
    {
      "id": 1,
      "title": "Summer Sale",
      "type": "horizontal",
      "aspect_ratio": "5:1",
      "media_type": "image",
      "media_full_url": "https://your-domain.com/storage/custom-page-banner/banner.jpg",
      "page_type": "builder",
      "page_id": 5,
      "web_url": "https://your-domain.com/page/summer-sale-abc123",
      "status": 1,
      "is_active": true,
      "created_at": "2026-02-28T12:00:00+00:00",
      "updated_at": "2026-02-28T12:00:00+00:00"
    }
  ]
}
```

**New Fields:**
- `page_type`: Indicates whether this links to a `'builder'` page or `'custom'` page
- `web_url`: Direct URL to the page (only for builder pages)

---

### 2. Get Banner Details

**Endpoint:** `GET /api/v1/custom-page-banners/{id}`

**Response for Builder Page:**
```json
{
  "id": 1,
  "title": "Summer Sale",
  "type": "horizontal",
  "aspect_ratio": "5:1",
  "media_type": "image",
  "media_full_url": "https://your-domain.com/storage/custom-page-banner/banner.jpg",
  "page_type": "builder",
  "page_id": 5,
  "web_url": "https://your-domain.com/page/summer-sale-abc123",
  "status": 1,
  "is_active": true,
  "created_at": "2026-02-28T12:00:00+00:00",
  "updated_at": "2026-02-28T12:00:00+00:00",
  "linked_page": {
    "id": 5,
    "title": "Summer Sale",
    "slug": "summer-sale-abc123",
    "description": "Get 50% off on all items",
    "page_type": "builder",
    "web_url": "https://your-domain.com/page/summer-sale-abc123",
    "is_published": true
  }
}
```

**Response for Old Custom Page:**
```json
{
  "id": 2,
  "title": "Old Promo",
  "type": "square",
  "aspect_ratio": "1:1",
  "media_type": "image",
  "media_full_url": "https://your-domain.com/storage/custom-page-banner/banner2.jpg",
  "page_type": "custom",
  "page_id": 3,
  "web_url": null,
  "status": 1,
  "is_active": true,
  "created_at": "2026-01-15T10:00:00+00:00",
  "updated_at": "2026-01-15T10:00:00+00:00",
  "linked_page": {
    "id": 3,
    "title": "Old Custom Page",
    "slug": "old-promo-xyz789",
    "subtitle": "Limited time offer",
    "promotional_text": "Shop now!",
    "background_color": "#FF5733",
    "background_media_type": "image",
    "background_image_full_url": "https://your-domain.com/storage/custom-page/bg.jpg",
    "page_type": "custom"
  }
}
```

---

## Flutter Integration

### Handling Banner Clicks

```dart
void handleBannerClick(Map<String, dynamic> banner) {
  final pageType = banner['page_type'] ?? 'custom';
  
  if (pageType == 'builder') {
    // New page builder - open in WebView
    final webUrl = banner['web_url'];
    if (webUrl != null) {
      Navigator.push(
        context,
        MaterialPageRoute(
          builder: (_) => PageBuilderWebView(url: webUrl),
        ),
      );
    }
  } else {
    // Old custom page - use existing custom page screen
    final linkedPage = banner['linked_page'];
    if (linkedPage != null) {
      Navigator.push(
        context,
        MaterialPageRoute(
          builder: (_) => CustomPageScreen(pageData: linkedPage),
        ),
      );
    }
  }
}
```

### WebView Implementation for Page Builder Pages

```dart
import 'package:flutter_inappwebview/flutter_inappwebview.dart';

class PageBuilderWebView extends StatefulWidget {
  final String url;
  
  const PageBuilderWebView({required this.url});
  
  @override
  _PageBuilderWebViewState createState() => _PageBuilderWebViewState();
}

class _PageBuilderWebViewState extends State<PageBuilderWebView> {
  late InAppWebViewController _controller;
  
  @override
  Widget build(BuildContext context) {
    return Scaffold(
      appBar: AppBar(
        title: Text('Promotion'),
        backgroundColor: Colors.transparent,
        elevation: 0,
      ),
      body: InAppWebView(
        initialUrlRequest: URLRequest(url: Uri.parse(widget.url)),
        onWebViewCreated: (controller) {
          _controller = controller;
          
          // Register handler for page actions (product clicks, etc.)
          controller.addJavaScriptHandler(
            handlerName: 'onPageAction',
            callback: (args) {
              if (args.isNotEmpty) {
                _handlePageAction(args[0]);
              }
            },
          );
        },
        initialOptions: InAppWebViewGroupOptions(
          crossPlatform: InAppWebViewOptions(
            useShouldOverrideUrlLoading: true,
            mediaPlaybackRequiresUserGesture: false,
          ),
          android: AndroidInAppWebViewOptions(
            useHybridComposition: true,
          ),
          ios: IOSInAppWebViewOptions(
            allowsInlineMediaPlayback: true,
          ),
        ),
      ),
    );
  }
  
  void _handlePageAction(Map<String, dynamic> action) {
    final type = action['type'];
    
    switch (type) {
      case 'navigate_product':
        final productId = action['product_id'];
        Navigator.push(
          context,
          MaterialPageRoute(
            builder: (_) => ProductDetailScreen(productId: productId),
          ),
        );
        break;
        
      case 'navigate_restaurant':
        final restaurantId = action['restaurant_id'];
        Navigator.push(
          context,
          MaterialPageRoute(
            builder: (_) => RestaurantScreen(restaurantId: restaurantId),
          ),
        );
        break;
        
      case 'navigate_category':
        final categoryId = action['category_id'];
        Navigator.push(
          context,
          MaterialPageRoute(
            builder: (_) => CategoryScreen(categoryId: categoryId),
          ),
        );
        break;
        
      case 'open_url':
        final url = action['url'];
        launchUrl(Uri.parse(url));
        break;
    }
  }
}
```

---

## Migration Guide

### For Existing Banners

1. **No action required** - Existing banners will continue to work with `page_type = 'custom'`
2. Old custom pages remain functional
3. The API automatically handles both types

### For New Banners

1. **Admin creates a page** using the new Page Builder (`/admin/page-builder`)
2. **Admin creates a banner** and selects the page builder page
3. **Backend sets** `page_type = 'builder'` and `builder_page_id`
4. **API returns** `web_url` for direct WebView access

---

## Page Builder Features

When a banner links to a page builder page, users get access to:

### Advanced Layouts
- Hero banners with overlay text
- Product grids (2 or 3 columns)
- Product carousels (horizontal scroll)
- Restaurant grids and carousels
- Restaurant + Foods combo cards
- Tabbed content sections
- Text blocks, images, buttons
- Countdown timers
- Spacers and dividers

### Customization
- **Spacing**: Padding, margin control
- **Background**: Colors, images, opacity
- **Typography**: Font size, color, weight
- **Positioning**: Fixed, absolute, sticky elements
- **Animations**: fadeIn, slideIn, zoomIn, bounceIn, pulse
- **Borders**: Radius, shadows, borders
- **Product/Restaurant Cards**: Toggle fields (image, name, price, rating), custom colors

### Interactive Elements
- Click actions on any component
- Navigate to products, restaurants, categories
- Open external URLs
- Countdown timers
- Video backgrounds

---

## Testing Checklist

### Backend
- [ ] Run migration: `php artisan migrate`
- [ ] Create a test page builder page
- [ ] Create a banner linked to the page builder page
- [ ] Verify API returns correct `page_type` and `web_url`

### Flutter
- [ ] Fetch banners from API
- [ ] Check `page_type` field
- [ ] Open builder pages in WebView
- [ ] Test JavaScript bridge for actions
- [ ] Verify product/restaurant navigation works
- [ ] Test old custom pages still work

---

## Troubleshooting

### Banner not showing web_url
- Check `page_type` is set to `'builder'`
- Verify `builder_page_id` is not null
- Ensure the linked page exists and is published

### WebView not loading
- Check the page is published (`is_published = true`)
- Verify the URL is correct
- Check network connectivity

### Actions not working in WebView
- Ensure `flutter_inappwebview` is properly configured
- Register the `onPageAction` JavaScript handler
- Check browser console for errors

---

## Database Schema

### custom_page_banners

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| title | string | Banner title |
| image | string | Banner image path |
| media_type | string | 'image', 'video', or 'gif' |
| type | string | 'square' or 'horizontal' |
| page_id | bigint | FK to custom_pages (old system) |
| **page_type** | **string** | **'custom' or 'builder'** |
| **builder_page_id** | **bigint** | **FK to builder_pages** |
| status | tinyint | Active status |

---

## Example Use Cases

### 1. Flash Sale Banner
- Create page builder page with countdown timer
- Add product grid with sale items
- Link banner to page
- Users click banner → WebView opens → See countdown + products

### 2. Restaurant Promotion
- Create page with restaurant + foods combo card
- Add hero banner with promo text
- Link banner to page
- Users click → See restaurant info + scrollable food list

### 3. Category Showcase
- Create tabbed page with multiple product categories
- Each tab shows different category products
- Link banner to page
- Users click → Browse categories in tabs

---

## Support

For issues or questions:
1. Check this documentation
2. Review the Page Builder API docs (`PAGE_BUILDER_API.md`)
3. Contact the backend team

---

**Last Updated:** February 28, 2026
