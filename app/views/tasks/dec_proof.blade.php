@extends('layouts.default')

@section('content')
<h1>Declaration of Mailing</h1>

<div>

I,{{ $data['declarant'] }} , being first duly sworn, depose and say: that I am over the age of 18 years and 
not a party to this action, and that within the boundaries of the state where service was effected, I was authorized by law to 
perform said service. <p>

On {{ $data['mail_date'] }}, after serving documents on {{ $data['serve_date'] }} upon {{ $data['served'] }}, a copy of the summons and complaint were mailed, postage prepaid, first class to:<p>
	{{ $data['defendant'] }}<p>	
	{{ $data['street'] }}<p>
	{{ $data['city'] }},{{ $data['state'] }}{{ $data['zip'] }}<p>
	
I declare under penalty of perjury under the laws of the State of {{ $data['state'] }} that the foregoing is true and correct.<p>

DATED this ____ day of {{ $data['month'] }}, {{ $data['year'] }}.
</div>
-------------------------------------------<p>
{{ $data['declarant'] }}<p>
Declarant<p>

@stop
