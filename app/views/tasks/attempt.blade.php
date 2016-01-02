@extends('layouts.default')

@section('content')
<h1>Enter Service Results:</h1><p>
<div>

    {{ Form::open(['route'=>'tasks.attempt']) }}
        <br/>
    {{ Form::hidden('served', 'true') }}
    {{ Form::hidden('taskId', $taskId) }}
        <!-- submit buttons -->
        {{ Form::submit('Defendant Served') }}


        {{ Form::close() }}

    {{ Form::open(['route'=>'tasks.attempt']) }}
    <br/>
    {{ Form::hidden('served', 'false') }}
    {{ Form::hidden('taskId', $taskId) }}
            <!-- submit buttons -->
    {{ Form::submit('Enter Service Attempt') }}


    {{ Form::close() }}

<a href="{{ URL::previous() }}">Go Back</a>
@stop
