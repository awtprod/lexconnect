@extends('layouts.default')
@section('head')
@section('content')
<td>{{ Form::open(['route' => 'search.index']) }}{{ Form::text('search') }}{{ Form::submit('Search') }}{{ Form::close() }}</td>
<h1>View Active Tasks</h1>


@if (!empty($tasks))

<table>
  <tr>
    <th>Job #</th>
    <th>Task</th>		
    <th>Defendant</th>
    <th>Deadline</th>
  </tr>
@foreach ($tasks as $task)
<tr>
<td>{{ link_to("/jobs/{$task->job_id}", $task->job_id) }}</td>
<td>{{ $task->service }}</td>
<td>{{ $task->defendant }}</td>
<td>{{ date("m/d/y", strtotime($task->deadline)) }}</td>

@endforeach
@else
<h2>No tasks to display!</h2>
@endif

<a href="{{ URL::previous() }}">Go Back</a>
@stop
