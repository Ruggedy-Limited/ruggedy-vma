@extends('layouts.main')

@section ('breadcrumb')
    <button type="button" class="btn round-btn pull-right c-grey" data-toggle="modal" data-target="#help">
        <i class="fa fa-question fa-lg" aria-hidden="true"></i>
    </button>
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
    <a href="{{ route('workspace.view', [$file->getWorkspaceApp()->getWorkspace()->getRouteParameterName() => $file->getWorkspaceApp()->getWorkspace()->getId()]) }}">
        <button type="button" class="btn round-btn pull-right c-yellow">
            <i class="fa fa-times fa-lg" aria-hidden="true"></i>
        </button>
    </a>
    {!! Breadcrumbs::render('dynamic', $file->getWorkspaceApp()) !!}
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
        <div class="col-md-12">
            <a href="{{ route('vulnerability.create', ['fileId' => $file->getId()]) }}"
               class="primary-btn" type="button">Add Vulnerability</a>
        </div>
    </div>
    <div class="row animated fadeIn">
        <ul class=tabs>
            <li>
                <input type=radio name=tabs id=tab1 checked>
                <label for=tab1>Vulnerabilities <span class="badge c-purple">{{ $vulnerabilities->total() }}</span></label>
                <div id=tab-content1 class=tab-content>
                    <div class="dash-line"></div>
                    @if (empty($vulnerabilities) || $vulnerabilities->isEmpty())
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="content-card">
                                    <p>No Vulnerabilities here yet. Add a Vulnerability by click the "Add Vulnerability"
                                            button above, then enter the custom Vulnerability details.</p>
                                </div>
                            </div>
                        </div>
                    @else
                        @foreach ($vulnerabilities as $vulnerability)
                            @include('partials.vulnerability')
                        @endforeach
                        {{ $vulnerabilities->links() }}
                    @endif
                </div>
            </li>
            <li>
                <input type=radio name=tabs id=tab2>
                <label for=tab2>Assets <span class="badge c-purple">{{ $assets->count() }}</span></label>
                <div id=tab-content2 class=tab-content>
                    <div class="dash-line"></div>
                    @if ($assets->isEmpty())
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="content-card">
                                    <p>No Assets here yet. When adding a Vulnerability, add Asset details for Assets to
                                        be displayed here.</p>
                                </div>
                            </div>
                        </div>
                    @else
                        @foreach ($assets as $asset)
                            @include('partials.asset')
                        @endforeach
                    @endif
                </div>
            </li>
        </ul>
        <br style=clear:both;>
    </div>

@endsection
