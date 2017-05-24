@extends('layouts.main')

@section ('breadcrumb')
    <a href="{{ route('settings.view') }}">
        <button type="button" class="btn round-btn pull-right c-yellow">
            <i class="fa fa-times fa-lg" aria-hidden="true"></i>
        </button>
    </a>
    @if (!empty(Auth::user()) && Auth::user()->getId() !== $user->getId())
        <a href="{{ route('settings.user.delete', [$user->getRouteParameterName() => $user->getId()]) }}"
           class="delete-link">
            <button type="button" class="btn round-btn pull-right c-red">
                <i class="fa fa-trash-o fa-lg" aria-hidden="true"></i>
            </button>
        </a>
    @endif
    {!! $user === Auth::user() ? Breadcrumbs::render('profile') : Breadcrumbs::render('editUser', $user) !!}
@endsection

@section('content')
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
            @if (!empty(Auth::user()) && Auth::user()->isAdmin() && Auth::user() !== $user)
                <div class="form-group">
                    {!! Form::checkbox('is_admin', true, $user->isAdmin()) !!}
                    {!! Form::label('is_admin', 'User is an Admin') !!}
                </div>
            @else
                {!! Form::hidden('is_admin', $user->isAdmin()) !!}
            @endif
            <button class="primary-btn" type="submit">Save Changes</button>
            {!! Form::close() !!}
        </div>
        <div class="col-md-2"></div>
        <div class="col-md-6 animated fadeInUp">

        </div>
    </div>

@endsection
