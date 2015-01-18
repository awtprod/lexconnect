@extends('layouts.default')

@section('content')
@if($orders_id==0)
<h1> Create New Order </h1>
@else
<h1>Order # {{ $orders_id }}</h1>
@endif
<a href="{{ URL::route('orders.create') }}">Create New Order</a>
@if(!$jobs==0)
@foreach ($jobs as $job)
<div>
Defendant: {{ $job->defendant }}<p>
{{ $job->street }}, {{ $job->city }}, {{ $job->state }} {{ $job->zipcode }}<p>
@endforeach
@endif
</script>

{{ Form::open(['route' => 'orders.verify']) }}
{{ dd($input) }}
	<div>
	{{ Form::label('Defendant', 'Defendant: ') }}
	{{ Form::text('defendant', $input["_old_input"]["defendant"]) }}
	{{ $errors->first('defendant') }}
	</div>
	<div>
	{{ Form::label('street', 'Street: ') }}
	{{ Form::text('street', $input["_old_input"]["street"]) }}
	{{ $errors->first('street') }}
	</div>
	<div>
	{{ Form::label('street2', 'Apt/Suite/Unit: ') }}
	{{ Form::text('street2', $input["_old_input"]["street2"]) }}
	{{ $errors->first('street2') }}
	</div>
	<div>
	{{ Form::label('city', 'City: ') }}
	{{ Form::text('city', $input["_old_input"]["city"]) }}
	{{ $errors->first('city') }}
	</div>
	<div>
	{{ Form::label('state', 'State: ') }}
	{{ Form::select('state', $states, $input["_old_input"]["state"]); }}
	{{ $errors->first('state') }}
	</div>
	<div>
	{{ Form::label('zipcode', 'Zip Code: ') }}
	{{ Form::text('zipcode', $input["_old_input"]["zipcode"]) }}
	{{ $errors->first('zipcode') }}
	</div>
	@if(!$orders_id==0)
	{{ Form::hidden('orders_id', $orders_id) }}
	@endif
	<div>{{ Form::submit('Update Defendant') }}</div>
{{ Form::close() }}
{{ link_to("/orders/clear/", 'Clear Form') }}
<a href="{{ URL::previous() }}">Go Back</a>
@stop
