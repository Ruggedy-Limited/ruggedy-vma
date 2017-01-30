@extends('layouts.main')

@section('content')

    <h5>Breadcrumbs / Goes / Here </h5>

    <div class="row animated fadeIn">
        <div class="col-md-12">
            <a href="#" class="primary-btn" type="button">Add Application</a>
            <a href="{{ route('folders.create') }}" class="primary-btn" type="button">Add Folder</a>
        </div>
    </div>

    <div class="row animated fadeIn">
        <ul class=tabs>
            <li>
                <input type=radio name=tabs id=tab1 checked>
                <label for=tab1>Apps</label>
                <div id=tab-content1 class=tab-content>
                    <div class="col-md-4">
                        <div class="card hovercard animated pulse-hover">
                            <div class="cardheader c-white">
                            </div>
                            <div class="avatar avatar-white">
                                <img src="/img/nessus-logo.png">
                            </div>
                            <div class="info">
                                <div class="title h-3">
                                    <h4>DMZ Nessus Scan</h4>
                                </div>
                                <div class="desc t-3">DMZ scan completed on 18 November 2016.
                                </div>
                                <div class="adm-card-footer">
                                    <div class="pull-right">
                                        <i class="fa fa-pencil"></i>
                                        <i class="fa fa-trash p-l-8"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
            <li>
                <input type=radio name=tabs id=tab2>
                <label for=tab2>Folders</label>
                <div id=tab-content2 class=tab-content>
                    <div class="col-md-4">
                        <div class="card hovercard animated pulse-hover">
                            <div class="cardheader c-white"></div>
                            <div class="avatar avatar-white">
                                <i class="fa fa-folder fa-5x t-c-red"></i>
                            </div>
                            <div class="info">
                                <div class="title h-3">
                                    <h4>Folder Card</h4>
                                </div>
                                <div class="desc t-3">Pellentesque lacinia sagittis libero. Praesent vitae justo purus.
                                    In hendrerit
                                    lorem nisl,
                                    ac lacinia urna aliquet non. Quisque nisi tellus, rhoncus quis est s, rhoncus quis
                                    est s,
                                    rhoncus quis est s, rhoncus quis est s, rhoncus quis est s, rhoncus quis est
                                </div>
                                <div class="adm-card-footer">
                                    <div class="pull-right">
                                        <i class="fa fa-pencil"></i>
                                        <i class="fa fa-trash p-l-8"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </li>
        </ul>
        <br style=clear:both;>
    </div>

@endsection
