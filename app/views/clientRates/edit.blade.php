@extends('layouts.default')

@section('content')
<h1>Edit Rates for {{$client->name}}</h1>

{{ Form::open(['route' => 'clientRates.update']) }}
<table>
  <tr>
    <th>State</th>
    <th>Filing Surcharge</th>
    <th>Filing Flat Rate</th>
    <th>Serve Surcharge</th>
    <th>Serve Flat Rate</th>
  </tr>

@foreach ($rates as $rate)
<tr>
	<div>
	<td>{{ Form::hidden('state['.$rate->id.']', $rate->state) }}{{ $rate->state }}</td></div>
        <div><td>
                {{ Form::label('filingSurcharge') }}
                {{ Form::text('filingSurcharge['.$rate->id.']', $rate->filingSurcharge) }}
        </div>
        </td>
    <div><td>
            {{ Form::label('filingFlat') }}
            {{ Form::text('filingFlat['.$rate->id.']', $rate->filingFlat) }}
    </div>
    </td>
    <div><td>
            {{ Form::label('serveSurcharge') }}
            {{ Form::text('serveSurcharge['.$rate->id.']', $rate->serveSurcharge) }}
    </div>
    </td>
    <div><td>
            {{ Form::label('serveFlat') }}
            {{ Form::text('serveFlat['.$rate->id.']', $rate->serveFlat) }}
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
