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
        <h1>Új film hozzáadása</h1>
        <form action="{{ route('movie.store') }}" method="POST">
            @csrf
            <div class="form-group">
                <label for="inputTitle">Film címe</label>
                <input type="text" name="title" class="form-control" id="inputTitle" aria-describedby="titleMessage"
                    placeholder="Cím">
                @error('title')
                    <small id="titleMessage" class="form-text text-danger">{{ $message }}</small>
                @enderror
            </div>
            <div class="form-group">
                <label for="inputDirector">Rendező</label>
                <input type="text" name="director" class="form-control" id="inputDirector"
                    aria-describedby="directorMessage" placeholder="Rendező">
                @error('director')
                    <small id="directorMessage" class="form-text text-danger">{{ $message }}</small>
                @enderror
            </div>
            <div class="form-group">
                <label for="descriptionTextArea">Leírás</label>
                <textarea name="description" class="form-control" id="descriptionTextArea" rows="3"
                    aria-describedby="descriptionMessage"></textarea>
                @error('description')
                    <small id="descriptionMessage" class="form-text text-danger">{{ $message }}</small>
                @enderror
            </div>
            <button type="submit" class="btn btn-primary">Mentés</button>
        </form>
    </div>
</body>

</html>
