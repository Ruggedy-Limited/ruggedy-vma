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
                <div class="modal-footer">
                    <button type="button" class="primary-btn" data-dismiss="modal">Close</button>
                </div>
            </div>

        </div>
    </div>

    <div class="row animated fadeIn">
        <div class="col-md-12">
            <a href="#" class="primary-btn" type="button">Send to JIRA</a>
            <a href="#" class="primary-btn" type="button">Add to Folder</a>
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
                            <span class="label label-danger">High Risk</span>
                            <span></span>
                            <h4 class="h-4-1">CVE-2014-6278 bash: code execution via specially crafted environment
                                variables</h4>
                            <p>GNU Bash through 4.3 bash43-026 does not properly parse function definitions in the
                                values of
                                environment variables, which allows remote attackers to execute arbitrary commands via a
                                crafted
                                environment, as demonstrated by vectors involving the ForceCommand feature in OpenSSH
                                sshd, the
                                mod_cgi and mod_cgid modules in the Apache HTTP Server, scripts executed by unspecified
                                DHCP
                                clients, and other situations in which setting the environment occurs across a privilege
                                boundary from Bash execution. NOTE: this vulnerability exists because of an incomplete
                                fix for
                                CVE-2014-6271, CVE-2014-7169, and CVE-2014-6277.</p>
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
                        <div class="list-content-card">
                            <p>Use your operating system's package manager to upgrade GNU bash to the latest
                                version.</p>
                        </div>
                    </div>
                </div>
            </li>
            <li>
                <input type=radio name=tabs id=tab3>
                <label for=tab3>Vulnerable Assets</label>
                <div id=tab-content3 class=tab-content>
                    <div class="dash-line"></div>
                    <div class="col-md-3">
                        <a href="#">
                            <div class="list-content-card animated pulse-hover">
                                <span class="label label-danger">12</span>
                                <span class="label label-warning">24</span>
                                <span class="label label-success">3</span>
                                <h4 class="h-4-1">Host Name</h4>
                                <h5>IP Address</h5>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="#">
                            <div class="list-content-card animated pulse-hover">
                                <span class="label label-danger">12</span>
                                <span class="label label-warning">24</span>
                                <span class="label label-success">3</span>
                                <h4 class="h-4-1">Host Name</h4>
                                <h5>IP Address</h5>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-3">
                        <a href="#">
                            <div class="list-content-card animated pulse-hover">
                                <span class="label label-danger">12</span>
                                <span class="label label-warning">24</span>
                                <span class="label label-success">3</span>
                                <h4 class="h-4-1">Host Name</h4>
                                <h5>IP Address</h5>
                            </div>
                        </a>
                    </div>
                </div>
            </li>
        </ul>
        <br style=clear:both;>
    </div>

@endsection
