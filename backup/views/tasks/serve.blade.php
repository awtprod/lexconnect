@extends('layouts.default')

@section('content')
<h1>Declaration of Service</h1>

<div>
I,{{ $server }} , being first duly sworn, depose and say: that I am over the age of 18 years and 
not a party to this action, and that within the boundaries of the state where service was effected, I was authorized by law to 
perform said service. <p>

I served <b>{{ $proof->defendant }}</b> with <b>Summons and Complaint</b> by leaving with <b>{{ $data['served'] }}, {{ $data['relationship'] }}</b> At {{ $proof->street }},{{ $proof->city }},{{ $proof->state }}{{ $proof->zipcode }}
On {{ $data['date'] }} AT {{ $data['time'] }}.<p>
 
Description:. Age: {{ $serve->age }} Sex: {{ $serve->gender }} Race: {{ $serve->race }} Height: {{ $serve->height }} Weight: {{ $serve->weight }} Hair: {{ $serve->hair }} Beard: {{ $serve->beard }} Glasses: {{ $serve->glasses }} Moustache: {{ $serve->moustache }}<p>

 Inquired if subject was a member of the U.S. Military and was informed they are not.<p>
</div>
-------------------------------------------<p>
{{ $server }}<p>
Process Server<p>

@stop
