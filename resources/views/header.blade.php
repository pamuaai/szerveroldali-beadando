<div class="border-bottom py-3">
    <div class="container">
        <div class="d-flex justify-content-between">
            <h1>BDMI (Base for Data about Movies on the Internet)</h1>
            <div>
                @auth
                    {{ Auth::user()->name }} | <a href="/logout">Logout</a>
                @else
                    <a href="/login">Login</a>
                @endauth
            </div>
        </div>
    </div>
</div>
