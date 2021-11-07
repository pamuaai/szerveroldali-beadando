@csrf
<div class="form-group">
    <label for="inputTitle">Film címe</label>
    <input type="text" name="title" value="{{ old('title') ?? ($movie->title ?? '') }}" class="form-control"
        id="inputTitle" aria-describedby="titleMessage" placeholder="Cím">
    @error('title')
        <small id="titleMessage" class="form-text text-danger">{{ $message }}</small>
    @enderror
</div>
<div class="form-group">
    <label for="inputDirector">Rendező</label>
    <input type="text" name="director" value="{{ old('director') ?? ($movie->director ?? '') }}" class="form-control"
        id="inputDirector" aria-describedby="directorMessage" placeholder="Rendező">
    @error('director')
        <small id="directorMessage" class="form-text text-danger">{{ $message }}</small>
    @enderror
</div>
<div class="form-group">
    <label for="inputYear">Év</label>
    <input type="text" name="year" value="{{ old('year') ?? ($movie->year ?? '') }}" class="form-control"
        id="inputYear" aria-describedby="yearMessage" placeholder="Év">
    @error('year')
        <small id="yearMessage" class="form-text text-danger">{{ $message }}</small>
    @enderror
</div>
<div class="form-group">
    <label for="descriptionTextArea">Leírás</label>
    <textarea name="description" class="form-control" id="descriptionTextArea" rows="3"
        aria-describedby="descriptionMessage">{{ old('description') ?? ($movie->description ?? '') }}</textarea>
    @error('description')
        <small id="descriptionMessage" class="form-text text-danger">{{ $message }}</small>
    @enderror
</div>
<div class="form-group">
    <label for="inputLength">Hossz</label>
    <input type="text" name="length" value="{{ old('length') ?? ($movie->length ?? '') }}" class="form-control"
        id="inputLength" aria-describedby="lengthMessage" placeholder="Hossz">
    @error('length')
        <small id="lengthMessage" class="form-text text-danger">{{ $message }}</small>
    @enderror
</div>

<div class="form-group">
    <label for="inputImage">
        Kép
        @isset($movie->image)
            <br>
            <div>
                <img src="{{ asset('storage/' . $movie->image) }}" alt="{{ $movie->title }} :Movie poster" width=300
                    class="img-fluid mb-3" />
            </div>
        @endisset
    </label>

    <input type="file" name="image" class="form-control" id="inputImage" aria-describedby="imageMessage"
        placeholder="Kép">
    @error('image')
        <small id="imageMessage" class="form-text text-danger">{{ $message }}</small>
    @enderror
    <div class="form-check">
        <input type="checkbox" name="deleteImage" class="form-check-input" id="deleteImage">
        <label class="form-check-label" for="deleteImage">Kép törlése</label>
    </div>
</div>
<button type="submit" class="btn btn-primary mt-3">Mentés</button>
