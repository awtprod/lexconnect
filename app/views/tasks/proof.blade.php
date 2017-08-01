<html>
<head>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.0/jquery.min.js"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js"></script>
    <link href="http://netdna.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.css" rel="stylesheet">
    <link href="http://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.4/summernote.css" rel="stylesheet">
    <script src="http://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.4/summernote.js"></script>
</head>
<body>

<div id="proof" hidden>
<form id="proof_form"> <textarea id="template_body" name="template_body"></textarea>
                        <input type="hidden" name="job_id" id="job_id" value="{{ $job->id }}">
                        <input type="hidden" name="type" id="type" value="">
                        <input id="token" name="_token" type="hidden" value="{{ csrf_token() }}">
                        <input id="submit" value="Save" type="submit">
                    </form>
</div>

    <a href="{{ URL::previous() }}">Go Back</a>
    <script>
        $(document).ready(function() {

                $.ajax({

                    method: 'POST', // Type of response and matches what we said in the route
                    url: '/tasks/proof', // This is the url we gave in the route
                    data: {
                        jobId: $('#job_id').val(),
                        _token: $('#token').val(),
                        server: $('#server').find(":selected").text()
                    },
                    success: function (response) {
                        console.log(response);
                        $('#proof').show();
                        $('#template_body').summernote('code', response);

                    },
                    error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                        console.log('poop');

                        console.log(JSON.stringify(jqXHR));
                        console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                    }
                });



            $('#submit').click(function (e) {
                e.preventDefault();
                var form = $(this);
                $.ajax({
                    method: 'POST', // Type of response and matches what we said in the route
                    url: '/states/save', // This is the url we gave in the route
                    data: {template_body: $("#template_body").val(), id: $("#state_id").val(), type: $("#type").val(), _token: $("#token").val()},
                    success: function (response) {
                        console.log(response);
                    },
                    error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                        console.log(JSON.stringify(jqXHR));
                        console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                    }
                });

            });
            function task_table() {

                $.get("{{ url('api/tasksTable')}}",
                        function (data) {
                            $('#taskTable').html(data);

                        });
            }

        });
    </script>

</body>
</html>