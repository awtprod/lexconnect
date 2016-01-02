@extends('layouts.default')
@section('head')
@section('content')
<td>{{ Form::open(['route' => 'search.index']) }}{{ Form::text('search') }}{{ Form::submit('Search') }}{{ Form::close() }}</td>
<h1>Task List</h1>


@if (!empty($job))

<table>
  <tr>
    <th>Job #</th>
    <th>Order #</th>
    <th>Vendor</th>
    <th>Task</th>		
    <th>Defendant</th>
    <th>Deadline</th>
  </tr>
@foreach ($job as $jobs)
<tr>
<td>{{ link_to("/jobs/{$jobs["id"]}", $jobs["id"]) }}</td>
<td>{{ link_to("/orders/{$jobs["order_id"]}", $jobs["order_id"]) }}</td>
<td>{{ $jobs["vendor"] }}</td>
<td>{{ $jobs["task"] }}</td>
<td>{{ $jobs["defendant"] }}</td> 
@if (!empty($jobs["link"]["link"]))
<td>{{ link_to("/reprojections/{$jobs["link"]["link"]}", $jobs["deadline"]) }} </td>
@else
<td>{{ $jobs["deadline"] }}</td> 
@endif

@endforeach
@else
<h2>No Jobs to display!</h2>
@endif

<a href="{{ URL::previous() }}">Go Back</a>
@stop
