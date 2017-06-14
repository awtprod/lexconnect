<html>

<head>
    <script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
    <script src="http://cdn.jsdelivr.net/jquery.validation/1.15.0/jquery.validate.js"></script>
    <script src="//d79i1fxsrar4t.cloudfront.net/jquery.liveaddress/2.8/jquery.liveaddress.min.js"></script>
    <script src="http://cdn.jsdelivr.net/jquery.validation/1.13.1/additional-methods.js"></script>
    <script>


        $(document).ready(function() {




            //Validate Data
            $("#verify-task").validate({

                rules: {
                    plaintiff: "required",
                    'documents[0][text]': "required",
                    court: "required"
                },
            });

            $(".doc_type_select").rules('add', {
                required: {
                    depends: function (element) {
                        return $('.documents').is(':filled');
                    }
                },
                messages: {
                    required: "Please select a doc type!"
                }
            });

            $(".documents").rules('add', {
                accept: "application/pdf",
                messages: {
                    required: "Document must be a pdf!"
                }
            });

        });


    </script>
</head>
<body>
<div>

    <form id="verify-task">
        <input type="text" name="website" value="<?php echo $website;?>">

    <input id="taskId" type="hidden" value="{{ $taskId }}">
    <input id="token" type="hidden" value="{{ csrf_token() }}">
    <input id="orderId" type="hidden" name="orderId" value= {{ $order->id }}>
<!-- submit buttons -->
    <input type="submit">

    {{HTML::script('/js/documents.js')}}
    </form>


</div>

<script>
    $(document).ready(function () {
        function task_table() {

            $.get("{{ url('api/tasksTable')}}",
                    function (data) {
                        $('#taskTable').html(data);
                    });
        }

        $("#verify-task").submit(function(event){
            event.preventDefault();
            var taskId = $('#taskId').val();
            var accept = $('#accept').val();
            var token = $('#token').val();

            $.ajax({
                method: 'POST', // Type of response and matches what we said in the route
                url: '/tasks/verify', // This is the url we gave in the route
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
</body>
</html>