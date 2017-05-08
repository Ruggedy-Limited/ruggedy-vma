@extends('layouts.main')

@section ('breadcrumb')
    <a href="{{ route('app.view', [$workspaceApp->getRouteParameterName() => $workspaceApp->getId()]) }}">
        <button type="button" class="btn round-btn pull-right c-yellow">
            <i class="fa fa-times fa-lg" aria-hidden="true"></i>
        </button>
    </a>
    {!! Breadcrumbs::render('dynamic', $workspaceApp) !!}
@endsection

@section('content')
    <div class="row">
        <div class="col-md-4 col-sm-4 animated fadeIn">
            <h3>
                Edit Workspace App: {{ $workspaceApp->getName() }}
            </h3>

            <p><img src="{{ $workspaceApp->getScannerApp()->getLogo() }}" class="img-secondary"></p>
            <br>
            {!! Form::open([
                'url' => route('app.update',['workspaceAppId' => $workspaceApp->getId()])
            ]) !!}
            <div class="form-group">
                {!! Form::label('name', 'Name') !!}
                {!! Form::text('name', $workspaceApp->getName(), ['class' => 'black-form-control']) !!}
            </div>
            <div class="form-group">
                {!! Form::label('description', 'Description') !!}
                {!! Form::textarea('description', $workspaceApp->getDescription(), ['class' => 'black-form-control', 'rows' => '3']) !!}
            </div>
            <button class="primary-btn" type="submit">Save Changes</button>
            {!! Form::close() !!}
        </div>
        <div class="col-md-2"></div>
        <div class="col-md-6 animated fadeInUp">
        </div>
    </div>

@endsection
