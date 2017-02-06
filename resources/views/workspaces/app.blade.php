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
            <a href="#" class="primary-btn" type="button">Upload File</a>
        </div>
    </div>

    <div class="row animated fadeIn">
        <div class="col-md-4">
            <a href="{{ route('workspaces.appShow') }}">
                <div class="content-card">
                    <h4 class="h-4-1">DMZ scan data</h4>
                    <p>The scan was completed on 17 January 2017.</p>
                </div>
            </a>
        </div>
    </div>

@endsection
