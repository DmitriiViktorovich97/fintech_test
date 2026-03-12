<?php

namespace Modules\CryptoBalance\Services;

use Modules\CryptoBalance\Models\Balance;
use Modules\CryptoBalance\Models\Cryptodictionary;
use Modules\CryptoBalance\Models\Transaction;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class BalanceService
{
    public function createTransaction(string $type,
        int $userId, string $currencyCode, string $amount, ?string
        $txid = null, array $metadata = [], $status = "pending"
    ) {
        $crypto = Cryptodictionary::query()
            ->where('code', $currencyCode)
            ->firstOrFail();

        if ($txid && Transaction::query()->where('txid', $txid)->exists())
            throw new RuntimeException('Такая транзакция уже есть');

        $balance = Balance::query()
            ->firstOrCreate(
                ['user_id' => $userId, 'cryptodictionaries_id' => $crypto->id],
                ['amount' => '0']
            );

        $balanceBefore = $balance->amount;
        $balanceAfter = match($type) {
            Transaction::CREDIT => bcadd($balanceBefore, $amount, 18),
            Transaction::DEBIT => bcsub($balanceBefore, $amount, 18)
        };

        return Transaction::query()->create([
            'user_id' => $userId,
            'cryptodictionaries_id' => $crypto->id,
            'type' => $type,
            'amount' => $amount,
            'txid' => $txid,
            'metadata' => $metadata,
            'status' => $status,
            'balance_before' => $balanceBefore,
            'balance_after' => $balanceAfter,
        ]);
    }

    public function processCredit(Transaction $transaction): Balance
    {
        return DB::transaction(function () use ($transaction)
        {
            $balance = Balance::query()
                ->lockForUpdate()
                ->firstOrCreate(
                    ['user_id' => $transaction->user_id, 'cryptodictionaries_id' => $transaction->cryptodictionaries_id],
                    ['amount' => '0']
                );

            $newBalance = bcadd($balance->amount, $transaction->amount, 18);
            $balance->update(['amount' => $newBalance]);

            $transaction->update([
                'status' => 'completed'
            ]);

            return $balance;
        });
    }

    public function processDebit(Transaction $transaction): Balance
    {
        return DB::transaction(function () use ($transaction)
        {
            $balance = Balance::query()
                ->lockForUpdate()
                ->where('user_id', $transaction->user_id)
                ->where('cryptodictionaries_id', $transaction->cryptodictionaries_id)
                ->first();

            if (!$balance)
                throw new RuntimeException('Баланс не найден');

            if (bccomp($balance->amount, $transaction->amount, 18) < 0)
                throw new RuntimeException('Нехватает средств ' . $balance->amount ." " . $transaction->amount);

            $newBalance = bcsub($balance->amount, $transaction->amount, 18);
            $balance->update(['amount' => $newBalance]);

            $transaction->update([
                'status' => 'completed'
            ]);

            return $balance;
        });
    }
}
