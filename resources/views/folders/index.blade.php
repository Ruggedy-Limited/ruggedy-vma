@extends('layouts.main')

@section('content')
    <div class="animated fadeIn">
        <h5>Breadcrumbs / Goes / Here
            <a data-toggle="modal" data-target="#help">
                <i class="fa fa-question-circle fa-2x pull-right" aria-hidden="true"></i></a>
        </h5>
    </div>
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
                <div class="modal-footer">
                    <button type="button" class="primary-btn" data-dismiss="modal">Close</button>
                </div>
            </div>

        </div>
    </div>
    <div class="row animated fadeIn">
        <div class="col-md-12">
            <a href="#" class="edit-btn" type="button">Edit Folder</a>
            <a href="#" class="delete-btn" type="button">Delete Folder</a>
        </div>
    </div>

    <div class="row animated fadeIn">
        <div class="col-md-12">
            <a href="{{ route('folders.show') }}">
                <div class="list-content-card">
                    <i class="fa fa-comments fa-lg pull-right t-c-yellow">
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
                    <i class="fa fa-comments fa-lg pull-right t-c-yellow">
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
                    <i class="fa fa-comments fa-lg pull-right t-c-yellow">
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
                    <i class="fa fa-comments fa-lg pull-right t-c-yellow">
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