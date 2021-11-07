<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous">
    </script>
    <title>{{ $movie->title }}</title>
</head>

<body>
    @include('header')
    <div class="container mt-5">
        @if (Session::has('movie_stored'))
            <div class="alert alert-success" role="alert">
                A filmet létrehoztad!
            </div>
        @endif
        @if (Session::has('movie_edited'))
            <div class="alert alert-success" role="alert">
                A módosítás sikeres volt!
            </div>
        @endif
        @if (Session::has('movie_deleted'))
            <div class="alert alert-success" role="alert">
                A film törlésre került!
            </div>
        @endif
        @if (Session::has('movie_restored'))
            <div class="alert alert-success" role="alert">
                A filmet sikeresen visszaállítottad!
            </div>
        @endif
        @if ($movie->deleted_at)
            <div class="alert alert-danger" role="alert">
                Figyelem!
                Ez a film már törölve lett, csak Admin jogosultsággal tekinthető meg
            </div>
        @endif
        <h1>{{ $movie->title }} ({{ $movie->year }})</h1>
        <h2>Rendező: {{ $movie->director }}</h2>
        <div class="row">
            <div class="col-md-6">
                <img src="{{ $movie->image ? asset('storage/' . $movie->image) : asset('images/moviePlaceholder.jpg') }}"
                    alt="{{ $movie->title }} - Movie poster" class="img-fluid" />
            </div>
            <div class="col-md-6">
                @auth
                    @if (Auth::user()->is_admin)
                        <div class="d-inline-block">
                            @if ($movie->deleted_at)
                                <form action="{{ route('admin.movies.restore', $movie) }}" method="POST">
                                    @method('GET')
                                    @csrf
                                    <button type="submit" class="btn btn-danger">Film visszaállítása</button>
                                </form>
                            @else
                                <form action="{{ route('admin.movies.destroy', $movie) }}" method="POST">
                                    @method('DELETE')
                                    @csrf
                                    <button type="submit" class="btn btn-danger">Film törlése</button>
                                </form>
                            @endif
                        </div>
                        <a href="{{ route('admin.movies.edit', $movie) }}"
                            class="btn btn-warning d-inline-block">Módosítás</a>

                    @endif
                @endauth
                <h3>Leírás</h3>
                {{ $movie->description }}
            </div>
        </div>
        <h3 class="avg-rating mt-3">Átlagos értékelés: {{ $movie->getRating() }}/5.00</h3>
        <div class="row ratings-list">
            @auth
                @if (Auth::user()->is_admin)
                    <form action="{{ route('admin.movie.rating.clear', $movie) }}" method="POST">
                        @csrf
                        <button type="submit" class="btn btn-danger">Összes értékelés törlése</button>
                    </form>
                @endif
                @if (Session::has('ratings_cleared'))
                    <div class="alert alert-success mt-3" role="alert">
                        Sikeresen törölted az értékeléseket!
                    </div>
                @endif
            @endauth
            <h3>Értékelések:</h3>
            @if ($movie->ratings_enabled)
                @auth
                    <?php
                    $userRating = $movie->ratings->where('user_id', Auth::id())->first();
                    ?>
                    @if (Session::has('movie_rated'))
                        <div class="alert alert-success" role="alert">
                            A filmet sikeresen értékelted!
                        </div>
                    @endif
                    <div class="h5">
                        @if ($userRating) Már írtál véleményt erről a filmről, itt módosíthatod @else Mondd el a véleményed! @endif
                    </div>
                    <form action="{{ route('movie.rate', $movie) }}" method="POST">
                        @csrf
                        <?php
                        $initRating = old('rating') ? old('rating') : ($userRating ? $userRating->rating : ''); ?>
                        <h3></h3>
                        <div class="d-flex">
                            <div class="mx-2 text-center">
                                <label for="movieRating1">1</label> <br>
                                <input type="radio" name="rating" id="movieRating1" value="1" @if ($initRating === '1') checked @endif>
                            </div>
                            <div class="mx-2 text-center">
                                <label for="movieRating2">2</label> <br>
                                <input type="radio" name="rating" id="movieRating2" value="2" @if ($initRating === '2') checked @endif>
                            </div>
                            <div class="mx-2 text-center">
                                <label for="movieRating3">3</label> <br>
                                <input type="radio" name="rating" id="movieRating3" value="3" @if ($initRating === '3') checked @endif>
                            </div>
                            <div class="mx-2 text-center">
                                <label for="movieRating4">4</label> <br>
                                <input type="radio" name="rating" id="movieRating4" value="4" @if ($initRating === '4') checked @endif>
                            </div>
                            <div class="mx-2 text-center">
                                <label for="movieRating5">5</label> <br>
                                <input type="radio" name="rating" id="movieRating5" value="5" @if ($initRating === '5') checked @endif>
                            </div>
                        </div>
                        @error('rating')
                            <p class="text-danger">
                                {{ $message }}
                            </p>
                        @enderror
                        <label for="movieComment">Megjegyzés</label>
                        <br>
                        <textarea name="comment" id="movieComment"
                            cols="100">{{ old('comment') ? old('comment') : ($userRating ? $userRating->comment : '') }}</textarea>
                        @error('comment')
                            <p class="text-danger">
                                {{ $message }}
                            </p>
                        @enderror
                        <br>
                        <button type="submit" class="btn btn-primary">Küldés</button>
                    </form>
                @else
                    <div>A film értékeléséhez <a href="/login">jelentkezz be!</a></div>
                @endauth

            @else
                A filmhez új értékelés nem írható
            @endif
            @foreach ($ratings as $rating)
                <div class="col-12 p-3 rounded border my-3">
                    <div class="d-flex justify-content-between">
                        <div class="{{ $rating->user->is_admin ? 'text-danger' : '' }}">{{ $rating->user->name }}
                        </div>
                        <div>{{ $rating->rating }}/5</div>
                    </div>
                    <p>
                        {{ $rating->comment }}
                    </p>
                </div>
            @endforeach
            <div class="d-flex justify-content-center">
                {{ $ratings->links('pagination::bootstrap-4') }}
            </div>

        </div>


    </div>
</body>

</html>
