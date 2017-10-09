<div>
    {{link_to("/tasks/service_documents/{$job->id}","Download Service Documents",["target"=>"_blank"])}}<p>

    Send documents to:
    {{$server->name}}<br>
    {{$server->address}}<br>
    {{$server->city}}, {{$server->state}} {{$server->zip_code}}<p>

    <form id="print-task">
        <select id="print_docs">
            <option value="Accept">Accept</option>
        </select>
        <input type="submit">
        <input id="taskId" name="taskId" type="hidden" value="{{ $taskId }}">
        <input id="token" name="_token" type="hidden" value="{{ csrf_token() }}">

    </form>


</div>

<script>
    $(document).ready(function () {

        $('#dataModal').modal("show");

        $("#print-task").submit(function(event){
            event.preventDefault();
            var taskId = $('#taskId').val();
            var print_docs = $('#print_docs').val();
            var accept = $('#accept').val();
            var token = $('#token').val();

            $.ajax({
                method: 'POST', // Type of response and matches what we said in the route
                url: '/tasks/vendor_print', // This is the url we gave in the route
                data: {taskId: taskId, print_docs: print_docs, _token: token }, // a JSON object to send back
                success: function(response){ // What to do if we succeed
                    console.log(response);
                    $('#dataModal').modal("hide");
                },
                error: function(jqXHR, textStatus, errorThrown) { // What to do if we fail
                    console.log(JSON.stringify(jqXHR));
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            });

    });
    });
</script>