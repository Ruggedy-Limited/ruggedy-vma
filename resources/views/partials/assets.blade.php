@if ($assets->isEmpty())
        <div class="row">
            <div class="col-xs-12">
                <div class="col-xs-12">
                    <br>
                    <p class="p-l-8">No Assets here. The file is probably still in the queue for processing.</p>
                    <p class="p-l-8">If nothing appears within the next 20-30 minutes, something is probably wrong and you should
                        contact technical support.</p>
                </div>
            </div>
        </div>
@else
    @foreach ($assets as $asset)
        <div class="col-md-3">
            <a href="#">
                <div class="list-content-card animated pulse-hover">
                    <span class="label label-danger m-r-5">
                        {{ $asset->getCriticalSeverityVulnerabilities()->count() }}
                    </span>
                    <span class="label label-high m-r-5">
                        {{ $asset->getHighSeverityVulnerabilities()->count() }}
                    </span>
                    <span class="label label-warning m-r-5">
                        {{ $asset->getMediumSeverityVulnerabilities()->count() }}
                    </span>
                    <span class="label label-success m-r-5">
                        {{ $asset->getLowSeverityVulnerabilities()->count() }}
                    </span>
                    <span class="label label-info">
                        {{ $asset->getInformationalVulnerabilities()->count() }}
                    </span>
                    <h4 class="h-4-1">{{ $asset->getHostname() ?? $asset->getName() }}</h4>
                    <p>IP Address: {{ $asset->getIpAddressV4() }}</p>
                </div>
            </a>
        </div>
    @endforeach
@endif