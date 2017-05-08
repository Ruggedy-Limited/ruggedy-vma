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
                        @include('partials.workspaces-tab', [
                            'workspaces' => $searchResults->get('Workspaces'),
                            'tabNo'      => 1,
                        ])
                    </li>
                @endif
                @if (!empty($searchResults->get('Apps')))
                    <li class="p-l-25">
                        @include('partials.apps-tab', [
                            'apps'  => $searchResults->get('Apps'),
                            'tabNo' => 2,
                        ])
                    </li>
                @endif
                @if (!empty($searchResults->get('Vulnerabilities')))
                    <li class="p-l-25">
                        @include('partials.vulnerabilities-tab', [
                            'vulnerabilities' => $searchResults->get('Vulnerabilities'),
                            'tabNo'           => 3,
                        ])
                    </li>
                @endif
                @if (!empty($searchResults->get('Assets')))
                    <li class="p-l-25">
                        @include('partials.assets-tab', [
                            'assets' => $searchResults->get('Assets'),
                            'tabNo'  => 4,
                        ])
                    </li>
                @endif
                @if (!empty($searchResults->get('Users')))
                    <li class="p-l-25">
                        @include('partials.users-tab', [
                            'users' => $searchResults->get('Users'),
                            'tabNo' => 5,
                        ])
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
