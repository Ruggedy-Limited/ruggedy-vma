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

    @include('layouts.formError')

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

    <div class="row>">
        <div class="col-md-4">
            <button class="primary-btn" type="button">Primary Button</button>
            <a href="#" class="border-btn" type="button">Cancel</a>
        </div>
        <div class="col-md-4">
            <h3>Form Heading</h3>
            <br>
            {!! Form::open(['url' => '/home']) !!}
            <div class="form-group">
                {!! Form::label('name', 'Name') !!}
                {!! Form::text('name', null, ['class' => 'black-form-control']) !!}
            </div>
            <div class="form-group">
                {!! Form::label('description', 'Description') !!}
                {!! Form::textarea('description', null, ['class' => 'black-form-control', 'rows' => '3']) !!}
            </div>
            <a href="#" class="border-btn" type="button">Cancel</a>
            <button class="primary-btn" type="submit">Submit</button>
            {!! Form::close() !!}
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
            <div class="black-content-card">
                <h4 class="h-4-1 t-c-red">Basic Card / Notes</h4>
                <p>Pellentesque lacinia sagittis libero. Praesent vitae justo purus. In hendrerit lorem nisl, ac
                    lacinia urna aliquet non. Quisque nisi tellus, rhoncus quis est s, rhoncus quis est s,
                    rhoncus quis est s, rhoncus quis est s, rhoncus quis est s, rhoncus quis est</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="clear-content-card">
                <h4 class="h-4-1 t-c-red">Basic Card / Notes</h4>
                <p>Pellentesque lacinia sagittis libero. Praesent vitae justo purus. In hendrerit lorem nisl, ac
                    lacinia urna aliquet non. Quisque nisi tellus, rhoncus quis est s, rhoncus quis est s,
                    rhoncus quis est s, rhoncus quis est s, rhoncus quis est s, rhoncus quis est</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card hovercard animated pulse-hover">
                <div class="cardheader c-white"></div>
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
                <div class="cardheader c-white"></div>
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
                <div class="avatar avatar-img">
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
        <div class="col-md-4">
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
                    <div class="desc t-3">Pellentesque lacinia sagittis libero. Praesent vitae justo purus. In hendrerit
                        lorem nisl,
                        ac lacinia urna aliquet non. Quisque nisi tellus, rhoncus quis est s, rhoncus quis est s,
                        rhoncus quis est s, rhoncus quis est s, rhoncus quis est s, rhoncus quis est
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
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
                    <div class="desc t-3">Pellentesque lacinia sagittis libero. Praesent vitae justo purus. In hendrerit
                        lorem nisl,
                        ac lacinia urna aliquet non. Quisque nisi tellus, rhoncus quis est s, rhoncus quis est s,
                        rhoncus quis est s, rhoncus quis est s, rhoncus quis est s, rhoncus quis est
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
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
                    <div class="desc t-3">Pellentesque lacinia sagittis libero. Praesent vitae justo purus. In hendrerit
                        lorem nisl,
                        ac lacinia urna aliquet non. Quisque nisi tellus, rhoncus quis est s, rhoncus quis est s,
                        rhoncus quis est s, rhoncus quis est s, rhoncus quis est s, rhoncus quis est
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
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
                    <div class="desc t-3">Pellentesque lacinia sagittis libero. Praesent vitae justo purus. In hendrerit
                        lorem nisl,
                        ac lacinia urna aliquet non. Quisque nisi tellus, rhoncus quis est s, rhoncus quis est s,
                        rhoncus quis est s, rhoncus quis est s, rhoncus quis est s, rhoncus quis est
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
