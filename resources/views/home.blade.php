@extends('layouts.main')

@section('content')
    <div class="col-md-4">
    <button class="flat-btn" type="button">Primary Button</button>
    </div>
    <div class="col-md-4">
        <div class="content-card">
            <h4 class="h-4-1">Basic Card</h4>
            <p>Pellentesque lacinia sagittis libero. Praesent vitae justo purus. In hendrerit lorem nisl, ac
                lacinia urna aliquet non. Quisque nisi tellus, rhoncus quis est s, rhoncus quis est s,
                rhoncus quis est s, rhoncus quis est s, rhoncus quis est s, rhoncus quis est</p>
        </div>
    </div>
    <div class="col-md-4">
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
                <div class="adm-card-footer">
                    <div class="pull-right">
                        <i class="fa fa-pencil"></i>
                        <i class="fa fa-trash p-l-8"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card hovercard animated pulse-hover">
            <div class="cardheader c-white">
            </div>
            <div class="avatar avatar-white">
                <i class="fa fa-folder fa-5x t-c-red"></i>
            </div>
            <div class="info">
                <div class="title h-3">
                    <h4>Folder Card</h4>
                </div>
                <div class="desc t-3">Pellentesque lacinia sagittis libero. Praesent vitae justo purus. In hendrerit
                    lorem nisl,
                    ac lacinia urna aliquet non. Quisque nisi tellus, rhoncus quis est s, rhoncus quis est s,
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
    <div class="col-md-4">
        <div class="card hovercard animated pulse-hover">
            <div class="cardheader c-white">
            </div>
            <div class="avatar avatar-white">
                <img src="/img/nessus-logo.png">
            </div>
            <div class="info">
                <div class="title h-3">
                    <h4>Nessus Scanner</h4>
                </div>
                <div class="desc t-3">Pellentesque lacinia sagittis libero. Praesent vitae justo purus. In hendrerit
                    lorem nisl,
                    ac lacinia urna aliquet non. Quisque nisi tellus, rhoncus quis est s, rhoncus quis est s,
                    rhoncus quis est s, rhoncus quis est s, rhoncus quis est s, rhoncus quis est
                </div>
            </div>
        </div>
    </div>
@endsection
