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

    @include('layouts.formError')

    <!-- Modal -->
    <div id="help" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Help Title</h4>
                </div>
                <div class="modal-body">
                    <p>Help text goes here.</p>
                </div>
            </div>

        </div>
    </div>

    <div class="row animated fadeIn">
        <div class="col-md-12">
            <a href="{{ route('settings.user.create') }}" class="primary-btn" type="button">Add User</a>
        </div>
    </div>

    <div class="row animated fadeIn">
        <ul class=tabs>
            <li>
                <input type=radio name=tabs id=tab1 checked>
                <label for=tab1>Users</label>
                <div id=tab-content1 class=tab-content>
                    <div class="dash-line"></div>
                    @if (empty($users))
                        <p>
                            No Users have been created yet.
                            <a href="{{ route('settings.user.create') }}">Add a User</a>.
                        </p>
                    @else
                        @foreach ($users as $user)
                            <div class="col-md-4 col-sm-6">
                                <a href="{{ route('settings.users.edit') }}">
                                    <div class="content-card animated pulse-hover">
                                        <h4>{{ $user->getName() }}</h4>
                                        <p>{{ $user->getEmail() }}</p>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    @endif
                </div>
            </li>
        </ul>
        <br style=clear:both;>
    </div>

@endsection
