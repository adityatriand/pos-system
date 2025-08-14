<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Carbon\Carbon;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_number',
        'branch_id',
        'employee_id',
        'subtotal',
        'service_charge',
        'total_amount',
        'payment_method',
        'status',
        'transaction_date',
        'notes',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'service_charge' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'transaction_date' => 'datetime',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function transactionItems(): HasMany
    {
        return $this->hasMany(TransactionItem::class);
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('transaction_date', [$startDate, $endDate]);
    }

    public function scopeByBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    public function calculateServiceCharge(): float
    {
        return round($this->subtotal * 0.05, 2);
    }

    public function calculateTotal(): float
    {
        return round($this->subtotal + $this->service_charge, 2);
    }

    public static function generateTransactionNumber(): string
    {
        $date = Carbon::now()->format('Ymd');
        $lastTransaction = self::whereDate('created_at', Carbon::today())
            ->orderBy('id', 'desc')
            ->first();
        
        $sequence = $lastTransaction ? (int)substr($lastTransaction->transaction_number, -4) + 1 : 1;
        
        return 'TRX' . $date . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}