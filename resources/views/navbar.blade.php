<nav class="navbar" role="navigation" aria-label="main navigation">
    <div class="navbar-brand">
        <a class="navbar-item" href="/">
            <p>{{ Config::get('app.name') }}</p>
        </a>
    </div>

    <div id="nav-menu" class="navbar-menu">
        <div class="navbar-start">
            {{-- <a class="navbar-item">
        Home
      </a> --}}
        </div>

        <div class="navbar-end">
            <div class="navbar-item">
                <div class="buttons">
                    <a class="button is-light" href="/">
                        Map
                    </a>
                    @if (Auth::check())
                    <a class="button is-light" href="/datasets">
                        Dataset Manager
                    </a>
                    <a class="button is-light" href="{{ route('logout') }}"onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                        Logout
                    </a>
                    <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        {{ csrf_field() }}
                    </form>
                    @else
                    <a class="button is-light" href="/login">
                        Login
                    </a>
                    @endif
                </div>
            </div>
        </div>
    </div>
</nav>
