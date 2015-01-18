@extends('layouts.default')

@section('content')
<h1>Order # {{ $orders_id }}</h1>
<a href="{{ URL::route('orders.create') }}">Create New Order</a><p>

</script>
@if(!$jobs==0)
@foreach ($jobs as $job)
<div>
Defendant: {{ $job->defendant }}<p>
{{ $job->street }}, {{ $job->city }}, {{ $job->state }} {{ $job->zipcode }}<p>
@endforeach
@endif
{{ Form::open(['route' => 'orders.verify']) }}
	<div>
	{{ Form::label('Defendant', 'Defendant: ') }}
	{{ Form::text('defendant') }}
	{{ $errors->first('defendant') }}
	</div>
	<div>
	{{ Form::label('street', 'Street: ') }}
	{{ Form::text('street') }}
	{{ $errors->first('street') }}
	</div>
	<div>
	{{ Form::label('street2', 'Apt/Suite/Unit: ') }}
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
	{{ Form::select('state', $states, Input::old('name')) }}
	{{ $errors->first('state') }}
	</div>
	<div>
	{{ Form::label('zipcode', 'Zip Code: ') }}
	{{ Form::text('zipcode') }}
	{{ $errors->first('zipcode') }}
	</div>
	{{ Form::hidden('orders_id', $orders_id) }}
	<div>{{ Form::submit('Add Defendant') }}</div>
{{ Form::close() }}
<a href="{{ URL::previous() }}">Go Back</a>
@stop
