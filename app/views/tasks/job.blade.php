<html>
<head>

</head>
<body>
<input id="_token" name="_token" type="hidden" value="{{ csrf_token() }}">
<input type="hidden" id="jobId" value="{{$jobs->id}}">


@if (!empty($tasks))
    <form id="clear_steps">

    <table>
        <tr>
            <th>Task</th>
            <th>Deadline</th>
            <th>Group</th>
            <th>Completed</th>
@if(Auth::user()->user_role=="Admin")
            <th>Clear Step</th>
            <th>Delete Step</th>
@endif
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
    @if(Auth::user()->user_role=="Admin")
    @if($task->completion != NULL)
        <td><input type="checkbox" name="clear[{{ $task->id }}]" id="clear[{{ $task->id }}]" value="{{ $task->id }}"></td>
    @else
        <td></td>
    @endif
        <td><input type="button" name="{{ $task->id }}" value="Delete Step" class="btn btn-info btn-xs delete_step" /></td>
    @endif
    @endforeach
</tr>
    </table>
    <input type="submit">
    </form>
    @else
        <h2>No tasks to display!</h2>
    @endif


    <script>
        $(document).ready(function() {
            function task_table() {
                var id = $("#jobId").val();

                $.get("{{ url('api/jobsTable')}}",{id:id},
                        function (data) {
                            $('#taskTable').html(data);
                        });
            }

            $(document).ready(function() {
                $('.view_data').click(function () {
                    var task_id = $(this).attr("id");
                    var token = $('#_token').val();
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

                $('.delete_step').click(function () {
                    var task_id = $(this).attr("name");
                    var token = $('#_token').val();
                    $.ajax({
                        type: 'POST', // Type of response and matches what we said in the route
                        url:'/tasks/destroy', // This is the url we gave in the route
                        data: {id: task_id, _token: token},
                        success: function(response) {
                                console.log(response);
                                task_table()
                        },
                        error: function(jqXHR, textStatus, errorThrown) { // What to do if we fail
                            console.log(JSON.stringify(jqXHR));
                            console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                        }
                    });
                });

                $("#clear_steps").submit(function () {
                    var formData = new FormData(this);

                    $.ajax({
                        url: '/tasks/clear',
                        type: 'POST',
                        data: formData,
                        success: function (data) {
                            console.log(data);
                            task_table()

                        },
                        cache: false,
                        contentType: false,
                        processData: false
                    });

                });

            });
        });
    </script>
</tr>
</table>
</body>
</html>
