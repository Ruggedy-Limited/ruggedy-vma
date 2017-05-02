@extends('layouts.main')

@section ('breadcrumb')

    @can (App\Policies\ComponentPolicy::ACTION_EDIT, $folder)
        <a href="{{ route('folder.delete', [$folder->getRouteParameterName() => $folder->getId()]) }}">
            <button type="button" class="btn round-btn pull-right c-red">
                <i class="fa fa-trash-o fa-lg" aria-hidden="true"></i>
            </button>
        </a>
        <a href="{{ route('folder.edit', [$folder->getRouteParameterName() => $folder->getId()]) }}">
            <button type="button" class="btn round-btn pull-right c-purple">
                <i class="fa fa-pencil fa-lg" aria-hidden="true"></i>
            </button>
        </a>
    @endcan
    <a href="{{ route('workspace.view', [
        $folder->getWorkspace()->getRouteParameterName() => $folder->getWorkspace()->getId()
    ]) }}">
        <button type="button" class="btn round-btn pull-right c-yellow">
            <i class="fa fa-times fa-lg" aria-hidden="true"></i>
        </button>
    </a>
    {!! Breadcrumbs::render('dynamic', $folder) !!}
@endsection

@section('content')
    <div class="row animated fadeIn">
        <ul class=tabs>
            <li>
                <input type=radio name=tabs id=tab1 checked>
                <label for=tab1>
                    <div class="visible-xs mobile-tab">
                        <span class="label-count c-grey">{{ $vulnerabilities->total() }}</span>
                        <i class="fa fa-bomb fa-2x" aria-hidden="true"></i>
                        <small>Vulnerabilities</small>
                    </div>
                    <p class="hidden-xs">
                        Vulnerabilities <span class="label-count c-grey">{{ $vulnerabilities->total() }}</span>
                    </p>
                </label>
                <div id=tab-content1 class=tab-content>
                    <div class="dash-line"></div>
                    <div>
                        <div>
                            @include('partials.vulnerabilities')
                        </div>
                    </div>
                </div>
            </li>
        </ul>
        <br style=clear:both;>
    </div>
@endsection