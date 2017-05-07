@extends('layouts.main')

@section ('breadcrumb')
    <a href="{{ redirect()->back() }}">
        <button type="button" class="btn round-btn pull-right c-yellow">
            <i class="fa fa-times fa-lg" aria-hidden="true"></i>
        </button>
    </a>
    {!! Breadcrumbs::render('dynamic', $asset) !!}
@endsection

@section ('content')

    <div class="row animated fadeIn">
        <ul class=tabs>
            <li>
                <input type="radio" name="tabs" id="tab1" checked>
                <label for="tab1">
                    <div class="visible-xs mobile-tab">
                        <i class="fa fa-bomb fa-2x" aria-hidden="true"></i><br>
                        <small>Asset</small>
                    </div>
                    <p class="hidden-xs">Asset</p>
                </label>
                <div id="tab-content1" class="tab-content">
                    <div class="dash-line"></div>
                    <div class="col-md-12">
                        <div class="content-card solution">
                            <h4 class="h-4-1">{{ $asset->getHostname() ?? $asset->getName() }}</h4>
                            <p>IP Address: {{ $asset->getIpAddressV4() }}</p>
                        </div>
                    </div>
                </div>
            </li>
            <li>
                <input type="radio" name="tabs" id="tab2">
                <label for="tab2">
                    <div class="visible-xs mobile-tab">
                        <i class="fa fa-thumbs-up fa-2x" aria-hidden="true"></i><br>
                        <small>Vulnerabilities</small>
                    </div>
                    <p class="hidden-xs">Vulnerabilities</p>
                </label>
                <div id="tab-content2" class="tab-content">
                    <div class="dash-line"></div>
                    <div class="col-md-12">
                        <div class="content-card solution">

                        </div>
                    </div>
                </div>
            </li>
        </ul>
        <br style=clear:both;>
    </div>
@endsection
