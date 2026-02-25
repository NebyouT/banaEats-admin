<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GamificationEligibilityRule extends Model
{
    use HasFactory;

    protected $fillable = [
        'game_id',
        'rule_type',
        'operator',
        'value',
        'is_required',
        'priority',
    ];

    protected $casts = [
        'value' => 'array',
        'is_required' => 'boolean',
    ];

    public function game()
    {
        return $this->belongsTo(GamificationGame::class, 'game_id');
    }

    public function evaluate($user): bool
    {
        return match($this->rule_type) {
            'order_count' => $this->evaluateOrderCount($user),
            'new_user' => $this->evaluateNewUser($user),
            'inactive_user' => $this->evaluateInactiveUser($user),
            'total_spent' => $this->evaluateTotalSpent($user),
            'zone' => $this->evaluateZone($user),
            'time_of_day' => $this->evaluateTimeOfDay(),
            'day_of_week' => $this->evaluateDayOfWeek(),
            'last_order_days' => $this->evaluateLastOrderDays($user),
            default => false,
        };
    }

    protected function evaluateOrderCount($user): bool
    {
        $orderCount = \App\Models\Order::where('user_id', $user->id)
            ->whereIn('order_status', ['delivered', 'confirmed'])
            ->count();

        return $this->compare($orderCount, $this->value[0] ?? 0);
    }

    protected function evaluateNewUser($user): bool
    {
        $daysOld = now()->diffInDays($user->created_at);
        $threshold = $this->value[0] ?? 7;
        return $daysOld <= $threshold;
    }

    protected function evaluateInactiveUser($user): bool
    {
        $lastOrder = \App\Models\Order::where('user_id', $user->id)
            ->whereIn('order_status', ['delivered', 'confirmed'])
            ->latest()
            ->first();

        if (!$lastOrder) {
            return false;
        }

        $daysSinceLastOrder = now()->diffInDays($lastOrder->created_at);
        $threshold = $this->value[0] ?? 30;
        
        return $daysSinceLastOrder >= $threshold;
    }

    protected function evaluateTotalSpent($user): bool
    {
        $totalSpent = \App\Models\Order::where('user_id', $user->id)
            ->whereIn('order_status', ['delivered', 'confirmed'])
            ->sum('order_amount');

        return $this->compare($totalSpent, $this->value[0] ?? 0);
    }

    protected function evaluateZone($user): bool
    {
        if (!isset($user->zone_id)) {
            return false;
        }

        $allowedZones = $this->value;
        
        return match($this->operator) {
            'in' => in_array($user->zone_id, $allowedZones),
            'not_in' => !in_array($user->zone_id, $allowedZones),
            default => false,
        };
    }

    protected function evaluateTimeOfDay(): bool
    {
        $currentHour = now()->hour;
        $startHour = $this->value[0] ?? 0;
        $endHour = $this->value[1] ?? 23;

        return $currentHour >= $startHour && $currentHour <= $endHour;
    }

    protected function evaluateDayOfWeek(): bool
    {
        $currentDay = now()->dayOfWeek; // 0 = Sunday, 6 = Saturday
        $allowedDays = $this->value;

        return match($this->operator) {
            'in' => in_array($currentDay, $allowedDays),
            'not_in' => !in_array($currentDay, $allowedDays),
            default => false,
        };
    }

    protected function evaluateLastOrderDays($user): bool
    {
        $lastOrder = \App\Models\Order::where('user_id', $user->id)
            ->whereIn('order_status', ['delivered', 'confirmed'])
            ->latest()
            ->first();

        if (!$lastOrder) {
            return $this->operator === '>=' && ($this->value[0] ?? 0) == 0;
        }

        $daysSinceLastOrder = now()->diffInDays($lastOrder->created_at);
        
        return $this->compare($daysSinceLastOrder, $this->value[0] ?? 0);
    }

    protected function compare($actual, $expected): bool
    {
        return match($this->operator) {
            '>=' => $actual >= $expected,
            '<=' => $actual <= $expected,
            '=' => $actual == $expected,
            '!=' => $actual != $expected,
            '>' => $actual > $expected,
            '<' => $actual < $expected,
            'between' => $actual >= ($this->value[0] ?? 0) && $actual <= ($this->value[1] ?? 0),
            default => false,
        };
    }

    public function getRuleDescriptionAttribute(): string
    {
        return match($this->rule_type) {
            'order_count' => "Order count {$this->operator} {$this->value[0]}",
            'new_user' => "New user (within {$this->value[0]} days)",
            'inactive_user' => "Inactive for {$this->value[0]}+ days",
            'total_spent' => "Total spent {$this->operator} \${$this->value[0]}",
            'zone' => "Zone " . ($this->operator === 'in' ? 'is' : 'is not') . " in selected zones",
            'time_of_day' => "Time between {$this->value[0]}:00 - {$this->value[1]}:00",
            'day_of_week' => "Day of week " . ($this->operator === 'in' ? 'is' : 'is not') . " in selected days",
            'last_order_days' => "Last order {$this->operator} {$this->value[0]} days ago",
            default => $this->rule_type,
        };
    }
}
