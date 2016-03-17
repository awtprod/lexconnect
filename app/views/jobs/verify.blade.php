@extends('layouts.default')
@section('head')

@stop
@section('content')

<h3>{{ link_to("/orders/{$orders_id}", "Order # {$orders_id}") }}</h3><p>

<h2> Verify Defendant</h2>

    {{ Form::open(['route' => 'jobs.store']) }}

Defendant(s):
            @foreach($input["defendants"] as $defendant)
            {{ $defendant }}{{ Form::hidden('defendant[]', $defendant) }}<br>

             @endforeach


{{ $result[0]['delivery_line_1'] }}{{ Form::hidden('street', $result[0]['delivery_line_1']) }}&nbsp;
{{ $result[0]['components']['city_name'] }}{{ Form::hidden('city', $result[0]['components']['city_name']) }},&nbsp;
{{ $result[0]['metadata']['county_name'] }}{{ Form::hidden('county', $result[0]['metadata']['county_name']) }},&nbsp;
{{ $result[0]['components']['state_abbreviation'] }}{{ Form::hidden('state', $result[0]['components']['state_abbreviation']) }}&nbsp;
{{ $result[0]['components']['zipcode'] }}{{ Form::hidden('zipcode', $result[0]['components']['zipcode']) }}<p>
<b>Property Vacant:&nbsp;{{ $result[0]['analysis']['dpv_vacant'] }}</b><p>

    Estimated Cost:${{$rate}}{{ Form::hidden('vendorrate', $server["rate"]) }}<br>

        {{ Form::hidden('notes', Input::get('notes')) }}
    {{Input::get('type')}}{{ Form::hidden('type', Input::get('type')) }}

    @if(!empty($input["priority"]))
        {{ Form::hidden('priority', $input["priority"]) }}
    @endif

    @if(!empty($input["servee_id"]))
	{{ Form::hidden('orders_id', $input["orders_id"]) }}
	{{ Form::hidden('servee_id', $input["servee_id"]) }}
    {{ Form::hidden('server', $server["server"]) }}


    <div><input type="submit" name="verify" value="Verify Defendant"><input type="submit" name="edit_add" value="Edit Defendant"></div>

@else
	{{ Form::hidden('orders_id', $orders_id) }}
	
	<div><input type="submit" name="verify" value="Verify Defendant"><input type="submit" name="edit_create" value="Edit Defendant"></div>
@endif


{{ Form::close() }}

@endif

@if(!empty($jobs) AND empty($input["servee_id"]))
<h2> Current Defendants: </h2><p>
@foreach ($jobs as $job)
<table>
  <tr>
    <th><b>Defendant: {{ $job->defendant }}</b></th>
  </tr>
<tr>
<td>{{ $job->street }}<br> {{ $job->city }}, {{ $job->state }} {{ $job->zipcode }}</td>
</tr>
</table>
@endforeach
@elseif(!empty($jobs) AND !empty($input["servee_id"]))
<h2> Previous Attempted Addresses: </h2><p>
@foreach ($serveejobs as $job)
<table>
<td>{{ $job->street }}<br> {{ $job->city }}, {{ $job->state }} {{ $job->zipcode }}</td>
</tr>
</table>
@endforeach
@endif

@stop
