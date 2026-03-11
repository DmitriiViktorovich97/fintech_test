<?php

namespace Modules\CryptoBalance\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Balance extends Model
{
    protected $fillable = ['user_id', 'cryptodictionaries_id', 'amount'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function cryptodictionary(): BelongsTo
    {
        return $this->belongsTo(Cryptodictionary::class, 'cryptodictionaries_id');
    }

    public static function getLocked($userId, $currencyId)
    {
        return self::query()
            ->where('user_id', $userId)
            ->where('cryptodictionaries_id', $currencyId)
            ->lockForUpdate()
            ->first();
    }
}
