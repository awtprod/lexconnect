<html>
<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

</head>
<body>
Defendant: {{$servee->defendant}}<br>
@foreach($jobs as $job)


        Attempt #{{$service_attempts[$job->id]["attempt_count"]}}<br>
    {{$job->street}}, &nbsp;{{$job->city}},&nbsp;{{$job->state}}&nbsp;{{$job->zipcode}}<p>
        @if (count($service_attempts[$job->id]["attempts"])>0)

            <table>
        <tr>
            <th>Date</th>
            <th>Time</th>
            <th>Comment</th>
        </tr>
        @foreach ($service_attempts[$job->id]["attempts"] as $attempt)
            <tr>
                <td>{{ date("n/j/y", strtotime($service_attempts[$job->id][$attempt->id]["attempt"]->date)) }}</td>
                <td>{{ date('h:i A', strtotime($service_attempts[$job->id][$attempt->id]["attempt"]->time))}}</td>
                <td>{{ $service_attempts[$job->id][$attempt->id]["attempt"]->description }}</td>

                @endforeach
        </tr>
                </table>
    @else
        <h2>No attempts to display!</h2>
    @endif
        @endforeach



            </body>
</html>
