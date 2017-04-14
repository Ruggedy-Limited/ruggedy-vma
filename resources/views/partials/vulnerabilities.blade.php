@if (empty($vulnerabilities) || $vulnerabilities->isEmpty())
    <div class="row">
        <div class="col-xs-12">
            <div class="content-card">
                @if (isset($folder))
                    <p>No Vulnerabilities here yet. To add one, when viewing a Vulnerability, click the "Add to Folder"
                        button and select this folder from the list.</p>
                @else
                    <p>No Vulnerabilities here yet. The file is probably still in the queue for
                        processing.</p>
                    <p>If nothing appears within the next 20-30 minutes, something is probably wrong and
                        you should contact technical support.</p>
                @endif
            </div>
        </div>
    </div>
@else
    @foreach ($vulnerabilities as $vulnerability)
        @include('partials.vulnerability')
    @endforeach
    {{ $vulnerabilities->links() }}
@endif