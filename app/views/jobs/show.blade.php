@extends('layouts.default')

@section('content')
<h1>Job #{{ $jobs->id }}</h1>

<div>



<table>
  <tr>
    <th>Task</th>
    <th>Deadline</th>
    <th>Completed</th>
    <th>Actions</th>
  </tr>
@foreach ($jobtask as $jobtasks)
<tr>
<td>{{ $jobtasks["description"] }}</td>
<td>{{ $jobtasks["deadline"] }}</td>
@if ($jobtasks["completed"] == NULL)
<td></td>
@else
<td>{{ $jobtasks["completed"] }}</td>
@endif
@if ($jobtasks["action"] == NULL)
<td></td>

@else
<td>{{ Form::open(['route' => 'tasks.complete']) }}{{ Form::token() }}{{ Form::hidden('tasks_id', $jobtasks["action"]) }}{{ Form::submit('Complete') }}{{ Form::close() }}</td>
@endif
@endforeach

@stop
