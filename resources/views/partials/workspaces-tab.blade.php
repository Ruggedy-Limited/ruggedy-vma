<input type="radio" name="tabs" id="tab{{ $tabNo }}"{{ $tabNo !== 1 ?: ' checked'  }}>
<label for="tab{{ $tabNo }}">
    <div class="visible-xs mobile-tab">
        <span class="label-count c-grey">
            {{ $workspaces->total() }}
        </span>
        <i class="fa fa-th fa-2x" aria-hidden="true"></i><br>
        <small>Workspaces</small>
    </div>
    <p class="hidden-xs">
        Workspaces<span class="label-count c-grey">{{ $workspaces->total() }}</span>
    </p>
</label>
<div id="tab-content{{ $tabNo }}" class="tab-content">
    <div class="dash-line"></div>
    <div>
        <div>
            @if (empty($workspaces) || $workspaces->isEmpty())
                <br>
                <div class="col-xs-12">
                    <p class="p-l-8">There aren't any workspaces, it's very quiet in here.
                        <a href="{{ route('workspace.create') }}">Add a Workspace</a></p>
                </div>
            @else
                @foreach ($workspaces as $workspace)
                    @include('partials.workspace')
                @endforeach
                <div class="col-xs-12">
                    {{ $workspaces->links() }}
                </div>
            @endif
        </div>
    </div>
</div>