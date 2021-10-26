
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-ka7Sk0Gln4gmtz2MlQnikT1wXgYsOg+OMhuP+IlRH9sENBO0LRn5q+8nbTov4+1p" crossorigin="anonymous"></script>
    <title>{{$movie->title}}</title>
</head>
<body>
    <div class="container mt-5">
        
        <h1>{{$movie->title}} ({{$movie->year}})</h1>
        <h2>Rendező: {{$movie->director}}</h2>
        <div class="row">
            <div class="col-md-6">
                <img src="{{ $movie->image }}" alt="{{ $movie->title }} :Movie poster" class="img-fluid"/>
            </div>
            <div class="col-md-6">
                {{$movie->description}}
            </div>
        </div>
        <div class="avg-rating">Átlagos értékelés: {{ $movie->getRating()}}/5.00</div>
        <div class="row ratings-list">
            <h3>Vélemények:</h3>
            @if (Auth::check())
                userid: {{Auth::user()->id}}
            @endif
            @foreach ( $ratings as $rating )
            <div class="col-12 p-3 rounded border my-3">
                <div class="d-flex justify-content-between">
                    <div>{{$rating->user->name}}</div>
                    <div>{{$rating->rating}}/5</div>
                </div>
                <p>
                    {{$rating->comment}}
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