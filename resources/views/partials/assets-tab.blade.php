<input type="radio" name="tabs" id="tab{{ $tabNo }}"{{ $tabNo !== 1 ?: ' checked'  }}>
<label for="tab{{ $tabNo }}">
    <div class="visible-xs mobile-tab">
        <span class="label-count c-grey">
            {{ !empty($assets) ? $assets->total() : 0 }}
        </span>
        <i class="fa fa-server fa-2x" aria-hidden="true"></i><br>
        <small>Assets</small>
    </div>
    <p class="hidden-xs">
        Assets<span class="label-count c-grey">{{ !empty($assets) ? $assets->total() : 0 }}</span>
    </p>
</label>
<div id="tab-content{{ $tabNo }}" class="tab-content">
    <div class="dash-line"></div>
    <div>
        @include('partials.assets')
    </div>
</div>