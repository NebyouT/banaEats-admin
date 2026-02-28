# Gamification Prize Wallet System Documentation

## Overview

The Gamification Prize Wallet System is a comprehensive solution for managing user prizes won through gamification games. It implements a full lifecycle from winning prizes to applying them to orders, with status tracking, expiration management, and flexible application logic.

## 🎯 System Architecture

### Core Components

1. **Game Play Records** - Every game interaction creates a record
2. **Prize Wallet** - User's collection of won prizes with status tracking
3. **Application Logic** - Smart prize application based on type and conditions
4. **Expiration Management** - Automatic expiration handling

## 🗄️ Database Structure

### Primary Table: `gamification_game_plays`

This table serves as the central hub for all gamification activities and prize management.

#### Key Fields

| Field | Type | Description |
|-------|------|-------------|
| `id` | bigint | Unique play record identifier |
| `game_id` | foreign | Reference to the game played |
| `user_id` | foreign | User who played the game |
| `prize_id` | foreign | Prize that was won (nullable) |
| `is_winner` | boolean | Whether the user won a prize |
| `prize_code` | string | Unique code for prize claiming |
| `prize_status` | enum | Current prize status (locked/unlocked/applied/expired) |
| `is_claimed` | boolean | Legacy claim status (deprecated) |
| `claimed_at` | timestamp | When prize was claimed |
| `unlocked_at` | timestamp | When prize was unlocked |
| `applied_at` | timestamp | When prize was applied to order |
| `applied_to_order_id` | foreign | Order where prize was used |
| `applied_details` | json | Details of prize application |
| `expires_at` | timestamp | Prize expiration date |
| `order_id` | foreign | Related order (legacy field) |
| `game_data` | json | Game-specific play data |
| `ip_address` | string | User's IP address |
| `user_agent` | string | User's browser/device info |

### Supporting Tables

#### `gamification_prizes`
- Prize definitions and configurations
- Application rules and conditions
- Display information and values

#### `gamification_games`
- Game configurations and settings
- Display themes and backgrounds
- Play limits and schedules

## 🔄 Prize Lifecycle

### Status Flow Diagram

```
[GAME PLAY] → [WIN] → [LOCKED] → [UNLOCK] → [APPLY] → [APPLIED]
                     │           │          │
                     ▼           ▼          ▼
                 [EXPIRED]   [EXPIRED]  [USED]
```

### Status Explanations

#### 1. **LOCKED** 📦
- Prize won but not yet accessible
- User must unlock to view/use
- Common for "surprise" prizes

#### 2. **UNLOCKED** 🔓
- Prize accessible and ready to use
- User can apply to orders
- Visible in prize wallet

#### 3. **APPLIED** ✅
- Prize successfully used
- Linked to specific order
- No longer available

#### 4. **EXPIRED** ⏰
- Prize not used before expiration
- Cannot be applied
- Still visible for history

## 🎮 Game Integration

### Prize Generation Process

```php
// When user plays and wins
$gamePlay = GamificationGamePlay::create([
    'game_id' => $game->id,
    'user_id' => $user->id,
    'prize_id' => $prize->id,
    'is_winner' => true,
    'prize_code' => strtoupper(Str::random(8)), // e.g., "PRIZE-ABC123"
    'prize_status' => 'locked', // Initial status
    'expires_at' => now()->addDays(7), // 7-day expiration
]);
```

### Prize Selection Logic

```php
private function selectRandomPrize($prizes) {
    $totalProb = $prizes->sum('probability');
    $rand = mt_rand(1, $totalProb * 1000) / 1000;
    
    $current = 0;
    foreach ($prizes as $prize) {
        $current += $prize->probability;
        if ($rand <= $current) {
            return $prize;
        }
    }
    
    return $prizes->first();
}
```

## 📱 API Endpoints

### Prize Wallet Management

#### Get User Prizes
```
GET /api/v1/gamification/my_prizes?status={status}
```

**Parameters:**
- `status` (optional): `locked`, `unlocked`, `applied`, `expired`, `all`

**Response:**
```json
{
  "data": [
    {
      "id": 123,
      "game_name": "Spin to Win",
      "game_type": "spin_wheel",
      "prize_name": "10% Discount",
      "prize_description": "Get 10% off your next order",
      "prize_type": "discount_percentage",
      "prize_value": "10.00",
      "display_value": "10% OFF",
      "prize_color": "#8DC63F",
      "prize_image": "https://example.com/storage/prize.png",
      "prize_code": "PRIZE-ABC123",
      "prize_status": "unlocked",
      "is_locked": false,
      "is_unlocked": true,
      "can_unlock": false,
      "can_apply": true,
      "expires_at": "2026-02-28 23:59:59",
      "won_at": "2026-02-26 10:30:00",
      "conditions": {
        "min_order_amount": "50.00",
        "valid_order_types": ["delivery", "takeaway"]
      }
    }
  ],
  "pagination": {
    "current_page": 1,
    "total_pages": 3,
    "total_items": 25
  }
}
```

#### Unlock Prize
```
POST /api/v1/gamification/unlock/{play_id}
```

**Response:**
```json
{
  "success": true,
  "message": "Prize unlocked successfully!",
  "data": {
    "prize_status": "unlocked",
    "unlocked_at": "2026-02-26 10:35:00"
  }
}
```

#### Apply Prize
```
POST /api/v1/gamification/apply/{play_id}
```

**Request Body:**
```json
{
  "order_id": 456,
  "order_amount": 75.50,
  "order_type": "delivery"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Prize applied successfully!",
  "data": {
    "prize_status": "applied",
    "applied_at": "2026-02-26 10:40:00",
    "applied_to_order_id": 456,
    "applied_details": {
      "discount_amount": "7.55",
      "final_amount": "67.95",
      "application_type": "discount_percentage"
    }
  }
}
```

#### Get Prize Details
```
GET /api/v1/gamification/prize/{prize_code}
```

#### Validate Prize for Order
```
POST /api/v1/gamification/validate/{play_id}
```

**Request Body:**
```json
{
  "order_amount": 45.00,
  "order_type": "delivery",
  "delivery_distance_km": 5.2,
  "payment_method": "card",
  "cart_items": [
    {"id": 1, "category_id": 2, "price": 15.00},
    {"id": 3, "category_id": 2, "price": 30.00}
  ]
}
```

## 🎁 Prize Types & Application Logic

### 1. **Discount Percentage** 💰
```php
"prize_type": "discount_percentage"
"prize_value": "15.00"
"min_order_amount": "50.00"
"max_discount_amount": "25.00"
```

**Application:** `discount = order_amount * (prize_value / 100)`

### 2. **Discount Fixed** 💵
```php
"prize_type": "discount_fixed"
"prize_value": "10.00"
"min_order_amount": "30.00"
```

**Application:** `discount = min(prize_value, order_amount)`

### 3. **Free Delivery** 🚚
```php
"prize_type": "free_delivery"
"max_delivery_distance_km": 10
"valid_order_types": ["delivery"]
```

**Application:** `delivery_fee = 0`

### 4. **Loyalty Points** ⭐
```php
"prize_type": "loyalty_points"
"prize_value": "100"
```

**Application:** `user.loyalty_point += prize_value`

### 5. **Wallet Credit** 💳
```php
"prize_type": "wallet_credit"
"prize_value": "20.00"
```

**Application:** `user.wallet_balance += prize_value`

### 6. **Free Item** 🍔
```php
"prize_type": "free_item"
"valid_food_ids": [1, 5, 12]
"valid_category_ids": [2, 3]
```

**Application:** Add free item to cart

### 7. **Mystery Prize** 🎁
```php
"prize_type": "mystery"
"prize_value": "random"
```

**Application:** Random prize from pool

## 🔧 Advanced Prize Controls

### Time-based Restrictions
```php
"schedule_type": "specific_time"
"valid_from_time": "09:00:00"
"valid_until_time": "21:00:00"
"valid_days": [1, 2, 3, 4, 5] // Mon-Fri
```

### Order-based Conditions
```php
"min_order_amount": "50.00"
"min_cart_items": 2
"valid_order_types": ["delivery", "takeaway"]
"valid_payment_methods": ["card", "paypal"]
```

### Geographic Restrictions
```php
"max_delivery_distance_km": 15
"valid_zone_ids": [1, 2, 5]
```

### Customer Restrictions
```php
"new_customer_only": true
"min_order_count": 5
```

## ⏰ Expiration Management

### Automatic Expiration

```php
// Check for expired prizes
GamificationGamePlay::where('is_winner', true)
    ->where('prize_status', '!=', 'applied')
    ->where('expires_at', '<', now())
    ->update(['prize_status' => 'expired']);
```

### Expiration Rules

- **Default Expiration:** 7 days from win
- **Custom Expiration:** Set per prize type
- **No Expiration:** `expires_at = null`
- **Grace Period:** 24 hours after expiration for special cases

## 📊 Analytics & Reporting

### Prize Performance Metrics

```php
// Prize win rates
$winRate = GamificationGamePlay::where('game_id', $gameId)
    ->where('is_winner', true)
    ->count() / GamificationGamePlay::where('game_id', $gameId)->count();

// Prize redemption rates
$redemptionRate = GamificationGamePlay::where('prize_status', 'applied')
    ->count() / GamificationGamePlay::where('is_winner', true)->count();

// Prize type popularity
$typeStats = GamificationGamePlay::join('gamification_prizes', 'gamification_game_plays.prize_id', '=', 'gamification_prizes.id')
    ->where('is_winner', true)
    ->groupBy('gamification_prizes.type')
    ->selectRaw('gamification_prizes.type, count(*) as count')
    ->get();
```

## 🔍 Frontend Integration

### React Component Example

```javascript
const PrizeWallet = () => {
  const [prizes, setPrizes] = useState([]);
  const [filter, setFilter] = useState('all');
  
  useEffect(() => {
    fetchPrizes(filter);
  }, [filter]);
  
  const fetchPrizes = async (status) => {
    const response = await api.get(`/gamification/my_prizes?status=${status}`);
    setPrizes(response.data.data);
  };
  
  const unlockPrize = async (playId) => {
    await api.post(`/gamification/unlock/${playId}`);
    fetchPrizes(filter);
  };
  
  const applyPrize = async (playId, orderId) => {
    await api.post(`/gamification/apply/${playId}`, { order_id: orderId });
    fetchPrizes(filter);
  };
  
  return (
    <div className="prize-wallet">
      <div className="filter-tabs">
        {['all', 'locked', 'unlocked', 'applied', 'expired'].map(status => (
          <button 
            key={status}
            onClick={() => setFilter(status)}
            className={filter === status ? 'active' : ''}
          >
            {status.charAt(0).toUpperCase() + status.slice(1)}
          </button>
        ))}
      </div>
      
      <div className="prizes-grid">
        {prizes.map(prize => (
          <PrizeCard 
            key={prize.id} 
            prize={prize}
            onUnlock={() => unlockPrize(prize.id)}
            onApply={(orderId) => applyPrize(prize.id, orderId)}
          />
        ))}
      </div>
    </div>
  );
};
```

## 🚀 Performance Optimizations

### Database Indexes
```sql
-- Optimized queries for prize wallet
CREATE INDEX idx_user_prizes ON gamification_game_plays(user_id, is_winner, prize_status);
CREATE INDEX idx_prize_code ON gamification_game_plays(prize_code);
CREATE INDEX idx_expiration ON gamification_game_plays(expires_at, prize_status);
CREATE INDEX idx_game_user_date ON gamification_game_plays(game_id, user_id, created_at);
```

### Caching Strategy
```php
// Cache user prize counts
$cacheKey = "user_prize_counts_{$userId}";
$prizeCounts = Cache::remember($cacheKey, 300, function () use ($userId) {
    return [
        'locked' => GamificationGamePlay::where('user_id', $userId)
            ->where('prize_status', 'locked')->count(),
        'unlocked' => GamificationGamePlay::where('user_id', $userId)
            ->where('prize_status', 'unlocked')->count(),
        'applied' => GamificationGamePlay::where('user_id', $userId)
            ->where('prize_status', 'applied')->count(),
    ];
});
```

## 🔒 Security Considerations

### Prize Code Security
- 8-character alphanumeric codes
- Case-insensitive comparison
- Rate limiting on prize attempts
- Audit logging for prize applications

### Fraud Prevention
```php
// Check for suspicious patterns
$suspiciousPatterns = [
    'multiple_wins_short_time' => 'User wins multiple prizes in < 5 minutes',
    'same_ip_multiple_users' => 'Same IP used by different users',
    'unusual_redemption_rate' => 'User redeems > 90% of prizes',
];
```

## 🛠️ Troubleshooting

### Common Issues

#### 1. Prize Not Showing in Wallet
**Check:** `prize_status`, `user_id`, `is_winner` fields
**Solution:** Verify game play record creation

#### 2. Cannot Apply Prize
**Check:** Prize conditions, expiration date, order compatibility
**Solution:** Validate prize conditions before application

#### 3. Prize Not Unlocking
**Check:** Unlock permissions, user authentication
**Solution:** Verify user owns the prize and has unlock rights

#### 4. Expiration Issues
**Check:** `expires_at` field, timezone settings
**Solution:** Ensure proper timezone configuration

## 📋 Best Practices

### For Developers
1. **Always validate** prize conditions before application
2. **Handle edge cases** for expired prizes gracefully
3. **Implement proper error handling** for API calls
4. **Use transactions** for prize application operations
5. **Log all prize activities** for audit trails

### For Administrators
1. **Set reasonable expiration periods** (7-30 days)
2. **Monitor redemption rates** and adjust difficulty
3. **Regular cleanup** of old expired prizes
4. **Test prize conditions** before going live
5. **Backup prize data** regularly

## 🔄 Future Enhancements

### Planned Features
- **Prize Gifting** - Allow users to gift prizes to friends
- **Prize Trading** - Enable prize exchange between users
- **Tiered Prizes** - Implement prize rarity levels
- **Prize Bundling** - Combine multiple prizes
- **Social Sharing** - Share prize wins on social media
- **Prize Marketplace** - Buy/sell special prizes

### Integration Opportunities
- **Loyalty Programs** - Connect with existing loyalty systems
- **Referral Systems** - Award prizes for referrals
- **Marketing Campaigns** - Special prizes for promotions
- **Partnership Prizes** - Prizes from partner businesses

---

This documentation provides a comprehensive understanding of the Gamification Prize Wallet System. For specific implementation details or API examples, refer to the individual API documentation files.
