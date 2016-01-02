<!DOCTYPE html>
<html>

<head>
<style>
table {
    width:60%;
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
<td>{{ Form::open(['route' => 'search.index']) }}{{ Form::text('search') }}{{ Form::submit('Search') }}{{ Form::close() }}</td>

{{ link_to('/orders/create', 'New Order') }}&nbsp;{{ link_to("/orders/edit/{$orders->id}", 'Edit Order') }}

@if (!empty($orders))
<h2>Order # {{ $orders->id }}</h2><p>

@if($orders->status == 0)
<h2>{{ Form::open(['route' => 'orders.status']) }}{{ Form::hidden('status', '1') }}{{ Form::hidden('orders_id', $orders->id) }}{{ Form::submit('Place Order on Hold') }}{{ Form::close() }}{{ Form::open(['route' => 'orders.status']) }}{{ Form::hidden('status', '2') }}{{ Form::hidden('orders_id', $orders->id) }}{{ Form::submit('Cancel Order') }}{{ Form::close() }}</h2><p>
@endif
@if($orders->status == 1)
<h2>{{ Form::open(['route' => 'orders.status']) }}{{ Form::hidden('status', '0') }}{{ Form::hidden('orders_id', $orders->id) }}{{ Form::submit('Remove Order from Hold') }}{{ Form::close() }}{{ Form::open(['route' => 'orders.status']) }}{{ Form::hidden('status', '2') }}{{ Form::hidden('orders_id', $orders->id) }}{{ Form::submit('Cancel Order') }}{{ Form::close() }}</h2><p>
@endif

{{ $orders->court }}<p>
{{ $orders->plaintiff }}v.{{ $orders->defendant }}<p>
Case: {{ $orders->courtcase }}<p>
Reference: {{ $orders->reference }}<p>
@else
<h2>No Order to display!</h2>
@endif
<h2>{{ Form::open(['route' => 'jobs.create']) }}{{ Form::hidden('orders_id', $orders->id) }}{{ Form::submit('Add Defendants') }}{{ Form::close() }}</h2>
 <div>

     {{ link_to("/documents/upload/?orderId={$orders->id}&_token={$token}", 'Upload Documents') }}<br>
     {{ link_to("/documents/view/?orderId={$orders->id}&_token={$token}", 'View Documents') }}<br>

  </div>
@if (!empty($invoices))
<div>
<h2> Invoices: </h2><p>
<table>
  <tr>
    <th>Date</th>
    <th>Invoice</th>
    <th>Amount</th>
  </tr> 

@foreach($invoice_data as $invoice)

<tr>
<td>{{ $invoices[$invoice->id]["date"] }}</td>
<td><a href="/invoices/{{ $invoices[$invoice->id]["invoice"] }}"> {{ $invoices[$invoice->id]["id"] }} </a></td>
<td>${{ $invoices[$invoice->id]["amount"] }}.00</td>
</tr>

@endforeach
</table>
</div>
<br>
@endif
 <h2>No Defendants to display!</h2>


<a href="{{ URL::previous() }}">Go Back</a>

</body>
</html>
