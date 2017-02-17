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
        <ul class=tabs>
            <li>
                <input type=radio name=tabs id=tab1 checked>
                <label for=tab1>Vulnerabilities <span class="badge c-purple">48</span></label>
                <div id=tab-content1 class=tab-content>
                    <div class="dash-line"></div>
                    <div class="col-md-12">
                        <a href="{{ route('workspaces.appShowRecord') }}">
                            <div class="list-content-card">
                                <p><span class="label label-danger t-s-10">High Risk</span>
                                    <span class="badge c-purple">5</span>
                                    &nbsp;CVE-2014-6278 bash: code execution via specially crafted environment variables
                                </p>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-12">
                        <a href="{{ route('workspaces.appShowRecord') }}">
                            <div class="list-content-card">
                                <p><span class="label label-danger" style="font-size: 10px;">High Risk</span>
                                    <span class="badge c-purple">5</span>
                                    &nbsp;CVE-2014-6278 bash: code execution via specially crafted environment variables
                                </p>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-12">
                        <a href="{{ route('workspaces.appShowRecord') }}">
                            <div class="list-content-card">
                                <p><span class="label label-danger" style="font-size: 10px;">High Risk</span>
                                    <span class="badge c-purple">5</span>
                                    &nbsp;CVE-2014-6278 bash: code execution via specially crafted environment variables
                                </p>
                            </div>
                        </a>
                    </div>
                </div>
            </li>
            <li>
                <input type=radio name=tabs id=tab2>
                <label for=tab2>Assets <span class="badge c-purple">3</span></label>
                <div id=tab-content2 class=tab-content>
                    <div class="dash-line"></div>
                    <div class="col-md-3">
                        <a href="#">
                            <div class="list-content-card animated pulse-hover">
                                <span class="label label-danger">12</span>
                                <span class="label label-warning">24</span>
                                <span class="label label-success">3</span>
                                <span class="label label-info">10</span>
                                <h4 class="h-4-1">Host Name</h4>
                                <p>IP Address</p>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="#">
                            <div class="list-content-card animated pulse-hover">
                                <span class="label label-danger">12</span>
                                <span class="label label-warning">24</span>
                                <span class="label label-success">3</span>
                                <span class="label label-info">10</span>
                                <h4 class="h-4-1">Host Name</h4>
                                <p>IP Address</p>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="#">
                            <div class="list-content-card animated pulse-hover">
                                <span class="label label-danger">12</span>
                                <span class="label label-warning">24</span>
                                <span class="label label-success">3</span>
                                <span class="label label-info">10</span>
                                <h4 class="h-4-1">Host Name</h4>
                                <p>IP Address</p>
                            </div>
                        </a>
                    </div>
                </div>
            </li>
        </ul>
        <br style=clear:both;>
    </div>

@endsection
