<html>
<head>

</head>
<body>
<h1>Declaration of Service by Posting</h1>

<div>
I,{{ $server }} , being first duly sworn, depose and say: that I am over the age of 18 years and 
not a party to this action, and that within the boundaries of the state where service was effected, I was authorized by law to 
perform said service. <p>

<b>{{ $proof->defendant }}</b>  On {{ $data['date'] }} I served a Summons and Complaint on the defendant(s) by posting a true and correct copy of each document in a conspicuous place at defendant’s residence at {{ $proof->street }},{{ $proof->city }},{{ $proof->state }}{{ $proof->zipcode }}.<p>

</div>
-------------------------------------------<p>
{{ $server }}<p>
Process Server<p>
</body>
</html>
