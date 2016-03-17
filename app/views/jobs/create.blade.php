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

<h1>Add New Defendant</h1>

{{ Form::open(['route' => 'jobs.verify']) }}
		<div>
	@foreach($input['defendants'] as $defendant)
	{{ Form::label('defendants', 'Defendant: ') }}
	{{ Form::text('defendants', $defendant) }}
	{{ $errors->first('defendants') }}
@endforeach
		</div>

<div>
	{{ Form::label('street', 'Street: ') }}
	{{ Form::text('street', $input["street"]) }}
	{{ $errors->first('street') }}
	</div>
			<div>
	@if(!empty($input["street2"]))

	{{ Form::label('street2', 'Apt/Unit/Suite: ') }}
	{{ Form::text('street2', $input["street2"]) }}
	{{ $errors->first('street2') }}

	@else

	{{ Form::label('street2', 'Apt/Unit/Suite: ') }}
	{{ Form::text('street2') }}
	{{ $errors->first('street2') }}

	@endif
	</div>
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
<div>
    {{ Form::label('notes', 'Notes to Server: ') }}
    {{ Form::textarea('notes') }}
    {{ $errors->first('notes') }}
</div>
<div>
	@if($input["type"]=="service")
	{{ Form::label('type', 'Service Type: ') }}
	{{ Form::radio('type', 'service', true) }}
	{{ Form::label('type', 'Process Service', true) }}
	{{ Form::radio('type', 'posting') }}
	{{ Form::label('type', 'Property Posting') }}

		@else
			{{ Form::label('type', 'Service Type: ') }}
			{{ Form::radio('type', 'service') }}
			{{ Form::label('type', 'Process Service') }}
			{{ Form::radio('type', 'posting',true) }}
			{{ Form::label('type', 'Property Posting') }}
		@endif

	{{ Form::label('priority', 'Priority: ') }}
	{{ Form::select('priority]', array('Routine' => 'Routine', 'Rush' => 'Rush', 'SameDay' => 'Same Day'),$input["priority"]) }}<p>
</div>

{{ Form::hidden('orders_id', $input["orders_id"]) }}

	<div>{{ Form::submit('Add Defendant') }}{{ Form::reset('Reset') }}</div>
{{ Form::close() }}
@else
<h3>{{ link_to("/orders/{$orders_id}", "Order # {$orders_id}") }}</h3><p>

<h1>Add New Defendant</h1>

{{ Form::open(['route' => 'jobs.verify']) }}
		<div>
	{{ Form::label('defendant', 'Defendant: ') }}
	{{ Form::text('defendant') }}
	{{ $errors->first('defendant') }}
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
<div>
    {{ Form::label('notes', 'Notes to Server: ') }}
    {{ Form::textarea('notes') }}
    {{ $errors->first('notes') }}<p>
</div>
<div>
	{{ Form::label('type', 'Service Type: ') }}
	{{ Form::radio('type', 'service', true) }}
	{{ Form::label('type', 'Process Service', true) }}
	{{ Form::radio('type', 'posting') }}
	{{ Form::label('type', 'Property Posting') }}
	{{ Form::label('service[priority]', 'Priority: ') }}
	{{ Form::select('service[priority]', array('Routine' => 'Routine', 'Rush' => 'Rush', 'SameDay' => 'Same Day')) }}<p>
</div>

{{ Form::hidden('orders_id', $orders_id) }}

	<div>{{ Form::submit('Add Defendant') }}{{ Form::reset('Reset') }}</div>
{{ Form::close() }}
@endif

@if(!empty($jobs))
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
@endif
<a href="{{ URL::previous() }}">Go Back</a>
</body>
</html>
