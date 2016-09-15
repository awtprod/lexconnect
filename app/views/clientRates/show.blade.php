@extends('layouts.default')

@section('content')

<br><div>{{ Form::open(['route' => 'clientRates.index']) }}


@if(!empty($company))
<h1>Enter Rates for {{$company->name}}</h1>

{{ Form::open(['route' => 'clientRates.update']) }}
<table>
  <tr>
    <th>State</th>
    <th>ACH Discount</th>
    <th>Court Run Max Rate</th>
    <th>Court Run Surcharge (%)</th>
    <th>Court Run Flat Rate</th>
    <th>Serve Max Rate</th>
    <th>Serve Surcharge (%)</th>
    <th>Serve Flat Rate</th>
    <th>Posting Max Rate</th>
    <th>Posting Surcharge (%)</th>
    <th>Posting Flat Rate</th>
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
                    {{ Form::text('runMax['.$rate->id.']',$rate->runMax) }}
                </div>
                </td>
                <div><td>
                    {{ Form::label('') }}
                    {{ Form::text('runSurcharge['.$rate->id.']',$rate->runSurcharge) }}
                </div>
                </td>
                <div><td>
                    {{ Form::label('') }}
                    {{ Form::text('runFlat['.$rate->id.']',$rate->runFlat) }}
                </div>
                </td>
                <div><td>
                    {{ Form::label('') }}
                    {{ Form::text('serviceMax['.$rate->id.']',$rate->serviceMax) }}
                </div>
                </td>
                <div><td>
                    {{ Form::label('') }}
                    {{ Form::text('serviceSurcharge['.$rate->id.']',$rate->serviceSurcharge) }}
                </div>
                </td>
                <div><td>
                    {{ Form::label('') }}
                    {{ Form::text('serviceFlat['.$rate->id.']',$rate->serviceFlat) }}
                </div>
                </td>
                <div><td>
                    {{ Form::label('') }}
                    {{ Form::text('postMax['.$rate->id.']',$rate->postMax) }}
                </div>
                </td>
                <div><td>
                    {{ Form::label('') }}
                    {{ Form::text('postSurcharge['.$rate->id.']',$rate->postSurcharge) }}
                </div>
                </td>
                <div><td>
                    {{ Form::label('') }}
                    {{ Form::text('postFlat['.$rate->id.']',$rate->postFlat) }}
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
            {{ Form::text('runMax['.$state->abbrev.']') }}
    </div>
    </td>
    <div><td>
            {{ Form::label('') }}
            {{ Form::text('runSurcharge['.$state->abbrev.']') }}
    </div>
    </td>
    <div><td>
            {{ Form::label('') }}
            {{ Form::text('runFlat['.$state->abbrev.']') }}
    </div>
    </td>
    <div><td>
            {{ Form::label('') }}
            {{ Form::text('serviceMax['.$state->abbrev.']') }}
    </div>
    </td>
    <div><td>
            {{ Form::label('') }}
            {{ Form::text('serviceSurcharge['.$state->abbrev.']') }}
    </div>
    </td>
    <div><td>
            {{ Form::label('') }}
            {{ Form::text('serviceFlat['.$state->abbrev.']') }}
    </div>
    </td>
    <div><td>
            {{ Form::label('') }}
            {{ Form::text('postMax['.$state->abbrev.']') }}
    </div>
    </td>
    <div><td>
            {{ Form::label('') }}
            {{ Form::text('postSurcharge['.$state->abbrev.']') }}
    </div>
    </td>
    <div><td>
            {{ Form::label('') }}
            {{ Form::text('postFlat['.$state->abbrev.']') }}
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
