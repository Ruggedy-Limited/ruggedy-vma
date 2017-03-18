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
    <div class="row animated fadeIn">
        <ul class=tabs>
            <li>
                <input type=radio name=tabs id=tab1 checked>
                <label for=tab1>Vulnerabilities <span class="badge c-purple">{{ $file->getVulnerabilities()->count() }}</span></label>
                <div id=tab-content1 class=tab-content>
                    <div class="dash-line"></div>
                    @if ($file->getVulnerabilities()->isEmpty())
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="content-card">
                                    <p>No Vulnerabilities here yet. The file is probably still in the queue for
                                        processing.</p>
                                    <p>If nothing appears within the next 20-30 minutes, something is probably wrong and
                                        you should contact technical support.</p>
                                </div>
                            </div>
                        </div>
                    @else
                        @foreach ($file->getVulnerabilities() as $vulnerability)
                            <div class="col-md-12">
                                <a href="{{ route('file.vulnerability.view', [
                                    'fileId' => $file->getId(),
                                    'vulnerabilityId' => $vulnerability->getId()
                                ]) }}">
                                    <div class="list-content-card">
                                        <p><span class="label label-danger t-s-10">{{ $vulnerability->getSeverityText() }}</span>
                                            <span class="badge c-purple">{{ $vulnerability->getAssets()->count() }}</span>
                                            {{ $vulnerability->getName() }}
                                        </p>
                                    </div>
                                </a>
                            </div>
                        @endforeach
                    @endif
                </div>
            </li>
            <li>
                <input type=radio name=tabs id=tab2>
                <label for=tab2>Assets <span class="badge c-purple">{{ $file->getAssets()->count() }}</span></label>
                <div id=tab-content2 class=tab-content>
                    <div class="dash-line"></div>
                    @include('partials.assets')
                </div>
            </li>
        </ul>
        <br style=clear:both;>
    </div>

@endsection