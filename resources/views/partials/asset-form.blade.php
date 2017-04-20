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
                    {!! Form::label('asset-name', 'Name') !!}
                    {!! Form::text('asset-name', null, ['class' => 'black-form-control']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('asset-cpe', 'CPE') !!}
                    {!! Form::text('asset-cpe', null, ['class' => 'black-form-control']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('asset-vendor', 'Vendor') !!}
                    {!! Form::select('asset-vendor', $vendors, ['class' => 'black-form-control']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('asset-ip_address_v4', 'IP Address v4') !!}
                    {!! Form::text('asset-ip_address_v4', null, ['class' => 'black-form-control']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('asset-ip_address_v6', 'IP Address v6') !!}
                    {!! Form::text('asset-ip_address_v6', null, ['class' => 'black-form-control']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('asset-hostname', 'Hostname') !!}
                    {!! Form::url('asset-hostname', null, ['class' => 'black-form-control']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('asset-mac_address', 'MAC Address') !!}
                    {!! Form::text('asset-mac_address', null, ['class' => 'black-form-control']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('asset-os_version', 'OS Version') !!}
                    {!! Form::text('asset-os_version', null, ['class' => 'black-form-control']) !!}
                </div>
                <div class="form-group">
                    {!! Form::label('asset-netbios', 'Netbios Name') !!}
                    {!! Form::text('asset-netbios', null, ['class' => 'black-form-control']) !!}
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