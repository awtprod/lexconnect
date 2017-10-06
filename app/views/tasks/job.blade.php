<html>
<head>

</head>
<body>
<input type="hidden" id="jobId" value="{{$jobs->id}}">


@if (!empty($tasks))

    <table>
        <tr>
            <th>Task</th>
            <th>Deadline</th>
            <th>Group</th>
            <th>Completed</th>
        </tr>
        @foreach ($tasks as $task)
            <tr>
                <td>{{ str_replace('_', ' ', $task->process) }}</td>
                <td>{{ date("n/j/y", strtotime($task->deadline))}}</td>
                <td>{{ $tableData[$task->id]["group"] }}</td>

                @if($task->status == 1 AND $task->completion == NULL)

                    @if((Auth::user()->user_role=="Admin" AND $tableData[$task->id]["group"]=="Admin"))
                    <td><input type="button" name="view" value="Complete Task" id={{ $task->id }} class="btn btn-info btn-xs view_data" /></td>
                    @elseif(Auth::user()->user_role=="Admin" OR (Auth::user()->user_role=="Vendor" AND Auth::user()->company_id == $task->group))
                    <td><input type="button" name="view" value="Complete Task" id={{ $task->id }} class="btn btn-info btn-xs view_data" /></td>
                    @else

                <td></td>

                @endif
                @elseif($task->completion != NULL)
                    <td>{{ date("n/j/y", strtotime($task->completion))}}</td>
                @else
                <td></td>
                @endif
                @endforeach
        </tr>
                </table>
                @else
                    <h2>No tasks to display!</h2>
                @endif


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

                        $(document).ready(function() {
                            $('.view_data').click(function () {
                                var task_id = $(this).attr("id");
                                var token = $('#token').val();
                                $.ajax({
                                    method: 'POST', // Type of response and matches what we said in the route
                                    url: '/tasks/complete', // This is the url we gave in the route
                                    data: {id: task_id, _token: token },
                                    success: function(response) {
                                        if($.trim(response) == 'no') {
                                            console.log(response);
                                            task_table()
                                        }
                                        else {
                                            console.log(response);
                                            $('#complete_task').html(response['body']);
                                            $('#task-title').html(response['title']);
                                            $('#dataModal').modal("show");
                                        }
                                    },
                                    error: function(jqXHR, textStatus, errorThrown) { // What to do if we fail
                                        console.log(JSON.stringify(jqXHR));
                                        console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                                    }
                                });
                            });

                        });
                    });
                </script>
    </tr>
    </table>
            </body>
</html>
