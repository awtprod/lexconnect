@extends('layouts.default')
@section('head')

@stop
@section('content')

<h3>{{ link_to("/orders/{$orders_id}", "Order # {$orders_id}") }}</h3><p>

<h2> Verify Defendant</h2>

<!--If address was not verified-->

@if(empty($result))
{{ dd(Input::all()) }}
{{ Form::open(['route' => 'jobs.store']) }}
<h3><font color="red">Warning! The Address You Entered Cannot Be Verified. Please Verify That You Correctly Entered The Address. Click "Verify Defendant" If You Wish To Continue With This Address.</font></h3>
Defendant:{{ $input["defendant"] }}{{ Form::hidden('defendant', $input["defendant"]) }}<p>

{{ $input["street"] }}{{ Form::hidden('street', $input["street"]) }}&nbsp;
{{ $input["street2"] }}{{ Form::hidden('street2', $input["street2"]) }}&nbsp;
{{ $input["city"] }}{{ Form::hidden('city', $input["city"]) }},&nbsp;
{{ $input["state"] }}{{ Form::hidden('state', $input["state"]) }}&nbsp;
{{ $input["zipcode"] }}{{ Form::hidden('zipcode', $input["zipcode"]) }}<p>

    {{ Form::hidden('notes', Input::get('notes')) }}
    {{ Form::hidden('type', Input::get('type')) }}

@if(!empty($input["servee_id"]))
	{{ Form::hidden('orders_id', $input["orders_id"]) }}
	{{ Form::hidden('servee_id', $input["servee_id"]) }}
	<div><input type="submit" name="verify" value="Verify Defendant"><input type="submit" name="edit_add" value="Edit Defendant"></div>
	
@else
	{{ Form::hidden('orders_id', $input["orders_id"]) }}
	<div><input type="submit" name="verify" value="Verify Defendant"><input type="submit" name="edit_create" value="Edit Defendant"></div>
@endif


{{ Form::close() }}

<!--If address was verified-->

@else
    {{ Form::open(['route' => 'jobs.store']) }}

Defendant(s):
            @foreach($input["service"]["defendants"] as $defendant)
            {{ $defendant }}{{ Form::hidden('defendant[]', $defendant) }}<br>

             @endforeach


{{ $result[0]['delivery_line_1'] }}{{ Form::hidden('street', $result[0]['delivery_line_1']) }}&nbsp;
{{ $result[0]['components']['city_name'] }}{{ Form::hidden('city', $result[0]['components']['city_name']) }},&nbsp;
{{ $result[0]['metadata']['county_name'] }}{{ Form::hidden('county', $result[0]['metadata']['county_name']) }},&nbsp;
{{ $result[0]['components']['state_abbreviation'] }}{{ Form::hidden('state', $result[0]['components']['state_abbreviation']) }}&nbsp;
{{ $result[0]['components']['zipcode'] }}{{ Form::hidden('zipcode', $result[0]['components']['zipcode']) }}<p>
<b>Property Vacant:&nbsp;{{ $result[0]['analysis']['dpv_vacant'] }}</b><p>

    Estimated Cost:${{$server["rate"]}}{{ Form::hidden('rate', $server["rate"]) }}<br>

        {{ Form::hidden('notes', Input::get('notes')) }}
    {{Input::get('type')}}{{ Form::hidden('type', Input::get('type')) }}

    @if(!empty($input["service"]["priority"]))
        {{ Form::hidden('service[priority]', $input["service"]["priority"]) }}
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
<td>{{ $job->street }}<p> {{ $job->city }}, {{ $job->state }} {{ $job->zipcode }}</td>
</tr>
</table>
@endforeach
@elseif(!empty($jobs) AND !empty($input["servee_id"]))
<h2> Previous Attempted Addresses: </h2><p>
@foreach ($serveejobs as $job)
<table>
<td>{{ $job->street }}<p> {{ $job->city }}, {{ $job->state }} {{ $job->zipcode }}</td>
</tr>
</table>
@endforeach
@endif

@stop
