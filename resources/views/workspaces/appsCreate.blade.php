@extends('layouts.main')

@section ('breadcrumb')
    <a href="{{ route('workspace.apps', [$workspace->getRouteParameterName() => $workspace->getId()]) }}">
        <button type="button" class="btn round-btn pull-right c-yellow">
            <i class="fa fa-times fa-lg" aria-hidden="true"></i>
        </button>
    </a>
    {!! Breadcrumbs::render('createWorkspaceApp', $workspace, $scannerApp) !!}
@endsection

@section('content')
    <div class="row">
        <div class="col-md-4 col-sm-4 animated fadeIn">
            <h3>
                Add {{ $scannerApp->getFriendlyName() }} App to Workspace
            </h3>

            <p><img src="{{ $scannerApp->getLogo() }}" class="img-secondary"></p>
            <br>
            {!! Form::open([
                'url' => route('app.store',['workspaceId' => $workspaceId, 'scannerAppId' => $scannerAppId])
            ]) !!}
            <div class="form-group">
                {!! Form::label('name', 'Name') !!}
                {!! Form::text('name', null, ['class' => 'black-form-control']) !!}
            </div>
            <div class="form-group">
                {!! Form::label('description', 'Description') !!}
                {!! Form::textarea('description', null, ['class' => 'black-form-control', 'rows' => '3']) !!}
            </div>
            <button class="primary-btn" type="submit">Submit</button>
            {!! Form::close() !!}
        </div>
        <div class="col-md-2"></div>
        <div class="col-md-6 animated fadeInUp">
        </div>
    </div>

@endsection
