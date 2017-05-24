@extends('layouts.main')

@section ('breadcrumb')
    @if ($file->getWorkspaceApp()->isRuggedyApp())
        <a href="{{ route('workspace.view', [$file->getWorkspaceApp()->getWorkspace()->getRouteParameterName() => $file->getWorkspaceApp()->getWorkspace()->getId()]) }}">
    @else
        <a href="{{ route('app.view', [$file->getWorkspaceApp()->getRouteParameterName() => $file->getWorkspaceApp()->getId()]) }}">
    @endif
        <button type="button" class="btn round-btn pull-right c-yellow">
            <i class="fa fa-times fa-lg" aria-hidden="true"></i>
        </button>
    </a>
    @can (App\Policies\ComponentPolicy::ACTION_EDIT, $file)
        @if ($file->getWorkspaceApp()->isRuggedyApp())
            <a href="{{ route('app.delete', [$file->getWorkspaceApp()->getRouteParameterName() => $file->getWorkspaceApp()->getId()]) }}"
               class="delete-link">
        @else
            <a href="{{ route('file.delete', [
                $file->getRouteParameterName()                    => $file->getId(),
                $file->getWorkspaceApp()->getRouteParameterName() => $file->getWorkspaceApp()->getId()
            ]) }}" class="delete-link">
        @endif
            <button type="button" class="btn round-btn pull-right c-red">
                <i class="fa fa-trash-o fa-lg" aria-hidden="true"></i>
            </button>
        </a>
        @if ($file->getWorkspaceApp()->isRuggedyApp())
            <a href="{{ route('app.edit', [$file->getWorkspaceApp()->getRouteParameterName() => $file->getWorkspaceApp()->getId()]) }}">
        @else
            <a href="{{ route('file.edit', [$file->getRouteParameterName() => $file->getId()]) }}">
        @endif
            <button type="button" class="btn round-btn pull-right c-purple">
                <i class="fa fa-pencil fa-lg" aria-hidden="true"></i>
            </button>
        </a>
    @endcan
    @if ($file->getWorkspaceApp()->isRuggedyApp())
        {!! Breadcrumbs::render('dynamic', $file->getWorkspaceApp()) !!}
    @else
        {!! Breadcrumbs::render('dynamic', $file) !!}
    @endif
@endsection

@section('content')
    <div class="row animated fadeIn">
        <ul class=tabs>
            <li>
                @include('partials.vulnerabilities-tab', ['tabNo' => 1])
            </li>
            <li class="p-l-25">
                @include('partials.assets-tab', ['tabNo' => 2])
            </li>
        </ul>
        <br style=clear:both;>
    </div>

@endsection
