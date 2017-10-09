<html>
<head>

</head>
<body>
<h1>View Active Tasks</h1>


@if (!empty($tasks))

    <table>
        <tr>
            <th>Job #</th>
            <th>Order #</th>
            <th>Vendor</th>
            <th>Task</th>
            <th>Defendant</th>
            <th>Deadline</th>
            <th>Action</th>
        </tr>
        @foreach ($tasks as $task)
            <tr>
                <td>{{ link_to("/jobs/{$task->job_id}", $task->job_id) }}</td>
                <td>{{ link_to("/orders/{$task->order_id}", $task->order_id) }}</td>
                <td>{{ $tableData[$task->id]["vendor"] }}</td>
                <td>{{ str_replace('_', ' ', $task->process) }}</td>
                <td></td>
                <td>{{ date("n/j/y", strtotime($task->deadline))}}</td>
                <td><input type="button" name="view" value="Complete Task" id={{ $task->id }} class="btn btn-info btn-xs view_data" /></td>


                @endforeach
                @else
                    <h2>No tasks to display!</h2>
                @endif
                <script>
                    $(document).ready(function() {
                        function task_table() {

                            $.get("{{ url('api/tasksTable')}}",
                                    function (data) {
                                        $('#taskTable').html(data);
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
