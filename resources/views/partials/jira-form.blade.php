<div id="jira" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <!-- Modal content-->
        <div class="modal-content">
            <div class="waiting-icon-container">
                <i class="fa fa-spinner fa-pulse fa-3x fa-fw"></i>
                <span class="sr-only">Loading...</span>
            </div>
            <div class="waiting-overlay"></div>
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <h4 class="modal-title">Send to JIRA</h4>
            </div>
            <div class="modal-body">
                {!! Form::open(['url' => route('jira.create', [
                    $vulnerability->getRouteParameterName() => $vulnerability->getId()
                ])]) !!}
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('username', 'Username') !!}
                        {!! Form::text('username', null, ['class' => 'black-form-control']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('password', 'Password') !!}
                        {!! Form::password('password', ['class' => 'black-form-control']) !!}
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        {!! Form::label('project-id', 'JIRA Project ID') !!}
                        {!! Form::text('project-id', null, ['class' => 'black-form-control']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('host', 'Host') !!}
                        {!! Form::text('host', null, ['class' => 'black-form-control']) !!}
                    </div>
                    <div class="form-group">
                        {!! Form::label('port', 'Port') !!}
                        {!! Form::text('port', null, ['class' => 'black-form-control']) !!}
                    </div>
                </div>
                <button class="primary-btn" type="submit">Create Jira Issue</button>
                {{ csrf_field() }}
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>