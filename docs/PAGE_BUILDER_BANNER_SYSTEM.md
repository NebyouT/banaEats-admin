# Page Builder Banner System - Complete Guide

**Date:** February 28, 2026

---

## Overview

The **Page Builder Banner System** is a new banner management system that links directly to Page Builder pages. It replaces the old custom page banner system with a cleaner, more integrated solution.

---

## Key Features

- ✅ **Direct Page Builder Integration**: Banners link directly to page builder pages
- ✅ **Same Structure as Old System**: Familiar interface and workflow
- ✅ **Automatic WebView URLs**: API returns direct URLs for Flutter WebView
- ✅ **Image & Video Support**: Supports images, videos, and GIFs
- ✅ **Two Banner Types**: Square (1:1) and Horizontal (5:1) aspect ratios
- ✅ **Status Management**: Active/Inactive toggle
- ✅ **Full CRUD Operations**: Create, Read, Update, Delete

---

## Database Schema

### Table: `page_builder_banners`

| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| title | string | Banner title |
| image | string | Banner image path |
| media_type | string | 'image', 'video', or 'gif' |
| type | string | 'square' or 'horizontal' |
| builder_page_id | bigint | FK to builder_pages |
| status | tinyint | 1=active, 0=inactive |
| created_at | timestamp | |
| updated_at | timestamp | |

---

## Installation

### 1. Run Migrations

```bash
# Run all pending migrations
php artisan migrate

# This will create:
# - page_builder_banners table
# - Add builder_page_id to custom_page_banners (for backward compatibility)
```

### 2. Create Storage Directory

```bash
mkdir -p storage/app/public/page-builder-banner
```

---

## Admin Panel Usage

### Access Banner Management

Navigate to: `/admin/page-builder-banner`

### Create a Banner

1. Click **"Create Banner"**
2. Fill in the form:
   - **Title**: Banner name (for admin reference)
   - **Image**: Upload banner image (max 5MB)
   - **Type**: Select Square (1:1) or Horizontal (5:1)
   - **Media Type**: Image, Video, or GIF
   - **Page**: Select which page builder page to link
   - **Status**: Active or Inactive
3. Click **"Save"**

### Edit a Banner

1. Click **"Edit"** on any banner
2. Update fields as needed
3. Upload new image (optional)
4. Click **"Update"**

### Delete a Banner

1. Click **"Delete"** on any banner
2. Confirm deletion
3. Banner and associated image will be removed

### Toggle Status

- Click the status toggle to activate/deactivate banners
- Inactive banners won't appear in API responses

---

## API Endpoints

### 1. List All Banners

**Endpoint:** `GET /api/v1/page-builder-banners`

**Query Parameters:**
- `type` (optional): Filter by 'square' or 'horizontal'

**Response:**
```json
{
  "banners": [
    {
      "id": 1,
      "title": "Summer Sale Banner",
      "type": "horizontal",
      "aspect_ratio": "5:1",
      "media_type": "image",
      "media_full_url": "https://domain.com/storage/page-builder-banner/banner.jpg",
      "page_id": 5,
      "web_url": "https://domain.com/page/summer-sale-abc123",
      "status": 1,
      "is_active": true,
      "created_at": "2026-02-28T12:00:00+00:00",
      "updated_at": "2026-02-28T12:00:00+00:00"
    }
  ]
}
```

### 2. Get Banner Details

**Endpoint:** `GET /api/v1/page-builder-banners/{id}`

**Response:**
```json
{
  "id": 1,
  "title": "Summer Sale Banner",
  "type": "horizontal",
  "aspect_ratio": "5:1",
  "media_type": "image",
  "media_full_url": "https://domain.com/storage/page-builder-banner/banner.jpg",
  "page_id": 5,
  "web_url": "https://domain.com/page/summer-sale-abc123",
  "status": 1,
  "is_active": true,
  "created_at": "2026-02-28T12:00:00+00:00",
  "updated_at": "2026-02-28T12:00:00+00:00",
  "linked_page": {
    "id": 5,
    "title": "Summer Sale",
    "slug": "summer-sale-abc123",
    "description": "Get 50% off on all items",
    "web_url": "https://domain.com/page/summer-sale-abc123",
    "is_published": true
  }
}
```

---

## Flutter Integration

### Fetch Banners

```dart
Future<List<Banner>> fetchPageBuilderBanners({String? type}) async {
  final response = await http.get(
    Uri.parse('$baseUrl/api/v1/page-builder-banners${type != null ? '?type=$type' : ''}'),
  );
  
  if (response.statusCode == 200) {
    final data = json.decode(response.body);
    return (data['banners'] as List)
        .map((json) => Banner.fromJson(json))
        .toList();
  }
  throw Exception('Failed to load banners');
}
```

### Banner Model

```dart
class Banner {
  final int id;
  final String title;
  final String type;
  final String aspectRatio;
  final String mediaType;
  final String mediaFullUrl;
  final int? pageId;
  final String? webUrl;
  final bool isActive;
  
  Banner({
    required this.id,
    required this.title,
    required this.type,
    required this.aspectRatio,
    required this.mediaType,
    required this.mediaFullUrl,
    this.pageId,
    this.webUrl,
    required this.isActive,
  });
  
  factory Banner.fromJson(Map<String, dynamic> json) {
    return Banner(
      id: json['id'],
      title: json['title'],
      type: json['type'],
      aspectRatio: json['aspect_ratio'],
      mediaType: json['media_type'],
      mediaFullUrl: json['media_full_url'],
      pageId: json['page_id'],
      webUrl: json['web_url'],
      isActive: json['is_active'],
    );
  }
}
```

### Handle Banner Click

```dart
void handleBannerClick(Banner banner) {
  if (banner.webUrl != null) {
    Navigator.push(
      context,
      MaterialPageRoute(
        builder: (_) => PageBuilderWebView(url: banner.webUrl!),
      ),
    );
  }
}
```

### Display Banners

```dart
Widget buildBannerCarousel(List<Banner> banners) {
  return CarouselSlider.builder(
    itemCount: banners.length,
    itemBuilder: (context, index, realIndex) {
      final banner = banners[index];
      return GestureDetector(
        onTap: () => handleBannerClick(banner),
        child: Container(
          margin: EdgeInsets.symmetric(horizontal: 8),
          decoration: BoxDecoration(
            borderRadius: BorderRadius.circular(12),
            boxShadow: [
              BoxShadow(
                color: Colors.black.withOpacity(0.1),
                blurRadius: 8,
                offset: Offset(0, 2),
              ),
            ],
          ),
          child: ClipRRect(
            borderRadius: BorderRadius.circular(12),
            child: CachedNetworkImage(
              imageUrl: banner.mediaFullUrl,
              fit: BoxFit.cover,
              placeholder: (context, url) => Container(
                color: Colors.grey[200],
                child: Center(child: CircularProgressIndicator()),
              ),
              errorWidget: (context, url, error) => Icon(Icons.error),
            ),
          ),
        ),
      );
    },
    options: CarouselOptions(
      aspectRatio: banner.type == 'square' ? 1.0 : 5.0,
      autoPlay: true,
      enlargeCenterPage: true,
      viewportFraction: 0.9,
    ),
  );
}
```

---

## Differences from Old Custom Page Banner

| Feature | Old System | New System |
|---------|-----------|------------|
| **Links To** | Custom Pages | Page Builder Pages |
| **Page Type** | Limited layout | Advanced drag-and-drop builder |
| **API Endpoint** | `/api/v1/custom-page-banners` | `/api/v1/page-builder-banners` |
| **WebView URL** | Not provided | Automatically included |
| **Admin Route** | `/admin/custom-page-banner` | `/admin/page-builder-banner` |
| **Table** | `custom_page_banners` | `page_builder_banners` |
| **Model** | `CustomPageBanner` | `PageBuilderBanner` |

---

## Migration from Old System

### Option 1: Keep Both Systems

- Old banners continue to work
- Create new banners using page builder system
- Gradually phase out old system

### Option 2: Migrate Existing Banners

```php
// Migration script (run in tinker or create migration)
use App\Models\CustomPageBanner;
use App\Models\PageBuilderBanner;

CustomPageBanner::where('page_type', 'builder')->each(function($oldBanner) {
    PageBuilderBanner::create([
        'title' => $oldBanner->title,
        'image' => $oldBanner->image,
        'media_type' => $oldBanner->media_type,
        'type' => $oldBanner->type,
        'builder_page_id' => $oldBanner->builder_page_id,
        'status' => $oldBanner->status,
    ]);
});
```

---

## Files Created

### Backend

| File | Purpose |
|------|---------|
| `app/Models/PageBuilderBanner.php` | Model for page builder banners |
| `app/Http/Controllers/Admin/PageBuilderBannerController.php` | Admin CRUD controller |
| `app/Http/Controllers/Api/V1/PageBuilderBannerController.php` | API controller |
| `database/migrations/2026_02_28_000002_create_page_builder_banners_table.php` | Database migration |

### Routes

| File | Changes |
|------|---------|
| `routes/admin.php` | Added page-builder-banner routes |
| `routes/api/v1/api.php` | Added API endpoints |

---

## Troubleshooting

### Banner Not Showing in API

**Problem:** Banner doesn't appear in API response

**Solutions:**
- Check banner status is set to Active (1)
- Verify linked page exists and is published
- Check API endpoint is correct: `/api/v1/page-builder-banners`

### Image Not Loading

**Problem:** Banner image returns 404

**Solutions:**
- Run `php artisan storage:link`
- Check image exists in `storage/app/public/page-builder-banner/`
- Verify image path in database

### WebView Not Opening

**Problem:** Clicking banner doesn't open page

**Solutions:**
- Check `web_url` field is present in API response
- Verify page is published
- Check Flutter WebView implementation

---

## Best Practices

### Banner Images

- **Horizontal Banners**: 1500x300px (5:1 ratio)
- **Square Banners**: 600x600px (1:1 ratio)
- **Format**: JPG or PNG
- **Size**: Under 500KB for fast loading
- **Quality**: 80-90% compression

### Banner Management

- Use descriptive titles for easy identification
- Link to published pages only
- Test banners before activating
- Monitor click-through rates
- Update banners regularly for freshness

---

## Support

For issues or questions:
1. Check this documentation
2. Review API responses for errors
3. Check Laravel logs: `storage/logs/laravel.log`
4. Contact development team

---

**Last Updated:** February 28, 2026
