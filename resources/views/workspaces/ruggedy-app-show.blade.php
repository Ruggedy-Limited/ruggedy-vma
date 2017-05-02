@extends('layouts.main')

@section ('breadcrumb')
    <a href="{{ route('workspace.view', [$file->getWorkspaceApp()->getWorkspace()->getRouteParameterName() => $file->getWorkspaceApp()->getWorkspace()->getId()]) }}">
        <button type="button" class="btn round-btn pull-right c-yellow">
            <i class="fa fa-times fa-lg" aria-hidden="true"></i>
        </button>
    </a>
    @can (App\Policies\ComponentPolicy::ACTION_EDIT, $file->getWorkspaceApp())
        <a href="{{ route('app.delete', [
            $file->getWorkspaceApp()->getRouteParameterName() => $file->getWorkspaceApp()->getId(),
            $file->getWorkspaceApp()->getWorkspace()->getRouteParameterName() => $file->getWorkspaceApp()->getWorkspace()->getId(),
        ]) }}">
            <button type="button" class="btn round-btn pull-right c-red">
                <i class="fa fa-trash-o fa-lg" aria-hidden="true"></i>
            </button>
        </a>
        <a href="{{ route('app.edit', [$file->getWorkspaceApp()->getRouteParameterName() => $file->getWorkspaceApp()->getId()]) }}">
            <button type="button" class="btn round-btn pull-right c-purple">
                <i class="fa fa-pencil fa-lg" aria-hidden="true"></i>
            </button>
        </a>
    @endcan
    {!! Breadcrumbs::render('dynamic', $file->getWorkspaceApp()) !!}
@endsection

@section('content')
    <div class="row animated fadeIn">
        <div class="col-md-12">
            @can (App\Policies\ComponentPolicy::ACTION_CREATE, $file->getWorkspaceApp())
                <a href="{{ route('vulnerability.create', ['fileId' => $file->getId()]) }}"
                    class="primary-btn" type="button">Add Vulnerability</a>
            @endcan
        </div>
    </div>
    <div class="row animated fadeIn">
        <ul class=tabs>
            <li>
                @include('partials.vulnerabilities-tab', ['tabNo' => 1, 'isRuggedyApp' => true])
            </li>
            <li>
                @include('partials.assets-tab', ['tabNo' => 2, 'isRuggedyApp' => true])
            </li>
        </ul>
        <br style=clear:both;>
    </div>
@endsection
