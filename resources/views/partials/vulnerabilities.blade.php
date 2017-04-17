@if (empty($vulnerabilities) || $vulnerabilities->isEmpty())
    @if (isset($folder))
        <br>
        <p class="p-l-8">No Vulnerabilities here yet. To add one, when viewing a Vulnerability, click the "Add to
            Folder"
            button and select this folder from the list.</p>
    @else
        <br>
        <p class="p-l-8">No Vulnerabilities here yet. The file is probably still in the queue for
            processing.</p>
        <p class="p-l-8">If nothing appears within the next 20-30 minutes, something is probably wrong and
            you should contact technical support.</p>
    @endif
@else
    @foreach ($vulnerabilities as $vulnerability)
        @include('partials.vulnerability')
    @endforeach
    {{ $vulnerabilities->links() }}
@endif




