@extends('layouts.default')

@section('content')
@if($orders_id==0)
<h1> Create New Order </h1>
@else
<h1>Order # {{ $orders_id }}</h1>
@endif

<a href="{{ URL::route('orders.create') }}">Create New Order</a><p>
@if(!$jobs==0)
@foreach ($jobs as $job)
<div>
Defendant: {{ $job->defendant }}<p>
{{ $job->street }}, {{ $job->city }}, {{ $job->state }} {{ $job->zipcode }}<p>
@endforeach
@endif

{{ Form::open(['route' => 'orders.store']) }}

Defendant:{{ $defendant }}{{ Form::hidden('defendant', $defendant) }}<p>

{{ $result[0]['delivery_line_1'] }}{{ Form::hidden('street', $result[0]['delivery_line_1']) }}&nbsp;
{{ $result[0]['components']['city_name'] }}{{ Form::hidden('city', $result[0]['components']['city_name']) }},&nbsp;
{{ $result[0]['components']['state_abbreviation'] }}{{ Form::hidden('state', $result[0]['components']['state_abbreviation']) }}&nbsp;
{{ $result[0]['components']['zipcode'] }}{{ Form::hidden('zipcode', $result[0]['components']['zipcode']) }}<p>
<b>Property Vacant:&nbsp;{{ $result[0]['analysis']['dpv_vacant'] }}</b>
	@if(!$orders_id==0)
	{{ Form::hidden('orders_id', $orders_id) }}
	@endif
	<div>{{ Form::submit('Verify Defendant') }}{{ Form::reset('Reset') }}</div>
{{ Form::close() }}
<a href="{{ URL::route('orders.revise', array('input' => $input)) }}">Edit Defendant</a>
@stop
