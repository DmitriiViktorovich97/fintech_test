<?php

namespace Modules\CryptoBalance\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CryptoBalanceDatabaseSeeder extends Seeder
{
//    public function run(): void
//    {
//        // $this->call([]);
//    }

    public function run(): void
    {
        $cryptos = [
            ['code' => 'BTC', 'name' => 'Bitcoin', 'decimals' => 8],
            ['code' => 'ETH', 'name' => 'Ethereum', 'decimals' => 18],
            ['code' => 'USDT', 'name' => 'Tether', 'decimals' => 6],
            ['code' => 'BNB', 'name' => 'Binance Coin', 'decimals' => 18],
            ['code' => 'SOL', 'name' => 'Solana', 'decimals' => 9],
            ['code' => 'XRP', 'name' => 'Ripple', 'decimals' => 6],
            ['code' => 'ADA', 'name' => 'Cardano', 'decimals' => 6],
            ['code' => 'DOGE', 'name' => 'Dogecoin', 'decimals' => 8],
            ['code' => 'DOT', 'name' => 'Polkadot', 'decimals' => 10],
            ['code' => 'MATIC', 'name' => 'Polygon', 'decimals' => 18],
        ];

        DB::table('cryptodictionaries')->upsert(
            $cryptos,
            ['code'],
            ['name', 'decimals']
        );
    }
}
