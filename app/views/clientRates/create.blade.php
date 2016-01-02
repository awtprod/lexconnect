@extends('layouts.default')

@section('content')
<h1>Enter Rates for {{$client->name}}</h1>

{{ Form::open(['route' => 'clientRates.store']) }}
<table>
  <tr>
    <th>State</th>
    <th>Filing Surcharge</th>
    <th>Filing Flat Rate</th>
    <th>Serve Surcharge</th>
    <th>Serve Flat Rate</th>
  </tr>

@foreach ($states as $state)
<tr>
	<div>
	<td>{{ Form::hidden('state['.$state->abbrev.']', $state->abbrev) }}{{ $state->name }}</td></div>
        <div><td>
                {{ Form::label('filingSurcharge') }}
                {{ Form::text('filingSurcharge['.$state->abbrev.']') }}
        </div>
        </td>
    <div><td>
            {{ Form::label('filingFlat') }}
            {{ Form::text('filingFlat['.$state->abbrev.']') }}
    </div>
    </td>
    <div><td>
            {{ Form::label('serveSurcharge') }}
            {{ Form::text('serveSurcharge['.$state->abbrev.']') }}
    </div>
    </td>
    <div><td>
            {{ Form::label('serveFlat') }}
            {{ Form::text('serveFlat['.$state->abbrev.']') }}
    </div>
    </td>
@endforeach
</tr>
</table>
{{ Form::hidden('clientId', $client->id) }}
	<div>{{ Form::submit('Save Rates') }}{{ Form::reset('Reset') }}</div>
{{ Form::close() }}
<a href="{{ URL::previous() }}">Go Back</a>
@stop
