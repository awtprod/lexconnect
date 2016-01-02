@extends('layouts.default')

@section('content')

<br><div>{{ Form::open(['route' => 'clientRates.index']) }}
{{ Form::label('clientId', 'Select Client: ') }}

@if(!empty($company))
{{ Form::select('clientId', $clients, $company->id) }}
@else
{{ Form::select('clientId', $clients) }}
@endif
{{ Form::submit('Submit') }}</div></br>
{{ Form::close() }}

@if(!empty($company))
<h1>Enter Rates for {{$company->name}}</h1>

{{ Form::open(['route' => 'clientRates.update']) }}
<table>
  <tr>
    <th>State</th>
    <th>ACH Discount</th>
    <th>Filing Max Rate</th>
    <th>Filing Surcharge</th>
    <th>Filing Flat Rate</th>
    <th>Serve Max Rate</th>
    <th>Serve Surcharge</th>
    <th>Serve Flat Rate</th>
    <th>Recording Max Rate</th>
    <th>Recording Surcharge</th>
    <th>Recording Flat Rate</th>
  </tr>
@if(count($rates)>0)

@foreach($rates as $rate)
            <tr>
                <div>
                    <td>{{ $rate->state }}</td></div>
                <div><td>
                    {{ Form::label('') }}
                    {{ Form::text('discount['.$rate->id.']',$rate->discount) }}
                </div>
                </td>
                <div><td>
                    {{ Form::label('') }}
                    {{ Form::text('filingMax['.$rate->id.']',$rate->filingMax) }}
                </div>
                </td>
                <div><td>
                    {{ Form::label('') }}
                    {{ Form::text('filingSurcharge['.$rate->id.']',$rate->filingSurcharge) }}
                </div>
                </td>
                <div><td>
                    {{ Form::label('') }}
                    {{ Form::text('filingFlat['.$rate->id.']',$rate->filingFlat) }}
                </div>
                </td>
                <div><td>
                    {{ Form::label('') }}
                    {{ Form::text('serveMax['.$rate->id.']',$rate->serveMax) }}
                </div>
                </td>
                <div><td>
                    {{ Form::label('') }}
                    {{ Form::text('serveSurcharge['.$rate->id.']',$rate->serveSurcharge) }}
                </div>
                </td>
                <div><td>
                    {{ Form::label('') }}
                    {{ Form::text('serveFlat['.$rate->id.']',$rate->serveFlat) }}
                </div>
                </td>
                <div><td>
                    {{ Form::label('') }}
                    {{ Form::text('recordingMax['.$rate->id.']',$rate->recordingMax) }}
                </div>
                </td>
                <div><td>
                    {{ Form::label('') }}
                    {{ Form::text('recordingSurcharge['.$rate->id.']',$rate->recordingSurcharge) }}
                </div>
                </td>
                <div><td>
                    {{ Form::label('') }}
                    {{ Form::text('recordingFlat['.$rate->id.']',$rate->recordingFlat) }}
                </div>
                </td>
                @endforeach
            </tr>
</table>
@else

@foreach ($states as $state)
<tr>
	<div>
	<td>{{ Form::hidden('state['.$state->abbrev.']') }}{{ $state->name }}</td></div>
    <div><td>
            {{ Form::label('') }}
            {{ Form::text('discount['.$state->abbrev.']') }}
    </div>
    </td>
    <div><td>
            {{ Form::label('') }}
            {{ Form::text('filingMax['.$state->abbrev.']') }}
    </div>
    </td>
    <div><td>
            {{ Form::label('') }}
            {{ Form::text('filingSurcharge['.$state->abbrev.']') }}
    </div>
    </td>
    <div><td>
            {{ Form::label('') }}
            {{ Form::text('filingFlat['.$state->abbrev.']') }}
    </div>
    </td>
    <div><td>
            {{ Form::label('') }}
            {{ Form::text('serveMax['.$state->abbrev.']') }}
    </div>
    </td>
    <div><td>
            {{ Form::label('') }}
            {{ Form::text('serveSurcharge['.$state->abbrev.']') }}
    </div>
    </td>
    <div><td>
            {{ Form::label('') }}
            {{ Form::text('serveFlat['.$state->abbrev.']') }}
    </div>
    </td>
    <div><td>
            {{ Form::label('') }}
            {{ Form::text('recordingMax['.$state->abbrev.']') }}
    </div>
    </td>
    <div><td>
            {{ Form::label('') }}
            {{ Form::text('recordingSurcharge['.$state->abbrev.']') }}
    </div>
    </td>
    <div><td>
            {{ Form::label('') }}
            {{ Form::text('recordingFlat['.$state->abbrev.']') }}
    </div>
    </td>
@endforeach
</tr>
</table>
@endif
{{ Form::hidden('clientId', $company->id) }}
	<div>{{ Form::submit('Save Rates') }}{{ Form::reset('Reset') }}</div>
{{ Form::close() }}

@endif
<a href="{{ URL::previous() }}">Go Back</a>
@stop
