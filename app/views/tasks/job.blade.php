<html>
<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" />
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
    <script src="http://cdn.jsdelivr.net/jquery.validation/1.15.0/jquery.validate.js"></script>
    <script src="http://cdn.jsdelivr.net/jquery.validation/1.13.1/additional-methods.js"></script>
</head>
<body>
<input type="hidden" id="jobId" value="{{$jobs->id}}">


@if (!empty($tasks))

    <table>
        <tr>
            <th>Task</th>
            <th>Deadline</th>
            <th>Group</th>
            <th>Action</th>
        </tr>
        @foreach ($tasks as $task)
            <tr>
                <td>{{ str_replace('_', ' ', $task->process) }}</td>
                <td>{{ date("n/j/y", strtotime($task->deadline))}}</td>
                <td>{{ $tableData[$task->id]["group"] }}</td>

                @if($task->status == 1)
                    
                    @if((Auth::user()->user_role=="Admin" AND $tableData[$task->id]["group"]=="Admin"))
                    <td><input type="button" name="view" value="Complete Task" id={{ $task->id }} class="btn btn-info btn-xs view_data" /></td>
                    @elseif(Auth::user()->user_role=="Admin" OR Auth::user()->user_role=="Vendor")
                    <td><input type="button" name="view" value="Complete Task" id={{ $task->id }} class="btn btn-info btn-xs view_data" /></td>
                    @else

                <td></td>

                @endif
                @endif
                @endforeach
                @else
                    <h2>No tasks to display!</h2>
                @endif

                <div id="taskTable"></div>
                <div id="dataModal" class="modal fade">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close" data-dismiss="modal">&times;</button>
                                <h4 class="modal-title">Complete Task</h4>
                            </div>
                            <div class="modal-body" id="complete_task">
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                            </div>
                        </div>
                    </div>
                </div>

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
