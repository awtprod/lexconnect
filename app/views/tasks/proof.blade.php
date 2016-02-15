@extends('layouts.default')

@section('content')
<div>
{{ Form::open(['route' => 'tasks.proof']) }}
{{ Form::label('server', 'Server: ') }}
{{ Form::select('server', $servers) }}
{{ Form::hidden('jobId', $job->id) }}
{{ Form::submit('Generate Proof') }}
{{ Form::close() }}

</div>
@if(!empty($proof))

    <a href="/documents/{{ $proof->id }}" target="_blank"> Unexecuted Proof </a><br>
@endif

    {{ Form::open(array('route'=>'jobs.proof','files'=>true)) }}
    {{ $errors->first('proof') }}<p>
        <input type="file" name="proof" id="">
        <br/>
        {{ Form::hidden('jobId', $job->id) }}
                <!-- submit buttons -->
        {{ Form::submit('Upload Executed Proof') }}


        {{ Form::close() }}

<a href="{{ URL::previous() }}">Go Back</a>
@stop
