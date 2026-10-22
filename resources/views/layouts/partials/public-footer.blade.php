<footer class="site-footer">
    <div class="container">
        <div class="site-footer__grid">
            <div>
                <x-logo variant="light" tagline />
                <p class="site-footer__about">
                    BantuNet connects Malaysian volunteers with registered non-profit organisations,
                    using AI to match skills with the initiatives that need them.
                </p>
            </div>

            <div>
                <h4 class="site-footer__title">Volunteers</h4>
                <ul class="site-footer__links">
                    <li><a href="{{ route('events.index') }}">Browse initiatives</a></li>
                    <li><a href="{{ route('match.index') }}">Smart Match</a></li>
                    <li><a href="{{ route('track.index') }}">Track application</a></li>
                </ul>
            </div>

            <div>
                <h4 class="site-footer__title">Organisations</h4>
                <ul class="site-footer__links">
                    <li><a href="{{ route('register') }}">Register your NPO</a></li>
                    <li><a href="{{ route('login') }}">NPO sign in</a></li>
                </ul>
            </div>
        </div>

        <p class="site-footer__bottom">
            &copy; {{ now()->year }} BantuNet · Intelligence Volunteer Matching in Event Management System · UiTM Final Year Project
        </p>
    </div>
</footer>