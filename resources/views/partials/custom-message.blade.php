@if (!empty($message))
    <div class="alert alert-{{ !empty($bsClass) ? $bsClass : 'danger' }}">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
        @if (!is_array($message))
            {!! $message !!}
        @else
            <ul>
                @foreach ($message as $bulletMsg)
                    <li>{!! $bulletMsg !!}</li>
                @endforeach
            </ul>
        @endif
    </div>
@endif