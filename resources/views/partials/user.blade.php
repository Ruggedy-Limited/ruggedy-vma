<div class="col-md-4 col-sm-6">
    <div class="convenience-buttons">
        @if ($user->isAdmin())
            <span class="label label-success user-admin pull-left t-s-10 m-r-8">admin</span>
        @endif
        <a href="{{ route('settings.user.edit', ['userId' => $user->getId()]) }}">
            <button type="button" class="btn round-btn pull-right c-purple">
                <i class="fa fa-pencil fa-lg" aria-hidden="true"></i>
            </button>
        </a>
        @if (!empty(Auth::user()) && Auth::user()->getId() !== $user->getId())
            <a href="{{ route('settings.user.delete', [$user->getRouteParameterName() => $user->getId()]) }}"
               class="delete-link">
                <button type="button" class="btn round-btn pull-right c-red">
                    <i class="fa fa-trash-o fa-lg" aria-hidden="true"></i>
                </button>
            </a>
        @endif
    </div>
    <a href="{{ route('settings.user.edit', ['userId' => $user->getId()]) }}">
        <div class="content-card animated pulse-hover">
            <h4>{{ $user->getName() }}</h4>
            <p>{{ $user->getEmail() }}</p>
        </div>
    </a>
</div>