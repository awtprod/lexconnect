@extends('layouts.default')

@section('content')

@if(empty($clientId))

<p>
    <div>Select client: {{ Form::open(['route' => 'clientRates.index']) }}
{{ Form::select('clientId', $clients) }}
{{ Form::submit('Select') }}</div>

@elseif(count($rates)>0)

Select client: {{ Form::open(['route' => 'clientRates.index']) }}
    {{ Form::select('clientId', $clients) }}
    {{ Form::submit('Select') }}

<h1>Rates for {{$company->name}}</h1>
{{ link_to("/clientRates/edit/{$company->id}", 'Edit') }}

<table>
  <tr>
    <th>State</th>
    <th>Filing Flat Rate</th>
    <th>Filing Surcharge</th>
    <th>Serve Flat Rate</th>
    <th>Serve Surcharge</th>
  </tr>
@foreach ($rates as $rate)
<tr>
	<div>
        @if(!empty($rate["state"]))
	<td>{{ $rate["state"] }}</td>
        @else
        <td></td>
        @endif
	</div>

    <div>
        @if(!empty($rate["filingFlat"]))
            <td>{{ $rate["filingFlat"] }}</td>
        @else
            <td></td>
        @endif
    </div>

    <div>
        @if(!empty($rate["filingSurcharge"]))
            <td>{{ $rate["filingSurcharge"] }}</td>
        @else
            <td></td>
        @endif
    </div>



    <div>
        @if(!empty($rate["serveFlat"]))
            <td>{{ $rate["serveFlat"] }}</td>
        @else
            <td></td>
        @endif
    </div>

    <div>
        @if(!empty($rate["serveSurcharge"]))
            <td>{{ $rate["serveSurcharge"] }}</td>
        @else
            <td></td>
        @endif
    </div>

@endforeach
</tr>
</table>

@else


    Select client: {{ Form::open(['route' => 'clientRates.index']) }}
    {{ Form::select('clientId', $clients) }}
    {{ Form::submit('Select') }}<p>

    <h3>Enter rates for client: {{ link_to("/clientRates/create/{$company->id}", $company->name) }}</h3>

@endif
<a href="{{ URL::previous() }}">Go Back</a>
@stop
