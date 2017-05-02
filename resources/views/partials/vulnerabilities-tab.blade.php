<input type="radio" name="tabs" id="tab{{ $tabNo }}"{{ $tabNo !== 1 ?: ' checked'  }}>
<label for="tab{{ $tabNo }}">
    <div class="visible-xs mobile-tab">
        <span class="label-count c-grey">
            {{ !empty($vulnerabilities) ? $vulnerabilities->total() : 0 }}
        </span>
        <i class="fa fa-bomb fa-2x" aria-hidden="true"></i><br>
        <small>Vulnerabilities</small>
    </div>
    <p class="hidden-xs">
        Vulnerabilities<span class="label-count c-grey">{{ !empty($vulnerabilities) ? $vulnerabilities->total() : 0 }}</span>
    </p>
</label>
<div id="tab-content{{ $tabNo }}" class="tab-content">
    <div class="dash-line"></div>
    <div>
        @include('partials.vulnerabilities')
    </div>
</div>