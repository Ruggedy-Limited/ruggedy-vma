@extends('layouts.main')

@section ('breadcrumb')
    <button type="button" class="btn round-btn pull-right c-grey" data-toggle="modal" data-target="#help">
        <i class="fa fa-question fa-lg" aria-hidden="true"></i>
    </button>
    <button type="button" class="btn round-btn pull-right c-yellow">
        <i class="fa fa-times fa-lg" aria-hidden="true"></i>
    </button>
    {!! Breadcrumbs::render('workspaceApps', $workspace) !!}
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
                <label for=tab1>XML Apps</label>
                <div id=tab-content1 class=tab-content>
                    <div class="dash-line"></div>
                    @if (empty($scannerApps))
                        <div class="container">
                            <div class="col-xs-12">
                                <p>
                                    No apps were found in this context.<br>
                                    This is unusual, so you should probably contact your support provider for help.
                                </p>
                            </div>
                        </div>
                    @else
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
                    @endif
                </div>
            </li>
            <li>
                <input type=radio name=tabs id=tab2>
                <label for=tab2>API Apps</label>
                <div id=tab-content2 class=tab-content>
                    <div class="dash-line"></div>
                    <div class="col-md-4 col-sm-6">
                        <div class="card hovercard animated pulse-hover">
                            <div class="cardheader c-white"></div>
                            <div class="avatar avatar-white">
                                <i class="fa fa-cogs fa-5x t-c-red"></i>
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
            </li>
        </ul>
        <br style=clear:both;>
    </div>
@endsection
