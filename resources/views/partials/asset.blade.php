<div class="col-md-3 asset" data-asset-id="{{ $asset->getId() }}">
    <a href="{{ route('asset.view', [$asset->getRouteParameterName() => $asset->getId()]) }}">
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
            <span class="label label-info m-r-5">
                        {{ $asset->getInformationalVulnerabilities()->count() }}
                    </span>
            <h4 class="h-4-1">{{ $asset->getHostname() ?? $asset->getName() }}</h4>
            <p>IP Address: {{ $asset->getIpAddressV4() }}</p>
        </div>
    </a>
</div>