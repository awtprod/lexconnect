@extends('layouts.default')

@section('content')

    {{ Form::open(['route' => 'counties.index']) }}
    {{ Form::label('state', 'State: ') }}
    @if(!empty($state))
    {{ Form::select('state', $states, $state) }}
    @else
    {{ Form::select('state', $states) }}
    @endif

    <div>{{ Form::submit('Submit') }}</div>
    {{ Form::close() }}
    @if(count($counties)>0)

    <table>
        <tr>
            <th>State</th>
            <th>County</th>
            <th>Filing Process</th>
            <th>Service Process</th>
            <th>Recording Process</th>
        </tr>
        {{ Form::open(['route' => 'counties.update']) }}

            @foreach($counties as $county)
            <tr>
                <div><td>
                    {{ $county->state }}
                </div>
                </td>
                <div><td>
                    {{ $county->county }}
                </div>
                </td>
                <div><td>
                    {{ Form::label('') }}
                    {{ Form::select('filing['.$county->id.']', $filing, $filingDefault[$county->id]) }}
                </div>
                </td>
                <div><td>
                    {{ Form::label('', '') }}
                    {{ Form::select('service['.$county->id.']', $service, $serviceDefault[$county->id]) }}
                </div>
                </td>
                <div><td>
                    {{ Form::label('') }}
                    {{ Form::select('recording['.$county->id.']', $recording, $recordingDefault[$county->id]) }}
                </div>
                </td>
                </tr>
            {{ Form::hidden('state', $county->state) }}

        @endforeach
    </table>

    <div>{{ Form::submit('Save Changes') }}</div>
    {{ Form::close() }}
    @else

@endif

<a href="{{ URL::previous() }}">Go Back</a>
@stop
