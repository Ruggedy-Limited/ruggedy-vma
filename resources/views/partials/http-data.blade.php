<div class="col-md-12 http-data">
    <a data-toggle="collapse" class="collapsed" href="#http-info-{{ $httpData->getId() }}" aria-expanded="false"
       aria-controls="http-info-{{ $httpData->getId() }}">
        <div class="list-content-card">
            <span class="pull-right collapsed-state"><i class="fa fa-plus-square"></i></span>
            <span class="pull-right expanded-state"><i class="fa fa-minus-square"></i></span>
            <p>
                <span class="label label-{{ $httpData->getBootstrapAlertCssClass() }} t-s-10 m-r-8">
                    {{ $httpData->getHttpMethod() }}
                </span>

                {{ $httpData->getHttpUri() }}
            </p>
            <div class="collapse" id="http-info-{{ $httpData->getId() }}">
                <div class="row">
                    <div class="col-xs-12">
                        <h4>Issue Detail</h4>
                        {!! $httpData->getIssueDetail() !!}
                    </div>
                </div>
            </div>
        </div>
    </a>
</div>