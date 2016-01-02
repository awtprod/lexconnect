@extends('layouts.default')

@section('content')
<h1>Send Proof of Service</h1><p>
<div>

    {{ Form::open(['route'=>'tasks.proofFiled']) }}
Send Proof to: {{$order->court}}
        <br/>
        {{ Form::hidden('taskId', $task->id) }}
        <!-- submit buttons -->
        {{ Form::submit('Proof Filed') }}


        {{ Form::close() }}

<a href="{{ URL::previous() }}">Go Back</a>
@stop
