<input type="radio" name="tabs" id="tab{{ $tabNo }}"{{ $tabNo !== 1 ?: ' checked'  }}>
<label for="tab{{ $tabNo }}">
    <div class="visible-xs mobile-tab">
        <span class="label-count c-grey">
            {{ $users->total() }}
        </span>
        <i class="fa fa-users fa-2x" aria-hidden="true"></i><br>
        <small>Users</small>
    </div>
    <p class="hidden-xs">
        Users<span class="label-count c-grey">{{ $users->total() }}</span>
    </p>
</label>
<div id="tab-content{{ $tabNo }}" class="tab-content">
    <div class="dash-line"></div>
    <div class="col-xs-12">
        @if (empty($users) || $users->isEmpty())
            <br>
            <p class="p-l-8">
                No Users have been created yet.
                <a href="{{ route('settings.user.create') }}">Add a User</a>.
            </p>
        @else
            @foreach ($users as $user)
                @include('partials.user')
            @endforeach
        @endif
    </div>
    <div class="col-xs-12">
        {{ $users->links() }}
    </div>
</div>