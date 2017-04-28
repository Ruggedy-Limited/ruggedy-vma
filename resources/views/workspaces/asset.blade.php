@extends('layouts.main')

@section ('breadcrumb')
    <a href="{{ redirect()->back() }}">
        <button type="button" class="btn round-btn pull-right c-yellow">
            <i class="fa fa-times fa-lg" aria-hidden="true"></i>
        </button>
    </a>
    {!! Breadcrumbs::render('dynamic', $asset) !!}
@endsection

@section ('content')

@endsection