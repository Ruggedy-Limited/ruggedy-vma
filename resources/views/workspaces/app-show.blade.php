@extends('layouts.main')

@section ('breadcrumb')
    <button type="button" class="btn round-btn pull-right c-grey" data-toggle="modal" data-target="#help">
        <i class="fa fa-question fa-lg" aria-hidden="true"></i>
    </button>
    @can (App\Policies\ComponentPolicy::ACTION_EDIT, $file)
        @if ($file->getWorkspaceApp()->isRuggedyApp())
            <a href="{{ route('app.delete', [$file->getWorkspaceApp()->getRouteParameterName() => $file->getWorkspaceApp()->getId()]) }}">
        @else
            <a href="{{ route('file.delete', [
                $file->getRouteParameterName()                    => $file->getId(),
                $file->getWorkspaceApp()->getRouteParameterName() => $file->getWorkspaceApp()->getId()
            ]) }}">
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
        <a href="{{ route('workspace.view', [$file->getWorkspaceApp()->getWorkspace()->getRouteParameterName() => $file->getWorkspaceApp()->getWorkspace()->getId()]) }}">
    @else
        <a href="{{ route('app.view', [$file->getWorkspaceApp()->getRouteParameterName() => $file->getWorkspaceApp()->getId()]) }}">
    @endif
        <button type="button" class="btn round-btn pull-right c-yellow">
            <i class="fa fa-times fa-lg" aria-hidden="true"></i>
        </button>
    </a>
    @if ($file->getWorkspaceApp()->isRuggedyApp())
        {!! Breadcrumbs::render('dynamic', $file->getWorkspaceApp()) !!}
    @else
        {!! Breadcrumbs::render('dynamic', $file) !!}
    @endif
@endsection

@section('content')
    <!-- Modal -->
    <div id="help" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Help Title</h4>
                </div>
                <div class="modal-body">
                    <p>Help text goes here.</p>
                </div>
            </div>

        </div>
    </div>
    <div class="row animated fadeIn">
        <ul class=tabs>
            <li>
                <input type=radio name=tabs id=tab1 checked>
                <label for=tab1>Vulnerabilities <span class="badge c-purple">{{ $vulnerabilities->total() }}</span></label>
                <div id=tab-content1 class=tab-content>
                    <div class="dash-line"></div>
                    @include('partials.vulnerabilities')
                </div>
            </li>
            <li>
                <input type=radio name=tabs id=tab2>
                <label for=tab2>Assets <span class="badge c-purple">{{ $assets->count() }}</span></label>
                <div id=tab-content2 class=tab-content>
                    <div class="dash-line"></div>
                    @include('partials.assets')
                </div>
            </li>
        </ul>
        <br style=clear:both;>
    </div>

@endsection
