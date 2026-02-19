# Custom Page Banners API — Frontend Developer Reference

Base URL: `https://your-domain.com/api/v1`

---

## Overview

Custom Page Banners are promotional media (image, GIF, or video) displayed in your app. Each banner:

- Has a **type** that tells Flutter which aspect ratio to use
- Has a **media_type** that tells Flutter whether to render an `Image`, animated GIF, or `VideoPlayer`
- Is linked to **exactly one** Custom Page — tapping the banner navigates directly to that page
- Has a **status** — only active banners (`is_active: true`) are returned by the API

### Banner Types

| `type` | Aspect Ratio | Min Recommended Size | Use Case |
|---|---|---|---|
| `square` | **1 : 1** | 600 × 600 px | Grid tiles, small cards |
| `wide` | **5 : 1** | 1500 × 300 px | Full-width hero banners |

### Media Types

| `media_type` | What it is | How to render in Flutter |
|---|---|---|
| `image` | JPG / PNG / WebP | `Image.network(url)` |
| `gif` | Animated GIF | `Image.network(url)` (Flutter handles GIF automatically) |
| `video` | MP4 / WebM / MOV | `VideoPlayerController.network(url)` — autoplay, muted, looped |

---

## Endpoints

### 1. List All Active Banners

**`GET /api/v1/custom-page-banners`**

Returns all active banners. You can optionally filter by type.

#### Query Parameters

| Parameter | Type | Required | Description |
|---|---|---|---|
| `type` | string | No | Filter by `square` or `wide` |

#### Examples

```
GET /api/v1/custom-page-banners
GET /api/v1/custom-page-banners?type=wide
GET /api/v1/custom-page-banners?type=square
```

#### Response

```json
{
  "banners": [
    {
      "id": 1,
      "title": "Summer Sale Banner",
      "type": "wide",
      "aspect_ratio": "5:1",
      "media_type": "video",
      "media_full_url": "https://your-domain.com/storage/custom-page-banner/abc123.mp4",
      "page_id": 2,
      "status": 1,
      "is_active": true,
      "created_at": "2024-01-15T10:30:00.000000Z",
      "updated_at": "2024-01-15T10:30:00.000000Z"
    },
    {
      "id": 2,
      "title": "Promo Square GIF",
      "type": "square",
      "aspect_ratio": "1:1",
      "media_type": "gif",
      "media_full_url": "https://your-domain.com/storage/custom-page-banner/def456.gif",
      "page_id": 5,
      "status": 1,
      "is_active": true,
      "created_at": "2024-01-14T08:00:00.000000Z",
      "updated_at": "2024-01-14T08:00:00.000000Z"
    },
    {
      "id": 3,
      "title": "Static Wide Banner",
      "type": "wide",
      "aspect_ratio": "5:1",
      "media_type": "image",
      "media_full_url": "https://your-domain.com/storage/custom-page-banner/ghi789.png",
      "page_id": null,
      "status": 1,
      "is_active": true,
      "created_at": "2024-01-13T06:00:00.000000Z",
      "updated_at": "2024-01-13T06:00:00.000000Z"
    }
  ]
}
```

---

### 2. Get Single Banner with Its Linked Page

**`GET /api/v1/custom-page-banners/{id}`**

Returns a single active banner plus the one Custom Page it is linked to (if any). Use this when the user taps a banner to navigate to its page.

#### URL Parameter

| Parameter | Description |
|---|---|
| `id` | The numeric banner ID from the list endpoint |

#### Response

```json
{
  "id": 1,
  "title": "Summer Sale Banner",
  "type": "wide",
  "aspect_ratio": "5:1",
  "media_type": "video",
  "media_full_url": "https://your-domain.com/storage/custom-page-banner/abc123.mp4",
  "page_id": 2,
  "status": 1,
  "is_active": true,
  "created_at": "2024-01-15T10:30:00.000000Z",
  "updated_at": "2024-01-15T10:30:00.000000Z",
  "linked_page": {
    "id": 2,
    "title": "Summer Specials",
    "slug": "summer-specials-a3f9bx",
    "subtitle": "Best deals this week",
    "promotional_text": "Limited time offer!",
    "background_color": "#FF6B35",
    "background_media_type": "image",
    "background_image_full_url": "https://your-domain.com/storage/custom-page/xyz.png"
  }
}
```

> `linked_page` is `null` if the admin did not link this banner to any page.

#### Error — Banner Not Found or Inactive

```json
{
  "errors": [
    { "code": "not_found", "message": "Banner not found or inactive." }
  ]
}
```
HTTP status: `404`

---

## Field Reference

### Banner Fields

| Field | Type | Description |
|---|---|---|
| `id` | integer | Unique banner ID |
| `title` | string | Admin-set label |
| `type` | string | `"square"` (1:1) or `"wide"` (5:1) — controls aspect ratio |
| `aspect_ratio` | string | Human-readable: `"1:1"` or `"5:1"` |
| `media_type` | string | `"image"`, `"gif"`, or `"video"` — controls how to render |
| `media_full_url` | string | Full URL to the banner media file |
| `page_id` | integer\|null | ID of the one linked Custom Page (null = no link) |
| `status` | integer | `1` = active, `0` = inactive |
| `is_active` | boolean | Convenience bool — same as `status == 1` |
| `linked_page` | object\|null | Only in single-banner endpoint. Full page summary. |

### `linked_page` Fields (in single-banner endpoint)

| Field | Type | Description |
|---|---|---|
| `id` | integer | Page ID |
| `title` | string | Page title |
| `slug` | string | Use this to call `GET /api/v1/custom-pages/{slug}` |
| `subtitle` | string\|null | Optional subtitle |
| `promotional_text` | string\|null | Optional promo text |
| `background_color` | string | Hex color fallback |
| `background_media_type` | string | `"image"`, `"gif"`, or `"video"` |
| `background_image_full_url` | string\|null | Full URL to page background media |

### Custom Page Background Media (from `GET /api/v1/custom-pages` and `GET /api/v1/custom-pages/{slug}`)

Both custom page endpoints now also return `background_media_type` so your app knows whether to render the background as an image, GIF, or video:

```json
{
  "id": 2,
  "title": "Summer Specials",
  "slug": "summer-specials-a3f9bx",
  "background_color": "#FF6B35",
  "background_media_type": "video",
  "background_image_full_url": "https://your-domain.com/storage/custom-page/bg.mp4",
  ...
}
```

---

## Flutter Implementation Guide

### Step 1 — Fetch banners

```dart
import 'dart:convert';
import 'package:http/http.dart' as http;

Future<List<dynamic>> fetchBanners({String? type}) async {
  final uri = Uri.parse('$baseUrl/api/v1/custom-page-banners')
      .replace(queryParameters: type != null ? {'type': type} : null);
  final response = await http.get(uri, headers: authHeaders);
  return (jsonDecode(response.body)['banners'] as List);
}
```

### Step 2 — Render based on `type` AND `media_type`

```dart
import 'package:video_player/video_player.dart';

Widget buildBanner(Map<String, dynamic> banner) {
  final type      = banner['type'];        // "square" or "wide"
  final mediaType = banner['media_type'];  // "image", "gif", "video"
  final url       = banner['media_full_url'] as String;
  final ratio     = type == 'square' ? 1.0 : 5.0;

  Widget media;
  if (mediaType == 'video') {
    media = _BannerVideo(url: url);        // see helper below
  } else {
    // Both "image" and "gif" are rendered with Image.network
    // Flutter's Image widget handles animated GIFs natively
    media = Image.network(url, fit: BoxFit.cover);
  }

  return AspectRatio(
    aspectRatio: ratio,
    child: ClipRRect(
      borderRadius: BorderRadius.circular(type == 'square' ? 12 : 8),
      child: media,
    ),
  );
}

// Auto-playing muted looped video widget
class _BannerVideo extends StatefulWidget {
  final String url;
  const _BannerVideo({required this.url});
  @override State<_BannerVideo> createState() => _BannerVideoState();
}
class _BannerVideoState extends State<_BannerVideo> {
  late VideoPlayerController _ctrl;
  @override void initState() {
    super.initState();
    _ctrl = VideoPlayerController.network(widget.url)
      ..setLooping(true)
      ..setVolume(0)
      ..initialize().then((_) { _ctrl.play(); setState(() {}); });
  }
  @override void dispose() { _ctrl.dispose(); super.dispose(); }
  @override Widget build(BuildContext context) =>
      _ctrl.value.isInitialized
          ? VideoPlayer(_ctrl)
          : const SizedBox.shrink();
}
```

### Step 3 — Navigate to linked page on tap

Each banner links to **exactly one** page. No picker needed.

```dart
void onBannerTap(Map<String, dynamic> banner) async {
  final pageId = banner['page_id'];
  if (pageId == null) return; // banner has no linked page

  // Fetch the banner detail to get the slug
  final res  = await http.get(
    Uri.parse('$baseUrl/api/v1/custom-page-banners/${banner['id']}'),
    headers: authHeaders,
  );
  final data = jsonDecode(res.body);
  final page = data['linked_page'];
  if (page == null) return;

  Navigator.push(context, MaterialPageRoute(
    builder: (_) => CustomPageScreen(slug: page['slug'] as String),
  ));
}
```

### Step 4 — Render a Custom Page background (image OR video)

```dart
Widget buildPageBackground(Map<String, dynamic> page) {
  final mediaType = page['background_media_type'] ?? 'image';
  final url       = page['background_image_full_url'] as String?;
  final color     = page['background_color'] as String? ?? '#ffffff';

  if (url == null) {
    return ColoredBox(color: _hexColor(color));
  }
  if (mediaType == 'video') {
    return _BannerVideo(url: url); // reuse the same widget from Step 2
  }
  // image or gif
  return Image.network(url, fit: BoxFit.cover);
}
```

### Step 5 — Fetch only wide or square banners

```dart
// Hero carousel at top of home screen
final wideBanners   = await fetchBanners(type: 'wide');

// Promo grid section
final squareBanners = await fetchBanners(type: 'square');
```

---

## How the Admin Sets This Up

1. Admin goes to **Custom Page Banners** in the sidebar
2. Clicks **Add Banner**
3. Enters a title and picks **Square (1:1)** or **Wide (5:1)**
4. Uploads the media — **image, GIF, or video** (max 20 MB). The admin UI auto-detects the type and shows a live preview
5. Selects **one** Custom Page from the dropdown (the page the user lands on when tapping)
6. Saves — banner is immediately **Active**
7. Admin can toggle status on/off from the list at any time

The `status` field is the single source of truth. Deactivated banners disappear from the API immediately.

---

## Recommended Widget Structure

```
HomePage
├── HeroCarousel      ← GET /custom-page-banners?type=wide
│   └── buildBanner() — renders video / gif / image + navigates on tap
├── CategoryRow
├── SquareBannerGrid  ← GET /custom-page-banners?type=square
└── RestaurantList

CustomPageScreen (slug)   ← GET /custom-pages/{slug}
├── buildPageBackground() ← background_media_type: image / gif / video
├── ProductGrid
└── RestaurantGrid
```
