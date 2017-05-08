@extends('layouts.main')

@section ('breadcrumb')
    <a href="{{ route('ruggedy-app.view', [$workspaceApp->getRouteParameterName() => $workspaceApp->getId()]) }}">
        <button type="button" class="btn round-btn pull-right c-yellow">
            <i class="fa fa-times fa-lg" aria-hidden="true"></i>
        </button>
    </a>
    {!! Breadcrumbs::render('dynamic', $workspaceApp) !!}
@endsection

@section('content')
    <!-- Add asset form -->
    @include('partials.asset-form')
    {!! Form::open([
        'url' => route(
            'vulnerability.store',
            [$file->getRouteParameterName() => $file->getId()]
        ),
        'files' => 'true'
    ]) !!}
    <div class="row">
        <div class="col-md-6 col-sm-6 animated fadeIn">
            <h3>Add Vulnerability</h3>
            <button class="primary-btn" type="submit">Create Vulnerability</button>
            <div class="form-group">
                {!! Form::label('name', 'Vulnerability Name') !!}
                {!! Form::text('name', null, ['class' => 'black-form-control']) !!}
            </div>
            <div class="form-group">
                {!! Form::label('description', 'Vulnerability Description') !!}
                {!! Form::textarea('description', null, ['class' => 'black-form-control', 'rows' => '3']) !!}
                <script>
                    CKEDITOR.replace( 'description', {
                        customConfig: '/js/ckeditor_config.js',
                        height: 100
                    });
                </script>
            </div>
            <div class="form-group">
                {!! Form::label('solution', 'Vulnerability Solution') !!}
                {!! Form::textarea('solution', null, ['class' => 'black-form-control', 'rows' => '3']) !!}
                <script>
                    CKEDITOR.replace( 'solution', {
                        customConfig: '/js/ckeditor_config.js',
                        height: 100
                    });
                </script>
            </div>
            <div class="form-group">
                {!! Form::label('poc', 'Proof of Concept') !!}
                {!! Form::textarea('poc', null, ['class' => 'black-form-control', 'rows' => '3']) !!}
                <script>
                    CKEDITOR.replace( 'poc', {
                        customConfig: '/js/ckeditor_config.js',
                        height: 100
                    });
                </script>
            </div>
        </div>
        <div class="col-md-1 col-sm-1 animated fadeIn"></div>
        <div class="col-md-4 col-sm-4 animated fadeIn">
            <br><br><br><br><br>
            <div class="form-group">
                {!! Form::label('severity', 'Risk Score (severity)') !!}
                {!! Form::select('severity', $severities) !!}
            </div>
            <div class="form-group">
                {!! Form::label('cvss_score', 'CVSS Score') !!}
                {!! Form::text('cvss_score', null, ['class' => 'black-form-control']) !!}
            </div>
            <div class="form-group">
                {!! Form::label('thumbnail_1', 'Screenshot 1', ['class' => '']) !!}
                {!! Form::file('thumbnail_1') !!}
            </div>
            <div class="form-group">
                {!! Form::label('thumbnail_2', 'Screenshot 2', ['class' => '']) !!}
                {!! Form::file('thumbnail_2') !!}
            </div>
            <div class="form-group">
                {!! Form::label('thumbnail_3', 'Screenshot 3', ['class' => '']) !!}
                {!! Form::file('thumbnail_3') !!}
            </div>
            @include('partials.related-assets')
        </div>
        <div class="col-md-2"></div>
        <div class="col-md-6 animated fadeInUp">

        </div>
    </div>
    {{ csrf_field() }}
    {!! Form::close() !!}
@endsection
