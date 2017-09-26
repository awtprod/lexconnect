<html>
<head>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>
<body>

<div>

    Defendant: {{$job->defendant}}<br>
    Address: {{$job->street}}, &nbsp; {{$job->city}}, &nbsp;{{$job->county}},&nbsp; {{$job->state}} &nbsp; {{$job->zipcode}}<br>


    <form id="assign-task">
        {{ Form::label('Assign', 'Assign Server: ') }}
        {{ Form::select('Assign', $vendors, null, ['id' => 'Server']) }}<br>
        <input type="submit">
        <input id="serveeId" type="hidden" value="{{ $servee->id }}">
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

        $("#assign-task").submit(function () {


            var formData = new FormData(this);


            $.ajax({
                url: '/tasks/assign',
                type: 'POST',
                data: formData,
                success: function (data) {
                    alert(data)
                },
                cache: false,
                contentType: false,
                processData: false
            });

            return false;
        });
    });
</script>
</body>
</html>