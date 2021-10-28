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
    <div class="container mt-5">

        <h1>{{ $movie->title }} ({{ $movie->year }})</h1>
        <h2>Rendező: {{ $movie->director }}</h2>
        <div class="row">
            <div class="col-md-6">
                <img src="{{ $movie->image }}" alt="{{ $movie->title }} :Movie poster" class="img-fluid" />
            </div>
            <div class="col-md-6">
                {{ $movie->description }}
            </div>
        </div>
        <div class="avg-rating">Átlagos értékelés: {{ $movie->getRating() }}/5.00</div>
        <div class="row ratings-list">
            <h3>Vélemények:</h3>
            @if ($movie->ratings_enabled)
                @auth
                    <?php
                    $userRating = $movie->ratings->where('user_id', Auth::user()->id)->first();
                    ?>
                    @if (Session::has('movie_rated'))
                        <div class="alert alert-success" role="alert">
                            A filmet sikeresen értékelted!
                        </div>
                    @endif
                    <div class="h5">
                        @if ($userRating) Már írtál véleményt erről a filmről: @else Mondd el a véleményed! @endif
                    </div>
                    <form action="{{ route('movie.rate', $movie) }}" method="POST">
                        @csrf
                        <?php
                        $initRating = old('rating') ? old('rating') : ($userRating ? $userRating->rating : ''); ?>
                        <div>
                            <label for="movieRating1">1</label>
                            <input type="radio" name="rating" id="movieRating1" value="1" @if ($initRating === '1') checked @endif>
                            <label for="movieRating2">2</label>
                            <input type="radio" name="rating" id="movieRating2" value="2" @if ($initRating === '2') checked @endif>
                            <label for="movieRating3">3</label>
                            <input type="radio" name="rating" id="movieRating3" value="3" @if ($initRating === '3') checked @endif>
                            <label for="movieRating4">4</label>
                            <input type="radio" name="rating" id="movieRating4" value="4" @if ($initRating === '4') checked @endif>
                            <label for="movieRating5">5</label>
                            <input type="radio" name="rating" id="movieRating5" value="5" @if ($initRating === '5') checked @endif>
                        </div>
                        @error('rating')
                            <p class="text-danger">
                                {{ $message }}
                            </p>
                        @enderror
                        <label for="movieComment">Megjegyzés</label>
                        <br>
                        <textarea name="comment"
                            id="movieComment">{{ old('comment') ? old('comment') : ($userRating ? $userRating->comment : '') }}</textarea>
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
                        <div>{{ $rating->user->name }}</div>
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
