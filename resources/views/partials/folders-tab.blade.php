<input type="radio" name="tabs" id="tab{{ $tabNo }}"{{ $tabNo !== 1 ?: ' checked'  }}>
<label for="tab{{ $tabNo }}">
    <div class="visible-xs mobile-tab">
        <span class="label-count c-grey">
            {{ !empty($folders) ? $folders->total() : 0 }}
        </span>
        <i class="fa fa-folder fa-2x" aria-hidden="true"></i><br>
        <small>Folders</small>
    </div>
    <p class="hidden-xs">
        Folders<span class="label-count c-grey">{{ !empty($folders) ? $folders->total() : 0 }}</span>
    </p>
</label>
<div id="tab-content{{ $tabNo }}" class="tab-content">
    <div class="dash-line"></div>
    @if (empty($folders) || $folders->isEmpty())
        <div class="row">
            <div class="col-xs-12">
            <br>
                <div class="p-l-8">
                    No Folders in this Workspace yet.
                    <a href="{{ route('folder.create', [
                        $workspace->getRouteParameterName() => $workspace->getId()
                    ]) }}">Add a Folder.</a>
                </div>
            </div>
        </div>
    @else
        <div class="row">
            @foreach ($folders as $folder)
                @include('partials.folder')
            @endforeach
        </div>
        <div class="row">
            {{ $folders->fragment('tab' . $tabNo)->links() }}
        </div>
    @endif
</div>