<?php

namespace App\Http\Controllers\Npo;

use App\Http\Controllers\Controller;
use App\Http\Requests\Npo\DeleteAccountRequest;
use App\Http\Requests\Npo\UpdateEmailRequest;
use App\Http\Requests\Npo\UpdatePasswordRequest;
use App\Http\Requests\Npo\UpdateProfileRequest;
use App\Services\OrganizationProfileService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(private OrganizationProfileService $profiles)
    {
    }

    public function edit(Request $request): View
    {
        return view('npo.profile.edit', [
            'organization' => $request->user(),
            'states'       => config('bantunet.states'),
        ]);
    }

    public function update(UpdateProfileRequest $request): RedirectResponse
    {
        $this->profiles->updateProfile(
            $request->user(),
            $request->validated(),
            $request->file('logo'),
            $request->boolean('remove_logo'),
        );

        return redirect()
            ->route('npo.profile.edit')
            ->with('success', 'Organisation profile updated.');
    }

    public function updateEmail(UpdateEmailRequest $request): RedirectResponse
    {
        $this->profiles->updateEmail($request->user(), $request->validated('org_email'));

        return redirect()
            ->to(route('npo.profile.edit').'#email')
            ->with('success', 'Login email changed. Use the new email next time you sign in.');
    }

    public function updatePassword(UpdatePasswordRequest $request): RedirectResponse
    {
        $this->profiles->updatePassword($request->user(), $request->validated('password'));

        $request->session()->regenerate();

        return redirect()
            ->to(route('npo.profile.edit').'#password')
            ->with('success', 'Password changed. Other devices have been signed out.');
    }

    public function destroy(DeleteAccountRequest $request): RedirectResponse
    {
        $organization = $request->user();

        // Sign out first, then delete everything.
        Auth::logout();
        $this->profiles->deleteAccount($organization);

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()
            ->route('home')
            ->with('success', 'Your organisation account and all its data have been deleted.');
    }
}