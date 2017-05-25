@extends('layouts.main')

@section ('breadcrumb')
    <a href="{{ route('home') }}">
        <button type="button" class="btn round-btn pull-right c-yellow">
            <i class="fa fa-times fa-lg" aria-hidden="true"></i>
        </button>
    </a>
    @can (App\Policies\ComponentPolicy::ACTION_EDIT, $workspace)
        <a href="{{ route('workspace.delete', [$workspace->getRouteParameterName() => $workspace->getId()]) }}"
           class="delete-link">
            <button type="button" class="btn round-btn pull-right c-red">
                <i class="fa fa-trash-o fa-lg" aria-hidden="true"></i>
            </button>
        </a>
        <a href="{{ route('workspace.edit', [$workspace->getRouteParameterName() => $workspace->getId()]) }}">
            <button type="button" class="btn round-btn pull-right c-purple">
                <i class="fa fa-pencil fa-lg" aria-hidden="true"></i>
            </button>
        </a>
    @endcan
    {!! Breadcrumbs::render('dynamic', $workspace) !!}
@endsection

@section('content')
    <div class="row animated fadeIn">
        <div class="col-md-12">
            @can (App\Policies\ComponentPolicy::ACTION_CREATE, $workspace)
                <a href="{{ route('workspace.apps', ['workspaceId' => $workspace->getId()]) }}"
                   class="primary-btn" type="button">Add Application</a>
                <a href="{{ route('folder.create', ['workspaceId' => $workspace->getId()]) }}"
                   class="primary-btn" type="button">Add Folder</a>
            @endcan
        </div>
    </div>

    <div class="row animated fadeIn">
        <ul class=tabs>
            <li>
                @include('partials.apps-tab', ['tabNo' => 1])
            </li>
            <li class="p-l-25">
                @include('partials.folders-tab', ['tabNo' => 2])
            </li>
        </ul>
        <br style=clear:both;>
    </div>

@endsection
