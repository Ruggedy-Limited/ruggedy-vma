@if (empty($assets) || $assets->isEmpty())
    @if (!empty($isRuggedyApp))
        <div class="row">
            <div class="col-xs-12">
            <br>
                <div class="m-l-8">
                    <p>No Assets here yet. When adding a Vulnerability, add Asset details for Assets to
                        be displayed here.</p>
                </div>
            </div>
        </div>
    @else
        <div class="row">
            <div class="col-xs-12">
                <br>
                <p class="p-l-8">No Assets here yet. The file is probably still in the queue for processing.</p>
                <p class="p-l-8">If nothing appears within the next 20-30 minutes, something is probably wrong and you should
                    contact technical support.</p>
            </div>
        </div>
    @endif
@else
    <div class="row">
        @foreach ($assets as $asset)
            @include('partials.asset')
        @endforeach
    </div>
    <div class="row">
        {{ !empty($tabNo) ? $assets->fragment('tab' . $tabNo)->links() : $assets->links() }}
    </div>
@endif