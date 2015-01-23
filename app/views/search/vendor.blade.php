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
<h1>Search Results</h1>

<td>{{ Form::open(['route' => 'search.index']) }}{{ Form::text('search') }}{{ Form::submit('Search') }}{{ Form::close() }}</td>

@if(!empty($search_results))

<table>
  <tr>
    <th>Job #</th>
    <th>Court Case #</th>
    <th>Plaintiff</th>
    <th>Defendant</th>
    <th>State</th>
    <th>Court</th>

  </tr>



@foreach($results['jobs'] as $result)

<tr>
<td>{{ link_to("/jobs/{$search_results[$result->order_id]["job_id"]}", $search_results[$result->order_id]["job_id"]) }}</td>
<td>{{ $search_results[$result->order_id]["case"] }}</td> 
<td>{{ $search_results[$result->order_id]["plaintiff"] }}</td>
<td>{{ $search_results[$result->order_id]["defendant"] }}</td>
<td>{{ $search_results[$result->order_id]["state"] }}</td>
<td>{{ $search_results[$result->order_id]["court"] }}</td>
@endforeach


@if(!empty($results['orders']))
@foreach($searchjobs as $searchjob)

<tr>
<td>{{ link_to("/jobs/{$search_results[$searchjob->id]["job_id"]}", $search_results[$searchjob->id]["job_id"]) }}</td>
<td>{{ $search_results[$searchjob->id]["case"] }}</td> 
<td>{{ $search_results[$searchjob->id]["plaintiff"] }}</td>
<td>{{ $search_results[$searchjob->id]["defendant"] }}</td>
<td>{{ $search_results[$searchjob->id]["state"] }}</td>
<td>{{ $search_results[$searchjob->id]["court"] }}</td>
@endforeach 
@endif
@else
<h2>No Jobs Found!</h2>
@endif

<a href="{{ URL::previous() }}">Go Back</a>

</body>
</html>
