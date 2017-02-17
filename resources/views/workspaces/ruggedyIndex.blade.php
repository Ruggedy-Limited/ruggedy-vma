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
            <a href="{{ route('workspaces.ruggedyCreate') }}" class="primary-btn" type="button">Add Vulnerability</a>
        </div>
    </div>

    <div class="row animated fadeIn">
        <div class="col-md-12">
            <a href="{{ route('workspaces.ruggedyShow') }}">
                <div class="list-content-card">
                    <p><span class="label label-danger t-s-10">High Risk</span>
                        &nbsp;Manual security finding 1.
                    </p>
                </div>
            </a>
        </div>
        <div class="col-md-12">
            <a href="{{ route('workspaces.ruggedyShow') }}">
                <div class="list-content-card">
                    <p><span class="label label-danger" style="font-size: 10px;">High Risk</span>
                        &nbsp;Manual security finding 2.
                    </p>
                </div>
            </a>
        </div>
        <div class="col-md-12">
            <a href="{{ route('workspaces.ruggedyShow') }}">
                <div class="list-content-card">
                    <p><span class="label label-danger" style="font-size: 10px;">High Risk</span>
                        &nbsp;Manual security finding 3.
                    </p>
                </div>
            </a>
        </div>
    </div>

@endsection
