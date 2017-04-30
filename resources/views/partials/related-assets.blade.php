<h3>Related Assets</h3>
<a href="#" class="primary-btn" data-toggle="modal" data-target="#add-asset-form">
    Add an Asset
</a>
<div id="related-assets" class="row">
    @foreach ($assets as $asset)
        @include('partials.related-asset')
    @endforeach
</div>
{!! Form::select('assets[]', $assetIds, $assetIds, [
    'multiple' => 'multiple',
    'class'    => 'invisible',
    'id'       => 'assets-select',
]) !!}