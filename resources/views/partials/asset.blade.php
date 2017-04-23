<div class="col-md-3 asset" data-asset-id="{{ $asset->getId() }}">
    <a href="#">
        <div class="list-content-card animated pulse-hover">
            <span class="label label-danger">
                {{ $asset->getCriticalSeverityVulnerabilities()->count() }}
            </span>
            <span class="label label-high">
                        {{ $asset->getHighSeverityVulnerabilities()->count() }}
                    </span>
            <span class="label label-warning">
                        {{ $asset->getMediumSeverityVulnerabilities()->count() }}
                    </span>
            <span class="label label-success">
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