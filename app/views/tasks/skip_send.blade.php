<div>
    Requester Name: {{$requester->fname}}&nbsp;{{$requester->lname}}<br>
    Requester Email: {{$requester->email}}<br>
    Requester Company: {{$requester->company}}<p>

    Defendant: {{$job->defendant}}<br>
    Notes: {{$job->notes}}<p>

    Documents:<br>
    @if(!empty($documents))
        @foreach($documents as $document)
            {{link_to("/documents/show/{$document->id}","Supporting Documents",["target"=>"_blank"])}}<br>
        @endforeach
    @endif
    <p>
    <h3>Invoice Details: </h3><p>
    Client Amt: <input type="text" id="client_amt" name="client_amt"><br>
    Vendor Amt: <input type="text" id="vendor_amt" name="vendor_amt"><br>


    <form id="accept-task">
        <input type="submit">
        <input id="jobId" type="hidden" value="{{ $job->id }}">
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

        $("#accept-task").submit(function(event){
            event.preventDefault();
            var taskId = $('#taskId').val();
            var token = $('#token').val();

            $.ajax({
                method: 'POST', // Type of response and matches what we said in the route
                url: '/tasks/invoice', // This is the url we gave in the route
                data: {taskId: taskId, _token: token }, // a JSON object to send back
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