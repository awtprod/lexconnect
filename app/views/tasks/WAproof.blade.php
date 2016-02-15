<html xmlns="http://www.w3.org/1999/html">
<head>
<style>
                table, th, td {
                        border: 1px solid black;
                        border-collapse: collapse;
                }
</style>
</head>
<body>
<div align="center">IN THE SUPERIOR COURT OF WASHINGTON<br> IN AND FOR THE COUNTY OF {{strtoupper($court->county)}}</div><p>

<table style="width:60%">
 <tr>
<td>
{{$order->plaintiff}},<br>
        Plaintiff,<br>
        vs.<br>
{{$job->defendant}},<br>
        Defendant.<br>
</td>

<td>
        Case No.{{$order->courtcase}}<br>

                Declaration of Service<br>
</td>
 </tr>
</table><br>
<div>

I,<b>{{ $server->fname }}&nbsp;{{$server->lname}}</b>, declare that I am a competent person 18 years of age or older and not a party to this action, and that within the boundaries of the state where service was effected, I was authorized by law to
perform said service. <p>

I inquired if subject was a member of the U.S. Military and was informed they are not.<p>

@if($serve->sub_served =='0')
{{-- Personal Service --}}
On the <b>{{$data["date"]}}</b> at <b>{{$data["time"]}}</b>, I PERSONALLY served <b>{{ $job->defendant }}</b> the following documents: <b>@foreach ($docsServed as $docServed){{ $docServed->documents }},&nbsp;@endforeach</b>
upon<b> {{ $job->defendant }}</b> at <b> {{ $job->street }}, {{ $job->city }}, {{ $job->state }}&nbsp;{{ $job->zipcode }}.</b><p>

@elseif($serve->sub_served =='1')
{{-- Sub-Service --}}
On the <b>{{$data["dateTime"]}}</b>, I served <b>{{ $job->defendant }}</b> the following documents: <b>@foreach ($docsServed as $docServed){{ $docServed->documents }},&nbsp;@endforeach</b>
by delivering them to, <b> {{ $serve->served_upon }}</b>, {{$serve->relationship}}, at <b> {{ $job->street }}, {{ $job->city }}, {{ $job->state }}&nbsp;{{ $job->zipcode }}.</b><p>
@endif

<b>Description</b>: Age:{{$serve->age}}&nbsp;Sex:{{$serve->gender}}&nbsp;Race:{{$serve->race}}&nbsp;Height:{{$serve->height}}&nbsp;Weight:{{$serve->weight}}&nbsp;Hair:{{$serve->hair}}&nbsp;@if(!empty($serve->moustache))Moustache:{{$serve->moustache}}&nbsp;@endif @if(!empty($serve->beard))Beard:{{$serve->beard}}&nbsp;@endif @if(!empty($serve->glasses))Glasses:{{$serve->glasses}}@endif</p>

<b>I hereby declare that the above statement is true to the best of my knowledge and belief, and that I understand it is made for use as evidence in court and is subject to penalty for perjury.</b>
</div><p>

-------------------------------------------<br>
{{ $server->fname }}&nbsp;{{$server->lname}}<br>
@if(!empty($server->county)){{$server->county}}&nbsp;County&nbsp;,@endif @if(!empty($server->state))&nbsp;{{$server->state}}@endif @if(!empty($server->registration))Registration:{{$server->registration}}<br>@endif
{{$serverFirm->street}}@if(!empty($serverFirm->street2)),{{$serverFirm->street2}}@endif<br>
{{$serverFirm->city}},{{$serverFirm->state}}&nbsp;{{$serverFirm->zip_code}}<br>
{{$serverFirm->phone}}
</body>
</html>