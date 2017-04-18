@extends('layouts.main')

@section ('breadcrumb')
    {!! Breadcrumbs::render('dynamic', $vulnerability) !!}
    <button type="button" class="btn round-btn pull-right c-grey" data-toggle="modal" data-target="#help">
        <i class="fa fa-question fa-lg" aria-hidden="true"></i>
    </button>
    <button type="button" class="btn round-btn pull-right c-yellow">
        <i class="fa fa-times fa-lg" aria-hidden="true"></i>
    </button>
@endsection

@section('content')

    @include('layouts.formError')

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
    <!-- JIRA -->
    @include('partials.jira-form')
    <!-- Folder -->
    <div id="folder" class="modal fade" role="dialog">
        <div class="modal-dialog">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Add to Folder</h4>
                </div>
                <div class="modal-body">
                    {!! Form::open([
                        'url' => route('vulnerability.folder.add', [
                            'vulnerabilityId' => $vulnerability->getId()
                        ])
                    ]) !!}
                        <div class="form-group col-md-12">
                            {!! Form::select('folder-id', $folders) !!}
                        </div>
                        <button class="primary-btn" type="submit">Add to Folder</button>
                    {!! Form::close() !!}
                </div>
            </div>

        </div>
    </div>
    <div class="row animated fadeIn">
        <div class="col-md-12">
            <a href="#" class="primary-btn" type="button" data-toggle="modal" data-target="#jira">Send to JIRA</a>
            <a href="#" class="primary-btn" type="button" data-toggle="modal" data-target="#folder">Add to Folder</a>
        </div>
    </div>

    <div class="row animated fadeIn {{ $vulnerability->getFile()->getWorkspaceApp()->getScannerApp()->getName() }}">
        <ul class=tabs>
            <li>
                <input type=radio name=tabs id=tab1 checked>
                <label for=tab1>
                    <i class="fa fa-bomb fa-2x" aria-hidden="true"></i>
                    <p class="hidden-xs">Vulnerability</p>
                </label>
                <div id=tab-content1 class=tab-content>
                    <div class="dash-line"></div>
                    <div class="col-md-12">
                        <div class="m-t-15">
                            <div class="t-s-14 label label-{{ $vulnerability->getBootstrapAlertCssClass() }} t-s-10">
                                {{ $vulnerability->getSeverityText() }} Risk
                            </div>
                        </div>
                    </div>
                    <div class="col-md-12 m-t-15">
                        <div class="single-content-card">
                        <p><i class="fa fa-book" aria-hidden="true"></i>
                            <span>Reference: </span>
                        </p>
                        <p><i class="fa fa-location-arrow" aria-hidden="true"></i>
                        <span>URL Path: </span>
                        </p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="content-card">
                            <h4>{{ $vulnerability->getName() }}</h4>
                            {!! $vulnerability->getDescription() !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="content-card">
                            <h4>Known Exploits</h4>
                            <p>List of known exploits to go here.</p>
                        </div>
                    </div>
                </div>
            </li>
            <li class="p-l-25">
                <input type=radio name=tabs id=tab2>
                <label for=tab2>
                    <i class="fa fa-thumbs-up fa-2x" aria-hidden="true"></i>
                    <p class="hidden-xs">Solution</p>
                </label>
                <div id=tab-content2 class=tab-content>
                    <div class="dash-line"></div>
                    <div class="col-md-6">
                        <div class="content-card solution">
                            {!! $vulnerability->getSolution() ?? '<p>No solution available at present.</p>' !!}
                        </div>
                    </div>
                </div>
            </li>
            <li class="p-l-25">
                <input type=radio name=tabs id=tab3>
                <label for=tab3>
                    <i class="fa fa-server fa-2x" aria-hidden="true"></i>
                    <span id="comment-count" class="label-count c-grey visible-xs pull-right">{{ $assets->count() }}</span>
                    <p class="hidden-xs">Vulnerable Assets<span class="label-count c-grey">{{ $assets->count() }}</span></p>
                    </label>
                <div id=tab-content3 class=tab-content>
                    <div class="dash-line"></div>
                    @include('partials.assets')
                </div>
            </li>
            <li class="p-l-25">
                <input type=radio name=tabs id=tab4>
                <label for=tab4>
                    <i class="fa fa-comments fa-2x" aria-hidden="true"></i>
                    <span id="comment-count" class="label-count c-grey visible-xs pull-right">{{ $comments->count() }}</span>
                    <p class="hidden-xs">Comments<span id="comment-count" class="label-count c-grey">{{ $comments->count() }}</span></p>
                </label>
                <div id=tab-content4 class=tab-content>
                    <div class="dash-line"></div>
                    <br>
                    @include('partials.comments')
                </div>
            </li>
        </ul>
        <br style=clear:both;>
    </div>

@endsection
