@if (!empty($thumbnail))
    <a class="thumbnail-block" href="{{ route('get.poc-image', ['slug' => basename($thumbnail)]) }}" target="_blank">
        <div class="remove-block">
            <span class="remove-poc btn round-btn c-red" data-image-id="{{ $fieldName }}">
                <i class="fa fa-trash fa-lg"></i>
            </span>
        </div>
        <div class="thumbnail-overlay"></div>
        <img class="thumbnail poc" src="{{ route('get.poc-image', ['slug' => basename($thumbnail)]) }}" />
        {!! Form::hidden($fieldName . '_path', $thumbnail) !!}
    </a>
@endif
{!! Form::label($fieldName, $labelText, ['class' => !empty($thumbnail) ? 'hidden' : '']) !!}
{!! Form::file($fieldName, ['id' => $fieldName, 'class' => !empty($thumbnail) ? 'hidden' : '']) !!}