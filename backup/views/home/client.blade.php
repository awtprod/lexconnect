@extends('layouts.default')

@section('content')
<h1>View Active Orders</h1>

{{ link_to('/orders/create', 'New Order') }}
<td>{{ Form::open(['route' => 'search.index']) }}{{ Form::text('search') }}{{ Form::submit('Search') }}{{ Form::close() }}</td>
@if (!empty($openorders))
<h2>Order # &nbsp; Order Date: </h2>
@foreach ($openorders as $order)
<li>{{ link_to("/orders/{$order->id}", $order->id) }} &nbsp; {{ $order->created_at }} </li> 
@endforeach
@else
<h2>No Orders to display!</h2>
@endif
@stop
<a href="{{ URL::previous() }}">Go Back</a>
@stop
