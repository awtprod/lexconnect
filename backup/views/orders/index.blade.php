@extends('layouts.default')

@section('content')
<h1>View Active Orders</h1>

{{ link_to('/orders/create', 'New Order') }}

@if (!empty($orders))
<h2>Order # &nbsp; Order Date: </h2>
@foreach ($orders as $order)
<li>{{ link_to("/orders/{$order->id}", $order->id) }} &nbsp; {{ $order->created_at }} </li> 
@endforeach
@else
<h2>No Orders to display!</h2>
@endif
@stop
<a href="{{ URL::previous() }}">Go Back</a>
@stop
