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
            <a href="{{ route('settings.users.create') }}" class="primary-btn" type="button">Add User</a>
        </div>
    </div>

    <div class="row animated fadeIn">
        <ul class=tabs>
            <li>
                <input type=radio name=tabs id=tab1 checked>
                <label for=tab1>Settings</label>
                <div id=tab-content1 class=tab-content>
                    <div class="col-md-4">
                        <br>
                        {!! Form::open(['url' => '/foo/bar']) !!}
                        <div class="form-group">
                            {!! Form::label('name', 'Company Name', ['class' => 'tabs-label']) !!}
                            {!! Form::text('name', null, ['class' => 'black-form-control']) !!}
                        </div>
                        <a href="#" class="border-btn" type="button">Cancel</a>
                        <button class="primary-btn" type="submit">Submit</button>
                        {!! Form::close() !!}
                    </div>
                    <div class="col-md-2"></div>
                    <div class="col-md-6 animated fadeInUp">

                    </div>
                </div>
            </li>
            <li>
                <input type=radio name=tabs id=tab2>
                <label for=tab2>Users</label>
                <div id=tab-content2 class=tab-content>
                    <div class="col-md-4">
                        <a href="#">
                            <div class="content-card animated pulse-hover">
                                <h4 class="h-4-1"><i class="fa fa-user"></i> User Name</h4>
                                <p>email@address.com</p>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="#">
                            <div class="content-card animated pulse-hover">
                                <h4 class="h-4-1"><i class="fa fa-user"></i> User Name</h4>
                                <p>email@address.com</p>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="#">
                            <div class="content-card animated pulse-hover">
                                <h4 class="h-4-1"><i class="fa fa-user"></i> User Name</h4>
                                <p>email@address.com</p>
                            </div>
                        </a>
                    </div>
                </div>
            </li>
        </ul>
        <br style=clear:both;>
    </div>

@endsection
