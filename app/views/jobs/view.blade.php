<html>
<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    <script>
        $(document).ready(function() {
            function task_table() {
                var jobId = $('#jobId').val();

                $.ajax({
                    method: 'GET', // Type of response and matches what we said in the route
                    url: '/tasks/jobsTable', // This is the url we gave in the route
                    data: {jobId: jobId}, // a JSON object to send back
                    success: function (response) { // What to do if we succeed
                        console.log(response);
                        $('#taskTable').html(data);
                    },
                    error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                        console.log(JSON.stringify(jqXHR));
                        console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                    }
                });
            }

            task_table()
        })
    </script>
</head>
<body>
<input type="hidden" id="jobId" value="{{$jobs->id}}">
<h1>Job #{{ $jobs->id }}</h1><p>
    Defendant: {{$jobs->defendant}}<br>
                {{$jobs->street}} @if(!empty($jobs->street2)),&nbsp;{{$jobs->street2}}@endif<br>
                {{$jobs->city}}, &nbsp; {{$jobs->state}}&nbsp; {{$jobs->zipcode}}<p>

    {{ link_to("/documents/view/?jobId={$jobs->id}&_token={$token}", 'View Documents') }}&nbsp;  {{link_to("/tasks/service_documents/{$jobs->id}","Download Service Documents",["target"=>"_blank"])}}<p>
    <br>



<table>
  <tr>
    <th>Task</th>
    <th>Deadline</th>
    <th>Completed</th>
  </tr>

    @foreach($tasks as $task)
<tr>
<td>{{ $data[$task->id]["process"] }}</td>
<td>{{ $data[$task->id]["deadline"] }}</td>
@if($data[$task->id]["completion"] == 'true')
    <td>{{ link_to("/tasks/complete/?id={$task->id}&_token={$token}", 'Complete Task') }}</td>
@else
<td>{{$data[$task->id]["completion"]}}</td>
@endif
</tr>

    @endforeach
</table>
</body>
</html>