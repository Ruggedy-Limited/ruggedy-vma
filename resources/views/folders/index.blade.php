@extends('layouts.main')

@section ('breadcrumb')

    @can (App\Policies\ComponentPolicy::ACTION_EDIT, $folder)
        <a href="{{ route('folder.delete', [$folder->getRouteParameterName() => $folder->getId()]) }}"
            class="delete-link">
            <button type="button" class="btn round-btn pull-right c-red">
                <i class="fa fa-trash-o fa-lg" aria-hidden="true"></i>
            </button>
        </a>
        <a href="{{ route('folder.edit', [$folder->getRouteParameterName() => $folder->getId()]) }}">
            <button type="button" class="btn round-btn pull-right c-purple">
                <i class="fa fa-pencil fa-lg" aria-hidden="true"></i>
            </button>
        </a>
    @endcan
    <a href="{{ route('workspace.view', [
        $folder->getWorkspace()->getRouteParameterName() => $folder->getWorkspace()->getId()
    ]) }}">
        <button type="button" class="btn round-btn pull-right c-yellow">
            <i class="fa fa-times fa-lg" aria-hidden="true"></i>
        </button>
    </a>
    {!! Breadcrumbs::render('dynamic', $folder) !!}
@endsection

@section('content')
    <div class="row animated fadeIn">
        <ul class=tabs>
            <li>
                @include('partials.vulnerabilities-tab', ['tabNo' => 1])
            </li>
        </ul>
        <br style=clear:both;>
    </div>
@endsection