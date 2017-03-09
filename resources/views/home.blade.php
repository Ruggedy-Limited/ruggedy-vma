@extends('layouts.main')
@section ('breadcrumb')
    <p>Breadcrumbs / Goes / Here
        <button type="button" class="btn round-btn pull-right c-grey" data-toggle="modal" data-target="#help">
            <i class="fa fa-question fa-lg" aria-hidden="true"></i>
        </button>
    </p>
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
            <a href="{{ route('workspace.create') }}" class="primary-btn" type="button">Add Workspace</a>
        </div>
    </div>

    <div class="row animated fadeIn">
        @if (empty($workspaces))
            <div class="col-sm-12">
                There aren't any workspaces, it's very quiet in here.
                <a href="{{ route('workspaces.create') }}">Add a Workspace</a>
            </div>
        @else
            @foreach ($workspaces as $workspace)
                <div class="col-md-4 col-sm-6">
                    <a href="{{ route('workspace.show', ['workspaceId' => $workspace->getId()]) }}">
                        <div class="card hovercard animated pulse-hover">
                            <div class="cardheader c-white">
                            </div>
                            <div class="avatar avatar-white">
                                <i class="fa fa-th-large fa-5x t-c-purple"></i>
                            </div>
                            <div class="info">
                                <div class="title h-3">
                                    <h4>{{ $workspace->getName() }}</h4>
                                </div>
                                <div class="desc t-3">
                                    {{ $workspace->getDescription() }}
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @endforeach
        @endif
    </div>
@endsection
