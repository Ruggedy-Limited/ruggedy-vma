<div class="col-md-4 col-sm-6">
    <div class="convenience-buttons">
        @can (App\Policies\ComponentPolicy::ACTION_EDIT, $folder)
            <a href="{{ route('folder.edit', [$folder->getRouteParameterName() => $folder->getId()]) }}">
                <button type="button" class="btn round-btn pull-right c-purple">
                    <i class="fa fa-pencil fa-lg" aria-hidden="true"></i>
                </button>
            </a>
            <a href="{{ route('folder.delete', [$folder->getRouteParameterName() => $folder->getId()]) }}"
               class="delete-link">
                <button type="button" class="btn round-btn pull-right c-red">
                    <i class="fa fa-trash-o fa-lg" aria-hidden="true"></i>
                </button>
            </a>
        @endcan
    </div>
    <a href="{{ route('folder.view', ['folderId' => $folder->getId()]) }}">
        <div class="card hovercard animated pulse-hover">
            <div class="cardheader c-white"></div>
            <div class="avatar avatar-white">
                <i class="fa fa-folder fa-5x t-c-grey"></i>
            </div>
            <div class="info">
                <div class="title h-3">
                    <h4>{{ $folder->getName() }}</h4>
                </div>
                <div class="desc t-3">
                    {{ $folder->getDescription() }}
                </div>
            </div>
        </div>
    </a>
</div>