@extends('layouts.main')

@section ('breadcrumb')
    {!! Breadcrumbs::render('dynamic', $workspaceApp) !!}
    <button type="button" class="btn round-btn pull-right c-grey" data-toggle="modal" data-target="#help">
        <i class="fa fa-question fa-lg" aria-hidden="true"></i>
    </button>
    <a href="{{ route('app.delete', [
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
            <a href="{{ route('file.create', ['workspaceAppId' => $workspaceApp->getId()]) }}"
               class="primary-btn" type="button">Add File</a>
        </div>
    </div>
    <div class="row animated fadeIn">
        <ul class=tabs>
            <li>
                <input type=radio name=tabs id=tab1 checked>
                <label for=tab1>
                    <i class="fa fa-file fa-2x" aria-hidden="true"></i>
                    <p class="hidden-xs">Files</p>
                </label>
                <div id=tab-content1 class=tab-content>
                    <div class="dash-line"></div>
                    <div>
                        <div class="col-xs-12">
                            @if ($workspaceApp->getFiles()->count() < 1)
                                <br>
                                <p class="p-l-8">
                                    You haven't uploaded any {{ ucwords($workspaceApp->getScannerApp()->getName()) }}
                                    files yet.
                                    <a href="{{ route('file.create', ['workspaceAppId' => $workspaceApp->getId()]) }}">
                                        Upload one now?
                                    </a>
                                </p>
                            @else
                                @foreach($workspaceApp->getFiles() as $file)
                                    <div class="col-md-4 animated pulse-hover">
                                        <a href="{{ route('file.view', ['fileId' => $file->getId()]) }}">
                                            <div class="content-card">
                                                <h4 class="h-4-1">{{ $file->getName() }}</h4>
                                                <p>{{ $file->getDescription() }}</p>
                                            </div>
                                        </a>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>
            </li>
        </ul>
        <br style=clear:both;>
    </div>
@endsection
