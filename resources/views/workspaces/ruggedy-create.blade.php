@extends('layouts.main')

@section ('breadcrumb')
    {!! Breadcrumbs::render('dynamic', $workspaceApp) !!}
    <button type="button" class="btn round-btn pull-right c-grey" data-toggle="modal" data-target="#help">
        <i class="fa fa-question fa-lg" aria-hidden="true"></i>
    </button>
    <button type="button" class="btn round-btn pull-right c-yellow">
        <i class="fa fa-times fa-lg" aria-hidden="true"></i>
    </button>
@endsection

@section('content')

    @include('layouts.formError')

    <!-- Modal -->
    <div id="help" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Help Ttile</h4>
                </div>
                <div class="modal-body">
                    <p>Help text goes here.</p>
                </div>
            </div>

        </div>
    </div>
    <!-- Add asset form -->
    @include('partials.asset-form')
    <div class="row">
        <div class="col-md-6 col-sm-6 animated fadeIn">
            <h3>Add Vulnerability</h3>
            {!! Form::open([
                'url' => route(
                    'vulnerability.store',
                    [$workspaceApp->getRouteParameterName() => $workspaceApp->getId()]
                ),
                'files' => 'true'
            ]) !!}
            <button class="primary-btn" type="submit">Submit</button>
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
        <div class="col-sm-4 col-sm-offset-1 animated fadeIn">
            <div class="form-group">
                {!! Form::label('severity', 'Risk Score') !!}
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
            {!! Form::close() !!}
        </div>
        <div class="col-md-2"></div>
        <div class="col-md-6 animated fadeInUp">

        </div>
    </div>

@endsection
