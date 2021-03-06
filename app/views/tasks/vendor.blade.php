
<h1>View Active Tasks</h1>


@if (!empty($tasks))

    <table>
        <tr>
            <th>Job #</th>
            <th>Task</th>
            <th>Defendant</th>
            <th>Deadline</th>
            <th>Actions</th>
        </tr>
        @foreach ($tasks as $task)
            <tr>
                <td>{{ link_to("/jobs/{$task->job_id}", $task->job_id) }}</td>
                <td>{{ $task->process }}</td>
                <td>{{ $tableData[$task->id]["defendant"] }}</td>
                <td>{{ date("n/j/y", strtotime($task->deadline)) }}</td>
                <td><input type="button" name="view" value="Complete Task" id={{ $task->id }} class="btn btn-info btn-xs view_data" /></td>


                @endforeach
                @else
                    <h2>No tasks to display!</h2>
@endif
                <script>
                    $(document).ready(function() {

                        $('.view_data').click(function () {
                            var task_id = $(this).attr("id");
                            $.get("{{ url('tasks/test')}}", { id: task_id },
                                    function(data) {
                                        $('#complete_task').html(data);
                                        $('#dataModal').modal("show");
                                    });
                        });
                    });
                </script>