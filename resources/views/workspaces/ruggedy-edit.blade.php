@extends('layouts.main')

@section ('breadcrumb')
    <a href="{{ route('vulnerability.view', [$vulnerability->getRouteParameterName() => $vulnerability->getId()]) }}">
        <button type="button" class="btn round-btn pull-right c-yellow">
            <i class="fa fa-times fa-lg" aria-hidden="true"></i>
        </button>
    </a>
    {!! Breadcrumbs::render('dynamic', $workspaceApp) !!}
@endsection

@section('content')
    <div class="waiting-icon-container">
        <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
        <span class="sr-only">Loading...</span>
    </div>
    <div class="waiting-overlay"></div>
    <!-- Add asset form -->
    @include('partials.asset-form')
    {!! Form::open([
        'url' => route(
            'vulnerability.update',
            [$vulnerability->getRouteParameterName() => $vulnerability->getId()]
        ),
        'files' => 'true'
    ]) !!}
    <div class="row">
        <div class="col-md-6 col-sm-6 animated fadeIn">
            <h3>Add Vulnerability</h3>
            <button class="primary-btn" type="submit">Save Changes</button>
            <div class="form-group">
                {!! Form::label('name', 'Vulnerability Name') !!}
                {!! Form::text('name', $vulnerability->getName(), ['class' => 'black-form-control']) !!}
            </div>
             <div class="form-group">
                {!! Form::label('severity', 'Risk Score (severity)') !!}
                {!! Form::select('severity', $severities, $vulnerability->getSeverity()) !!}
            </div>
            <div class="form-group">
                {!! Form::label('cvss_score', 'CVSS Score') !!}
                {!! Form::text('cvss_score', $vulnerability->getCvssScore(), ['class' => 'black-form-control']) !!}
            </div>
            <div class="form-group">
                {!! Form::label('description', 'Vulnerability Description') !!}
                {!! Form::textarea('description', $vulnerability->getDescription(), ['class' => 'black-form-control', 'rows' => '3']) !!}
                <script>
                    CKEDITOR.replace( 'description', {
                        customConfig: '/js/ckeditor_config.js',
                        height: 100
                    });
                </script>
            </div>
            <div class="form-group">
                {!! Form::label('solution', 'Vulnerability Solution') !!}
                {!! Form::textarea('solution', $vulnerability->getSolution(), ['class' => 'black-form-control', 'rows' => '3']) !!}
                <script>
                    CKEDITOR.replace( 'solution', {
                        customConfig: '/js/ckeditor_config.js',
                        height: 100
                    });
                </script>
            </div>
            <div class="form-group">
                {!! Form::label('poc', 'Proof of Concept') !!}
                {!! Form::textarea('poc', $vulnerability->getPoc(), ['class' => 'black-form-control', 'rows' => '3']) !!}
                <script>
                    CKEDITOR.replace( 'poc', {
                        customConfig: '/js/ckeditor_config.js',
                        height: 100
                    });
                </script>
            </div>
            <div class="col-xs-12">
                @include('partials.thumnail-edit', [
                    'thumbnail' => $vulnerability->getThumbnail1(),
                    'fieldName' => 'thumbnail_1',
                    'labelText' => 'Screenshot 1',
                ])
                </div>
                <div class="col-xs-12">
                    @include('partials.thumnail-edit', [
                        'thumbnail' => $vulnerability->getThumbnail2(),
                        'fieldName' => 'thumbnail_2',
                        'labelText' => 'Screenshot 2',
                    ])
                </div>
                <div class="col-xs-12">
                    @include('partials.thumnail-edit', [
                        'thumbnail' => $vulnerability->getThumbnail3(),
                        'fieldName' => 'thumbnail_3',
                        'labelText' => 'Screenshot 3',
                    ])
                </div>
        </div>
        <div class="col-md-6 animated fadeIn">
            @include('partials.related-assets')
        </div>
    </div>
    {{ csrf_field() }}
    {!! Form::close() !!}
@endsection
