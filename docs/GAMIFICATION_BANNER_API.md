# Gamification Banner API Documentation

## Overview

The Gamification Banner API provides access to promotional banners that are linked to gamification games. These banners can be displayed in different placements throughout the app to promote games and encourage user engagement.

## API Endpoint

### GET /api/v1/gamification/banners

**Description:** Fetches active gamification banners based on placement and zone filtering.

**Authentication:** Not required (public endpoint)

**URL Parameters:**
- `placement` (optional, string): Filter banners by placement location
  - Default: `home`
  - Available values: `home`, `profile`, `menu`, `cart`, `checkout`, `custom`
- `zone_id` (optional, integer): Filter banners by delivery zone ID

**Example Requests:**

```bash
# Get all home placement banners
GET /api/v1/gamification/banners

# Get banners for specific placement
GET /api/v1/gamification/banners?placement=menu

# Get banners for specific zone
GET /api/v1/gamification/banners?zone_id=123

# Get banners for specific placement and zone
GET /api/v1/gamification/banners?placement=cart&zone_id=123
```

## Expected Response Format

### Success Response (200 OK)

```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "game_id": 5,
            "game_type": "spin_wheel",
            "title": "Spin to Win Amazing Prizes!",
            "subtitle": "Try your luck and win discounts",
            "image": "https://your-domain.com/storage/gamification/banners/banner1.png",
            "background_color": "#8DC63F",
            "text_color": "#FFFFFF",
            "button_text": "Play Now",
            "button_color": "#F5D800",
            "placement": "home"
        },
        {
            "id": 2,
            "game_id": 8,
            "game_type": "scratch_card",
            "title": "Scratch & Win",
            "subtitle": "Hidden prizes waiting for you",
            "image": "https://your-domain.com/storage/gamification/banners/banner2.png",
            "background_color": "#FF6B6B",
            "text_color": "#FFFFFF",
            "button_text": "Scratch Now",
            "button_color": "#FFFFFF",
            "placement": "home"
        }
    ]
}
```

### Empty Response (No Banners)

```json
{
    "success": true,
    "data": []
}
```

## Response Fields Explained

| Field | Type | Description |
|-------|------|-------------|
| `id` | integer | Unique banner identifier |
| `game_id` | integer | ID of the linked gamification game |
| `game_type` | string | Type of game (`spin_wheel`, `scratch_card`, `slot_machine`, `mystery_box`, `decision_roulette`) |
| `title` | string | Main banner title text |
| `subtitle` | string | Secondary banner text (optional) |
| `image` | string | Full URL to banner image |
| `background_color` | string | Hex color code for banner background |
| `text_color` | string | Hex color code for text color |
| `button_text` | string | Text displayed on call-to-action button |
| `button_color` | string | Hex color code for button background |
| `placement` | string | Where the banner is designed to be displayed |

## Banner Filtering Logic

The API applies the following filtering rules:

1. **Status Filter**: Only banners with `status = 1` (active) are returned
2. **Schedule Filter**: Banners must be within their start/end date range
3. **Placement Filter**: Banners are filtered by the requested placement
4. **Zone Filter**: If `zone_id` is provided, only banners that include that zone in their `zone_ids` array are returned
5. **Game Status**: Only banners linked to active games are returned

## Placement Options

| Placement | Description | Typical Use Case |
|-----------|-------------|------------------|
| `home` | Main app homepage | General game promotion |
| `profile` | User profile page | Personalized game suggestions |
| `menu` | Restaurant menu page | Food-related games |
| `cart` | Shopping cart page | Cart value-based games |
| `checkout` | Checkout process | Last-minute game offers |
| `custom` | Custom app pages | Specific promotional campaigns |

## Integration Guide

### Frontend Implementation Example (React)

```javascript
// Fetch banners for home placement
const fetchGamificationBanners = async (placement = 'home', zoneId = null) => {
    try {
        let url = `/api/v1/gamification/banners?placement=${placement}`;
        if (zoneId) {
            url += `&zone_id=${zoneId}`;
        }
        
        const response = await fetch(url);
        const data = await response.json();
        
        if (data.success) {
            return data.data;
        }
        return [];
    } catch (error) {
        console.error('Error fetching gamification banners:', error);
        return [];
    }
};

// Usage in component
const BannerCarousel = ({ placement, zoneId }) => {
    const [banners, setBanners] = useState([]);
    
    useEffect(() => {
        fetchGamificationBanners(placement, zoneId).then(setBanners);
    }, [placement, zoneId]);
    
    return (
        <div className="banner-carousel">
            {banners.map(banner => (
                <div 
                    key={banner.id}
                    className="banner-item"
                    style={{
                        backgroundColor: banner.background_color,
                        color: banner.text_color
                    }}
                >
                    <img src={banner.image} alt={banner.title} />
                    <h3>{banner.title}</h3>
                    <p>{banner.subtitle}</p>
                    <button 
                        style={{ backgroundColor: banner.button_color }}
                        onClick={() => navigateToGame(banner.game_id, banner.game_type)}
                    >
                        {banner.button_text}
                    </button>
                </div>
            ))}
        </div>
    );
};
```

### Error Handling

The API returns a 200 OK response even when no banners are found. Handle empty arrays gracefully:

```javascript
const banners = await fetchGamificationBanners('home');
if (banners.length === 0) {
    // Show default content or hide banner section
    return null;
}
```

## Caching Strategy

- **Cache Time**: 5-10 minutes recommended
- **Cache Key**: Include placement and zone_id parameters
- **Cache Invalidation**: Clear cache when banners are updated in admin

```javascript
// Example cache implementation
const cacheKey = `gamification_banners_${placement}_${zoneId}`;
const cachedData = localStorage.getItem(cacheKey);

if (cachedData) {
    const { data, timestamp } = JSON.parse(cachedData);
    if (Date.now() - timestamp < 5 * 60 * 1000) { // 5 minutes
        return data;
    }
}

// Fetch fresh data and cache
const freshData = await fetchGamificationBanners(placement, zoneId);
localStorage.setItem(cacheKey, JSON.stringify({
    data: freshData,
    timestamp: Date.now()
}));
```

## Image Specifications

- **Recommended Size**: 1200x400 pixels for wide banners, 600x600 for square
- **Supported Formats**: JPG, PNG, WebP
- **Max File Size**: 2MB
- **Aspect Ratio**: Flexible, but maintain consistency across placements

## Testing

### Test with Different Parameters

```bash
# Test basic endpoint
curl -X GET "https://your-domain.com/api/v1/gamification/banners"

# Test with placement
curl -X GET "https://your-domain.com/api/v1/gamification/banners?placement=menu"

# Test with zone
curl -X GET "https://your-domain.com/api/v1/gamification/banners?zone_id=123"

# Test with both parameters
curl -X GET "https://your-domain.com/api/v1/gamification/banners?placement=cart&zone_id=123"
```

### Expected Test Results

- Valid requests should return `success: true` with a `data` array
- Empty results should return `success: true` with empty `data` array
- Invalid zone IDs should return empty array (no error)
- Invalid placement values should return empty array (no error)

## Rate Limiting

- No authentication required, but implement client-side rate limiting
- Recommended: Maximum 1 request per minute per placement
- Implement exponential backoff for failed requests

## Game Play Route (WebView)

### GET /gamification/play/{gameId}

**Description:** Public route for playing gamification games in a mobile WebView.

**Authentication:** Not required (public endpoint)

**URL Parameters:**
- `{gameId}` (required, integer): The ID of the game to play

**Example URL:**
```
https://your-domain.com/gamification/play/1
```

**Response:** Returns a mobile-optimized HTML page with:
- Full game interface (spin wheel, scratch card, slot machine, mystery box, or decision roulette)
- Responsive design for mobile devices
- Touch-friendly controls
- Prize reveal modal
- All necessary JavaScript and CSS included

**Mobile App Integration:**

```dart
// Construct game URL from banner data
String getGameUrl(String baseUrl, int gameId) {
  return '$baseUrl/gamification/play/$gameId';
}

// Open in WebView
InAppWebView(
  initialUrlRequest: URLRequest(
    url: Uri.parse(getGameUrl('https://your-domain.com', gameId))
  ),
  initialOptions: InAppWebViewGroupOptions(
    crossPlatform: InAppWebViewOptions(
      javaScriptEnabled: true,
      supportZoom: false,
    ),
  ),
)
```

**Features:**
- ✅ Mobile-responsive design
- ✅ Touch-optimized controls
- ✅ No authentication required
- ✅ Standalone page (no admin layout)
- ✅ jQuery loaded before other scripts
- ✅ Prize code generation
- ✅ Animated game mechanics

**Game Types Supported:**
1. **Spin Wheel** - Rotating wheel with prize segments
2. **Scratch Card** - Touch-based scratch-to-reveal
3. **Slot Machine** - Animated slot reels
4. **Mystery Box** - Grid of mystery boxes to tap
5. **Decision Roulette** - Choice-based prize selection

## Common Issues & Solutions

### Issue: Banners not showing
**Solution:** Check if:
- Banner status is active (1)
- Current date is within start/end date range
- Linked game is active
- Zone filtering matches user's zone

### Issue: Images not loading
**Solution:** Verify:
- Image URL is accessible
- Storage directory permissions
- CDN configuration (if applicable)

### Issue: Wrong banners for placement
**Solution:** Ensure:
- Correct placement parameter in API call
- Banner placement matches in admin panel
- No caching conflicts

### Issue: 404 error when opening game
**Solution:** Verify:
- Game ID exists in database
- Game status is active
- Route `/gamification/play/{gameId}` is properly configured
- WebView has internet connectivity

### Issue: "$ is not defined" JavaScript error
**Solution:** This has been fixed by loading jQuery before other scripts in the game play page. If you still see this error, clear the WebView cache.
