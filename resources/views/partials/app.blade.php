<div class="col-md-4 col-sm-6">
    @can (App\Policies\ComponentPolicy::ACTION_EDIT, $app)
        <div class="convenience-buttons">
            <a href="{{ route('app.edit', [$app->getRouteParameterName() => $app->getId()]) }}">
                <button type="button" class="btn round-btn pull-right c-purple">
                    <i class="fa fa-pencil fa-lg" aria-hidden="true"></i>
                </button>
            </a>
            <a href="{{ route('app.delete', [
                'workspaceId'    => $app->getWorkspace()->getId(),
                'workspaceAppId' => $app->getId()
            ]) }}" class="delete-link">
                <button type="button" class="btn round-btn pull-right c-red">
                    <i class="fa fa-trash-o fa-lg" aria-hidden="true"></i>
                </button>
            </a>
        </div>
    @endcan
    @if ($app->isRuggedyApp())
        <a href="{{ route('ruggedy-app.view', ['fileId' => $app->getFiles()->first()->getId()]) }}">
    @else
        <a href="{{ route('app.view', ['workspaceAppId' => $app->getId()]) }}">
    @endif
        <div class="card hovercard animated pulse-hover">
            <div class="cardheader c-white">
            </div>
            <div class="avatar avatar-white">
                <img src="{{ $app->getScannerApp()->getLogo() }}">
            </div>
            <div class="info">
                <div class="title h-3">
                    <h4>{{ $app->getName() }}</h4>
                </div>
                <div class="desc t-3">
                    {{ $app->getDescription() }}
                </div>
            </div>
        </div>
    </a>
</div>