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
<div>
    <nav class="navbar navbar-default navigation-clean-search">
        <div class="container-nav">
            <div class="navbar-header"><a class="navbar-brand navbar-link">Ruggedy.io </a>
                <button class="navbar-toggle collapsed" data-toggle="collapse" data-target="#navcol-1"><span
                            class="sr-only">Toggle navigation</span><span class="icon-bar"></span><span
                            class="icon-bar"></span><span class="icon-bar"></span></button>
            </div>
            <div class="collapse navbar-collapse" id="navcol-1">
                <ul class="nav navbar-nav">
                    <li role="presentation"><a href="{{ route('home') }}"><span class="t-c-red">.W</span>orkspaces</a></li>
                    <li role="presentation"><a href="{{ route('settings.index') }}"><span class="t-c-red">.S</span>ettings</a></li>
                    <li role="presentation"><a href="#"><span class="t-c-red">.P</span>rofile</a></li>
                </ul>
                <form class="navbar-form navbar-left" target="_self">
                    <div class="form-group">
                        <label class="control-label" for="search-field"><i
                                    class="glyphicon glyphicon-search t-c-red"></i></label>
                        <input class="form-control search-field" type="search" name="search" id="search-field">
                    </div>
                </form>
                <a class="btn btn-default navbar-btn navbar-right action-button" role="button" href="">Logout </a>
            </div>
        </div>
    </nav>
</div>
    <div class="container">

        @yield('content')

        <script src="{{ asset('/vendor/jquery/dist/jquery.js') }}"></script>
        <script src="{{ asset('/vendor/bootstrap/dist/js/bootstrap.js') }}"></script>
    </div>
</body>

</html>