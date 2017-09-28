<html>
<head>


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
