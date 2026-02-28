# Backend Gamification Requirements - Issue Documentation

## Current Status

### ✅ Working
- **Gamification Banner API**: The endpoint `/api/v1/gamification/banners?placement=home` is working correctly
- **Banner Display**: The app successfully fetches and displays gamification banners in the UI
- **Banner Data**: Backend returns banner data correctly with all required fields
- **Navigation**: App successfully navigates when user taps on a gamification banner

### ❌ Not Working - 404 Error

When a user taps on a gamification banner, the app attempts to open the game page but receives a **404 Not Found** error.

## What the App is Doing

### 1. Banner Data Received from Backend
The app receives this data from your backend API:
```json
{
  "success": true,
  "data": [
    {
      "id": 2,
      "game_id": 1,
      "game_type": "spin_wheel",
      "title": "sppin",
      "subtitle": "this is the new food",
      "image": "https://food.balemoyaw.com/storage/app/public/gamification/banners/2026-02-25-699f42711d2fd.png",
      "background_color": "#8dc63f",
      "text_color": "#ffffff",
      "button_text": "Play Now",
      "button_color": "#f5d800",
      "placement": "home"
    }
  ]
}
```

### 2. URL Construction
When the user taps the banner, the app constructs the game URL using:
- **Base URL**: `https://food.balemoyaw.com`
- **Path Pattern**: `/gamification/play/{game_id}`
- **Resulting URL**: `https://food.balemoyaw.com/gamification/play/1`

### 3. WebView Navigation
The app opens an InAppWebView and attempts to load:
```
https://food.balemoyaw.com/gamification/play/1
```

### 4. Error Received
- **HTTP Status**: 404 Not Found
- **Error**: The backend does not have a route configured for this URL pattern

## Additional JavaScript Error Observed

After the 404 error, the console shows:
```
Uncaught ReferenceError: $ is not defined
source: https://food.balemoyaw.com/public/assets/admin/js/theme.min.js (10)
```

This indicates that when/if the page loads, jQuery is not available before `theme.min.js` tries to use it.

## Required Backend Route

The backend needs to handle this route:
```
GET /gamification/play/{gameId}
```

### Route Parameters
- `{gameId}`: Integer - The ID of the game to play (from `game_id` field in banner data)

### Expected Response
The route should return an HTML page that:
1. Displays the game interface (spin wheel, scratch card, etc.)
2. Is mobile-responsive (will be displayed in a WebView)
3. Includes all necessary JavaScript and CSS dependencies
4. Loads jQuery before any scripts that depend on it

## App Implementation Details

### URL Construction Code
```dart
String getGameUrl(String baseUrl) {
  return '$baseUrl/gamification/play/$gameId';
}
```

### WebView Configuration
- Uses `InAppWebView` from `flutter_inappwebview` package
- Loads the URL directly
- Expects a full HTML page response
- Supports JavaScript execution
- Handles navigation and back button

### Example Flow
1. User sees banner with title "sppin" on home screen
2. User taps banner
3. App navigates to `GamificationWebViewScreen`
4. WebView loads `https://food.balemoyaw.com/gamification/play/1`
5. Backend should return game page HTML
6. User plays game in WebView

## Console Logs from App

```
I/flutter (28570): 🎮 GameWebView: Uncaught ReferenceError: $ is not defined
I/chromium(28570): [INFO:CONSOLE:10] "Uncaught ReferenceError: $ is not defined", 
                   source: https://food.balemoyaw.com/public/assets/admin/js/theme.min.js (10)
```

## Summary

**Issue**: Backend does not have a route configured for `/gamification/play/{gameId}`

**Impact**: Users can see gamification banners but cannot play games when they tap them

**App Behavior**: 
- Correctly fetches banner data ✅
- Correctly displays banners ✅
- Correctly constructs game URL ✅
- Correctly opens WebView ✅
- Backend returns 404 ❌

**Required Backend Action**: Implement the `/gamification/play/{gameId}` route that returns an HTML page with the game interface.
