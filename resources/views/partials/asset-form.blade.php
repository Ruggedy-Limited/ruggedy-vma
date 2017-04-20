<div id="add-asset-form" class="modal fade" role="dialog">
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
                <h4 class="modal-title">Add a related Asset</h4>
            </div>
            <div class="modal-body">
                {!! Form::open([
                    'url'  => route('asset.create', [$file->getRouteParameterName() => $file->getId()]),
                    'id'   => 'new-asset',
                    'name' => 'new-asset',
                ]) !!}
                <div class="form-group">
                    {!! Form::label('name', 'Name') !!}
                    {!! Form::text('name', null, ['class' => 'black-form-control']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('cpe', 'CPE') !!}
                    {!! Form::text('cpe', null, ['class' => 'black-form-control']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('vendor', 'Vendor') !!}
                    {!! Form::select('vendor', $vendors, ['class' => 'black-form-control']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('ip_address_v4', 'IP Address v4') !!}
                    {!! Form::text('ip_address_v4', null, ['class' => 'black-form-control']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('ip_address_v6', 'IP Address v6') !!}
                    {!! Form::text('ip_address_v6', null, ['class' => 'black-form-control']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('hostname', 'Hostname') !!}
                    {!! Form::url('hostname', null, ['class' => 'black-form-control']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('mac_address', 'MAC Address') !!}
                    {!! Form::text('mac_address', null, ['class' => 'black-form-control']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('os_version', 'OS Version') !!}
                    {!! Form::text('os_version', null, ['class' => 'black-form-control']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('netbios', 'Netbios Name') !!}
                    {!! Form::text('netbios', null, ['class' => 'black-form-control']) !!}
                </div>
                <div class="form-group">
                    {!! Form::submit('Create Asset', ['class' => 'primary-btn']) !!}
                </div>
                {{ csrf_field() }}
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>