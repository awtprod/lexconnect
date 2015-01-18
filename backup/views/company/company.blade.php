@extends('layouts.default')

@section('content')
@foreach ($companies as $company)
<p>Company: {{ $company->company }}</p>
@endforeach
@stop
