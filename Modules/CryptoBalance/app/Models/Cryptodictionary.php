<?php

namespace Modules\CryptoBalance\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cryptodictionary extends Model
{
    protected $table = 'cryptodictionaries';

    protected $fillable = [
        'code', 'name', 'decimals',
    ];

    public $timestamps = false;

    public function balances(): HasMany
    {
        return $this->hasMany(Balance::class, 'cryptodictionaries_id');
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'cryptodictionaries_id');
    }
}
