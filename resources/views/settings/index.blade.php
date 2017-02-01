@extends('layouts.main')

@section('content')

    <h5>Breadcrumbs / Goes / Here </h5>

    <div class="row animated fadeIn">
        <div class="col-md-12">
            <a href="#" class="primary-btn" type="button">Add User</a>
        </div>
    </div>

    <div class="row animated fadeIn">
        <ul class=tabs>
            <li>
                <input type=radio name=tabs id=tab1 checked>
                <label for=tab1>Settings</label>
                <div id=tab-content1 class=tab-content>
                        <div class="col-md-4">
                            <br>
                            {!! Form::open(['url' => '/foo/bar']) !!}
                            <div class="form-group">
                                {!! Form::label('name', 'Company Name', ['class' => 'tabs-label']) !!}
                                {!! Form::text('name', null, ['class' => 'black-form-control']) !!}
                            </div>
                            <blackutton class="primary-btn" type="submit">Submit</blackutton>
                            {!! Form::close() !!}
                        </div>
                        <div class="col-md-2"></div>
                        <div class="col-md-6 animated fadeInUp">
                            <div class="white-content-card">
                                <h4 class="h-4-1 t-c-red">Settings</h4>
                                <p>Pellentesque lacinia sagittis libero. Praesent vitae justo purus. In hendrerit lorem
                                    nisl, ac
                                    lacinia urna aliquet non. Quisque nisi tellus, rhoncus quis est s, rhoncus quis est
                                    s,
                                    rhoncus quis est s, rhoncus quis est s, rhoncus quis est s, rhoncus quis est</p>
                            </div>
                        </div>
                </div>
            </li>
            <li>
                <input type=radio name=tabs id=tab2>
                <label for=tab2>Users</label>
                <div id=tab-content2 class=tab-content>
                    <div class="col-md-4">

                    </div>
                </div>
            </li>
        </ul>
        <br style=clear:both;>
    </div>

@endsection
