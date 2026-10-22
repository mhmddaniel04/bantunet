@extends('layouts.npo')

@section('title', 'Profile & settings')

@section('content')
    <x-page-header eyebrow="Account" title="Profile & settings"
                   lead="How your organisation appears to volunteers, and how you sign in." />

    <div class="settings">

        {{-- Section links --}}
        <nav class="settings__nav" aria-label="Settings sections">
            <a href="#profile" class="settings__link">Organisation profile</a>
            <a href="#email" class="settings__link">Login email</a>
            <a href="#password" class="settings__link">Password</a>
            <a href="#delete" class="settings__link settings__link--danger">Delete account</a>
        </nav>

        <div class="settings__sections">

            {{-- 1. Organisation profile --}}
            <section id="profile" class="card">
                <div class="profile-head">
                    <x-org-avatar :organization="$organization" size="lg" dark />
                    <div>
                        <h2 class="card__title">{{ $organization->org_name }}</h2>
                        @if ($organization->is_verified)
                            <span class="badge badge--approved">✓ ROS verified</span>
                        @else
                            <span class="badge badge--pending">Not yet verified</span>
                        @endif
                    </div>
                </div>

                <form method="POST" action="{{ route('npo.profile.update') }}" enctype="multipart/form-data" class="form">
                    @csrf
                    @method('PUT')

                    <x-form.image name="logo" label="Profile picture (logo)" shape="circle"
                                  :current-url="$organization->logoUrl()"
                                  hint="Square JPG, PNG or WEBP, at least 100 × 100 px, up to 1 MB." />

                    <x-form.input name="org_name" label="Organisation name" :value="$organization->org_name" required />

                    <div class="form-row form-row--2">
                        <div class="field">
                            <span class="field__label">ROS number</span>
                            <p class="readonly-field">{{ $organization->ros_number }}</p>
                            <p class="field__hint">Your legal registration number cannot be changed here.</p>
                        </div>

                        <x-form.select name="state" label="State" :options="$states"
                                       :value="$organization->state" required />
                    </div>

                    <div class="form-row form-row--2">
                        <x-form.input name="org_phone" label="Phone" type="tel"
                                      :value="$organization->org_phone" placeholder="03-1234 5678" required />

                        <x-form.input name="website" label="Website (optional)" type="url"
                                      :value="$organization->website" placeholder="https://www.example.org" />
                    </div>

                    <x-form.textarea name="org_hq_location" label="Headquarters address (optional)" rows="2"
                                     :value="$organization->org_hq_location" />

                    <x-form.textarea name="description" label="About your organisation (optional)" rows="5"
                                     :value="$organization->description" maxlength="2000"
                                     hint="Shown to volunteers on your initiative pages. Describe your mission and who you help." />

                    <div class="form-actions">
                        <button type="submit" class="btn btn--primary">Save profile</button>
                    </div>
                </form>
            </section>

            {{-- 2. Login email --}}
            <section id="email" class="card">
                <h2 class="card__title">Login email</h2>
                <p class="card__subtitle">Currently <strong>{{ $organization->org_email }}</strong>. New application alerts are also sent here.</p>

                <form method="POST" action="{{ route('npo.profile.email') }}" class="form">
                    @csrf
                    @method('PUT')

                    <div class="form-row form-row--2">
                        <x-form.input name="org_email" label="New email" type="email"
                                      autocomplete="email" required />
                        <x-form.input name="email_password" label="Your password" type="password"
                                      autocomplete="current-password" required />
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn--primary">Change email</button>
                    </div>
                </form>
            </section>

            {{-- 3. Password --}}
            <section id="password" class="card">
                <h2 class="card__title">Password</h2>
                <p class="card__subtitle">Changing your password signs you out on other devices.</p>

                <form method="POST" action="{{ route('npo.profile.password') }}" class="form">
                    @csrf
                    @method('PUT')

                    <x-form.input name="current_password" label="Current password" type="password"
                                  autocomplete="current-password" required />

                    <div class="form-row form-row--2">
                        <x-form.input name="password" label="New password" type="password"
                                      autocomplete="new-password" required />
                        <x-form.input name="password_confirmation" label="Confirm new password" type="password"
                                      autocomplete="new-password" hint="Min. 8 characters, letters and numbers." required />
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn--primary">Change password</button>
                    </div>
                </form>
            </section>

            {{-- 4. Delete account --}}
            <section id="delete" class="card danger-card">
                <h2 class="card__title">Delete account</h2>
                <p class="card__subtitle">This permanently deletes:</p>
                <ul class="danger-list">
                    <li>your organisation profile and logo</li>
                    <li>all your initiatives and their photos</li>
                    <li>all volunteer applications and résumés you received</li>
                </ul>
                <p class="card__subtitle" style="margin-top: 12px;">
                    Tip: export your reports and volunteer lists first. <strong>This cannot be undone.</strong>
                </p>

                <form method="POST" action="{{ route('npo.profile.destroy') }}" class="form"
                      data-confirm="Permanently delete {{ $organization->org_name }} and ALL its data?">
                    @csrf
                    @method('DELETE')

                    <div class="form-row form-row--2">
                        <x-form.input name="delete_password" label="Your password" type="password"
                                      autocomplete="current-password" required />
                        <x-form.input name="confirm_text" label="Type DELETE to confirm"
                                      autocomplete="off" placeholder="DELETE" required />
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn--danger">Delete my organisation</button>
                    </div>
                </form>
            </section>
        </div>
    </div>
@endsection