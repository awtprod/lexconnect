@extends('layouts.default')

@section('content')
@foreach ($companies as $company)
<p>Company: {{ $company->name }}</p>
@endforeach
@stop
