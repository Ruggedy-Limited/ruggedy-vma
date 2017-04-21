@extends('layouts.main')

@section ('breadcrumb')
    <button type="button" class="btn round-btn pull-right c-grey" data-toggle="modal" data-target="#help">
        <i class="fa fa-question fa-lg" aria-hidden="true"></i>
    </button>
    <button type="button" class="btn round-btn pull-right c-red">
        <i class="fa fa-trash-o fa-lg" aria-hidden="true"></i>
    </button>
    <button type="button" class="btn round-btn pull-right c-yellow">
        <i class="fa fa-times fa-lg" aria-hidden="true"></i>
    </button>
    {!! $user === Auth::user() ? Breadcrumbs::render('profile') : Breadcrumbs::render('editUser', $user) !!}
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
    <br>
    <div class="row">
        <div class="col-md-4 col-sm-4 animated fadeIn">
            <h3>Edit User</h3>
            <br>
            {!! Form::open(['url' => route('settings.user.update', ['userId' => $user->getId()])]) !!}
            {{ csrf_field() }}
            <div class="form-group">
                {!! Form::label('name', 'Name') !!}
                {!! Form::text('name', $user->getName(), ['class' => 'black-form-control']) !!}
            </div>
            <div class="form-group">
                {!! Form::label('email', 'Email') !!}
                {!! Form::email('email', $user->getEmail(), ['class' => 'black-form-control']) !!}
            </div>
            <div class="form-group">
                {!! Form::label('password', 'Password') !!}
                {!! Form::password('password', ['class' => 'black-form-control']) !!}
            </div>
            <div class="form-group">
                {!! Form::label('password-confirm', 'Confirm Password') !!}
                {!! Form::password('password-confirm', ['class' => 'black-form-control']) !!}
            </div>
            <button class="primary-btn" type="submit">Save Changes</button>
            {!! Form::close() !!}
        </div>
        <div class="col-md-2"></div>
        <div class="col-md-6 animated fadeInUp">

        </div>
    </div>

@endsection
