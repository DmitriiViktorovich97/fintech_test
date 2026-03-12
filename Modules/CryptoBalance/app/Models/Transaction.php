<?php

namespace Modules\CryptoBalance\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    protected $table = 'transactions';

    const CREDIT = 'credit';
    const DEBIT = 'debit';

    protected $fillable = [
        'user_id',
        'cryptodictionaries_id',
        'type',
        'amount',
        'balance_before',
        'balance_after',
        'txid',
        'status',
        'metadata',
    ];

    protected $casts = [
        'amount' => 'decimal:18',
        'balance_before' => 'decimal:18',
        'balance_after' => 'decimal:18',
        'metadata' => 'array',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function cryptocurrency(): BelongsTo
    {
        return $this->belongsTo(Cryptodictionary::class, 'cryptodictionaries_id');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }
}
