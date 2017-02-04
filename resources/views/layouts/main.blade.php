<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ruggedy-App</title>
    <link rel="stylesheet" href="{{ asset('/vendor/bootstrap/dist/css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('/vendor/font-awesome/css/font-awesome.css') }}">
    <link rel="stylesheet" href="{{ asset("/css/animate.min.css") }}">
    <link rel="stylesheet" href="{{ asset('/css/styles.css') }}">
</head>

<body>
<div id="wrapper">
    <div id="sidebar-wrapper">
        <ul class="sidebar-nav">
            <li class="sidebar-brand">
                Ruggedy
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
            <li>
                <a href="{{ route('settings.index') }}">
                    <div class="nav-btn">
                        <h4 class="nav-btn-header"><i class="fa fa-wrench fa-lg nav-indent" aria-hidden="true"></i></h4>
                        <p class="nav-btn-text">Settings</p>
                    </div>
                </a>
            </li>
            <li>
                <a href="#">
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
        <div class="c-black nav-sm-btn">
            <i class="fa fa-bars fa-2x" id="menu-toggle"></i>
            <div class="pull-right" style="width:275px;">
                <div class="input-group custom-search-form">
                    <input type="text" class="search-form-control">
                    <span class="input-group-btn">
              <button class="btn btn-default" type="button">
              <span class="fa fa-search"></span>
             </button>
             </span>
                </div><!-- /input-group -->
            </div>
        </div>

        <div class="container">

            @yield('content')

        </div>
    </div>
</div>

    <script src="{{ asset('/vendor/jquery/dist/jquery.js') }}"></script>
    <script src="{{ asset('/vendor/bootstrap/dist/js/bootstrap.js') }}"></script>
    <script src="{{ asset('/js/custom.js') }}"></script>
</body>

</html>