<div class="col-md-4 col-sm-6">
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