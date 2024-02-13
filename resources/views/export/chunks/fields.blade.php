@if($fields !== null)
    @foreach($fields as $field)
        <div class="field">
            <div class="field-name">
                {{$field->field_name}}
            </div>
            <div class="field-value">
                {!! $field->content !!}
            </div>
        </div>

    @endforeach
@endif
