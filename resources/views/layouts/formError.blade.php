@if($errors->any())
    <div class="col-md-12">
    <div class="alert alert-danger" role="alert">
        @foreach($errors->all() as $error)
            <p>{{ $error }}</p>
        @endforeach
    </div>
    </div>
@endif