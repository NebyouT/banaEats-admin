# Page Builder API Documentation

## Overview

The Page Builder system allows administrators to create custom, fully customizable web pages using a drag-and-drop interface. These pages can be displayed in the Flutter app via WebView or rendered natively using the API data.

## Features

- **Drag-and-drop page builder** with real-time preview
- **Multiple section types**: Hero banners, product grids, restaurant lists, text blocks, buttons, spacers, dividers, countdown timers, and more
- **Component-based architecture**: Each section contains customizable components
- **Product & Restaurant integration**: Link actual products and restaurants to display real data
- **Action system**: Configure click actions to navigate to products, restaurants, categories, or external URLs
- **Mobile-responsive**: Pages are optimized for WebView display in mobile apps
- **Flutter communication**: JavaScript bridge for sending actions to the Flutter app

---

## Admin Panel Access

Access the Page Builder at: `/admin/page-builder`

### Creating a Page

1. Go to **Admin Panel → Page Builder**
2. Click **Create Page**
3. Enter page title and description
4. Optionally select a template
5. Click **Create & Edit Page**

### Editing a Page

The editor has three main areas:

1. **Left Sidebar**: Drag sections and components from here
2. **Center Canvas**: Preview and arrange your page content
3. **Right Panel**: Edit properties of selected sections/components

### Available Section Types

| Type | Description |
|------|-------------|
| `hero` | Large banner with text overlay and CTA button |
| `products_grid` | Grid layout of product cards |
| `products_carousel` | Horizontal scrolling products |
| `restaurants_grid` | Grid layout of restaurant cards |
| `restaurants_carousel` | Horizontal scrolling restaurants |
| `text_block` | Rich text content |
| `image_banner` | Full-width image |
| `button_group` | Action buttons |
| `spacer` | Empty vertical space |
| `divider` | Horizontal line separator |
| `countdown` | Countdown timer to a date |

### Available Component Types

| Type | Description |
|------|-------------|
| `text` | Paragraph text |
| `heading` | H1, H2, H3 headings |
| `image` | Single image |
| `button` | Clickable button with action |
| `product_card` | Single product display |
| `product_list` | Multiple products |
| `restaurant_card` | Single restaurant display |
| `restaurant_list` | Multiple restaurants |
| `spacer` | Vertical space |
| `divider` | Horizontal line |

---

## API Endpoints

### Base URL
```
https://your-domain.com/api/v1
```

### 1. List Published Pages

Get all published pages available for display.

**Endpoint:** `GET /api/v1/pages`

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "title": "Summer Sale",
      "slug": "summer-sale-abc123",
      "description": "Hot deals for summer",
      "page_type": "promotion",
      "web_url": "https://your-domain.com/page/summer-sale-abc123",
      "published_at": "2026-02-28T12:00:00+00:00"
    }
  ]
}
```

### 2. Get Page Details

Get full page structure for native rendering.

**Endpoint:** `GET /api/v1/pages/{slug}`

**Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "title": "Summer Sale",
    "slug": "summer-sale-abc123",
    "description": "Hot deals for summer",
    "page_type": "promotion",
    "settings": {
      "background_color": "#ffffff",
      "primary_color": "#FC6A57",
      "secondary_color": "#8DC63F",
      "text_color": "#1a1a1a"
    },
    "sections": [
      {
        "id": 1,
        "type": "hero",
        "name": "Hero Banner",
        "settings": {
          "height": 300,
          "overlay_color": "rgba(0,0,0,0.4)"
        },
        "components": [
          {
            "id": 1,
            "type": "heading",
            "content": {
              "text": "Summer Sale!",
              "level": "h1"
            },
            "settings": {
              "color": "#ffffff"
            },
            "action": null
          }
        ]
      },
      {
        "id": 2,
        "type": "products_grid",
        "name": "Featured Products",
        "settings": {
          "columns": 2,
          "gap": 12
        },
        "components": [
          {
            "id": 2,
            "type": "product_list",
            "content": {
              "title": "Hot Deals",
              "product_ids": [1, 2, 3]
            },
            "settings": {
              "show_price": true,
              "show_rating": true
            },
            "products": [
              {
                "id": 1,
                "name": "Delicious Burger",
                "price": 99.00,
                "image": "https://...",
                "rating": 4.5,
                "restaurant_id": 1,
                "restaurant_name": "Best Burgers"
              }
            ],
            "action": null
          }
        ]
      }
    ],
    "web_url": "https://your-domain.com/page/summer-sale-abc123"
  }
}
```

### 3. Get WebView URL

Get the URL to load in WebView.

**Endpoint:** `GET /api/v1/pages/{slug}/webview-url`

**Response:**
```json
{
  "success": true,
  "data": {
    "url": "https://your-domain.com/page/summer-sale-abc123",
    "title": "Summer Sale"
  }
}
```

---

## WebView Integration (Flutter)

### Loading a Page in WebView

```dart
import 'package:flutter_inappwebview/flutter_inappwebview.dart';

class PageBuilderWebView extends StatefulWidget {
  final String pageSlug;
  
  const PageBuilderWebView({required this.pageSlug});
  
  @override
  _PageBuilderWebViewState createState() => _PageBuilderWebViewState();
}

class _PageBuilderWebViewState extends State<PageBuilderWebView> {
  late InAppWebViewController _controller;
  
  @override
  Widget build(BuildContext context) {
    final baseUrl = 'https://your-domain.com';
    final pageUrl = '$baseUrl/page/${widget.pageSlug}';
    
    return Scaffold(
      appBar: AppBar(title: Text('Page')),
      body: InAppWebView(
        initialUrlRequest: URLRequest(url: Uri.parse(pageUrl)),
        onWebViewCreated: (controller) {
          _controller = controller;
          
          // Register handler for page actions
          controller.addJavaScriptHandler(
            handlerName: 'onPageAction',
            callback: (args) {
              if (args.isNotEmpty) {
                _handlePageAction(args[0]);
              }
            },
          );
        },
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
        
      case 'open_search':
        Navigator.push(
          context,
          MaterialPageRoute(builder: (_) => SearchScreen()),
        );
        break;
    }
  }
}
```

### Action Types

The page can send the following action types to the Flutter app:

| Action Type | Parameters | Description |
|-------------|------------|-------------|
| `navigate_product` | `product_id` | Navigate to product detail screen |
| `navigate_restaurant` | `restaurant_id` | Navigate to restaurant screen |
| `navigate_category` | `category_id` | Navigate to category screen |
| `navigate_page` | `page_slug` | Navigate to another page |
| `open_url` | `url` | Open external URL |
| `open_search` | - | Open search screen |
| `call_phone` | `phone` | Initiate phone call |
| `send_email` | `email` | Open email composer |

---

## Native Rendering (Alternative)

If you prefer to render pages natively in Flutter instead of using WebView, you can use the page structure from the API:

```dart
class PageBuilderNative extends StatelessWidget {
  final Map<String, dynamic> pageData;
  
  const PageBuilderNative({required this.pageData});
  
  @override
  Widget build(BuildContext context) {
    final sections = pageData['sections'] as List;
    final settings = pageData['settings'] as Map<String, dynamic>;
    
    return Scaffold(
      backgroundColor: Color(int.parse(
        settings['background_color'].replaceFirst('#', '0xFF')
      )),
      body: ListView.builder(
        itemCount: sections.length,
        itemBuilder: (context, index) {
          return _buildSection(sections[index]);
        },
      ),
    );
  }
  
  Widget _buildSection(Map<String, dynamic> section) {
    switch (section['type']) {
      case 'hero':
        return _buildHeroSection(section);
      case 'products_grid':
        return _buildProductsGrid(section);
      case 'restaurants_grid':
        return _buildRestaurantsGrid(section);
      // ... handle other section types
      default:
        return SizedBox.shrink();
    }
  }
  
  Widget _buildProductsGrid(Map<String, dynamic> section) {
    final components = section['components'] as List;
    final products = <Map<String, dynamic>>[];
    
    for (var comp in components) {
      if (comp['products'] != null) {
        products.addAll(List<Map<String, dynamic>>.from(comp['products']));
      }
    }
    
    return GridView.builder(
      shrinkWrap: true,
      physics: NeverScrollableScrollPhysics(),
      gridDelegate: SliverGridDelegateWithFixedCrossAxisCount(
        crossAxisCount: section['settings']['columns'] ?? 2,
        crossAxisSpacing: 12,
        mainAxisSpacing: 12,
        childAspectRatio: 0.75,
      ),
      itemCount: products.length,
      itemBuilder: (context, index) {
        return ProductCard(product: products[index]);
      },
    );
  }
}
```

---

## Database Schema

### builder_pages
| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| title | string | Page title |
| slug | string | URL-friendly identifier |
| description | text | Page description |
| page_type | string | Type: custom, promotion, category, etc. |
| settings | json | Global page settings |
| status | boolean | Active/inactive |
| is_published | boolean | Published state |
| published_at | timestamp | Publication date |

### builder_sections
| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| page_id | bigint | Foreign key to pages |
| section_type | string | Type of section |
| name | string | Section name |
| order | integer | Display order |
| settings | json | Section settings |
| style | json | CSS styles |
| is_visible | boolean | Visibility flag |

### builder_components
| Column | Type | Description |
|--------|------|-------------|
| id | bigint | Primary key |
| section_id | bigint | Foreign key to sections |
| component_type | string | Type of component |
| order | integer | Display order |
| column_span | integer | Grid column span (1-12) |
| content | json | Component content |
| settings | json | Component settings |
| style | json | CSS styles |
| data_source | json | Dynamic data config |
| action | json | Click action config |
| is_visible | boolean | Visibility flag |

---

## Example: Creating a Promotion Page

1. **Create the page** with title "Flash Sale"
2. **Add a Hero section** with:
   - Background image
   - Heading: "50% OFF Everything!"
   - Button: "Shop Now" → action: navigate to category
3. **Add Products Grid section** with:
   - Select featured products
   - Configure 2-column layout
4. **Add Countdown section** with:
   - Target date for sale end
5. **Publish the page**

The page will be accessible at:
- WebView URL: `https://your-domain.com/page/flash-sale-xyz123`
- API: `GET /api/v1/pages/flash-sale-xyz123`

---

## Troubleshooting

### Page not showing in API
- Ensure the page is **published** (not just active)
- Check the `is_published` flag is set to `true`

### Actions not working in WebView
- Ensure `flutter_inappwebview` is properly configured
- Register the `onPageAction` JavaScript handler
- Check console logs for action data

### Products/Restaurants not loading
- Verify the product/restaurant IDs are valid
- Check that items are active in the system

---

## Support

For issues or feature requests, contact the development team.
