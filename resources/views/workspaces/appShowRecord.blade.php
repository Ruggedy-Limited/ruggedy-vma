@extends('layouts.main')

@section ('breadcrumb')
    <p>Breadcrumbs / Goes / Here
        <button type="button" class="btn round-btn pull-right c-grey" data-toggle="modal" data-target="#help">
            <i class="fa fa-question fa-lg" aria-hidden="true"></i>
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
    <!-- JIRA -->
    <div id="jira" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Send to JIRA</h4>
                </div>
                <div class="modal-body">
                    {!! Form::open(['url' => '/foo/bar']) !!}
                    <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('name', 'User Name') !!}
                        {!! Form::text('name', null, ['class' => 'black-form-control']) !!}
                    </div>
                        <div class="form-group">
                            {!! Form::label('name', 'Password') !!}
                            {!! Form::password('name', null, ['class' => 'black-form-control']) !!}
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            {!! Form::label('name', 'JIRA Project ID') !!}
                            {!! Form::text('name', null, ['class' => 'black-form-control']) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('name', 'Host') !!}
                            {!! Form::text('name', null, ['class' => 'black-form-control']) !!}
                        </div>
                        <div class="form-group">
                            {!! Form::label('name', 'Port') !!}
                            {!! Form::text('name', null, ['class' => 'black-form-control']) !!}
                        </div>
                    </div>
                    <button class="primary-btn" type="submit">Submit</button>
                    {!! Form::close() !!}
                </div>
            </div>

        </div>
    </div>
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
                    {!! Form::open(['url' => '/foo/bar']) !!}
                        <div class="form-group col-md-12">
                            {!! Form::select('folder', ['1' => 'Folder One', '2' => 'Folder Two']) !!}
                        </div>
                    <button class="primary-btn" type="submit">Submit</button>
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

    <div class="row animated fadeIn">
        <ul class=tabs>
            <li>
                <input type=radio name=tabs id=tab1 checked>
                <label for=tab1>Vulnerability</label>
                <div id=tab-content1 class=tab-content>
                    <div class="dash-line"></div>
                    <div class="col-md-12">
                        <div class="list-content-card">
                            <span class="label label-danger t-s-10">High Risk</span>
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
                <label for=tab3>Vulnerable Assets <span class="badge c-purple">5</span></label>
                <div id=tab-content3 class=tab-content>
                    <div class="dash-line"></div>
                    <div class="col-md-3">
                        <a href="#">
                            <div class="list-content-card animated pulse-hover">
                                <span class="label label-danger">12</span>
                                <span class="label label-warning">24</span>
                                <span class="label label-success">3</span>
                                <span class="label label-info">10</span>
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
                                <span class="label label-info">10</span>
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
                                <span class="label label-info">10</span>
                                <h4 class="h-4-1">Host Name</h4>
                                <h5>IP Address</h5>
                            </div>
                        </a>
                    </div>
                </div>
            </li>
            <li>
                <input type=radio name=tabs id=tab4>
                <label for=tab4>Comments <span class="badge c-purple">3</span></label>
                <div id=tab-content4 class=tab-content>
                    <div class="dash-line"></div>
                    <br>
                    <div class="col-md-12">
                        <div>
                            <textarea class="post-form-control" rows="1" placeholder="Type your comment here..."></textarea>
                            <span class="pull-left">
                                <button class="primary-btn" id="btn-chat">Post</button>
                                </span>
                        </div>
                        <div class="chat-card">
                            <div>
                                <ul class="chat">
                                    <li>
                                        <div class="chat-body">
                                            <div class="header">
                                                <strong class="primary-font">User Name</strong>
                                                <p class="text-muted">
                                                    <small class=" text-muted"><span class="fa fa-clock-o"></span>13
                                                        mins
                                                        ago
                                                    </small>
                                                </p>
                                            </div>
                                            <p>
                                                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur
                                                bibendum ornare
                                                dolor, quis ullamcorper ligula sodales. aasdfs fdsfsd fs dfdsfds hgjgjgjjghjgjgj
                                            </p>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="chat-body">
                                            <div class="header">
                                                <strong class="primary-font">User Name</strong>
                                                <p class="text-muted">
                                                    <small class=" text-muted"><span class="fa fa-clock-o"></span>13
                                                        mins
                                                        ago
                                                    </small>
                                                </p>
                                            </div>
                                            <p>
                                                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur
                                                bibendum ornare
                                                dolor, quis ullamcorper ligula sodales. aasdfs fdsfsd fs dfdsfds hgjgjgjjghjgjgj
                                            </p>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="chat-body">
                                            <div class="header">
                                                <strong class="primary-font">User Name</strong>
                                                <p class="text-muted">
                                                    <small class=" text-muted"><span class="fa fa-clock-o"></span>13
                                                        mins
                                                        ago
                                                    </small>
                                                </p>
                                            </div>
                                            <p>
                                                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur
                                                bibendum ornare
                                                dolor, quis ullamcorper ligula sodales. aasdfs fdsfsd fs dfdsfds hgjgjgjjghjgjgj
                                            </p>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="chat-body">
                                            <div class="header">
                                                <strong class="primary-font">User Name</strong>
                                                <p class="text-muted">
                                                    <small class=" text-muted"><span class="fa fa-clock-o"></span>13
                                                        mins
                                                        ago
                                                    </small>
                                                </p>
                                            </div>
                                            <p>
                                                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur
                                                bibendum ornare
                                                dolor, quis ullamcorper ligula sodales. aasdfs fdsfsd fs dfdsfds hgjgjgjjghjgjgj
                                            </p>
                                        </div>
                                    </li>
                                    <li>
                                        <div class="chat-body">
                                            <div class="header">
                                                <strong class="primary-font">User Name</strong>
                                                <p class="text-muted">
                                                    <small class=" text-muted"><span class="fa fa-clock-o"></span>13
                                                        mins
                                                        ago
                                                    </small>
                                                </p>
                                            </div>
                                            <p>
                                                Lorem ipsum dolor sit amet, consectetur adipiscing elit. Curabitur
                                                bibendum ornare
                                                dolor, quis ullamcorper ligula sodales. aasdfs fdsfsd fs dfdsfds hgjgjgjjghjgjgj
                                            </p>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
        </ul>
        <br style=clear:both;>
    </div>

@endsection
