@extends('layouts.main')

@section ('breadcrumb')
    {!! Breadcrumbs::render('dynamic', $workspace) !!}
    <button type="button" class="btn round-btn pull-right c-grey" data-toggle="modal" data-target="#help">
        <i class="fa fa-question fa-lg" aria-hidden="true"></i>
    </button>
    <button type="button" class="btn round-btn pull-right c-red">
        <i class="fa fa-trash-o fa-lg" aria-hidden="true"></i>
    </button>
    <button type="button" class="btn round-btn pull-right c-purple">
        <i class="fa fa-pencil fa-lg" aria-hidden="true"></i>
    </button>
    <button type="button" class="btn round-btn pull-right c-yellow">
        <i class="fa fa-times fa-lg" aria-hidden="true"></i>
    </button>
@endsection

@section('content')
    <!-- Modal -->
    <div id="help" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Help Ttile</h4>
                </div>
                <div class="modal-body">
                    <p>Help text goes here.</p>
                </div>
            </div>

        </div>
    </div>

    <div class="row animated fadeIn">
        <div class="col-md-12">
            <a href="{{ route('workspace.apps', ['workspaceId' => $workspace->getId()]) }}"
               class="primary-btn" type="button">Add Application</a>
            <a href="{{ route('folder.create', ['workspaceId' => $workspace->getId()]) }}"
               class="primary-btn" type="button">Add Folder</a>
        </div>
    </div>

    <div class="row animated fadeIn">
        <ul class=tabs>
            <li>
                <input type=radio name=tabs id=tab1 checked>
                <label for=tab1>
                    <i class="fa fa-window-maximize fa-2x" aria-hidden="true"></i>
                    <p class="hidden-xs">Apps</p>
                </label>
                <div id="tab-content1" class="tab-content">
                    <div class="dash-line"></div>
                    <div class="col-xs-12">
                    @if (empty($workspace->getWorkspaceApps()))
                        <p>
                            No Apps in this Workspace yet.
                            <a href="{{ route('workspaces.apps') }}">Add an Application.</a>
                        </p>
                    @else
                        @foreach ($workspace->getWorkspaceApps() as $app)
                            @include('partials.app')
                        @endforeach
                    @endif
                    </div>
                </div>
            </li>
            <li class="p-l-25">
                <input type=radio name=tabs id=tab2>
                <label for=tab2>
                    <i class="fa fa-folder fa-2x" aria-hidden="true"></i>
                    <p class="hidden-xs">Folders</p>
                </label>
                <div id=tab-content2 class=tab-content>
                    <div class="dash-line"></div>
                    <div class="col-xs-12">
                    @if (empty($workspace->getFolders()))
                        <p>
                            No Folders in this Workspace yet.
                            <a href="{{ route('folders.create') }}">Add a Folder.</a>
                        </p>
                    @else
                        @foreach ($workspace->getFolders() as $folder)
                            <div class="col-md-4 col-sm-6">
                                <a href="{{ route('folder.view', ['folderId' => $folder->getId()]) }}">
                                    <div class="card hovercard animated pulse-hover">
                                        <div class="cardheader c-white"></div>
                                        <div class="avatar avatar-white">
                                            <i class="fa fa-folder fa-5x t-c-grey"></i>
                                        </div>
                                        <div class="info">
                                            <div class="title h-3">
                                                <h4>{{ $folder->getName() }}</h4>
                                            </div>
                                            <div class="desc t-3">
                                                {{ $folder->getDescription() }}
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    @endif
                    </div>
                </div>
            </li>
        </ul>
        <br style=clear:both;>
    </div>

@endsection
