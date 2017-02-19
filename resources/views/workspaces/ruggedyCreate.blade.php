@extends('layouts.main')

@section ('breadcrumb')
    <p>Breadcrumbs / Goes / Here
        <button type="button" class="btn round-btn pull-right c-grey" data-toggle="modal" data-target="#help">
            <i class="fa fa-question fa-lg" aria-hidden="true"></i>
        </button>
        <button type="button" class="btn round-btn pull-right c-yellow">
            <i class="fa fa-times fa-lg" aria-hidden="true"></i>
        </button>
    </p>
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
    <br>
    <div class="row">
        <div class="col-md-6 col-sm-6 animated fadeIn">
            <h3>Add Vulnerability</h3>
            {!! Form::open(['url' => '/foo/bar'], ['files' => 'true']) !!}
            <button class="primary-btn" type="submit">Submit</button>
            <div class="form-group">
                {!! Form::label('vuln_desc.', 'Vulnerability Title') !!}
                {!! Form::text('vuln_desc', null, ['class' => 'black-form-control']) !!}
            </div>
            <div class="form-group">
                {!! Form::label('description', 'Vulnerability Description') !!}
                {!! Form::textarea('description', null, ['class' => 'black-form-control', 'rows' => '3', 'name' => 'description' ]) !!}
                <script>
                    CKEDITOR.replace( 'description', {
                        customConfig: '/js/ckeditor_config.js',
                        height: 100
                    });
                </script>
            </div>
            <div class="form-group">
                {!! Form::label('description', 'Vulnerability Solution') !!}
                {!! Form::textarea('description', null, ['class' => 'black-form-control', 'rows' => '3', 'name' => 'solution']) !!}
                <script>
                    CKEDITOR.replace( 'solution', {
                        customConfig: '/js/ckeditor_config.js',
                        height: 100
                    });
                </script>
            </div>
            <div class="form-group">
                {!! Form::label('description', 'Proof of Concept') !!}
                {!! Form::textarea('description', null, ['class' => 'black-form-control', 'rows' => '3', 'name' => 'poc']) !!}
                <script>
                    CKEDITOR.replace( 'poc', {
                        customConfig: '/js/ckeditor_config.js',
                        height: 100
                    });
                </script>
            </div>
            <div class="form-group">
                {!! Form::label('description', 'Assets') !!}
                {!! Form::textarea('description', null, ['class' => 'black-form-control', 'rows' => '3', 'name' => 'assets']) !!}
                <script>
                    CKEDITOR.replace( 'assets', {
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
                {!! Form::label('vuln_desc.', 'Risk Score') !!}
                {!! Form::select('folder', ['1' => 'High Risk', '2' => 'Medium Risk',
                '3' => 'Low Risk', '4' => 'Information']) !!}
            </div>
            <div class="form-group">
                {!! Form::label('vuln_desc.', 'CVSS Score') !!}
                {!! Form::text('vuln_desc', null, ['class' => 'black-form-control']) !!}
            </div>
            <div class="form-group">
                {!! Form::label('file', 'Screenshot 1', ['class' => '']) !!}
                {!! Form::file('file') !!}
            </div>
            <div class="form-group">
                {!! Form::label('file', 'Screenshot 2', ['class' => '']) !!}
                {!! Form::file('file') !!}
            </div>
            <div class="form-group">
                {!! Form::label('file', 'Screenshot 3', ['class' => '']) !!}
                {!! Form::file('file') !!}
            </div>
            {!! Form::close() !!}
        </div>
        <div class="col-md-2"></div>
        <div class="col-md-6 animated fadeInUp">

        </div>
    </div>

@endsection
