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
<td>{{ $task->process }}</td>
<td>{{ $task->deadline }}</td>
@if (is_null($task->completion) AND $first  == 'true')
        @if($task->status == '0')
<td>Job on Hold</td>
            {{ $first = false; }}
        @else
<td>{{ link_to("/tasks/complete/?id={$task->id}&_token={$token}", 'Complete Task') }}</td>

            {{ $first = false; }}
@endif
@elseif(is_null($task->completion) AND $first  == 'false')
    <td></td>
@else
<td>{{ date("m/d/y", strtotime($task->completion)) }}</td>
@endif

</tr>

    @endforeach
</table>
@stop
