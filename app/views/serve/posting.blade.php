@extends('layouts.default')

@section('content')
<h1>Completed Posting</h1>

{{ Form::open(['route' => 'serve.store']) }}

	<div>
	{{ Form::label('date', 'Date: ') }}
	{{ Form::input('date', 'date') }}
	{{ $errors->first('date') }}
	</div>
	<div>
	{{ Form::label('time', 'Time: ') }}
	{{ Form::input('time', 'time') }}
	{{ $errors->first('time') }}
	</div>
	{{ Form::hidden('tasks_id', $task->id) }}
	<div>{{ Form::submit('Complete Posting') }}{{ Form::reset('Reset') }}</div>
{{ Form::close() }}
<a href="{{ URL::previous() }}">Go Back</a>
@stop
