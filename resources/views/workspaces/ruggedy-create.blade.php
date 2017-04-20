@extends('layouts.main')

@section ('breadcrumb')
    <button type="button" class="btn round-btn pull-right c-grey" data-toggle="modal" data-target="#help">
        <i class="fa fa-question fa-lg" aria-hidden="true"></i>
    </button>
    <button type="button" class="btn round-btn pull-right c-yellow">
        <i class="fa fa-times fa-lg" aria-hidden="true"></i>
    </button>
    {!! Breadcrumbs::render('dynamic', $workspaceApp) !!}
@endsection

@section('content')
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
    {!! Form::open([
        'url' => route(
            'vulnerability.store',
            [$workspaceApp->getRouteParameterName() => $workspaceApp->getId()]
        ),
        'files' => 'true'
    ]) !!}
    <div class="row">
        <div class="col-md-6 col-sm-6 animated fadeIn">
            <h3>Add Vulnerability</h3>
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
            <h3>Related Assets</h3>
            <a href="#" class="primary-btn" data-toggle="modal" data-target="#add-asset-form">
                Add an Asset
            </a>
            <div id="related-assets">
            </div>
            {!! Form::select('assets', [], [], [
                'multiple' => 'multiple',
                'name'     => 'assets[]',
                'class'    => 'invisible',
                'id'       => 'assets-select',
            ]) !!}
        </div>
        <div class="col-md-2"></div>
        <div class="col-md-6 animated fadeInUp">

        </div>
    </div>
    {{ csrf_field() }}
    {!! Form::close() !!}
@endsection
