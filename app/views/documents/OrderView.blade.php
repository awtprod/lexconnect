@extends('layouts.default')

@section('content')
<h1>Documents for Order #{{$order->id}}</h1>

@if(count($documents)>0)
<table>
    <tr>
        <th>Document</th>
        <th>Date Uploaded</th>
    </tr>
@foreach($documents as $document)
<tr>
    <td>{{link_to("{$document->filepath}/{$document->filename}", str_replace('_', ' ', $document->document))}}</td>
    <td>{{$document->created_at}}</td>
    </tr>
@endforeach
    </table>
@else
<h2>No Documents to Display!</h2>
@endif
@stop
