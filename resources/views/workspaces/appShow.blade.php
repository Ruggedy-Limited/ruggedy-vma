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