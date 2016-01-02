@extends('layouts.default')

@section('content')
<h1>QA Documents</h1><p>
<div>
    <table>
    @foreach($documents as $document)

<tr>
    <td>
        {{ link_to("/{$document->filepath}/{$document->filename}", str_replace('_', ' ',$document->document))}}
    </td>
</tr>


    @endforeach
</table>

    {{ Form::open(['route' => 'tasks.qa']) }}
</div>
<a href="{{ URL::previous() }}">Go Back</a>
@stop
