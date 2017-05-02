@extends('layouts.main')
@section ('breadcrumb')
    <a href="{{ url()->previous() }}">
        <button type="button" class="btn round-btn pull-right c-yellow">
            <i class="fa fa-times fa-lg" aria-hidden="true"></i>
        </button>
    </a>
    {!! Breadcrumbs::render('search', $searchTerm) !!}
@endsection

@section('content')
    @if (empty($searchResults) || $searchResults->isEmpty())
        <div class="col-sm-12">
            <h4 class="p-l-8">No matching records found for "{{ $searchTerm }}". Please try a different search term.</h4>
        </div>
    @else
        <div class="row animated fadeIn">
            <h4 class="p-l-8">Matches for search phrase: "{{ $searchTerm }}"</h4>
            <ul class=tabs>
                @if (!empty($searchResults->get('Workspaces')))
                    <li>
                        <input type=radio name=tabs id=tab1>
                        <label for=tab1>
                            <i class="fa fa-th fa-2x" aria-hidden="true"></i>
                            <p class="hidden-xs">Workspaces</p>
                        </label>
                        <div id="tab-content1" class="tab-content">
                            <div class="dash-line"></div>
                            @foreach ($searchResults->get('Workspaces') as $workspace)
                                @include('partials.workspace')
                            @endforeach
                        </div>
                    </li>
                @endif
                @if (!empty($searchResults->get('Apps')))
                    <li class="p-l-25">
                        <input type=radio name=tabs id=tab2>
                        <label for=tab2>
                            <i class="fa fa-window-maximize fa-2x" aria-hidden="true"></i>
                            <p class="hidden-xs">Apps</p>
                        </label>
                        <div id="tab-content2" class="tab-content">
                            <div class="dash-line"></div>
                            @foreach ($searchResults->get('Apps') as $app)
                                @include('partials.app')
                            @endforeach
                        </div>
                    </li>
                @endif
                @if (!empty($searchResults->get('Vulnerabilities')))
                    <li class="p-l-25">
                        <input type=radio name=tabs id=tab3>
                        <label for=tab3>
                            <i class="fa fa-bomb fa-2x" aria-hidden="true"></i>
                            <p class="hidden-xs">Vulnerabilities</p>
                            </label>
                        <div id="tab-content3" class="tab-content">
                            <div class="dash-line"></div>
                            @foreach ($searchResults->get('Vulnerabilities') as $vulnerability)
                                @include('partials.vulnerability')
                            @endforeach
                        </div>
                    </li>
                @endif
                @if (!empty($searchResults->get('Assets')))
                    <?php $assets = $searchResults->get('Assets') ?>
                    <li class="p-l-25">
                        <input type=radio name=tabs id=tab4>
                        <label for=tab4>
                            <i class="fa fa-server fa-2x" aria-hidden="true"></i>
                            <p class="hidden-xs">Assets</p>
                        </label>
                        <div id="tab-content4" class="tab-content">
                            <div class="dash-line"></div>
                            @include('partials.assets')
                        </div>
                    </li>
                @endif
                @if (!empty($searchResults->get('Users')))
                    <li class="p-l-25">
                        <input type=radio name=tabs id=tab5>
                        <label for=tab5>
                            <i class="fa fa-users fa-2x" aria-hidden="true"></i>
                            <p class="hidden-xs">Users</p>
                        </label>
                        <div id="tab-content5" class="tab-content">
                            <div class="dash-line"></div>
                            @foreach ($searchResults->get('Users') as $user)
                                @include('partials.user')
                            @endforeach
                        </div>
                    </li>
                @endif
            </ul>
        </div>
        <script type="text/javascript">
            (function ($) {
                $('input[name="tabs"]:first').prop('checked', 'checked');
            })(jQuery);
        </script>
    @endif
@endsection
