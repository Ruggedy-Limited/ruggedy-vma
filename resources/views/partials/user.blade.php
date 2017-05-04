<div class="col-md-4 col-sm-6">
    <a href="{{ route('settings.user.edit', ['userId' => $user->getId()]) }}">
        <div class="content-card animated pulse-hover">
            @if ($user->isAdmin())
                <span class="label label-success pull-right t-s-10">admin</span>
            @endif
            <h4>{{ $user->getName() }}</h4>
            <p>{{ $user->getEmail() }}</p>
        </div>
    </a>
</div>