@extends('layouts.default')
@section('head')
@section('content')
<td>{{ Form::open(['route' => 'search.index']) }}{{ Form::text('search') }}{{ Form::submit('Search') }}{{ Form::close() }}</td>
<h1>Task List</h1>


@if (!empty($tasks))

<table>
  <tr>
    <th>Job #</th>
    <th>Order #</th>
    <th>Vendor</th>
    <th>Task</th>		
    <th>Defendant</th>
    <th>Deadline</th>
  </tr>
@foreach ($tasks as $task)
<tr>
<td>{{ link_to("/jobs/{$task->job_id}", $task->job_id) }}</td>
<td>{{ link_to("/orders/{$task->order_id}", $task->order_id) }}</td>
<td></td>
<td>{{ str_replace('_', ' ', $task->service) }}</td>
<td></td>
<td>{{ date("m/d/y", strtotime($task->deadline))}}</td>

@endforeach
@else
<h2>No tasks to display!</h2>
@endif

<a href="{{ URL::previous() }}">Go Back</a>
@stop
