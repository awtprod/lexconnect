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
<h1>Invoice</h1><p>
<h2>Bill To:</h2><p>
{{ $data['client'] }}<br>
{{ $data['client_street'] }}<br>
{{ $data['client_city'] }}, {{ $data['client_state'] }}&nbsp;{{ $data['client_zip'] }}<br>


<div>
Order #{{ $data['order'] }}<br>
Job #{{ $data['job'] }}<p>
<table>
  <tr>
    <th>Service</th>
    <th>Service Date</th>	
    <th>Servee</th>
    <th>Location</th>
    <th>Amount Due</th>
    
  </tr>

  <tr>
      <td>{{ $data['product'] }}</td>
      <td>{{ $data['serve_date'] }}</td>
      <td>{{ $data['defendant'] }}</td>
      <td>{{ $data['street'] }},&nbsp;{{ $data['city'] }},&nbsp;{{ $data['state'] }}&nbsp;{{ $data['zip'] }}</td>
      <td>${{ $data['client_fee'] }}.00</td>


  </tr>
</table>
</div>
-------------------------------------------<p>
<h2><table>
<tr><td>Amount Due</td><td>${{ $data['client_fee'] }}.00</td>
</tr>
</table>

</body>
</html>
