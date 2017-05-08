@extends('layouts.main')

@section ('breadcrumb')

    <button type="button" class="btn round-btn pull-right c-yellow">
        <i class="fa fa-times fa-lg" aria-hidden="true"></i>
    </button>
    {!! Breadcrumbs::render('dynamic', $folder) !!}
@endsection

@section('content')
    <div class="row">
        <div class="col-md-4 col-sm-4 animated fadeIn">
            <h3>Edit Folder: {{ $folder->getName() }}</h3>
            <br>
            {!! Form::open(['url' => route('folder.update', ['workspaceId' => $folder->getWorkspace()->getId() ])]) !!}
            <div class="form-group">
                {!! Form::label('name', 'Name') !!}
                {!! Form::text('name', $folder->getName(), ['class' => 'black-form-control']) !!}
            </div>
            <div class="form-group">
                {!! Form::label('description', 'Description') !!}
                {!! Form::textarea('description', $folder->getDescription(), ['class' => 'black-form-control', 'rows' => '3']) !!}
            </div>
            <button class="primary-btn" type="submit">Save Changes</button>
            {!! Form::close() !!}
        </div>
        <div class="col-md-2"></div>
        <div class="col-md-6 animated fadeInUp">
        </div>
    </div>

@endsection