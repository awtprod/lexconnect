@extends('layouts.default')
@section('head')
@section('content')
<td>{{ Form::open(['route' => 'search.index']) }}{{ Form::text('search') }}{{ Form::submit('Search') }}{{ Form::close() }}</td>
<h1>View Active Jobs</h1>


@if (!empty($job))

<table>
  <tr>
    <th>Job #</th>
    <th>Task</th>		
    <th>Defendant</th>
    <th>Deadline</th>
    <th></th>
  </tr>
@foreach ($job as $jobs)
<tr>
<td>{{ link_to("/jobs/{$jobs["id"]}", $jobs["id"]) }}</td>
<td>{{ $jobs["task"] }}</td>
<td>{{ $jobs["defendant"] }}</td> 
@if (!empty($jobs["link"]["link"]))
<td>{{ link_to("/reprojections/{$jobs["link"]["link"]}", $jobs["deadline"]) }} </td>
@else
<td>{{ $jobs["deadline"] }}</td> 
@endif
@if(!empty($jobs["link"]["text"]))
<td>{{ Form::open(['route' => 'tasks.complete']) }}{{ Form::token() }}{{ Form::hidden('tasks_id', $jobs["link"]["link"]) }}{{ Form::submit($jobs["link"]["text"]) }}{{ Form::close() }}
@if( !empty($jobs["link"]["text2"]))
{{ Form::open(['route' => 'serve.add']) }}{{ Form::token() }}{{ Form::hidden('tasks_id', $jobs["link"]["link"]) }}{{ Form::submit($jobs["link"]["text2"]) }}{{ Form::close() }}
@endif
</td>
@else
 <td></td>
@endif
@endforeach
@else
<h2>No Jobs to display!</h2>
@endif

<a href="{{ URL::previous() }}">Go Back</a>
@stop
