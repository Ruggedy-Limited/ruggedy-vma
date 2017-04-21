@if ($assets->isEmpty())
        <div class="row">
            <div class="col-xs-12">
                <div class="content-card">
                    <p>No Assets here. The file is probably still in the queue for processing.</p>
                    <p>If nothing appears within the next 20-30 minutes, something is probably wrong and you should
                        contact technical support.</p>
                </div>
            </div>
        </div>
@else
    @foreach ($assets as $asset)
        @include('partials.asset')
    @endforeach
@endif