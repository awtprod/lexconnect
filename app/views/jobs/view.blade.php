@extends('layouts.default')

@section('content')
<h1>Job #{{ $jobs->id }}</h1><p>

    {{ link_to("/documents/view/?jobId={$jobs->id}&_token={$token}", 'View Documents') }}<br>


<table>
  <tr>
    <th>Task</th>
    <th>Deadline</th>
    <th>Completed</th>
  </tr>

    @foreach($tasks as $task)
<tr>
<td>{{ $data[$task->id]["process"] }}</td>
<td>{{ $data[$task->id]["deadline"] }}</td>
@if($data[$task->id]["completion"] == 'true')
    <td>{{ link_to("/tasks/complete/?id={$task->id}&_token={$token}", 'Complete Task') }}</td>
@else
<td>{{$data[$task->id]["completion"]}}</td>
@endif
</tr>

    @endforeach
</table>
@stop
