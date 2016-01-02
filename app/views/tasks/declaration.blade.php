@extends('layouts.default')

@section('content')
<h1>Create Declaration of Mailing</h1><p>
@if(!empty($proof))
    <table>
    <tr>
<td>
<h2>Upload Executed Declaration of Mailing</h2><p>
        <a href="/declarations/{{ $proof }}"> Unexecuted Declaration </a><br>
{{ Form::open(array('route'=>'jobs.declaration','files'=>true)) }}
  	{{ $errors->first('declaration') }}<p>
  <input type="file" name="Executed_Declaration" id="">
  <br/>
  {{ Form::hidden('job_id', $job->id) }}
  <!-- submit buttons -->
  {{ Form::submit('Upload Executed Declaration') }}
</td>

 @endif
	<div>
        </tr>
        <td>

	<h3> Mail Documents To:</h3><p>
	{{ $job->street }}<p>
	{{ $job->city }},{{ $job->state }}{{ $job->zipcode }}<p>
	</div>
{{ Form::open(['route' => 'tasks.declaration']) }}

	<div>
	{{ Form::label('mail_date', 'Date Mailed: ') }}
	{{ Form::input('date', 'mail_date') }}<p>
	{{ $errors->first('date') }}
	</div>
	<div>
	{{ Form::label('declarant', 'Declarant: ') }}
	{{ Form::select('declarant', $servers) }}<p>
	</div>
	{{ Form::hidden('job_id', $job->id) }}
	{{ Form::hidden('tasks_id', $task->id) }}
	<div>{{ Form::submit('Create Declaration') }}{{ Form::reset('Reset') }}</div>
{{ Form::close() }}
</td>
</table>
<a href="{{ URL::previous() }}">Go Back</a>
@stop
