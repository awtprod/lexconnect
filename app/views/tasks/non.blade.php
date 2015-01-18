@extends('layouts.default')

@section('content')
<h1>Declaration of Attempted Service</h1>

<div>

I,{{ $server }} , being first duly sworn, depose and say: that I am over the age of 18 years and 
not a party to this action, and that within the boundaries of the state where service was effected, I was authorized by law to 
perform said service. <p>
I attempted to serve <b>{{ $proof->defendant }}</b> at <b> {{ $proof->street }}, {{ $proof->city }}, {{ $proof->state }}&nbsp;{{ $proof->zipcode }}</b> on the following dates and times:<p>
@foreach ($a as $attempts)
{{ $attempts["date"] }}&nbsp;{{ $attempts["time"] }}: {{ $attempts["description"] }}<p>
@endforeach
</div>
-------------------------------------------<p>
{{ $server }}<p>
Process Server<p>

@stop
