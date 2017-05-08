@extends('layouts.main')

@section ('breadcrumb')
    <a href="{{ route('home') }}">
        <button type="button" class="btn round-btn pull-right c-yellow">
            <i class="fa fa-times fa-lg" aria-hidden="true"></i>
        </button>
    </a>
    {!! Breadcrumbs::render('settings') !!}
@endsection

@section('content')
    <div class="row animated fadeIn">
        <div class="col-md-12">
            <a href="{{ route('settings.user.create') }}" class="primary-btn" type="button">Add User</a>
        </div>
    </div>

    <div class="row animated fadeIn">
        <ul class=tabs>
            <li>
                @include('partials.users-tab', ['tabNo' => 1])
            </li>
        </ul>
        <br style=clear:both;>
    </div>

@endsection
