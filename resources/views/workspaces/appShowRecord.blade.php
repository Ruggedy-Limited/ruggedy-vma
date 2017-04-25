@extends('layouts.main')

@section ('breadcrumb')
    <button type="button" class="btn round-btn pull-right c-grey" data-toggle="modal" data-target="#help">
        <i class="fa fa-question fa-lg" aria-hidden="true"></i>
    </button>
    @if ($vulnerability->getFile()->getWorkspaceApp()->isRuggedyApp())
        <a href="{{ route('ruggedy-app.view', [
            $vulnerability->getFile()->getRouteParameterName() => $vulnerability->getFile()->getId()
        ]) }}">
    @else
        <a href="{{ route('file.view', [
            $vulnerability->getFile()->getRouteParameterName() => $vulnerability->getFile()->getId()
        ]) }}">
    @endif
        <button type="button" class="btn round-btn pull-right c-yellow">
            <i class="fa fa-times fa-lg" aria-hidden="true"></i>
        </button>
    </a>
    {!! Breadcrumbs::render('dynamic', $vulnerability) !!}
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
            @if (!empty($folders))
                @can (App\Policies\ComponentPolicy::ACTION_EDIT, $vulnerability)
                    <a href="#" class="primary-btn" type="button" data-toggle="modal" data-target="#folder">
                        Add to Folder
                    </a>
                @endcan
            @endif
        </div>
    </div>

    <div class="row animated fadeIn">
        <ul class=tabs>
            <li>
                <input type=radio name=tabs id=tab1 checked>
                <label for=tab1>Vulnerability</label>
                <div id=tab-content1 class=tab-content>
                    <div class="dash-line"></div>
                    <div class="col-md-12">
                        <div class="list-content-card">
                            <span class="label label-{{ $vulnerability->getBootstrapAlertCssClass() }} t-s-10">
                                {{ $vulnerability->getSeverityText() }} Risk
                            </span>
                            <h4 class="h-4-1">{{ $vulnerability->getName() }}</h4>
                            {!! $vulnerability->getDescription() !!}
                        </div>
                    </div>
                </div>
            </li>
            <li>
                <input type=radio name=tabs id=tab2>
                <label for=tab2>Solution</label>
                <div id=tab-content2 class=tab-content>
                    <div class="dash-line"></div>
                    <div class="col-md-12">
                        <div class="content-card">
                            {!! $vulnerability->getSolution() ?? '<p>No solution available at present.</p>' !!}
                        </div>
                    </div>
                </div>
            </li>
            @if (!$vulnerability->getVulnerabilityHttpData()->isEmpty())
                <li>
                    <input type=radio name=tabs id=tab3>
                    <label for=tab3>Vulnerable URLs <span class="badge c-purple">{{ $vulnerability->getVulnerabilityHttpData()->count() }}</span></label>
                    <div id=tab-content3 class=tab-content>
                        <div class="dash-line"></div>
                        <div class="col-md-12">
                            @foreach ($vulnerability->getVulnerabilityHttpData() as $httpData)
                                @include('partials.http-data')
                            @endforeach
                        </div>
                    </div>
                </li>
            @endif
            <li>
                <input type=radio name=tabs id=tab4>
                <label for=tab4>Vulnerable Assets <span class="badge c-purple">{{ $assets->count() }}</span></label>
                <div id=tab-content4 class=tab-content>
                    <div class="dash-line"></div>
                    @include('partials.assets')
                </div>
            </li>
            <li>
                <input type=radio name=tabs id=tab5>
                <label for=tab5>Comments <span id="comment-count" class="badge c-purple">{{ $comments->count() }}</span></label>
                <div id=tab-content5 class=tab-content>
                    <div class="dash-line"></div>
                    <br>
                    @include('partials.comments')
                </div>
            </li>
        </ul>
        <br style=clear:both;>
    </div>

@endsection
