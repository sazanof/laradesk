@if($fields !== null)
    @foreach($fields as $field)
        <div class="field">
            <div class="field-name">
                {{$field->field_name}}
            </div>
            <div class="field-value">

                @if($field->field_type ==='MULTIJSON')
                    @php
                        $headers = json_decode($field?->field_options,true);
                        $content = json_decode($field->content,true);
                    @endphp
                    <table border="1" cellspacing="0">
                        <thead>
                        <tr>
                            @foreach($headers['fields'] as $th)
                                <th style="padding: 2px">{{$th['title']}}</th>
                            @endforeach
                        </tr>

                        </thead>
                        <tbody>
                        @foreach($content as $td)
                            <tr>
                                @foreach($headers['fields'] as $key=>$th)
                                    <td style="padding: 2px">{{$td[$key]['value']}}</td>
                                @endforeach
                            </tr>
                        @endforeach
                        </tbody>

                    </table>
                @else
                    {!! $field->content !!}
                @endif
            </div>
        </div>

    @endforeach
@endif
