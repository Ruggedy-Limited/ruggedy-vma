@extends('layouts.main')

@section ('breadcrumb')
    <p>Breadcrumbs / Goes / Here
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
            <a href="{{ route('workspaces.apps') }}" class="primary-btn" type="button">Add Application</a>
            <a href="{{ route('folders.create') }}" class="primary-btn" type="button">Add Folder</a>
        </div>
    </div>

    <div class="row animated fadeIn">
        <ul class=tabs>
            <li>
                <input type=radio name=tabs id=tab1 checked>
                <label for=tab1>
                    <i class="fa fa-th fa-2x" aria-hidden="true"></i>
                    <p class="hidden-xs">Apps</p>
                </label>
                <div id="tab-content1" class="tab-content">
                    <div class="dash-line"></div>
                    <div class="col-md-4 col-sm-6">
                        <a href="{{ route('workspaces.app') }}">
                        <div class="card hovercard animated pulse-hover">
                            <div class="cardheader c-white">
                            </div>
                            <div class="avatar avatar-white">
                                <img src="/img/nessus-logo.png">
                            </div>
                            <div class="info">
                                <div class="title h-3">
                                    <h4>DMZ Nessus Scan</h4>
                                </div>
                                <div class="desc t-3">DMZ scan completed on 18 November 2016.
                                </div>
                            </div>
                        </div>
                        </a>
                    </div>
                    <div class="col-md-4 col-sm-6">
                        <a href="{{ route('workspaces.ruggedyIndex') }}">
                            <div class="card hovercard animated pulse-hover">
                                <div class="cardheader c-white">
                                </div>
                                <div class="avatar avatar-white">
                                    <img src="/img/ruggedy-logo.png">
                                </div>
                                <div class="info">
                                    <div class="title h-3">
                                        <h4>DMZ Firewall - Pen Test</h4>
                                    </div>
                                    <div class="desc t-3">Security review of all DMZ Firewalls.
                                    </div>
                                </div>
                            </div>
                        </a>
                    </div>
                </div>
            </li>
            <li>
                <input type=radio name=tabs id=tab2>
                <label for=tab2>
                    <i class="fa fa-folder fa-2x" aria-hidden="true"></i>
                    <p class="hidden-xs">Folders</p>
                </label>
                <div id=tab-content2 class=tab-content>
                    <div class="dash-line"></div>
                    <div class="col-md-4 col-sm-6">
                        <a href="{{ route('folders.index') }}">
                        <div class="card hovercard animated pulse-hover">
                            <div class="cardheader c-white"></div>
                            <div class="avatar avatar-white">
                                <i class="fa fa-folder fa-5x t-c-purple"></i>
                            </div>
                            <div class="info">
                                <div class="title h-3">
                                    <h4>Folder Card</h4>
                                </div>
                                <div class="desc t-3">Pellentesque lacinia sagittis libero. Praesent vitae justo purus.
                                    In hendrerit
                                    lorem nisl,
                                    ac lacinia urna aliquet non. Quisque nisi tellus, rhoncus quis est s, rhoncus quis
                                    est s,
                                    rhoncus quis est s, rhoncus quis est s, rhoncus quis est s, rhoncus quis est
                                </div>
                            </div>
                        </div>
                        </a>
                    </div>
                </div>
            </li>
        </ul>
        <br style=clear:both;>
    </div>

@endsection
