@extends('layouts.main')
@section ('breadcrumb')
    <button type="button" class="btn round-btn pull-right c-grey" data-toggle="modal" data-target="#help">
        <i class="fa fa-question fa-lg" aria-hidden="true"></i>
    </button>
    <a href="{{ url()->previous() }}">
        <button type="button" class="btn round-btn pull-right c-yellow">
            <i class="fa fa-times fa-lg" aria-hidden="true"></i>
        </button>
    </a>
    {!! Breadcrumbs::render('search', $searchTerm) !!}
@endsection

@section('content')
    <!-- Modal -->
    <div id="help" class="modal fade" role="dialog">
        <div class="modal-dialog">

            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                    <h4 class="modal-title">Help Title</h4>
                </div>
                <div class="modal-body">
                    <p>Help text goes here.</p>
                </div>
            </div>

        </div>
    </div>
    @if (empty($searchResults) || $searchResults->isEmpty())
        <div class="col-sm-12">
            <h1>No matching records found for "{{ $searchTerm }}". Please try a different search term.</h1>
        </div>
    @else
        <div class="row animated fadeIn">
            <h1>Matches for search phrase: "{{ $searchTerm }}"</h1>
            <ul class=tabs>
                @if (!empty($searchResults->get('Workspaces')))
                    <li>
                        <input type=radio name=tabs id=tab1>
                        <label for=tab1>Workspaces</label>
                        <div id="tab-content1" class="tab-content">
                            <div class="dash-line"></div>
                            @foreach ($searchResults->get('Workspaces') as $workspace)
                                @include('partials.workspace')
                            @endforeach
                        </div>
                    </li>
                @endif
                @if (!empty($searchResults->get('Apps')))
                    <li>
                        <input type=radio name=tabs id=tab2>
                        <label for=tab2>Apps</label>
                        <div id="tab-content2" class="tab-content">
                            <div class="dash-line"></div>
                            @foreach ($searchResults->get('Apps') as $app)
                                @include('partials.app')
                            @endforeach
                        </div>
                    </li>
                @endif
                @if (!empty($searchResults->get('Vulnerabilities')))
                    <li>
                        <input type=radio name=tabs id=tab3>
                        <label for=tab3>Vulnerabilities</label>
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
                    <li>
                        <input type=radio name=tabs id=tab4>
                        <label for=tab4>Assets</label>
                        <div id="tab-content4" class="tab-content">
                            <div class="dash-line"></div>
                            @include('partials.assets')
                        </div>
                    </li>
                @endif
                @if (!empty($searchResults->get('Users')))
                    <li>
                        <input type=radio name=tabs id=tab5>
                        <label for=tab5>Users</label>
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
