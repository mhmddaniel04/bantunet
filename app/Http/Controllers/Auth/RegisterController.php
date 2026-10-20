<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\Organization;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function create(): View
    {
        return view('auth.register', [
            'states' => config('bantunet.states'),
        ]);
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        $organization = Organization::create(
            $request->safe()->except('declaration')   // password is hashed by the model
        );

        Auth::login($organization);
        $request->session()->regenerate();

        return redirect()
            ->route('npo.dashboard')
            ->with('success', "Welcome to BantuNet, {$organization->org_name}!");
    }
}