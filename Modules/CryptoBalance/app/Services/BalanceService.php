<?php

namespace Modules\CryptoBalance\Services;

use Modules\CryptoBalance\Models\Balance;
use Modules\CryptoBalance\Models\Cryptodictionary;
use Modules\CryptoBalance\Models\Transaction;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class BalanceService
{
    public function credit(int $userId, string $currencyCode, string $amount, ?string $txid = null, array $metadata = []): Balance
    {
        $crypto = Cryptodictionary::query()
            ->where('code', $currencyCode)
            ->firstOrFail();

        if ($txid && Transaction::query()->where('txid', $txid)->exists())
            throw new RuntimeException('Transaction with this txid already processed');

        return DB::transaction(function () use ($userId, $crypto, $amount, $txid, $metadata)
        {
            $balance = Balance::query()
                ->lockForUpdate()
                ->firstOrCreate(
                    ['user_id' => $userId, 'cryptodictionaries_id' => $crypto->id],
                    ['amount' => '0']
                );

            $oldBalance = $balance->amount;
            $newBalance = bcadd($oldBalance, $amount, 18);

            $balance->amount = $newBalance;
            $balance->save();

            Transaction::query()->create([
                'user_id' => $userId,
                'cryptodictionaries_id' => $crypto->id,
                'type' => 'credit',
                'amount' => $amount,
                'balance_before' => $oldBalance,
                'balance_after' => $newBalance,
                'txid' => $txid,
                'metadata' => $metadata,
                'status' => 'completed',
            ]);

            return $balance;
        });
    }

    public function debit(int $userId, string $currencyCode, string $amount, ?string $txid = null, array $metadata = []): Balance
    {
        $crypto = Cryptodictionary::query()
            ->where('code', $currencyCode)
            ->firstOrFail();

        return DB::transaction(function () use ($userId, $crypto, $amount, $txid, $metadata)
        {
            $balance = Balance::query()
                ->lockForUpdate()
                ->where('user_id', $userId)
                ->where('cryptodictionaries_id', $crypto->id)
                ->first();

            if (!$balance)
                throw new RuntimeException('Баланс не найден');

            if (bccomp($balance->amount, $amount, 18) < 0)
                throw new RuntimeException('Нехватает средств');

            $oldBalance = $balance->amount;
            $newBalance = bcsub($oldBalance, $amount, 18);

            $balance->amount = $newBalance;
            $balance->save();

            Transaction::query()->create([
                'user_id' => $userId,
                'cryptodictionaries_id' => $crypto->id,
                'type' => 'debit',
                'amount' => $amount,
                'balance_before' => $oldBalance,
                'balance_after' => $newBalance,
                'txid' => $txid,
                'metadata' => $metadata,
                'status' => 'completed',
            ]);


            return $balance;
        });
    }
}
