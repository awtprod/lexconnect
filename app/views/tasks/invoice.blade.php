<div>

    <form id="invoice-task">
        Client Amount: <input id="client_amt" type="text" value="{{$invoice->client_amt}}"><p>
        Server Base Rate (Base plus mileage): <input id="base_rate" type="text" value="{{$invoice->base_rate}}"><br>
        Page Rate: <input id="page_rate" type="text" value="{{$invoice->pg_rate}}"><br>
        Free Pages: <input id="free_pgs" type="text" value="{{$invoice->free_pgs}}"><br>
        Pages Served: <input id="pages" type="text" value="{{$pages}}"><br>
        Printing Costs: <input id="printing_costs" type="text" value="{{$pg_rate}}"><br>
        Vendor Total Amount:  <input id="vendor_amt" type="text" value=" "><br>
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
        $("#invoice-task").change(function () {
            var base_rate = $('#base_rate').val();
            var pg_rate = $('#pg_rate').val();
            var free_pgs = $('#free_pgs').val();
            var pages = $('#pages').val();
            var printing_costs = $('#printing_costs').val();

            var printing_total = (pages - free_pgs)*pg_rate;

            $("#printing_costs").val(printing_total);

            var vendor_total = printing_total+base_rate;

            $("#vendor_amt").val(vendor_total);
        });

        $("#invoice-task").submit(function(event){
            event.preventDefault();
            var jobId = $('#jobId').val();
            var taskId = $('#taskId').val();
            var client_amt = $('#client_amt').val();
            var vendor_amt = $('#vendor_amt').val();
            var token = $('#token').val();

            $.ajax({
                method: 'POST', // Type of response and matches what we said in the route
                url: '/tasks/invoice', // This is the url we gave in the route
                data: {taskId: taskId, jobId: jobId, client_amt: client_amt, vendor_amt: vendor_amt, _token: token }, // a JSON object to send back
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