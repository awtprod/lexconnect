@extends('layouts.default')

@section('content')
<h1>Create Proof of Service</h1><p>
<div>


        {{ Form::open(['route' => 'tasks.proof']) }}
        {{ Form::label('server', 'Server: ') }}
        {{ Form::select('server', $servers) }}
        {{ Form::hidden('job_id', $job->id) }}
        {{ Form::submit('Generate Proof') }}
        {{ Form::close() }}

</div>
{{dd($proof) }}
@if(!empty($proof))

    <a href="/documents/{{ $proof->id }}" target="_blank"> Unexecuted Proof </a><br>

    {{ Form::open(array('route'=>'jobs.proof','files'=>true)) }}
    {{ $errors->first('proof') }}<p>
        <input type="file" name="Executed_proof" id="">
        <br/>
        {{ Form::hidden('job_id', $job->id) }}
        {{Form::hidden('taskId', Input::get('id'))}}
        <!-- submit buttons -->
        {{ Form::submit('Upload Executed Proof') }}


        {{ Form::close() }}

        @endif
<a href="{{ URL::previous() }}">Go Back</a>
@stop
