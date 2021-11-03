<nav class="navbar navbar-expand-lg navbar-light bg-light">
    <div class="container">
        <a class="navbar-brand" href="/">BDMI (Base for Data about Movies on the Internet)</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent"
            aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse d-flex justify-content-between" id="navbarSupportedContent">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item active">
                    <a class="nav-link" href="/">Főoldal</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/toplist">Toplista</a>
                </li>
            </ul>
            <div>
                @auth
                    {{ Auth::user()->name }} | <a href="/logout">Logout</a>
                    @if (Auth::user()->is_admin)
                        | <a href="/new-movie/">Új film</a>
                    @endif
                @else
                    <a href="/login">Login</a>
                @endauth
            </div>
        </div>
    </div>

</nav>
