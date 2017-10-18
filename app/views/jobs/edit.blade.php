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

{{ Form::open(['route' => 'jobs.verify']) }}
		<div>
			{{ Form::label('defendant', 'Defendant: ') }}
			{{ Form::text('defendant', $job->defendant) }}

	</div>
<div>
	{{ Form::label('vendor', 'Vendor: ') }}
	{{ Form::select('vendor', $vendors, $job->vendor) }}
</div>
			<div>
	{{ Form::label('street', 'Street: ') }}
	{{ Form::text('street', $job->street) }}
	</div>
	
@if(!empty($job->street2))
			<div>
	{{ Form::label('street2', 'Apt/Unit/Suite: ') }}
	{{ Form::text('street2', $job->street2) }}
	</div>
@else
	<div>
		{{ Form::label('street2', 'Apt/Unit/Suite: ') }}
		{{ Form::text('street2') }}
	</div>
@endif
			<div>
	{{ Form::label('city', 'City: ') }}
	{{ Form::text('city', $job->city) }}
	</div>
	<div>
	{{ Form::label('state', 'State: ') }}
	{{ Form::select('state', $states, $job->state) }}
	</div>
				<div>
	{{ Form::label('zipcode', 'Zip Code: ') }}
	{{ Form::text('zipcode', $job->zipcode) }}
	</div>
{{ Form::hidden('jobId', $job->id) }}
{{ Form::hidden('serveeId', $job->servee_id) }}
{{ Form::hidden('orderId', $job->order_id) }}


	<div>{{ Form::submit('Save') }}{{ Form::reset('Reset') }}</div>
{{ Form::close() }}

</body>
</html>
