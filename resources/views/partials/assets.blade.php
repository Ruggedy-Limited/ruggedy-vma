@if ($assets->isEmpty())
        <div class="row">
            <div class="col-xs-12">
                <div class="content-card">
                    <p>No Assets here. The file is probably still in the queue for processing.</p>
                    <p>If nothing appears within the next 20-30 minutes, something is probably wrong and you should
                        contact technical support.</p>
                </div>
            </div>
        </div>
@else
    @foreach ($assets as $asset)
        <div class="col-md-3">
            <a href="#">
                <div class="list-content-card animated pulse-hover">
                    <span class="label label-danger">
                        {{ $asset->getCriticalSeverityVulnerabilities()->count() }}
                    </span>
                    <span class="label label-warning">
                        {{ $asset->getHighSeverityVulnerabilities()->count() }}
                    </span>
                    <span class="label label-success">
                        {{ $asset->getMediumSeverityVulnerabilities()->count() }}
                    </span>
                    <span class="label label-info">
                        {{ $asset->getLowSeverityVulnerabilities()->count() }}
                    </span>
                    <h4 class="h-4-1">{{ $asset->getHostname() ?? $asset->getName() }}</h4>
                    <p>IP Address: {{ $asset->getIpAddressV4() }}</p>
                </div>
            </a>
        </div>
    @endforeach
@endif