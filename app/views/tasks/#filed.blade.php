@extends('layouts.default')

@section('content')
<h1>Upload Documents</h1>

{{ Form::open(array('route'=>'documents.storeFiledDocuments','files'=>true)) }}
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

    @foreach($documentsServed as $documentServed)
        @if(!empty($filing) AND $documentServed->document == "Complaint")
   Unfiled {{$documentServed->document}}: <input type="file" name="Unfiled {{$documentServed->document}}" id="">{{ $errors->first($documentServed->document) }}
            {{  '<input type="hidden" name="documents[]" value="Unfiled_'. str_replace(' ', '_', $documentServed->document). '">' }}
    <br/>
        @elseif($documentServed->document == "Complaint")
            Filed {{$documentServed->document}}: <input type="file" name="Filed {{$documentServed->document}}" id="">{{ $errors->first($documentServed->document) }}
            {{  '<input type="hidden" name="documents[]" value="Filed_'. str_replace(' ', '_', $documentServed->document). '">' }}
<br/>

            @elseif((!empty($recording) AND $documentServed->document == "Lis Pendens") OR (!empty($recording) AND $documentServed->document == "Notice of Pendency"))
   Unrecorded {{$documentServed->document}}: <input type="file" name="Unrecorded {{$documentServed->document}}" id="">{{ $errors->first($documentServed->document) }}
            {{  '<input type="hidden" name="documents[]" value="Unrecorded_'. str_replace(' ', '_', $documentServed->document). '">' }}
    <br/>

        @elseif($documentServed->document == "Lis Pendens" OR $documentServed->document == "Notice of Pendency")
            Recorded {{$documentServed->document}}: <input type="file" name="Recorded {{$documentServed->document}}" id="">{{ $errors->first($documentServed->document) }}
            {{  '<input type="hidden" name="documents[]" value="Recorded_'. str_replace(' ', '_', $documentServed->document). '">' }}
            <br/>
    @else

            {{$documentServed->document}}: <input type="file" name="{{$documentServed->document}}" id="">{{ $errors->first($documentServed->document) }}
            {{  '<input type="hidden" name="documents[]" value="'. str_replace(' ', '_', $documentServed->document). '">' }}
            <br/>
    @endif

    @endforeach
    <p>
    {{ Form::hidden('orderId', $task->order_id) }}
    {{ Form::hidden('taskId', $task->id) }}
    {{ Form::hidden('documents', $documentsServed) }}
    <!-- submit buttons -->
    {{ Form::submit('Upload Document') }}
@stop
