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
            <a href="{{ route('folders.show') }}">
                <div class="list-content-card">
                    <i class="fa fa-comments fa-lg pull-right t-c-blue">
                        <small>189</small>
                    </i>
                    <span class="label label-danger">High Risk</span>
                    <span class="label label-primary">Open</span>
                    <h4 class="h-4-1">CVE-2014-6278 bash: code execution via specially crafted environment
                        variables</h4>
                    <p>GNU Bash through 4.3 bash43-026 does not properly parse function definitions in the values of
                        environment variables, which allows remote attackers to execute arbitrary commands via a crafted
                        environment, as demonstrated by vectors involving the ForceCommand feature in OpenSSH sshd, the
                        mod_cgi and mod_cgid modules in the Apache HTTP Server, scripts executed by unspecified DHCP
                        clients, and other situations in which setting the environment occurs across a privilege
                        boundary from Bash execution. NOTE: this vulnerability exists because of an incomplete fix for
                        CVE-2014-6271, CVE-2014-7169, and CVE-2014-6277.</p>
                    <p><i class="fa fa-thumb-tack"></i> Workspace/App/File/Asset</p>
                </div>
            </a>
        </div>
        <div class="col-md-12">
            <a href="#">
                <div class="list-content-card">
                    <i class="fa fa-comments fa-lg pull-right t-c-blue">
                        <small>189</small>
                    </i>
                    <span class="label label-warning">Medium Risk</span>
                    <span class="label label-success">Closed</span>
                    <h4 class="h-4-1">Vulnerability Title</h4>
                    <p>Description of vulnerability</p>
                    <p><i class="fa fa-thumb-tack"></i> Workspace/App/File/Asset</p>
                </div>
            </a>
        </div>
        <div class="col-md-12">
            <a href="#">
                <div class="list-content-card">
                    <i class="fa fa-comments fa-lg pull-right t-c-blue">
                        <small>189</small>
                    </i>
                    <span class="label label-success">Low Risk</span>
                    <span class="label label-default">In Progress</span>
                    <h4 class="h-4-1">Vulnerability Title</h4>
                    <p>Description of vulnerability</p>
                    <p><i class="fa fa-thumb-tack"></i> Workspace/App/File/Asset</p>
                </div>
            </a>
        </div>
        <div class="col-md-12">
            <a href="#">
                <div class="list-content-card">
                    <i class="fa fa-comments fa-lg pull-right t-c-blue">
                        <small>189</small>
                    </i>
                    <span class="label label-primary">Info</span>
                    <span class="label label-default">In Progress</span>
                    <h4 class="h-4-1">Vulnerability Title</h4>
                    <p>Description of vulnerability</p>
                    <p><i class="fa fa-thumb-tack"></i> Workspace/App/File/Asset</p>
                </div>
            </a>
        </div>
    </div>

@endsection