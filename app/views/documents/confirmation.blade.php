@extends('layouts.default')

@section('content')
<h1>Documents Uploaded!</h1>

@foreach($docsUploaded as $docUploaded)

    <h2>{{str_replace('_', ' ', $docUploaded)}}</h2><p>
@endforeach
@stop
