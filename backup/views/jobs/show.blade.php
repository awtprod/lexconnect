@extends('layouts.default')

@section('content')
<h1>Job #{{ $jobs->id }}</h1>

<div>
@if($process==7)

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
@foreach ($jobtask as $jobtasks)
<li>{{ $jobtasks["description"] }} &nbsp; {{ $jobtasks["deadline"] }}</li> 
@endforeach

@stop
