<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class FinancialTransaction extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'transaction_date',
        'type',
        'category_id',
        'amount',
        'description',
        'status',
        'reference_number'
    ];

    protected $casts = [
        'transaction_date' => 'date',
        'amount' => 'decimal:2',
        'type' => 'string',
        'status' => 'string'
    ];

    /**
     * Get the user that owns the transaction
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the category of the transaction
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(FinancialCategory::class, 'category_id');
    }

    /**
     * Get formatted amount with currency symbol
     */
    public function getFormattedAmountAttribute(): string
    {
        return '$' . number_format($this->amount, 2);
    }

    /**
     * Scope a query to only include transactions of a given type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope a query to only include transactions for a specific user
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to filter by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('transaction_date', [$startDate, $endDate]);
    }

    /**
     * Scope a query to only include completed transactions
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to filter by category
     */
    public function scopeByCategory($query, $categoryId)
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Get total amount for a specific type
     */
    public static function getTotalByType($userId, $type, $startDate = null, $endDate = null)
    {
        $query = static::forUser($userId)
            ->ofType($type)
            ->completed();

        if ($startDate && $endDate) {
            $query->dateRange($startDate, $endDate);
        }

        return $query->sum('amount');
    }

    /**
     * Get trend percentage compared to previous period
     */
    public static function getTrendPercentage($userId, $type, $startDate, $endDate)
    {
        $currentTotal = static::getTotalByType($userId, $type, $startDate, $endDate);

        $periodDiff = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate));
        $previousStart = Carbon::parse($startDate)->subDays($periodDiff)->toDateString();
        $previousEnd = Carbon::parse($startDate)->subDay()->toDateString();

        $previousTotal = static::getTotalByType($userId, $type, $previousStart, $previousEnd);

        if ($previousTotal == 0) {
            return $currentTotal > 0 ? 100 : 0;
        }

        return round((($currentTotal - $previousTotal) / $previousTotal) * 100, 2);
    }
}
