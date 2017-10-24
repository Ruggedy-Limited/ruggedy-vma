<div class="col-md-4 col-sm-6">
    @can (App\Policies\ComponentPolicy::ACTION_EDIT, $workspace)
        <div class="convenience-buttons">
            <a href="{{ route('workspace.edit', [$workspace->getRouteParameterName() => $workspace->getId()]) }}">
                <button type="button" class="btn round-btn pull-right c-purple">
                    <i class="fa fa-pencil fa-lg" aria-hidden="true"></i>
                </button>
            </a>
            <a href="{{ route('workspace.delete', [$workspace->getRouteParameterName() => $workspace->getId()]) }}"
               class="delete-link">
                <button type="button" class="btn round-btn pull-right c-red">
                    <i class="fa fa-trash-o fa-lg" aria-hidden="true"></i>
                </button>
            </a>
        </div>
    @endcan
    <a href="{{ route('workspace.view', ['workspaceId' => $workspace->getId()]) }}">
        <div class="card hovercard animated pulse-hover">
            <div class="cardheader c-white">
            </div>
            <div class="avatar avatar-white">
                <i class="fa fa-th fa-5x t-c-grey"></i>
            </div>
            <div class="info">
                <div class="title h-3">
                    <h4>{{ $workspace->getName() }}</h4>
                </div>
                <div class="desc t-3">
                    {{ $workspace->getDescription() }}
                </div>
            </div>
        </div>
    </a>
</div>