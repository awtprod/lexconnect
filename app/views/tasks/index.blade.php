@extends('layouts.default')
@section('head')
@stop
@section('content')

<h1>View Active Jobs</h1>


@if (!empty($jobTasks))
<table>
  <tr>
    <th>Defendant</th>
    <th>Task</th>
    <th>Service</th>
    <th>Priority</th>
    <th>Service Address</th>
    <th>Due Date</th>
  </tr>
@foreach ($jobTasks as $jobTask)
<tr>
<td>{{ link_to("/jobs/{$job[$jobTask->id]["id"]}", $job[$jobTask->id]["defendant"]) }}</td>
<td>{{ $jobTask->process }}</td>
<td>{{ $job[$jobTask->id]["service"] }}</td>
<td>{{ $job[$jobTask->id]["priority"] }}</td>
<td>{{ $job[$jobTask->id]["street"]}}&nbsp;{{$job[$jobTask->id]["street2"]}},{{$job[$jobTask->id]["city"]}},{{$job[$jobTask->id]["state"]}}&nbsp;{{$job[$jobTask->id]["zipcode"]}}</td>
<td>{{ date("m/d/y", strtotime($jobTask->deadline)) }}</td>

</tr>
@endforeach

</table>
@else
<h2>No Tasks to display!</h2>
@endif

<a href="{{ URL::previous() }}">Go Back</a>

@stop