<x-cryptobalance::layouts.master>
    @if(session('info'))
        <div style="color: green;">{{ session('info') }}</div>
    @endif
    @if(session('error'))
        <div style="color: red;">{{ session('error') }}</div>
    @endif

    <h1>Баланс пользователя {{ $user->name }}</h1>
    <ul>
        @foreach($balances as $balance)
            <li>{{ $balance->cryptodictionary->code }}: {{ $balance->amount }}</li>
        @endforeach
    </ul>

    <h2>Зачислить</h2>
    <form method="POST" action="{{ route('cryptobalance.credit') }}">
        @csrf
        <input type="hidden" name="user_id" value="{{ $user->id }}">
        <select name="currency_code">
            @foreach($cryptos as $crypto)
                <option value="{{ $crypto->code }}">{{ $crypto->code }}</option>
            @endforeach
        </select>
        <input type="text" name="amount" value="0.001">
        <button type="submit">Зачислить</button>
    </form>

    <h2>Списать</h2>
    <form method="POST" action="{{ route('cryptobalance.debit') }}">
        @csrf
        <input type="hidden" name="user_id" value="{{ $user->id }}">
        <select name="currency_code">
            @foreach($cryptos as $crypto)
                <option value="{{ $crypto->code }}">{{ $crypto->code }}</option>
            @endforeach
        </select>
        <input type="text" name="amount" value="0.001">
        <button type="submit">Списать</button>
    </form>
</x-cryptobalance::layouts.master>
