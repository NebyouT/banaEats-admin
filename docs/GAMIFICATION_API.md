# Gamification System - Frontend API Documentation

> **Base URL:** `/api/v1`
> **Auth:** All endpoints under `/customer/gamification/*` require `Authorization: Bearer {token}`
> **Banner endpoint is public** (no auth required)

---

## Table of Contents

1. [System Overview](#system-overview)
2. [Prize Lifecycle](#prize-lifecycle)
3. [API Endpoints](#api-endpoints)
4. [Prize Types & Apply Logic](#prize-types--apply-logic)
5. [Integration Guide](#integration-guide)

---

## System Overview

The gamification system allows customers to play interactive games (spin wheel, scratch card, slot machine, mystery box, decision roulette) and win prizes. Prizes follow a **locked → unlocked → applied** lifecycle.

### Game Types
| Type | Key | Description |
|------|-----|-------------|
| Spin Wheel | `spin_wheel` | Spinning wheel with prize segments |
| Scratch Card | `scratch_card` | Scratch to reveal prize |
| Slot Machine | `slot_machine` | 3-reel slot machine |
| Mystery Box | `mystery_box` | Choose a box to reveal prize |
| Decision Roulette | `decision_roulette` | Roulette-style wheel |

### Prize Types
| Type | Key | What Happens on Apply |
|------|-----|----------------------|
| Percentage Discount | `discount_percentage` | Returns discount details — frontend applies to order |
| Fixed Discount | `discount_fixed` | Returns discount details — frontend applies to order |
| Free Delivery | `free_delivery` | Returns free delivery flag — frontend removes delivery fee |
| Loyalty Points | `loyalty_points` | Points auto-credited to user account |
| Wallet Credit | `wallet_credit` | Amount auto-credited to user wallet |
| Free Item | `free_item` | Returns food IDs — frontend adds to cart for free |
| Mystery | `mystery` | Random reveal of one of the above types |

---

## Prize Lifecycle

```
WON → [LOCKED] → user taps "Unlock" → [UNLOCKED] → user taps "Apply" → [APPLIED]
                                                                      ↘ [EXPIRED] (if past expiry)
```

1. **LOCKED** — Prize won but not yet accessible. Shown with lock icon. User sees prize name/type but cannot use it.
2. **UNLOCKED** — User tapped unlock. Prize details visible. "Apply" button enabled. Conditions shown.
3. **APPLIED** — Prize has been used. Marked as claimed. Cannot be re-used.
4. **EXPIRED** — Past `expires_at` date and not applied. Cannot be used.

---

## API Endpoints

### 1. Get Gamification Banners (Public)

```
GET /api/v1/gamification/banners?placement=home&zone_id=1
```

**No auth required.** Used to show promotional banners that link to games.

**Query Params:**
- `placement` — `home` | `restaurant` | `checkout` | `cart` (default: `home`)
- `zone_id` — Filter by zone (optional)

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "game_id": 3,
      "game_type": "spin_wheel",
      "title": "Spin & Win!",
      "subtitle": "Win up to 50% off",
      "image": "https://domain.com/storage/gamification/banners/abc.png",
      "background_color": "#8DC63F",
      "text_color": "#FFFFFF",
      "button_text": "Play Now",
      "button_color": "#F5D800",
      "placement": "home"
    }
  ]
}
```

**Frontend Action:** When user taps banner → navigate to game screen using `game_id`.

---

### 2. Get Available Games

```
GET /api/v1/customer/gamification/available-games
Authorization: Bearer {token}
```

Returns games the user is eligible to play.

**Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 3,
      "name": "Lucky Spin",
      "slug": "lucky-spin",
      "type": "spin_wheel",
      "description": "Spin the wheel and win!",
      "instructions": "Tap SPIN to play",
      "background_image": "https://...",
      "button_text": "SPIN",
      "display_settings": {
        "primary_color": "#8DC63F",
        "secondary_color": "#F5D800",
        "text_color": "#1A1A1A"
      },
      "plays_remaining_today": 3,
      "can_play_now": true,
      "prizes": [
        {
          "id": 1,
          "name": "20% OFF",
          "description": "20% discount on your next order",
          "type": "discount_percentage",
          "display_value": "20%",
          "color": "#FF6B6B",
          "image": null,
          "position": 1
        }
      ]
    }
  ]
}
```

**Frontend:** Use `display_settings` for theming. Use `prizes` array to render wheel segments / scratch cards / boxes. Check `can_play_now` before allowing play.

---

### 3. Play a Game

```
POST /api/v1/customer/gamification/play/{game_id}
Authorization: Bearer {token}
```

**Response (Winner):**
```json
{
  "success": true,
  "message": "Congratulations! You won a prize!",
  "data": {
    "is_winner": true,
    "prize": {
      "id": 1,
      "name": "20% OFF",
      "description": "20% discount",
      "type": "discount_percentage",
      "display_value": "20%",
      "color": "#FF6B6B",
      "image": null,
      "prize_code": "ABC12345",
      "prize_status": "locked",
      "expires_at": "2026-03-10 14:00:00"
    }
  }
}
```

**Response (No Win):**
```json
{
  "success": true,
  "message": "Better luck next time!",
  "data": { "is_winner": false, "prize": null }
}
```

**Frontend:** After animation completes:
- If `is_winner` → show congratulations modal with prize info. Prize starts as `locked`.
- If not → show "try again" message.

---

### 4. My Prizes (Prize Wallet)

```
GET /api/v1/customer/gamification/my-prizes?status=all
Authorization: Bearer {token}
```

**Query Params:**
- `status` — `locked` | `unlocked` | `applied` | `expired` | `all` (default: all)

**Response:**
```json
{
  "success": true,
  "total": 5,
  "data": [
    {
      "id": 42,
      "game_name": "Lucky Spin",
      "game_type": "spin_wheel",
      "prize_name": "20% OFF",
      "prize_description": "20% off your next order",
      "prize_type": "discount_percentage",
      "prize_value": 20,
      "display_value": "20%",
      "prize_color": "#FF6B6B",
      "prize_image": null,
      "prize_code": "ABC12345",
      "prize_status": "locked",
      "is_locked": true,
      "is_unlocked": false,
      "is_applied": false,
      "is_expired": false,
      "can_apply": false,
      "can_unlock": true,
      "expires_at": "2026-03-10 14:00:00",
      "unlocked_at": null,
      "applied_at": null,
      "applied_to_order_id": null,
      "won_at": "2026-02-25 14:00:00",
      "conditions": [
        { "type": "min_order", "value": 100, "label": "Min order: $100.00" },
        { "type": "max_discount", "value": 50, "label": "Max discount: $50.00" }
      ]
    }
  ]
}
```

**Frontend Display:**

| Status | Icon | Button | Card Style |
|--------|------|--------|------------|
| `locked` | 🔒 | "Unlock" | Grayscale/dimmed |
| `unlocked` | 🔓 | "Apply" | Full color, glow |
| `applied` | ✅ | "Used" (disabled) | Green badge |
| `expired` | ⏰ | "Expired" (disabled) | Red badge, strikethrough |

---

### 5. Unlock a Prize

```
POST /api/v1/customer/gamification/unlock/{play_id}
Authorization: Bearer {token}
```

`play_id` = the `id` field from my-prizes response.

**Response:**
```json
{ "success": true, "message": "Prize unlocked! You can now apply it." }
```

**Frontend:** Update card from locked → unlocked state. Show "Apply" button.

---

### 6. Validate Prize for Order (Pre-check)

```
POST /api/v1/customer/gamification/validate/{play_id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "order_amount": 150.00,
  "restaurant_id": 5,
  "zone_id": 1,
  "order_type": "delivery",
  "payment_method": "cash",
  "delivery_distance_km": 3.5,
  "cart_items_count": 4
}
```

Call this **before** applying to check if the prize can be used with the current order.

**Response (Valid):**
```json
{
  "success": true,
  "is_valid": true,
  "issues": [],
  "discount_amount": 30.00,
  "prize_type": "discount_percentage",
  "prize_value": 20
}
```

**Response (Invalid):**
```json
{
  "success": false,
  "is_valid": false,
  "issues": [
    "Minimum order amount is 200.",
    "Prize not valid for this payment method."
  ],
  "discount_amount": 0,
  "prize_type": "discount_percentage",
  "prize_value": 20
}
```

**Frontend:** Use `discount_amount` to show preview. Display `issues` as error messages if invalid.

---

### 7. Apply Prize

```
POST /api/v1/customer/gamification/apply/{play_id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "order_id": 123
}
```

`order_id` is optional — pass it when applying during checkout, or omit for instant prizes (loyalty, wallet).

**Response:**
```json
{
  "success": true,
  "message": "Discount applied! Use code 20% OFF at checkout.",
  "data": {
    "applied_type": "discount_percentage",
    "applied_value": 20,
    "details": {
      "action": "discount",
      "discount_type": "percent",
      "discount_value": 20,
      "max_discount": 50,
      "min_order": 100
    }
  }
}
```

---

### 8. Prize Details by Code

```
GET /api/v1/customer/gamification/prize/{prize_code}
Authorization: Bearer {token}
```

---

## Prize Types & Apply Logic

### How to Handle Each `action` in `details`:

#### `discount` (discount_percentage / discount_fixed)
```json
{
  "action": "discount",
  "discount_type": "percent",  // or "amount"
  "discount_value": 20,
  "max_discount": 50,
  "min_order": 100
}
```
**Frontend:** Apply discount to order total in checkout:
```dart
if (details.discount_type == 'percent') {
  discount = orderTotal * (details.discount_value / 100);
  if (details.max_discount != null) discount = min(discount, details.max_discount);
} else {
  discount = min(details.discount_value, orderTotal);
}
finalTotal = orderTotal - discount;
```

#### `free_delivery`
```json
{
  "action": "free_delivery",
  "max_distance_km": 10
}
```
**Frontend:** Set delivery fee to 0 in checkout. Optionally check distance.

#### `loyalty_points_credit`
```json
{
  "action": "loyalty_points_credit",
  "points_added": 50,
  "new_balance": 150
}
```
**Frontend:** Auto-applied on backend. Show success toast. Refresh loyalty balance.

#### `wallet_credit`
```json
{
  "action": "wallet_credit",
  "amount_credited": 25.00
}
```
**Frontend:** Auto-applied on backend. Show success toast. Refresh wallet balance.

#### `free_item`
```json
{
  "action": "free_item",
  "food_ids": [15, 22],
  "description": "Free burger with your order"
}
```
**Frontend:** Show eligible items. Let user add one to cart with price = 0.

---

## Integration Guide

### Step 1: Show Banners on Home Screen
```
GET /api/v1/gamification/banners?placement=home&zone_id={user_zone}
```
Display returned banners in a carousel/slider. Each banner has `game_id` — navigate to game screen on tap.

### Step 2: Game Screen
```
GET /api/v1/customer/gamification/available-games
```
Find game by `id`. Use `type` to render correct game UI. Use `display_settings` for colors. Use `prizes` for wheel segments / box items.

### Step 3: Play
```
POST /api/v1/customer/gamification/play/{game_id}
```
Start animation FIRST, then call API. When response arrives, land animation on the won prize (or show lose).

### Step 4: Prize Wallet Screen
```
GET /api/v1/customer/gamification/my-prizes?status=all
```
Show tabbed list: All | Locked | Unlocked | Applied | Expired. Each card shows prize info, status badge, and action button.

### Step 5: Unlock → Apply Flow
1. User taps locked prize → `POST unlock/{play_id}` → card becomes unlocked
2. User goes to checkout → selects prize → `POST validate/{play_id}` with order details
3. If valid → show discount preview → user confirms → `POST apply/{play_id}`
4. For instant prizes (loyalty/wallet) → unlock then apply immediately, no order needed

### Step 6: Checkout Integration
When user has unlocked prizes, show a "Use Prize" section in checkout:
1. List unlocked prizes with `can_apply: true`
2. On select → call `validate` to check conditions and get discount preview
3. On confirm → call `apply` with `order_id`
4. Subtract `discount_amount` from order total (for discount types)
5. Set delivery fee to 0 (for free_delivery type)

---

## Condition Types Reference

Conditions are returned in `my-prizes` and `prize-details` responses. Use these to show eligibility info to users.

| `type` | Description | Frontend Display |
|--------|-------------|------------------|
| `min_order` | Minimum order amount | "Spend at least $X" |
| `max_distance` | Max delivery distance | "Within Xkm delivery" |
| `delivery_time` | Delivery time window | "X-Y min delivery time" |
| `order_types` | Valid order types | "Delivery only" etc |
| `payment` | Valid payment methods | "Cash only" etc |
| `max_discount` | Maximum discount cap | "Up to $X off" |
| `min_items` | Min cart items | "Add X+ items" |
| `new_customer` | New customers only | "First order only" |
| `time_window` | Valid time range | "Valid 11:00-14:00" |
| `valid_days` | Valid days of week | "Mon-Fri only" |
| `restaurants` | Specific restaurants | "Select restaurants" |
| `zones` | Specific zones | "Select areas" |

---

## Error Responses

All error responses follow this format:
```json
{
  "success": false,
  "message": "Error description"
}
```

| HTTP Code | Meaning |
|-----------|---------|
| 400 | Prize already applied / expired / invalid state |
| 403 | Not eligible / daily limit / cooldown |
| 404 | Prize not found |
| 422 | Validation error |
