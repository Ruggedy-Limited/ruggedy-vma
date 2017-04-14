<div class="col-md-4 col-sm-6">
    <a href="{{ route('settings.user.edit', ['userId' => $user->getId()]) }}">
        <div class="content-card animated pulse-hover">
            <h4>{{ $user->getName() }}</h4>
            <p>{{ $user->getEmail() }}</p>
        </div>
    </a>
</div>