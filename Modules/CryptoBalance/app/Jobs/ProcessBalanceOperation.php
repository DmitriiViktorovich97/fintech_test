<?php

namespace Modules\CryptoBalance\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Modules\CryptoBalance\Models\Transaction;
use Modules\CryptoBalance\Services\BalanceService;

class ProcessBalanceOperation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Transaction $transaction) {}

    public function handle(BalanceService $balanceService): void
    {
        $updated = Transaction::query()
            ->where('id', $this->transaction->id)
            ->where('status', 'pending')
            ->update(['status' => 'processing']);

        if ($updated === 0) return;

        try {
            match ($this->transaction->type) {
                Transaction::CREDIT => $balanceService->processCredit($this->transaction),
                Transaction::DEBIT  => $balanceService->processDebit($this->transaction),
            };

        } catch (\Throwable $e) {

            $this->transaction->update([
                'status' => 'failed',
                'metadata->error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
