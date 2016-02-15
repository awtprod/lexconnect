<html>
<head></head>
<body>
<div align="center">IN THE SUPERIOR COURT OF WASHINGTON</p> IN AND FOR THE COUNTY OF {{strtoupper($court->county)}}</div></p>
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

            Declaration of Attempted Service<br>
        </td>
    </tr>
</table><br>
<div>

I,<b>{{ $server->fname }}&nbsp;{{$server->lname}}</b>, declare that I am a competent person 18 years of age or older
    and not a party to this action, and that within the boundaries of the state where service was effected,
    I was authorized by law to perform said service. <p>

I attempted to serve <b>{{ $job->defendant }}</b> at <b> {{ $job->street }}, {{ $job->city }}, {{ $job->state }}&nbsp;{{ $job->zipcode }}</b> on the following dates and times:<p>
@foreach($a as $attempts){{ $attempts["date"] }}&nbsp;{{ $attempts["time"] }}: {{ $attempts["description"] }}@endforeach

<b>I hereby declare that the above statement is true to the best of my knowledge and belief, and that I understand it is made for use as evidence in court and is subject to penalty for perjury.</b>

</div>

-------------------------------------------<br>
{{ $server->fname }}&nbsp;{{$server->lname}}<br>
@if(!empty($server->county)){{$server->county}}&nbsp;County&nbsp;,@endif @if(!empty($server->state))&nbsp;{{$server->state}}@endif @if(!empty($server->registration))Registration:{{$server->registration}}<br>@endif
{{$serverFirm->street}}@if(!empty($serverFirm->street2)),{{$serverFirm->street2}}@endif<br>
{{$serverFirm->city}},{{$serverFirm->state}}&nbsp;{{$serverFirm->zip_code}}<br>
{{$serverFirm->phone}}
</body>
</html>
