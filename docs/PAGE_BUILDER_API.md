# Page Builder API Documentation

## Overview

The Page Builder system allows administrators to create custom, fully customizable web pages using a drag-and-drop interface. These pages can be displayed in the Flutter app via WebView or rendered natively using the API data.

## Features

- **Drag-and-drop page builder** with real-time live preview
- **Advanced CSS controls**: Padding, margin, positioning (fixed/absolute/sticky), border radius, box shadow, opacity, background images/colors
- **Animation system**: fadeIn, slideInUp, slideInLeft, slideInRight, zoomIn, bounceIn, pulse
- **Image upload**: Upload images directly in the editor for sections and components
- **Multiple section types**: Hero banners, product grids, restaurant lists, text blocks, buttons, spacers, dividers, countdown timers, tabs, restaurant+foods combo, and more
- **Component-based architecture**: Each section contains customizable components
- **Advanced card customization**: Toggle fields (name, price, rating, image, description), customize colors per card
- **Restaurant + Foods combo card**: 3:1 aspect ratio card with restaurant info + horizontally scrollable food list
- **Tabs section**: WordPress-style tabbed containers with customizable colors and labels
- **Product picker with filters**: Filter products by restaurant, category, and search
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
| `tabs` | Tabbed container - WordPress-style tabs holding other components |
| `restaurant_foods` | Restaurant card with horizontally scrollable food list |
| `video` | Embedded video |
| `custom_html` | Raw HTML content |
| `columns` | Multi-column layout |

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

### Advanced Styling (Per Section & Component)

Every section and component supports these CSS properties via the editor's properties panel:

| Property Group | Controls |
|---------------|----------|
| **Spacing** | Padding (T/R/B/L), Margin (T/B) |
| **Background** | Color, Image upload/URL, Opacity (0-1) |
| **Position & Layout** | Position (static/relative/absolute/fixed/sticky), Top/Right/Bottom/Left offsets, Z-Index, Text Align, Display mode, Width, Height |
| **Border & Shadow** | Border Radius, Border (e.g. `1px solid #ddd`), Box Shadow |
| **Animation** | Type (fadeIn, slideInUp, slideInLeft, slideInRight, zoomIn, bounceIn, pulse), Duration |
| **Typography** | Font Size, Color, Line Height, Font Weight (400-800) |
| **Visibility** | Show/Hide toggle |

### Product Card Display Options

When editing product cards, you can toggle:
- Show Image, Show Name, Show Price, Show Restaurant, Show Rating, Show Description
- Customize: Card Background Color, Name Color, Price Color

### Restaurant Card Display Options

When editing restaurant cards, you can toggle:
- Show Image, Show Name, Show Rating, Show Address, Show Delivery Time
- Customize: Card Background Color, Name Color

### Restaurant + Foods Combo Card

This section type creates a 3:1 aspect ratio card with:
- Restaurant header (logo, name, rating)
- Horizontally scrollable food list from that restaurant
- Options: Auto-load all foods or select specific products
- Toggle: Show Logo, Name, Rating, Food Price, Food Name
- Customize: Card BG color, Name color, Max food count

### Tabs Section

WordPress-style tabbed containers:
- Add/remove tabs by editing tab labels (one per line)
- Components inside are assigned to tabs via `tab_index`
- Customize: Active Tab Color, Tab Text Color, Tab Background, Border Radius

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
   - Background image (upload directly or paste URL)
   - Heading: "50% OFF Everything!" with animation: slideInUp
   - Button: "Shop Now" → action: navigate to category
3. **Add Products Grid section** with:
   - Select featured products (filter by restaurant or category)
   - Configure 2-column layout
   - Toggle: Show Price ✓, Show Rating ✓, Show Restaurant ✓
   - Customize Price Color: `#FC6A57`
4. **Add Restaurant + Foods combo section** with:
   - Select a restaurant
   - Auto-load top 10 foods
   - Horizontal scrollable food cards
5. **Add Tabs section** with:
   - Tab 1: "Hot Deals" → add product list component
   - Tab 2: "Restaurants" → add restaurant list component
6. **Add Countdown section** with:
   - Target date for sale end
   - Animation: pulse
7. **Publish the page**

The page will be accessible at:
- WebView URL: `https://your-domain.com/page/flash-sale-xyz123`
- API: `GET /api/v1/pages/flash-sale-xyz123`

---

## Flutter Native Rendering: New Section Types

### Rendering `restaurant_foods` Section

```dart
Widget _buildRestaurantFoodsSection(Map<String, dynamic> section) {
  final settings = section['settings'] ?? {};
  final restaurantId = settings['restaurant_id'];
  
  return FutureBuilder(
    future: getRestaurantWithFoods(restaurantId),
    builder: (context, snapshot) {
      if (!snapshot.hasData) return CircularProgressIndicator();
      final data = snapshot.data!;
      final restaurant = data['restaurant'];
      final foods = data['foods'] as List;
      
      return Container(
        decoration: BoxDecoration(
          color: Color(parseColor(settings['card_bg_color'] ?? '#ffffff')),
          borderRadius: BorderRadius.circular(16),
          boxShadow: [BoxShadow(blurRadius: 12, color: Colors.black12)],
        ),
        child: Column(
          children: [
            // Restaurant header
            ListTile(
              leading: settings['show_restaurant_logo'] != false
                  ? ClipRRect(
                      borderRadius: BorderRadius.circular(12),
                      child: Image.network(restaurant['logo'], width: 52, height: 52, fit: BoxFit.cover),
                    )
                  : null,
              title: settings['show_restaurant_name'] != false
                  ? Text(restaurant['name'], style: TextStyle(fontWeight: FontWeight.w700))
                  : null,
              subtitle: settings['show_restaurant_rating'] != false
                  ? Row(children: [Icon(Icons.star, size: 14, color: Colors.amber), Text(' ${restaurant["rating"]}')])
                  : null,
            ),
            // Horizontally scrollable foods
            SizedBox(
              height: 160,
              child: ListView.builder(
                scrollDirection: Axis.horizontal,
                padding: EdgeInsets.symmetric(horizontal: 16),
                itemCount: foods.length,
                itemBuilder: (context, i) => _buildFoodMiniCard(foods[i], settings),
              ),
            ),
          ],
        ),
      );
    },
  );
}
```

### Rendering `tabs` Section

```dart
Widget _buildTabsSection(Map<String, dynamic> section) {
  final settings = section['settings'] ?? {};
  final labels = List<String>.from(settings['tab_labels'] ?? ['Tab 1', 'Tab 2']);
  final components = section['components'] as List;
  
  return DefaultTabController(
    length: labels.length,
    child: Column(
      children: [
        TabBar(
          tabs: labels.map((l) => Tab(text: l)).toList(),
          labelColor: Color(parseColor(settings['tab_active_text_color'] ?? '#ffffff')),
          unselectedLabelColor: Color(parseColor(settings['tab_text_color'] ?? '#888888')),
          indicator: BoxDecoration(
            color: Color(parseColor(settings['tab_active_color'] ?? '#FC6A57')),
            borderRadius: BorderRadius.circular(settings['tab_border_radius']?.toDouble() ?? 8),
          ),
        ),
        // Tab content: filter components by tab_index
        ...labels.asMap().entries.map((e) {
          final tabComponents = components.where(
            (c) => (c['settings']?['tab_index'] ?? 0) == e.key
          ).toList();
          return _buildComponentsList(tabComponents);
        }),
      ],
    ),
  );
}
```

### Applying Advanced Styles

Every section's `settings` and `style` JSON can be mapped to Flutter:

```dart
BoxDecoration buildDecoration(Map settings, Map style) {
  return BoxDecoration(
    color: settings['background_color'] != null
        ? Color(parseColor(settings['background_color']))
        : null,
    image: settings['background_image'] != null
        ? DecorationImage(image: NetworkImage(settings['background_image']), fit: BoxFit.cover)
        : null,
    borderRadius: BorderRadius.circular((settings['border_radius'] ?? 0).toDouble()),
    border: style['border'] != null ? parseBorder(style['border']) : null,
    boxShadow: style['box_shadow'] != null ? [parseBoxShadow(style['box_shadow'])] : null,
  );
}

EdgeInsets buildPadding(Map settings) {
  return EdgeInsets.fromLTRB(
    (settings['padding_left'] ?? 16).toDouble(),
    (settings['padding_top'] ?? 16).toDouble(),
    (settings['padding_right'] ?? 16).toDouble(),
    (settings['padding_bottom'] ?? 16).toDouble(),
  );
}

EdgeInsets buildMargin(Map settings) {
  return EdgeInsets.only(
    top: (settings['margin_top'] ?? 0).toDouble(),
    bottom: (settings['margin_bottom'] ?? 0).toDouble(),
  );
}
```

### Animations in Flutter

Map the `animation` setting to Flutter animations:

```dart
Widget applyAnimation(Widget child, Map settings) {
  final anim = settings['animation'];
  if (anim == null || anim.isEmpty) return child;
  
  final duration = parseDuration(settings['animation_duration'] ?? '0.5s');
  
  switch (anim) {
    case 'fadeIn': return FadeInAnimation(child: child, duration: duration);
    case 'slideInUp': return SlideAnimation(child: child, direction: AxisDirection.up, duration: duration);
    case 'slideInLeft': return SlideAnimation(child: child, direction: AxisDirection.left, duration: duration);
    case 'slideInRight': return SlideAnimation(child: child, direction: AxisDirection.right, duration: duration);
    case 'zoomIn': return ScaleAnimation(child: child, duration: duration);
    case 'bounceIn': return BounceAnimation(child: child, duration: duration);
    case 'pulse': return PulseAnimation(child: child, duration: duration);
    default: return child;
  }
}
```

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
