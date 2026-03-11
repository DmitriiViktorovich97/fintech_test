<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class AutoLogin
{
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check())
        {
            $user = User::query()->first() ?? User::factory()
                ->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
            ]);

            Auth::login($user);
        }

        return $next($request);
    }
}
