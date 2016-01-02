@extends('layouts.default')

@section('content')
<h1>Job #{{ $jobs->id }}</h1>

<div>
@if($step->process == 2 AND $step->step == 2)

{{ Form::open(['route' => 'tasks.proof']) }}
	{{ Form::label('server', 'Server: ') }}
	{{ Form::select('server', $servers) }}
{{ Form::hidden('job_id', $jobs->id) }}
{{ Form::submit('Generate Proof') }}
{{ Form::close() }}
@endif
</div>

@if(!empty($proof))
{{ Form::open(array('route'=>'jobs.proof','files'=>true)) }}
  	{{ $errors->first('proof') }}<p>
  <input type="file" name="proof" id="">
  <br/>
  {{ Form::hidden('job_id', $jobs->id) }}
  <!-- submit buttons -->
  {{ Form::submit('Upload Executed Proof') }}

  
  {{ Form::close() }}
<a href="/proof/{{ $proof }}"> Proof </a>
@endif

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
