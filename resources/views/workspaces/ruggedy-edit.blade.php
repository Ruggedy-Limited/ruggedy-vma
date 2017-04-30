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
        </div>
        <div class="col-md-1 col-sm-1 animated fadeIn"></div>
        <div class="col-md-4 col-sm-4 animated fadeIn">
            <br><br><br><br><br>
            <div class="form-group">
                {!! Form::label('severity', 'Risk Score (severity)') !!}
                {!! Form::select('severity', $severities, $vulnerability->getSeverity()) !!}
            </div>
            <div class="form-group">
                {!! Form::label('cvss_score', 'CVSS Score') !!}
                {!! Form::text('cvss_score', $vulnerability->getCvssScore(), ['class' => 'black-form-control']) !!}
            </div>
            <div class="form-group row">
                <div class="col-xs-12 col-md-4 text-center">
                @include('partials.thumnail-edit', [
                    'thumbnail' => $vulnerability->getThumbnail1(),
                    'fieldName' => 'thumbnail_1',
                    'labelText' => 'Screenshot 1',
                ])
                </div>
                <div class="col-xs-12 col-md-4 text-center">
                    @include('partials.thumnail-edit', [
                        'thumbnail' => $vulnerability->getThumbnail2(),
                        'fieldName' => 'thumbnail_2',
                        'labelText' => 'Screenshot 2',
                    ])
                </div>
                <div class="col-xs-12 col-md-4 text-center">
                    @include('partials.thumnail-edit', [
                        'thumbnail' => $vulnerability->getThumbnail3(),
                        'fieldName' => 'thumbnail_3',
                        'labelText' => 'Screenshot 3',
                    ])
                </div>
            </div>
            @include('partials.related-assets')
            <div class="form-group">
                <button class="primary-btn" type="submit">Save Changes</button>
            </div>
        </div>
        <div class="col-md-2"></div>
        <div class="col-md-6 animated fadeInUp">

        </div>
    </div>
    {{ csrf_field() }}
    {!! Form::close() !!}
@endsection
