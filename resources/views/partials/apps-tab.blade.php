<input type="radio" name="tabs" id="tab{{ $tabNo }}"{{ $tabNo !== 1 ?: ' checked'  }}>
<label for="tab{{ $tabNo }}">
    <div class="visible-xs mobile-tab">
        <span class="label-count c-grey">
            {{ !empty($apps) ? $apps->total() : 0 }}
        </span>
        <i class="fa fa-window-maximize fa-2x" aria-hidden="true"></i><br>
        <small>Apps</small>
    </div>
    <p class="hidden-xs">
        Apps<span class="label-count c-grey">{{ !empty($apps) ? $apps->total() : 0 }}</span>
    </p>
</label>
<div id="tab-content{{ $tabNo }}" class="tab-content">
    <div class="dash-line"></div>
    @if (empty($apps) || $apps->isEmpty())
        <p>
            No Apps in this Workspace yet.
            <a href="{{ route('workspaces.apps') }}">Add an Application.</a>
        </p>
    @else
        <div class="row">
            @foreach ($apps as $app)
                @include('partials.app')
            @endforeach
        </div>
        <div class="row">
            <div class="col-xs-12">
                {{ $apps->links() }}
            </div>
        </div>
    @endif
</div>