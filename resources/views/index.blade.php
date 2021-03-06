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
    <title>Főoldal</title>
</head>

<body>
    @include('header')
    <div class="container mt-5">
        <div class="row">
            <h1>Főoldal</h1>
            @foreach ($movies as $movie)
                <div class="col-xs-12 col-md-6 col-lg-4 my-3">
                    <a
                        href="{{ $movie->deleted_at && Auth::check() && Auth::user()->is_admin ? route('admin.deleted.movie', $movie) : route('movie', $movie) }}">
                        <div class="card p-3 rounded">
                            <img src="{{ $movie->image ? asset('storage/' . $movie->image) : asset('images/moviePlaceholder.jpg') }}"
                                alt="{{ $movie->title }} - Movie poster" class="img-fluid" />
                            {{ $movie->title }}
                            <p>
                                Értékelés: {{ $movie->getRating() }}/5.00
                            </p>
                            @if (Auth::check() && Auth::user()->is_admin && $movie->deleted_at)
                                <p class="text-danger">Törölve!</p>
                            @endif
                        </div>
                    </a>
                </div>
            @endforeach
        </div>
        <div class="d-flex justify-content-center">
            {{ $movies->links('pagination::bootstrap-4') }}
        </div>
    </div>
</body>

</html>
