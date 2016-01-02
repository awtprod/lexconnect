<!DOCTYPE html>
<html>

<head>
<style>
table {
    width:75%;
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
<h1>View Active Jobs</h1>


@if (!empty($jobs))

<table>
  <tr>
    <th>Job #</th>
    <th>Service</th>
    <th>Priority</th>
    <th>Defendant</th>
    <th>Service Address</th>
  </tr>
@foreach ($jobs as $job)
<tr>
<td>{{ link_to("/jobs/{$job->id}", $job->id) }}</td>
<td>{{ $job->service }}</td>
<td>{{ $job->priority }}</td>
<td>{{ $job->defendant }}</td>
<td>{{ $job->street}}&nbsp;{{$job->street2}},{{$job->city}},{{$job->state}}&nbsp;{{$job->zipcode}}</td>
@endforeach
@else
<h2>No Jobs to display!</h2>
@endif

<a href="{{ URL::previous() }}">Go Back</a>

</body>
</html>
