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
            <a href={{ route('workspaces.create') }} class="primary-btn" type="button">Add Workspace</a>
        </div>
    </div>

    <div class="row animated fadeIn">
        <div class="col-md-4">
            <a href="{{ route('workspaces.index') }}">
            <div class="card hovercard animated pulse-hover">
                <div class="cardheader c-white">
                </div>
                <div class="avatar avatar-white">
                    <i class="fa fa-th-large fa-5x t-c-red"></i>
                </div>
                <div class="info">
                    <div class="title h-3">
                        <h4>Workspace Card</h4>
                    </div>
                    <div class="desc t-3">Pellentesque lacinia sagittis libero. Praesent vitae justo purus. In hendrerit
                        lorem nisl,
                        ac lacinia urna aliquet non. Quisque nisi tellus, rhoncus quis est s, rhoncus quis est s,
                        rhoncus quis est s, rhoncus quis est s, rhoncus quis est s, rhoncus quis est
                    </div>
                </div>
            </div>
            </a>
        </div>
    </div>

@endsection
