@extends('layouts.default')

@section('content')
<h1>Job Documents</h1>
<table>
    <tr>
        <th>Document</th>
        <th>Date Uploaded</th>
    </tr>
@foreach($serviceDocuments as $serviceDocument)
<tr>
    <td>{{link_to("{$serviceDocument["filepath"]}/{$serviceDocument["filename"]}", str_replace('_', ' ', $serviceDocument["document"]))}}</td>
    <td>{{$serviceDocument["created_at"]}}</td>
    </tr>
@endforeach
    @foreach($jobDocuments as $jobDocument)
        <tr>
            <td>{{link_to("{$jobDocument->filepath}/{$jobDocument->filename}", str_replace('_', ' ', $jobDocument->document))}}</td>
            <td>{{$jobDocument->created_at}}</td>
        </tr>
    @endforeach
    </table>
@stop
