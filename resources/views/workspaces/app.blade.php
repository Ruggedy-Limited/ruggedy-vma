@extends('layouts.main')

@section ('breadcrumb')
    {!! Breadcrumbs::render('workspaceApp', $workspaceApp) !!}
    <button type="button" class="btn round-btn pull-right c-grey" data-toggle="modal" data-target="#help">
        <i class="fa fa-question fa-lg" aria-hidden="true"></i>
    </button>
    <a href="{{ route('workspace.app.delete', [
        'workspaceId'    => $workspaceApp->getWorkspace()->getId(),
        'workspaceAppId' => $workspaceApp->getId()
    ]) }}">
        <button type="button" class="btn round-btn pull-right c-red">
            <i class="fa fa-trash-o fa-lg" aria-hidden="true"></i>
        </button>
    </a>
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
            <a href="{{ route('workspace.app.file.form', ['workspaceAppId' => $workspaceApp->getId()]) }}"
               class="primary-btn" type="button">Add File</a>
        </div>
    </div>

    <div class="row animated fadeIn">
        @if ($workspaceApp->getFiles()->count() < 1)
            <p>
                You haven't uploaded any {{ ucwords($workspaceApp->getScannerApp()->getName()) }} files yet.
                <a href="{{ route('workspace.app.file.form', ['workspaceAppId' => $workspaceApp->getId()]) }}">
                    Upload one now?
                </a>
            </p>
        @else
            @foreach($workspaceApp->getFiles() as $file)
                <div class="col-md-4 animated pulse-hover">
                    <a href="{{ route('workspace.app.file.view', ['fileId' => $file->getId()]) }}">
                        <div class="content-card">
                            <h4 class="h-4-1">{{ $file->getName() }}</h4>
                            <p>{{ $file->getDescription() }}</p>
                        </div>
                    </a>
                </div>
            @endforeach
        @endif
    </div>

@endsection
