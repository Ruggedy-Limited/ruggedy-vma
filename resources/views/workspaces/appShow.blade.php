@extends('layouts.main')

@section ('breadcrumb')
    <p>Breadcrumbs / Goes / Here
        <a data-toggle="modal" data-target="#help">
            <i class="fa fa-question-circle fa-2x pull-right" aria-hidden="true"></i></a>
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
            <a href="#" class="border-btn" type="button">Cancel</a>
            <a href="#" class="edit-btn" type="button">Edit File</a>
            <a href="#" class="delete-btn" type="button">Delete File</a>
        </div>
    </div>

    <div class="row animated fadeIn">
        <ul class=tabs>
            <li>
                <input type=radio name=tabs id=tab1 checked>
                <label for=tab1>Vulnerabilities</label>
                <div id=tab-content1 class=tab-content>
                </div>
            </li>
            <li>
                <input type=radio name=tabs id=tab2>
                <label for=tab2>Assets</label>
                <div id=tab-content2 class=tab-content>
                </div>
            </li>
        </ul>
        <br style=clear:both;>
    </div>

@endsection
