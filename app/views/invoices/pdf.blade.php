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
<h1>Invoice # {{ $invoice->id }}</h1><p>
{{ $date }}<br>
<h2>Bill To:</h2><br>
{{ $client->name }}<br>
{{ $client->street }}<br>
{{ $client->city }}, {{ $client->state }}&nbsp;{{ $client->zip_code }}<br>


<div>
Order #{{ $job->order_id }}<br>
Job #{{ $job->id }}<p>
<table>
  <tr>
    <th>Service</th>
    @if(!empty($serve->data))
    <th>Service Date</th>
    @endif
    <th>Servee</th>
    <th>Location</th>
    <th>Amount Due</th>
    
  </tr>

  <tr>
      <td>{{ $job->service }}</td>
      @if(!empty($serve->data))
      <td>{{ $serve->date }}&nbsp;{{ $serve->time }}</td>
      @endif
      <td>{{ $job->defendant }}</td>
      <td>{{ $job->street }},&nbsp;{{ $job->city }},&nbsp;{{ $job->state }}&nbsp;{{ $job->zipcode }}</td>
      <td>${{ $invoice->client_amt }}.00</td>


  </tr>
</table>
</div>
-------------------------------------------<p>
<h2><table>
<tr><td>Amount Due</td><td>${{ $invoice->client_amt }}.00</td>
</tr>
</table></h2>

</body>
</html>
