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

{{ link_to('/orders/{$servee->order_id}', "Order # {$servee->order_id}") }}

<h2>Defendant:{{ $servee->defendant }}</h2><p>
@if (!empty($serveeinvoices))
<div>
<h2> Invoices: </h2><p>
<table>
  <tr>
    <th>Date</th>
    <th>Invoice</th>
    <th>Amount</th>
  </tr> 

@foreach($serveeinvoices as $invoice)

<tr>
<td>{{ date("m/d/y", strtotime($invoice->created_at)) }}</td>
<td><a href="/invoices/{{ $invoice->invoice }}"> {{ $invoice->id }} </a></td>
<td>${{ $invoice->amount }}.00</td>
</tr>

@endforeach
</table>
</div>
<br>
@endif


</table>
@if (!empty($completed))

<h2> Completed Serves: </h2><p>
<table>
  <tr>
    <th>Date</th>
    <th>Time</th>
    <th>Service</th>		
    <th>Served Upon</th>
    <th>Serve Address</th>
    <th>Proof</th>
  </tr>

  @foreach ($completed as $complete)
  @if(!empty($serves[$complete->id]))
<tr>
<td>{{ $serves[$complete->id]["date"] }}</td>
<td>{{ $serves[$complete->id]["time"] }}</td>
<td>{{ $serves[$complete->id]["description"] }}</td>
<td>{{ $serves[$complete->id]["served_upon"] }}</td>
<td>{{$serves[$complete->id]["street"] }}<br>{{ $serves[$complete->id]["city"] }},&nbsp;{{ $serves[$complete->id]["state"] }}&nbsp;{{ $serves[$complete->id]["zipcode"] }}</td>
@if($serves[$complete->id]["proof"] == NULL)
<td>N/A</td>
@else
<td>{{ link_to("/proofs/{$serves[$complete->id]["proof"]}", 'Proof') }} 
@endif
@if(!empty($serves[$complete->id]["declaration"]))
{{ link_to("/proofs/{$serves[$complete->id]["declaration"]}", 'Declaration of Mailing') }}
@endif
</td>
</tr>
   @endif
@endforeach
</table>
@endif

@if (!empty($active))

<h2> Serve In Progress: </h2><p>
<table>
  <tr>
    <th>Task</th>
    <th>Status</th>
    <th>Due Date</th>
    <th>Actions</th>
  </tr>

  @foreach ($servees as $servee)

  @if(!empty($progress[$servee->id]))
<tr>
<td>{{ link_to("/servee/{$servee->id}", $servee->defendant) }}</td> 
<td>{{ $progress[$servee->id]["description"] }}</td>

@if($progress[$servee->id]["status"] == 1 OR $progress[$servee->id]["status"] == 2)
<td>
{{ $progress[$servee->id]["hold"] }}
</td>
@elseif(!empty($progress[$servee->id]["status"]["date"]))

<td>
<b>{{ $progress[$servee->id]["status"]["date"] }}</b> at <b> {{ $progress[$servee->id]["status"]["time"] }}</b>: &nbsp; {{ $progress[$servee->id]["status"]["description"] }}
</td>
@else
<td>
{{ $progress[$servee->id]["status"]["description"] }}
</td>
@endif

<td>{{ $progress[$servee->id]["deadline"] }}</td>

@if($progress[$servee->id]["status"] == 1)

<td>{{ Form::open(['route' => 'jobs.status']) }}{{ Form::hidden('status', '0') }}{{ Form::hidden('id', $progress[$servee->id]["job"]) }}{{ Form::submit('Remove Job from Hold') }}{{ Form::close() }}{{ Form::open(['route' => 'jobs.status']) }}{{ Form::hidden('status', '2') }}{{ Form::hidden('id', $progress[$servee->id]["job"]) }}{{ Form::submit('Cancel Job') }}{{ Form::close() }}</td>

@elseif($progress[$servee->id]["status"] == 2)

<td>{{ Form::open(['route' => 'orders.status']) }}{{ Form::hidden('status', '0') }}{{ Form::hidden('id', $progress[$servee->id]["order"]) }}{{ Form::submit('Remove Order from Hold') }}{{ Form::close() }}</td>

@else
<td>{{ Form::open(['route' => 'jobs.status']) }}{{ Form::hidden('status', '1') }}{{ Form::hidden('id', $progress[$servee->id]["job"]) }}{{ Form::submit('Place Job on Hold') }}{{ Form::close() }}{{ Form::open(['route' => 'jobs.status']) }}{{ Form::hidden('status', '2') }}{{ Form::hidden('id', $progress[$servee->id]["job"]) }}{{ Form::submit('Cancel Job') }}{{ Form::close() }}</td>
@endif

</tr>
   @endif
@endforeach

</table>
@else <h2>No Defendants to display!</h2>
@endif

<a href="{{ URL::previous() }}">Go Back</a>

</body>
</html>
