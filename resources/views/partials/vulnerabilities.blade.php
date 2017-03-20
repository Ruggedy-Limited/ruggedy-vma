@if ($vulnerabilities->isEmpty())
    <div class="row">
        <div class="col-xs-12">
            <div class="content-card">
                <p>No Vulnerabilities here yet. The file is probably still in the queue for
                    processing.</p>
                <p>If nothing appears within the next 20-30 minutes, something is probably wrong and
                    you should contact technical support.</p>
            </div>
        </div>
    </div>
@else
    @foreach ($vulnerabilities as $vulnerability)
        <div class="col-md-12">
            <a href="{{ route('file.vulnerability.view', [
                                    'fileId' => $file->getId(),
                                    'vulnerabilityId' => $vulnerability->getId()
                                ]) }}">
                <div class="list-content-card">
                    <p><span class="label label-danger t-s-10">{{ $vulnerability->getSeverityText() }}</span>
                        <span class="badge c-purple">{{ $vulnerability->getAssets()->count() }}</span>
                        {{ $vulnerability->getName() }}
                    </p>
                </div>
            </a>
        </div>
    @endforeach
@endif