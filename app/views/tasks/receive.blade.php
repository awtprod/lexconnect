<div>

    <bold>Are service documents complete and acceptable?</bold>

    <form id="receive-task">
        <select id="receive">
            <option value="Accept">Accept</option>
            <option value="Deny">Deny</option>
        </select>
        <input type="submit">
        <input id="taskId" type="hidden" value="{{ $taskId }}">
        <input id="token" type="hidden" value="{{ csrf_token() }}">

    </form>


</div>

<script>
    $(document).ready(function () {

        $('#dataModal').modal("show");

        function task_table() {

            $.get("{{ url('api/tasksTable')}}",
                    function (data) {
                        $('#taskTable').html(data);

                    });
        }

        $("#receive-task").submit(function(event){
            event.preventDefault();
            var taskId = $('#taskId').val();
            var accept = $('#accept').val();
            var token = $('#token').val();

            $.ajax({
                method: 'POST', // Type of response and matches what we said in the route
                url: '/tasks/vendor_print', // This is the url we gave in the route
                data: {taskId: taskId, accept: accept, _token: token }, // a JSON object to send back
                success: function(response){ // What to do if we succeed
                    console.log(response);
                    $('#dataModal').modal("hide");
                    task_table()
                },
                error: function(jqXHR, textStatus, errorThrown) { // What to do if we fail
                    console.log(JSON.stringify(jqXHR));
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            });

    });
    });
</script>