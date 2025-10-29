<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FinancialCategory extends Model
{
    protected $fillable = [
        'name',
        'type',
        'icon',
        'color',
        'user_id'
    ];

    protected $casts = [
        'type' => 'string'
    ];

    /**
     * Get the user that owns the category (for custom categories)
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get all transactions for this category
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(FinancialTransaction::class, 'category_id');
    }

    /**
     * Get all budgets for this category
     */
    public function budgets(): HasMany
    {
        return $this->hasMany(FinancialBudget::class, 'category_id');
    }

    /**
     * Scope a query to only include categories of a given type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('type', $type);
    }

    /**
     * Scope a query to include default categories or user's custom categories
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where(function($q) use ($userId) {
            $q->whereNull('user_id')
              ->orWhere('user_id', $userId);
        });
    }
}
