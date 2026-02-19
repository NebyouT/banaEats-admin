# Custom Pages API — Frontend Developer Reference

Base URL: `https://your-domain.com/api/v1`

All requests must include the standard app authentication headers your app already uses for other API calls.

---

## 1. List All Active Custom Pages

**`GET /api/v1/custom-pages`**

Returns a lightweight list of all active custom pages. Use this to build a home screen carousel, menu, or navigation list.

### Response

```json
{
  "pages": [
    {
      "id": 1,
      "title": "Summer Specials",
      "slug": "summer-specials-a3f9bx",
      "subtitle": "Best deals this week",
      "promotional_text": "Limited time offer — order now!",
      "background_color": "#FF6B35",
      "background_image_full_url": "https://your-domain.com/storage/custom-page/abc123.png",
      "updated_at": "2024-01-15T10:30:00.000000Z"
    },
    {
      "id": 2,
      "title": "Top Restaurants",
      "slug": "top-restaurants-x7k2mn",
      "subtitle": null,
      "promotional_text": null,
      "background_color": "#ffffff",
      "background_image_full_url": null,
      "updated_at": "2024-01-14T08:00:00.000000Z"
    }
  ]
}
```

### Field Reference

| Field | Type | Description |
|---|---|---|
| `id` | integer | Unique page ID |
| `title` | string | Page title to display |
| `slug` | string | URL-safe identifier — use this to fetch full details |
| `subtitle` | string\|null | Optional subtitle |
| `promotional_text` | string\|null | Optional promo banner text |
| `background_color` | string | Hex color for page background (default `#ffffff`) |
| `background_image_full_url` | string\|null | Full URL to background image (1200×400 recommended) |
| `updated_at` | ISO 8601 | Last modified timestamp |

---

## 2. Get Full Page Details

**`GET /api/v1/custom-pages/{slug}`**

Returns the full page data including all associated products and restaurants.

### URL Parameter

| Parameter | Description |
|---|---|
| `slug` | The `slug` value from the list endpoint (e.g. `summer-specials-a3f9bx`) |

### Response

```json
{
  "id": 1,
  "title": "Summer Specials",
  "slug": "summer-specials-a3f9bx",
  "subtitle": "Best deals this week",
  "promotional_text": "Limited time offer!",
  "background_color": "#FF6B35",
  "background_image_full_url": "https://your-domain.com/storage/custom-page/abc123.png",
  "products": [
    {
      "id": 42,
      "name": "Grilled Chicken Burger",
      "image_full_url": "https://your-domain.com/storage/product/burger.png",
      "price": 12.99,
      "discount": 10,
      "discount_type": "percent",
      "restaurant_id": 5,
      "restaurant_name": "Burger Palace",
      "restaurant_status": 1,
      "avg_rating": 4.5,
      "rating_count": 120,
      "delivery_time": "20-30",
      "min_delivery_time": 20,
      "max_delivery_time": 30,
      "veg": 0,
      "add_ons": [],
      "variations": [],
      "category_ids": [
        { "id": "3", "position": 1 }
      ],
      "cuisines": [
        { "id": 1, "name": "American", "image": "..." }
      ]
    }
  ],
  "restaurants": [
    {
      "id": 5,
      "name": "Burger Palace",
      "logo_full_url": "https://your-domain.com/storage/restaurant/logo.png",
      "cover_photo_full_url": "https://your-domain.com/storage/restaurant/cover/cover.png",
      "address": "123 Main St, City",
      "avg_rating": 4.3,
      "rating_count": 850,
      "delivery_time": "20-30",
      "minimum_order": 5.00,
      "cuisines": ["American", "Fast Food"]
    }
  ]
}
```

### Products Field Reference

The `products` array uses the **same format as the existing product API** throughout the app. Every field your app already handles for product cards is present here.

| Field | Type | Description |
|---|---|---|
| `id` | integer | Product ID |
| `name` | string | Product name (translated if applicable) |
| `image_full_url` | string\|null | Full URL to product image |
| `price` | float | Base price |
| `discount` | float | Discount amount |
| `discount_type` | string | `"percent"` or `"amount"` |
| `restaurant_id` | integer | Owning restaurant ID |
| `restaurant_name` | string | Restaurant name |
| `restaurant_status` | integer | `1` = open, `0` = closed |
| `avg_rating` | float | Average product rating |
| `rating_count` | integer | Number of ratings |
| `min_delivery_time` | integer | Min delivery minutes |
| `max_delivery_time` | integer | Max delivery minutes |
| `veg` | integer | `1` = vegetarian |
| `add_ons` | array | Available add-ons |
| `variations` | array | Product variations |
| `category_ids` | array | `[{ "id": "3", "position": 1 }]` |
| `cuisines` | array | `[{ "id": 1, "name": "...", "image": "..." }]` |
| `free_delivery` | integer | `1` = free delivery |
| `halal_tag_status` | integer | `1` = halal certified |
| `schedule_order` | boolean | Whether schedule ordering is enabled |

### Restaurants Field Reference

| Field | Type | Description |
|---|---|---|
| `id` | integer | Restaurant ID |
| `name` | string | Restaurant name |
| `logo_full_url` | string\|null | Full URL to restaurant logo |
| `cover_photo_full_url` | string\|null | Full URL to cover photo |
| `address` | string | Street address |
| `avg_rating` | float | Average rating |
| `rating_count` | integer | Number of ratings |
| `delivery_time` | string | e.g. `"20-30"` (minutes) |
| `minimum_order` | float | Minimum order amount |
| `cuisines` | array of strings | e.g. `["American", "Fast Food"]` |

### Error Response (page not found)

```json
{
  "errors": [
    { "code": "not_found", "message": "Page not found" }
  ]
}
```
HTTP status: `404`

---

## Usage Examples

### Flutter / Dart

```dart
// 1. Fetch list
final response = await http.get(
  Uri.parse('$baseUrl/api/v1/custom-pages'),
  headers: yourAuthHeaders,
);
final pages = jsonDecode(response.body)['pages'] as List;

// 2. Fetch full page by slug
final slug = pages[0]['slug'];
final detail = await http.get(
  Uri.parse('$baseUrl/api/v1/custom-pages/$slug'),
  headers: yourAuthHeaders,
);
final pageData = jsonDecode(detail.body);
final products    = pageData['products']    as List;
final restaurants = pageData['restaurants'] as List;
```

### React Native / JavaScript

```js
// 1. Fetch list
const { pages } = await fetch(`${BASE_URL}/api/v1/custom-pages`, {
  headers: authHeaders,
}).then(r => r.json());

// 2. Fetch full page
const page = await fetch(`${BASE_URL}/api/v1/custom-pages/${pages[0].slug}`, {
  headers: authHeaders,
}).then(r => r.json());

// Render products using your existing ProductCard component
page.products.forEach(product => renderProductCard(product));

// Render restaurants using your existing RestaurantCard component
page.restaurants.forEach(restaurant => renderRestaurantCard(restaurant));
```

---

## Notes for Developers

- **Products use the same data shape** as every other product endpoint in the app. You can reuse your existing `ProductCard` widget/component directly.
- **Restaurants use the same data shape** as the restaurant list endpoint. Reuse your existing `RestaurantCard` widget/component.
- The **order of products and restaurants** in the response matches exactly the order the admin set in the admin panel.
- `background_image_full_url` may be `null` — fall back to `background_color` in that case.
- `background_color` is always a valid hex string (default `#ffffff`).
- Only **active** pages are returned. Inactive pages return `404`.
