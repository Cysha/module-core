<p>{{ $comment }}</p>

@if (count($api))
    @foreach ($api as $api)
    <h4>{{ array_get($api, 'description') }}</h4>
    <pre>{{ array_get($api, 'method') }} <a href="{{ Url::to(array_get($api, 'url')) }}">{{ array_get($api, 'url') }}</a></pre>
    @if (count($api['vars']))
    <table class="table table-hover">
        <tr>
            <th class="col-md-2">Variable</th>
            <th class="col-md-3">Usable Value(s)</th>
            <th class="col-md-7">Use</th>
        </tr>
        @foreach ($api['vars'] as $var)
        <tr>
            <td>{{ array_get($var, 'var') }}</td>
            <td>{{ array_get($var, 'value') }}</td>
            <td>{{ array_get($var, 'use') }}</td>
        </tr>
        @endforeach
    </table>
    @endif
    @endforeach
@endif
