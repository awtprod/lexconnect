@extends('layouts.default')

@section('content')
<h1>Upload Documents</h1>

{{ Form::open(array('route'=>'documents.filedDocuments','files'=>true)) }}
{{ $errors->first('documents') }}<p>

<div>
    {{ Form::label('date', 'Date Filed: ') }}
    {{ Form::input('date', 'date') }}
    {{ $errors->first('date') }}
</div>
<div>
    {{ Form::label('case', 'Court Case: ') }}
    {{ Form::text('case') }}
    {{ $errors->first('case') }}
</div>
{{ $errors->first('filedDocuments') }}<p>
   Filed Documents: <input type="file" name="filedDocuments" id="">
    <br/>

    <p>
    {{ Form::hidden('taskId', $taskId) }}
    <!-- submit buttons -->
    {{ Form::submit('Upload Documents') }}
@stop
