@extends('layouts.main')

@section ('breadcrumb')
    <a href="{{ route('settings.view') }}">
        <button type="button" class="btn round-btn pull-right c-yellow">
            <i class="fa fa-times fa-lg" aria-hidden="true"></i>
        </button>
    </a>
    {!! Breadcrumbs::render('newUser') !!}
@endsection

@section('content')
    <div class="row">
        <div class="col-md-4 col-sm-4 animated fadeIn">
            <h3>Add User</h3>
            <br>
            {!! Form::open(['url' => route('settings.user.store')]) !!}
            <div class="form-group">
                {!! Form::label('name', 'Name') !!}
                {!! Form::text('name', null, ['class' => 'black-form-control']) !!}
            </div>
            <div class="form-group">
                {!! Form::label('email', 'Email') !!}
                {!! Form::email('email', null, ['class' => 'black-form-control']) !!}
            </div>
            <div class="form-group">
                {!! Form::label('password', 'Password') !!}
                {!! Form::password('password', ['class' => 'black-form-control']) !!}
            </div>
            <div class="form-group">
                {!! Form::label('password-confirm', 'Confirm Password') !!}
                {!! Form::password('password-confirm', ['class' => 'black-form-control']) !!}
            </div>
            <div class="form-group">
                {!! Form::checkbox('is_admin', true, false) !!}
                {!! Form::label('is_admin', 'User is an Admin') !!}
            </div>
            <button class="primary-btn" type="submit">Create User</button>
            {!! Form::close() !!}
        </div>
        <div class="col-md-2"></div>
        <div class="col-md-6 animated fadeInUp">

        </div>
    </div>

@endsection
