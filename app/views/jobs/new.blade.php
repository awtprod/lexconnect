<!DOCTYPE html>
<html>

<head>
<style>
table {
    width:50%;
}
table, th, td {
    border: 1px solid black;
    border-collapse: collapse;
}
th, td {
    padding: 5px;
    text-align: left;
}
table#t01 tr:nth-child(even) {
    background-color: #eee;
}
table#t01 tr:nth-child(odd) {
   background-color:#fff;
}
table#t01 th	{
    background-color: black;
    color: white;
}
</style>
</head>

<body>


@if(!empty($input))

<h3>{{ link_to("/orders/{$input["orders_id"]}", "Order # {$input["orders_id"]}") }}</h3><p>

<h1>Add New Service Address</h1>

{{ Form::open(['route' => 'jobs.verify']) }}
		<div>
	Defendant:&nbsp; {{ $input["defendant"] }}
	{{ Form::hidden('defendant', $input["defendant"]) }}

	</div>
			<div>
	{{ Form::label('street', 'Street: ') }}
	{{ Form::text('street', $input["street"]) }}
	{{ $errors->first('street') }}
	</div>
	
@if(!empty($input["street2"]))
			<div>
	{{ Form::label('street2', 'Apt/Unit/Suite: ') }}
	{{ Form::text('street2', $input["street2"]) }}
	{{ $errors->first('street2') }}
	</div>
@endif
			<div>
	{{ Form::label('city', 'City: ') }}
	{{ Form::text('city', $input["city"]) }}
	{{ $errors->first('city') }}
	</div>
	<div>
	{{ Form::label('state', 'State: ') }}
	{{ Form::select('state', $states, $input["state"]) }}
	{{ $errors->first('state') }}
	</div>
				<div>
	{{ Form::label('zipcode', 'Zip Code: ') }}
	{{ Form::text('zipcode', $input["zipcode"]) }}
	{{ $errors->first('zipcode') }}
	</div>
{{ Form::hidden('servee_id', $input["servee_id"]) }}
{{ Form::hidden('orders_id', $input["orders_id"]) }}


	<div>{{ Form::submit('Add New Address') }}{{ Form::reset('Reset') }}</div>
{{ Form::close() }}
@else
<h3>{{ link_to("/orders/{$servee->order_id}", "Order # {$servee->order_id}") }}</h3><p>

<h1>Add New Serve Address</h1>

{{ Form::open(['route' => 'jobs.verify']) }}
		<div>
	Defendant: {{ $servee->defendant }}
	{{ Form::hidden('defendant', $servee->defendant) }}
	</div>
			<div>
	{{ Form::label('street', 'Street: ') }}
	{{ Form::text('street') }}
	{{ $errors->first('street') }}
	</div>
			<div>
	{{ Form::label('street2', 'Apt/Unit/Suite: ') }}
	{{ Form::text('street2') }}
	{{ $errors->first('street2') }}
	</div>
			<div>
	{{ Form::label('city', 'City: ') }}
	{{ Form::text('city') }}
	{{ $errors->first('city') }}
	</div>
	<div>
	{{ Form::label('state', 'State: ') }}
	{{ Form::select('state', $states, null, ['id' => 'state']) }}
	{{ $errors->first('state') }}
	</div>
				<div>
	{{ Form::label('zipcode', 'Zip Code: ') }}
	{{ Form::text('zipcode') }}
	{{ $errors->first('zipcode') }}
	</div>
{{ Form::hidden('servee_id', $servee->id) }}
{{ Form::hidden('orders_id', $servee->order_id) }}

	<div>{{ Form::submit('Add New Address') }}{{ Form::reset('Reset') }}</div>
{{ Form::close() }}
@endif
@if(!empty($jobs))
<h2> Previous Attempted Addresses: </h2><p>
@foreach ($jobs as $job)
<table>
<td>{{ $job->street }}<p> {{ $job->city }}, {{ $job->state }} {{ $job->zipcode }}</td>
</tr>
</table>
@endforeach
@endif
<a href="{{ URL::previous() }}">Go Back</a>
</body>
</html>
