<div class="col-md-6 asset" data-asset-id="{{ $asset->getId() }}">
    <a href="#">
        <div class="list-content-card animated pulse-hover">
            <h4 class="h-4-1">{{  $asset->getName() }}</h4>
            @if (!empty($asset->getIpAddressV4()))
                <p>IP Address: {{ $asset->getIpAddressV4() }}</p>
            @endif
            @if (!empty($asset->getHostname()))
                <p>Hostname: {{ $asset->getHostname() }}</p>
            @endif
        </div>
    </a>
</div>