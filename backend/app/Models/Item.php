<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'category',
        'price',
        'stock_quantity',
        'unit',
        'is_available',
        'image_url',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock_quantity' => 'integer',
        'is_available' => 'boolean',
    ];

    public function transactionItems(): HasMany
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function branchItems(): HasMany
    {
        return $this->hasMany(BranchItem::class);
    }

    public function branches()
    {
        return $this->belongsToMany(Branch::class, 'branch_items')
                    ->withPivot('stock_quantity', 'min_stock_level', 'is_available')
                    ->withTimestamps();
    }

    public function scopeAvailable($query)
    {
        return $query->where('is_available', true);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }

    public function scopeFood($query)
    {
        return $query->where('category', 'food');
    }

    public function scopeBeverage($query)
    {
        return $query->where('category', 'beverage');
    }

    public function getTotalSoldAttribute(): int
    {
        return $this->transactionItems()->sum('quantity');
    }
}