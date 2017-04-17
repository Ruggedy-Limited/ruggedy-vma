@extends('layouts.main')

@section ('breadcrumb')
    {!! Breadcrumbs::render('workspaceApps', $workspace) !!}
    <button type="button" class="btn round-btn pull-right c-grey" data-toggle="modal" data-target="#help">
        <i class="fa fa-question fa-lg" aria-hidden="true"></i>
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
        <ul class=tabs>
            <li>
                <input type=radio name=tabs id=tab1 checked>
                <label for=tab1>
                    <i class="fa fa-file-code-o fa-2x" aria-hidden="true"></i>
                    <p>XML Apps</p>
                </label>
                <div id=tab-content1 class=tab-content>
                    <div class="dash-line"></div>
                    @if (empty($scannerApps))
                        <div>
                            <div>
                                <p class="p-l-8">
                                    No apps were found in this context.<br>
                                    This is unusual, so you should probably contact your support provider for help.
                                </p>
                            </div>
                        </div>
                    @else
                        <div>
                        @foreach ($scannerApps as $scannerApp)
                            <div class="col-md-4 col-sm-6">
                                <a href="{{ route(
                                    'app.create',
                                    ['workspaceId' => $workspaceId, 'scannerAppId' => $scannerApp->getId()]
                                ) }}">
                                    <div class="card hovercard animated pulse-hover">
                                        <div class="cardheader c-white">
                                        </div>
                                        <div class="avatar avatar-img">
                                            <img src="{{ $scannerApp->getLogo() }}">
                                        </div>
                                        <div class="info">
                                            <div class="title h-3">
                                                <h4>{{ ucwords($scannerApp->getName()) }}</h4>
                                            </div>
                                            <div class="desc t-3">
                                                {{ $scannerApp->getDescription() }}
                                            </div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                        </div>
                    @endif
                </div>
            </li>
            <li class="p-l-25">
                <input type=radio name=tabs id=tab2>
                <label for=tab2>
                    <i class="fa fa-exchange fa-2x" aria-hidden="true"></i>
                    <p>API Apss</p>
                </label>
                <div id=tab-content2 class=tab-content>
                    <div class="dash-line"></div>
                    <div>
                    <div class="col-md-4 col-sm-6">
                        <div class="card hovercard animated pulse-hover">
                            <div class="cardheader c-white"></div>
                            <div class="avatar avatar-white">
                                <i class="fa fa-cogs fa-5x t-c-grey"></i>
                            </div>
                            <div class="info">
                                <div class="title h-3">
                                    <h4>Coming Soon</h4>
                                </div>
                                <div class="desc t-3">
                                </div>
                            </div>
                        </div>
                    </div>
                    </div>
                </div>
            </li>
        </ul>
        <br style=clear:both;>
    </div>
@endsection
