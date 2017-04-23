<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="_token" content="{{ csrf_token() }}">
    <title>Ruggedy-App</title>
    <link rel="stylesheet" href="{{ asset('/vendor/bootstrap/dist/css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('/vendor/font-awesome/css/font-awesome.css') }}">
    <link rel="stylesheet" href="{{ asset("/css/animate.min.css") }}">
    <link rel="stylesheet" href="{{ asset('/css/styles.css') }}">
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro" rel="stylesheet">
    <script src="{{ asset('/vendor/ckeditor/ckeditor.js') }}"></script>
    <script src="{{ asset('/vendor/jquery/dist/jquery.js') }}"></script>
</head>

<body>
<div id="wrapper">
    <div id="sidebar-wrapper">
        <ul class="sidebar-nav">
            <li class="sidebar-brand">
                <img src="/img/logo-small.png" height="30">
            </li>
            <li>
                <a href="{{ route('home') }}">
                    <div class="nav-btn">
                        <h4 class="nav-btn-header"><i class="fa fa-th-large fa-lg nav-indent" aria-hidden="true"></i>
                        </h4>
                        <p class="nav-btn-text">Workspaces</p>
                    </div>
                </a>
            </li>
            @can(App\Policies\ComponentPolicy::ACTION_EDIT, new App\Entities\User())
                <li>
                    <a href="{{ route('settings.view') }}">
                        <div class="nav-btn">
                            <h4 class="nav-btn-header"><i class="fa fa-wrench fa-lg nav-indent" aria-hidden="true"></i></h4>
                            <p class="nav-btn-text">Settings</p>
                        </div>
                    </a>
                </li>
            @endcan
            <li>
                <a href="{{ route('settings.user.profile') }}">
                    <div class="nav-btn">
                        <h4 class="nav-btn-header"><i class="fa fa-user fa-lg nav-indent" aria-hidden="true"></i>
                        </h4>
                        <p class="nav-btn-text">Profile</p>
                    </div>
                </a>
            </li>
            <li>
                <a href="#">
                    <div class="nav-btn">
                        <h4 class="nav-btn-header"><i class="fa fa-sign-out fa-lg nav-indent" aria-hidden="true"></i>
                        </h4>
                        <p class="nav-btn-text">Logout</p>
                    </div>
                </a>
            </li>
        </ul>
    </div>
    <div id="page-content-wrapper">
        <div class="c-darkgrey nav-sm-btn">
            <i class="fa fa-bars fa-2x" id="menu-toggle"></i>
            <div class="col-md-3 col-sm-6 col-xs-10 pull-right">
                <form name="search" action="{{ route('search.results') }}" method="POST"
                      enctype="application/x-www-form-urlencoded">
                    <div id="custom-search-input">
                        <div class="input-group col-md-12">
                                {{ csrf_field() }}
                                <input type="text" class="form-control" name="s" placeholder="Search" />
                                <span class="input-group-btn">
                                    <button class="btn btn-info btn-lg" type="submit">
                                        <i class="fa fa-search"></i>
                                    </button>
                                </span>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="c-lightgrey breadcrumb-nav hidden-xs">
            @yield('breadcrumb')
        </div>

        <div class="container">
            @include('partials.flash-message')
            @yield('content')

        </div>
    </div>
</div>
    <script src="{{ asset('/vendor/bootstrap/dist/js/bootstrap.js') }}"></script>
    <script src="{{ asset('/js/custom.js') }}"></script>
</body>

</html>
