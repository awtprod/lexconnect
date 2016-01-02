@extends('layouts.default')

@section('content')
<h1>Send Proof of Service</h1><p>
<div>

    {{ Form::open(['route'=>'tasks.accept']) }}
        <br/>
    {{ Form::hidden('accept', 'true') }}
    {{ Form::hidden('taskId', $taskId) }}
        <!-- submit buttons -->
        {{ Form::submit('Accept Job') }}


        {{ Form::close() }}

    {{ Form::open(['route'=>'tasks.accept']) }}
    <br/>
    {{ Form::hidden('accept', 'false') }}
    {{ Form::hidden('taskId', $taskId) }}
            <!-- submit buttons -->
    {{ Form::submit('Reject Job') }}


    {{ Form::close() }}

<a href="{{ URL::previous() }}">Go Back</a>
@stop
