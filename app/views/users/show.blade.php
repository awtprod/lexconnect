@extends('layouts.default')

@section('content')
<p>Company: {{ $user->company }}</p>
<p>Employee: {{ $user->name }}</p>
@stop
