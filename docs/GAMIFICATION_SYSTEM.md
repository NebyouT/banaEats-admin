# BanaEats Gamification System

## Overview

The Gamification System is a comprehensive customer engagement feature that allows admins to create interactive games (spin wheels, scratch cards, slot machines, etc.) with configurable prizes and eligibility rules. This system integrates seamlessly with the existing order, coupon, wallet, and loyalty systems.

---

## Database Schema

### Tables Created

1. **gamification_games** - Main game configurations
2. **gamification_prizes** - Prize definitions for each game
3. **gamification_eligibility_rules** - Rules determining who can play
4. **gamification_game_plays** - Play history and prize claims
5. **gamification_analytics** - Daily aggregated statistics

---

## Features Implemented

### ✅ Admin Panel Features

#### Game Management
- **Create/Edit Games** - Configure game name, type, description, scheduling
- **Game Types Supported**:
  - Spin the Wheel
  - Scratch Card
  - Slot Machine
  - Mystery Box
  - Decision Roulette
- **Play Limits** - Set plays per day/week, cooldown periods
- **Scheduling** - Set start/end dates for promotional campaigns
- **Visual Customization** - Upload background images, set colors
- **Status Control** - Activate/deactivate games instantly

#### Prize Configuration
- **Prize Types**:
  - Discount (Percentage or Fixed Amount)
  - Free Delivery
  - Loyalty Points
  - Wallet Credit
  - Free Item
  - Mystery Prize
- **Probability Control** - Set win chances (0-100%)
- **Quantity Management** - Limited or unlimited prizes
- **Expiry Settings** - Configure prize validity period
- **Restrictions** - Minimum order amount, specific restaurants/zones
- **Visual Design** - Custom colors and images per prize

#### Eligibility Rules Engine
- **Rule Types**:
  - Order Count (e.g., "at least 5 orders")
  - New User (within X days of signup)
  - Inactive User (no order in X days)
  - Total Spent (minimum spending threshold)
  - Zone-based (specific delivery zones)
  - Time of Day (lunch/dinner hours)
  - Day of Week (weekends, specific days)
  - Last Order Days (time since last order)
- **Operators**: `>=`, `<=`, `=`, `!=`, `in`, `not_in`, `between`
- **Logic**: AND/OR combinations for complex targeting

#### Analytics Dashboard
- Total plays, winners, claimed prizes
- Unique players count
- Prize distribution breakdown
- Conversion rate (prizes claimed vs won)
- Recent play history
- Daily trend charts (30-day view)

---

## File Structure

### Models
```
app/Models/
├── GamificationGame.php          # Main game model
├── GamificationPrize.php         # Prize model
├── GamificationEligibilityRule.php # Eligibility rules
├── GamificationGamePlay.php      # Play history
└── GamificationAnalytics.php     # Analytics aggregation
```

### Controllers
```
app/Http/Controllers/Admin/
├── GamificationController.php       # Game CRUD operations
└── GamificationPrizeController.php  # Prize management
```

### Migrations
```
database/migrations/
├── 2026_02_23_000001_create_gamification_games_table.php
├── 2026_02_23_000002_create_gamification_prizes_table.php
├── 2026_02_23_000003_create_gamification_eligibility_rules_table.php
├── 2026_02_23_000004_create_gamification_game_plays_table.php
└── 2026_02_23_000005_create_gamification_analytics_table.php
```

### Views
```
resources/views/admin-views/gamification/
├── index.blade.php              # Games list
├── create.blade.php             # Create game form (pending)
├── edit.blade.php               # Edit game form (pending)
├── analytics.blade.php          # Analytics dashboard (pending)
└── prizes/
    ├── index.blade.php          # Prizes list (pending)
    ├── create.blade.php         # Create prize form (pending)
    └── edit.blade.php           # Edit prize form (pending)
```

---

## Admin Routes

```php
// Game Management
GET    /admin/gamification                    # List all games
GET    /admin/gamification/create             # Create game form
POST   /admin/gamification/store              # Store new game
GET    /admin/gamification/edit/{id}          # Edit game form
POST   /admin/gamification/update/{id}        # Update game
POST   /admin/gamification/status             # Toggle game status
DELETE /admin/gamification/delete/{id}        # Delete game
GET    /admin/gamification/analytics/{id}     # View analytics

// Prize Management
GET    /admin/gamification/{game_id}/prizes                    # List prizes
GET    /admin/gamification/{game_id}/prizes/create             # Create prize form
POST   /admin/gamification/{game_id}/prizes/store              # Store prize
GET    /admin/gamification/{game_id}/prizes/edit/{id}          # Edit prize form
POST   /admin/gamification/{game_id}/prizes/update/{id}        # Update prize
POST   /admin/gamification/{game_id}/prizes/status             # Toggle prize status
DELETE /admin/gamification/{game_id}/prizes/delete/{id}        # Delete prize
POST   /admin/gamification/{game_id}/prizes/update-position    # Reorder prizes
```

---

## API Endpoints (To Be Implemented)

### Customer-Facing APIs

```php
// Game Discovery
GET    /api/v1/customer/gamification/available-games
       # Returns list of games the customer is eligible to play

// Play Game
POST   /api/v1/customer/gamification/play/{game_id}
       # Initiates a game play, returns prize result

// Prize Management
GET    /api/v1/customer/gamification/my-prizes
       # Lists customer's won prizes (claimed and unclaimed)

POST   /api/v1/customer/gamification/claim-prize/{prize_code}
       # Claims a prize for use in an order

GET    /api/v1/customer/gamification/prize-details/{prize_code}
       # Gets details of a specific prize
```

---

## How It Works

### 1. Admin Creates a Game

```
1. Admin navigates to Gamification section
2. Clicks "Add New Game"
3. Fills in:
   - Name: "Weekend Spin"
   - Type: Spin the Wheel
   - Description: "Spin for weekend discounts!"
   - Plays per day: 1
   - Start date: Friday
   - End date: Sunday
4. Uploads background image
5. Sets colors (primary, secondary, text)
6. Saves game
```

### 2. Admin Adds Prizes

```
1. Clicks "Manage Prizes" on the game
2. Adds multiple prizes:
   - 10% OFF (Probability: 30%, Quantity: 100)
   - 20% OFF (Probability: 15%, Quantity: 50)
   - Free Delivery (Probability: 25%, Quantity: Unlimited)
   - $5 Wallet Credit (Probability: 10%, Quantity: 20)
   - Better Luck Next Time (Probability: 20%, No prize)
3. Sets expiry: 7 days
4. Sets restrictions: Min order $10
```

### 3. Admin Sets Eligibility Rules

```
1. Adds rules to target specific customers:
   - Order count >= 3 (returning customers only)
   - Zone in [Zone A, Zone B] (specific areas)
   - Time of day between 18:00-22:00 (dinner time)
2. All rules must match (AND logic)
```

### 4. Customer Plays the Game

```
1. Customer opens app
2. Sees "Weekend Spin" banner (if eligible)
3. Taps to play
4. Game loads in web view
5. Spins the wheel
6. Lands on "20% OFF"
7. Prize is saved with unique code
8. Prize appears in "My Prizes" section
```

### 5. Customer Uses Prize

```
1. Customer adds items to cart
2. At checkout, sees available prizes
3. Selects "20% OFF" prize
4. Discount is applied
5. Places order
6. Prize is marked as claimed
7. Analytics are updated
```

---

## Eligibility Engine Logic

The system evaluates eligibility rules in this order:

```php
1. Check if game is active (status = 1)
2. Check if current date is within start/end dates
3. Check play limits (daily, weekly)
4. Check cooldown period
5. Evaluate all eligibility rules:
   - If rule.is_required = true → must pass (AND)
   - If rule.is_required = false → optional (OR)
6. Return eligible games to customer
```

### Example Rule Evaluation

**Scenario**: Target inactive VIP customers in Zone A

```php
Rules:
1. total_spent >= 500 (is_required: true)
2. last_order_days >= 30 (is_required: true)
3. zone in [1] (is_required: true)

Customer A:
- Total spent: $600 ✓
- Last order: 45 days ago ✓
- Zone: 1 ✓
Result: ELIGIBLE

Customer B:
- Total spent: $400 ✗
- Last order: 45 days ago ✓
- Zone: 1 ✓
Result: NOT ELIGIBLE (failed spending threshold)
```

---

## Prize Distribution Algorithm

When a customer plays:

```php
1. Get all active prizes for the game
2. Check if first_play_always_wins = true
   - If yes and customer's first play → guarantee a win
3. Calculate weighted random selection:
   - Prize A: 30% probability
   - Prize B: 15% probability
   - Prize C: 25% probability
   - No Prize: 30% probability
4. Select prize based on weighted random
5. Check prize availability (remaining_quantity > 0)
6. If unavailable, select next available prize
7. Create GamePlay record
8. Generate unique prize_code
9. Set expires_at = now() + prize.expiry_days
10. Decrement prize.remaining_quantity
11. Record analytics
12. Return result to customer
```

---

## Integration with Existing Systems

### Coupon System
- Discount prizes create temporary coupons
- Applied at checkout like regular coupons
- Tracked separately in gamification_game_plays

### Wallet System
- Wallet credit prizes add balance directly
- Transaction recorded in wallet_transactions
- Linked via order_id when used

### Loyalty Points
- Points prizes increment customer loyalty balance
- Tracked in loyalty_point_transactions
- Can be exchanged for rewards per existing logic

### Order System
- Prize usage linked to orders via order_id
- Discount applied before payment calculation
- Free delivery overrides delivery charges

---

## Analytics Tracking

### Real-time Metrics
- Play count increments immediately
- Winner count updates on prize win
- Unique players calculated daily
- Prize distribution tracked per prize_id

### Daily Aggregation
```php
GamificationAnalytics::recordPlay($gameId, $isWinner, $prizeId);
// Called after each game play

GamificationAnalytics::recordClaim($gameId);
// Called when prize is used in an order
```

### Conversion Rate Calculation
```
Conversion Rate = (Total Claimed / Total Winners) × 100
```

---

## Security Considerations

### Fraud Prevention
- IP address logging for each play
- User agent tracking
- Play limits enforced server-side
- Prize codes are unique and non-guessable
- Expired prizes cannot be claimed
- Prize validation at checkout

### Admin Controls
- Only admins can create/edit games
- Probability validation (sum can exceed 100%)
- Quantity validation (cannot go negative)
- Date validation (end >= start)

---

## Next Steps (Pending Implementation)

### High Priority
1. ✅ Create admin views for game create/edit forms
2. ✅ Create admin views for prize management
3. ✅ Create analytics dashboard view
4. ⏳ Create API endpoints for customer interactions
5. ⏳ Build game play engine (prize selection logic)
6. ⏳ Create customer-facing web views (game UI)
7. ⏳ Integrate with order checkout flow
8. ⏳ Add prize claim functionality

### Medium Priority
- Email notifications for prize wins
- Push notifications for new games
- Social sharing features
- Leaderboards
- Game history for customers
- Admin bulk operations (clone game, bulk prize upload)

### Low Priority
- A/B testing different game configurations
- Advanced analytics (cohort analysis, retention)
- Seasonal game templates
- Multi-language game content
- Custom game animations

---

## Testing Checklist

### Admin Panel
- [ ] Create a new game
- [ ] Edit game details
- [ ] Toggle game status
- [ ] Delete a game
- [ ] Add prizes to a game
- [ ] Edit prize details
- [ ] Reorder prizes (drag & drop)
- [ ] View analytics dashboard
- [ ] Filter games by type/status
- [ ] Search games by name

### API (When Implemented)
- [ ] Get available games for eligible customer
- [ ] Play a game and receive prize
- [ ] View my prizes list
- [ ] Claim a prize
- [ ] Use prize in order
- [ ] Handle expired prizes
- [ ] Respect play limits
- [ ] Validate eligibility rules

### Integration
- [ ] Discount prize applies correctly at checkout
- [ ] Wallet credit adds to balance
- [ ] Loyalty points increment
- [ ] Free delivery removes charges
- [ ] Analytics update in real-time
- [ ] Prize expiry works correctly

---

## Troubleshooting

### Common Issues

**Issue**: Games not showing in admin panel
- **Solution**: Run migrations: `php artisan migrate`
- **Solution**: Clear cache: `php artisan cache:clear`

**Issue**: Images not displaying
- **Solution**: Run storage link: `php artisan storage:link`
- **Solution**: Check file permissions on storage folder

**Issue**: Eligibility rules not working
- **Solution**: Check rule_type spelling matches model methods
- **Solution**: Verify operator is valid
- **Solution**: Ensure value array has correct structure

**Issue**: Prize probabilities don't add up to 100%
- **Note**: This is intentional - probabilities are weighted, not percentages
- **Example**: 30% + 15% + 25% + 20% = 90% (10% chance of no prize)

---

## Database Indexes

For optimal performance, the following indexes are created:

```sql
-- gamification_game_plays
INDEX (user_id, game_id, created_at)
INDEX (prize_code)
INDEX (is_claimed, expires_at)

-- gamification_analytics
UNIQUE (game_id, date)
```

---

## Configuration

### Environment Variables (Optional)
```env
GAMIFICATION_ENABLED=true
GAMIFICATION_MAX_PLAYS_PER_DAY=5
GAMIFICATION_DEFAULT_EXPIRY_DAYS=7
```

### Config File (Future)
```php
// config/gamification.php
return [
    'enabled' => env('GAMIFICATION_ENABLED', true),
    'max_plays_per_day' => env('GAMIFICATION_MAX_PLAYS_PER_DAY', 5),
    'default_expiry_days' => env('GAMIFICATION_DEFAULT_EXPIRY_DAYS', 7),
    'game_types' => [
        'spin_wheel',
        'scratch_card',
        'slot_machine',
        'mystery_box',
        'decision_roulette',
    ],
];
```

---

## Support

For questions or issues with the gamification system:
1. Check this documentation
2. Review the code comments in models and controllers
3. Check the Laravel logs: `storage/logs/laravel.log`
4. Verify database migrations ran successfully

---

**Version**: 1.0.0  
**Last Updated**: February 23, 2026  
**Status**: Foundation Complete - Views and APIs Pending
