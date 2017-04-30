@if (!empty($thumbnail))
    <a class="thumbnail-block" href="{{ route('get.poc-image', ['slug' => basename($thumbnail)]) }}" target="_blank">
        <span class="remove-poc" data-image-id="{{ $fieldName }}"><i class="fa fa-times t-c-red"></i></span>
        <div class="thumbnail-overlay"></div>
        <img class="thumbnail poc" src="{{ route('get.poc-image', ['slug' => basename($thumbnail)]) }}" />
        {!! Form::hidden($fieldName . '_path', $thumbnail) !!}
    </a>
@endif
{!! Form::label($fieldName, $labelText, ['class' => !empty($thumbnail) ? 'hidden' : '']) !!}
{!! Form::file($fieldName, ['id' => $fieldName, 'class' => !empty($thumbnail) ? 'hidden' : '']) !!}