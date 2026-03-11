<?php

namespace Modules\CryptoBalance\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Modules\CryptoBalance\Models\Balance;
use Modules\CryptoBalance\Models\Cryptodictionary;
use Modules\CryptoBalance\Services\BalanceService;

class CryptoBalanceController extends Controller
{
    public function __construct(readonly private BalanceService $balanceService) {}

    public function index(Request $request): Factory|View
    {
        $user = $request->user();
        $balances = Balance::with('cryptodictionary')->where('user_id', $user->id)->get();
        $cryptos = Cryptodictionary::all();

        return view('cryptobalance::index', compact('user', 'balances', 'cryptos'));
    }

    public function credit(Request $request): RedirectResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'currency_code' => 'required|exists:cryptodictionaries,code',
            'amount' => 'required|numeric|min:0.00000001',
            'txid' => 'nullable|string|unique:transactions,txid',
        ]);

        try {
            $this->balanceService->credit(
                $request->user_id,
                $request->currency_code,
                $request->amount,
                $request->txid,
                $request->except(['_token', 'user_id', 'currency_code', 'amount', 'txid'])
            );

            return redirect()->back()->with('success', 'Зачисление выполнено');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function debit(Request $request): RedirectResponse
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'currency_code' => 'required|exists:cryptodictionaries,code',
            'amount' => 'required|numeric|min:0.00000001',
        ]);

        try {
            $this->balanceService->debit(
                $request->user_id,
                $request->currency_code,
                $request->amount,
                $request->txid,
                $request->except(['_token', 'user_id', 'currency_code', 'amount', 'txid'])
            );

            return redirect()->back()->with('success', 'Списание выполнено');
        } catch (\Throwable $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('cryptobalance::create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request) {}

    /**
     * Show the specified resource.
     */
    public function show($id)
    {
        return view('cryptobalance::show');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        return view('cryptobalance::edit');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id) {}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id) {}
}
