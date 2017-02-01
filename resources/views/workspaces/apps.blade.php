@extends('layouts.main')

@section('content')

    <h5>Breadcrumbs / Goes / Here </h5>

    <div class="row animated fadeIn">
        <div class="col-md-12">
            <a href="#" class="border-btn" type="button">Cancel</a>
        </div>
    </div>

    <div class="row animated fadeIn">
        <ul class=tabs>
            <li>
                <input type=radio name=tabs id=tab1 checked>
                <label for=tab1>XML Apps</label>
                <div id=tab-content1 class=tab-content>
                    <div class="col-md-4">
                        <a href="{{ route('workspaces.apps.create') }}">
                        <div class="card hovercard animated pulse-hover">
                            <div class="cardheader c-white">
                            </div>
                            <div class="avatar avatar-img">
                                <img src="/img/nessus-logo.png">
                            </div>
                            <div class="info">
                                <div class="title h-3">
                                    <h4>Nessus Scanner</h4>
                                </div>
                                <div class="desc t-3">Pellentesque lacinia sagittis libero. Praesent vitae justo purus.
                                    In hendrerit
                                    lorem nisl,
                                    ac lacinia urna aliquet non. Quisque nisi tellus, rhoncus quis est s, rhoncus quis
                                    est s,
                                    rhoncus quis est s, rhoncus quis est s, rhoncus quis est s, rhoncus quis est
                                </div>
                            </div>
                        </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ route('workspaces.apps.create') }}">
                        <div class="card hovercard animated pulse-hover">
                            <div class="cardheader c-white">
                            </div>
                            <div class="avatar avatar-img">
                                <img src="/img/nmap-logo.png">
                            </div>
                            <div class="info">
                                <div class="title h-3">
                                    <h4>NMAP Scanner</h4>
                                </div>
                                <div class="desc t-3">Pellentesque lacinia sagittis libero. Praesent vitae justo purus.
                                    In hendrerit
                                    lorem nisl,
                                    ac lacinia urna aliquet non. Quisque nisi tellus, rhoncus quis est s, rhoncus quis
                                    est s,
                                    rhoncus quis est s, rhoncus quis est s, rhoncus quis est s, rhoncus quis est
                                </div>
                            </div>
                        </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ route('workspaces.apps.create') }}">
                        <div class="card hovercard animated pulse-hover">
                            <div class="cardheader c-white">
                            </div>
                            <div class="avatar avatar-img">
                                <img src="/img/burp-logo.png">
                            </div>
                            <div class="info">
                                <div class="title h-3">
                                    <h4>Burp Scanner</h4>
                                </div>
                                <div class="desc t-3">Pellentesque lacinia sagittis libero. Praesent vitae justo purus.
                                    In hendrerit
                                    lorem nisl,
                                    ac lacinia urna aliquet non. Quisque nisi tellus, rhoncus quis est s, rhoncus quis
                                    est s,
                                    rhoncus quis est s, rhoncus quis est s, rhoncus quis est s, rhoncus quis est
                                </div>
                            </div>
                        </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ route('workspaces.apps.create') }}">
                        <div class="card hovercard animated pulse-hover">
                            <div class="cardheader c-white">
                            </div>
                            <div class="avatar avatar-img">
                                <img src="/img/netsparker-logo.png">
                            </div>
                            <div class="info">
                                <div class="title h-3">
                                    <h4>Netsparker Scanner</h4>
                                </div>
                                <div class="desc t-3">Pellentesque lacinia sagittis libero. Praesent vitae justo purus.
                                    In hendrerit
                                    lorem nisl,
                                    ac lacinia urna aliquet non. Quisque nisi tellus, rhoncus quis est s, rhoncus quis
                                    est s,
                                    rhoncus quis est s, rhoncus quis est s, rhoncus quis est s, rhoncus quis est
                                </div>
                            </div>
                        </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ route('workspaces.apps.create') }}">
                        <div class="card hovercard animated pulse-hover">
                            <div class="cardheader c-white">
                            </div>
                            <div class="avatar avatar-img">
                                <img src="/img/nexpose-logo.png">
                            </div>
                            <div class="info">
                                <div class="title h-3">
                                    <h4>Nexpose Scanner</h4>
                                </div>
                                <div class="desc t-3">Pellentesque lacinia sagittis libero. Praesent vitae justo purus.
                                    In hendrerit
                                    lorem nisl,
                                    ac lacinia urna aliquet non. Quisque nisi tellus, rhoncus quis est s, rhoncus quis
                                    est s,
                                    rhoncus quis est s, rhoncus quis est s, rhoncus quis est s, rhoncus quis est
                                </div>
                            </div>
                        </div>
                        </a>
                    </div>
                </div>
            </li>
            <li>
                <input type=radio name=tabs id=tab2>
                <label for=tab2>API Apps</label>
                <div id=tab-content2 class=tab-content>
                    <div class="col-md-4">
                        <div class="card hovercard animated pulse-hover">
                            <div class="cardheader c-white"></div>
                            <div class="avatar avatar-white">
                                <i class="fa fa-cogs fa-5x t-c-red"></i>
                            </div>
                            <div class="info">
                                <div class="title h-3">
                                    <h4>Coming Soon</h4>
                                </div>
                                <div class="desc t-3">
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
