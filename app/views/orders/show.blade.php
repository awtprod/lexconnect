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

{{ link_to('/orders/create', 'New Order') }}

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
Case: {{ $orders->case }}<p>
Reference: {{ $orders->reference }}<p>
@else
<h2>No Order to display!</h2>
@endif
<h2>{{ Form::open(['route' => 'jobs.create']) }}{{ Form::hidden('orders_id', $orders->id) }}{{ Form::submit('Add Defendants') }}{{ Form::close() }}</h2>
 <div> 
{{ Form::open(array('route'=>'orders.documents','files'=>true)) }}
  	{{ $errors->first('documents') }}<p>
  <input type="file" name="documents" id="">
  <br/>
  {{ Form::hidden('orders_id', $orders->id) }}
  <!-- submit buttons -->
  {{ Form::submit('Upload Service Documents') }}

  
  {{ Form::close() }}
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

@if (!empty($filingtask))


<h2>Filing/Recording:</h2>
<table>
  <tr>
    <th>Status</th>
@if(!empty($filingtask["deadline"]))
    <th>Due Date</th>	
@endif
    <th>Documents</th>
    <th>Actions</th>
  </tr>

  <tr>

    <td>{{ $filingtask["description"] }}</td>
@if(!empty($filingtask["deadline"]))
    <td>{{ $filingtask["deadline"] }}</td>
@endif
    @if(!empty($filingtask["file"]))
    <td><a href="/service_documents/{{ $filingtask["file"] }}"> Service Documents </a></td>
    @else
    <td></td>
    @endif
@if($filingtask["status"] == 1)

<td>{{ Form::open(['route' => 'jobs.status']) }}{{ Form::hidden('status', '0') }}{{ Form::hidden('id', $filingtask["job"]) }}{{ Form::submit('Remove Job from Hold') }}{{ Form::close() }}</td>

@elseif($filingtask["status"] == 2)

<td>{{ Form::open(['route' => 'orders.status']) }}{{ Form::hidden('status', '0') }}{{ Form::hidden('orders_id', $filingtask["order"]) }}{{ Form::submit('Remove Order from Hold') }}{{ Form::close() }}</td>

@else
    <td>{{ Form::open(['route' => 'jobs.status']) }}{{ Form::hidden('status', '1') }}{{ Form::hidden('id', $filingtask["job"]) }}{{ Form::submit('Place Job on Hold') }}{{ Form::close() }}{{ Form::open(['route' => 'jobs.status']) }}{{ Form::hidden('status', '2') }}{{ Form::hidden('id', $filingtask["job"]) }}{{ Form::submit('Cancel Job') }}{{ Form::close() }}</td>
@endif
    </tr>
</table>
@endif

<table>
  
@if(!empty($filing))

<tr>
    <td>{{ $filing["description"] }}</td>
    <td><a href="/service_documents/{{ $filing["file"] }}"> Service Documents </a></td>
</tr>
    @endif
@if(!empty($recording))
<tr>    
<td>{{ $recording["description"] }}</td>
    <td><a href="/recorded_documents/{{ $recording["file"] }}"> Recorded Documents </a></td>
</tr>
    @endif
</tr>

</table>
@if (!empty($completed))

<h2> Completed Serves: </h2><p>
<table>
  <tr>
    <th>Defendant</th>
    <th>Service</th>		
    <th>Served Upon</th>
    <th>Date</th>
    <th>Time</th>
    <th>Serve Address</th>
    <th>Proof</th>
    <th>Actions</th>
  </tr>

  @foreach ($servees as $servee)
  @if(!empty($completed[$servee->id]))
<tr>
<td>{{ link_to("/servee/{$servee->id}", $servee->defendant) }}</td>
<td>{{ $completed[$servee->id]["description"] }}</td>
<td>{{ $completed[$servee->id]["served_upon"] }}</td>
<td>{{ $completed[$servee->id]["date"] }}</td>
<td>{{ $completed[$servee->id]["time"] }}</td>
<td>{{ $completed[$servee->id]["street"] }}<br>{{ $completed[$servee->id]["city"] }},&nbsp;{{ $completed[$servee->id]["state"] }}&nbsp;{{ $completed[$servee->id]["zipcode"] }}</td>
@if($completed[$servee->id]["proof"] == NULL)
<td>N/A</td>
@else
<td>{{ link_to("/proofs/{$completed[$servee->id]["proof"]}", 'Proof') }} 
@endif
@if(!empty($completed[$servee->id]["declaration"]))
{{ link_to("/declarations/{$completed[$servee->id]["declaration"]}", 'Declaration of Mailing') }}
@endif
</td>
<td>{{ Form::open(['route' => 'jobs.add']) }}{{ Form::hidden('servee_id', $servee->id) }}{{ Form::submit('New Serve Address') }}{{ Form::close() }}</td>
</tr>
   @endif
@endforeach
</table>
@endif

@if (!empty($progress))

<h2> Serves In Progress: </h2><p>
<table>
  <tr>
    <th>Defendant</th>
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
